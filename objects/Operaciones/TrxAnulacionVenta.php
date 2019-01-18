<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TrxAvancesProyecto
 *
 * @author baci5
 */
class TrxAnulacionVenta extends FastTransaction {
    function __construct(){
        parent::__construct();
        $this->setTitle('Anulación venta');
        $this->hasCustomSave = true;
    }
    
    protected function showModule() {
        include VIEWS . "/anulacion_venta.phtml";
    }
    
    public function myJavascript() {
        parent::myJavascript();
        ?>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.2/dist/sweetalert2.all.min.js"></script>
    <script>
        $("#fechaDe, #fechaA").datetimepicker({
            format: 'DD/MM/YYYY',
            maxDate: moment(),
        })
        app.controller('ModuleCtrl', function($scope, $http, $rootScope, $timeout){
            $scope.searchVentas = function(){
                let list = ['nombres', 'apellidos'];
                let query = "";
                for(let i = 0; list.length > i; i++){
                    let q = list[i];
                    query = query ? `${query}&` : query;
                    query += `${q}=${$scope.search[q]}`
                }
                let fechaDe = $("#fechaDe").val();
                console.log(fechaDe);
                let fechaA = $("#fechaA").val();
                if(fechaDe){
                    query = query ? `&${query}` : query;
                    query+='&fechaDe=' + fechaDe;
                    query+='&fechaA=' + fechaA;
                    $http.get(`${$scope.ajaxUrl}&act=getVentasFiltradas${query}`).success(res => {
                        $scope.ventas = res;
                    })
                } else {
                    swal("Campo requerido", "El campo \"fecha de\" es requerido", "warning");
                }
                $("#nombres").select();
            }
            
            $scope.startAgain = function(){
                let date = moment();  
                $scope.search = {
                    fechaDe: date.format('DD/MM/YYYY'),
                    fechaA: date.format('DD/MM/YYYY'),
                    nombres: "",
                    apellidos: ""
                } 
                $scope.ventas = [];
                $scope.searchVentas();
            };

            $scope.generarPdf = function(idVenta){
                window.open("?action=pdf&tmp=VT&id_venta=" + idVenta, '_blank');
            }

            $scope.anularVenta = function(venta){
                swal({
                    title: "Anular venta",
                    text: `¿Esta seguro de anular la venta (${venta.id_venta}) de ${venta.nombres} ${venta.apellidos}? Esta acción no es reversible.`,
                    type: "warning",
                    confirmButtonText: "Confirmar",
                    cancelButtonText: "Cancelar",
                    showCancelButton: true
                }).then(res => {
                    if(res.value === true){
                        $http.get($scope.ajaxUrl + '&act=anularVenta&id_venta=' + venta.id_venta).success(function (response) {
                            if(response.result == 1){
                                swal("Anular", "Se ha reingresado el inventario", "success");
                                $scope.startAgain();
                            } else {
                                swal("Oh oh", "Ocurrió un error al anular el pedido. Intente más tarde", "error");
                            }
                        });
                    }
                })
            }
            
            

            $(".searchEnter").keyup(e => {
                e.which == 13 && $scope.searchVentas();
            })

            $scope.cancelar = function(){
                $scope.cancel();
            };
            
            $scope.startAgain();
            $rootScope.addCallback(function(){
                $scope.startAgain(); 
            });
        });
    </script>
        <?php
    }

    public function getVentasFiltradas(){
        $nombres = getParam('nombres');
        $apellidos = getParam('apellidos');
        $fechaDe = getParam('fechaDe');
        $fechaA = getParam('fechaA');
        $where = "";
        $user = decode_email_address($this->user['ID']);
        if(!empty($nombres)){
            $where = "c.nombres like '%$nombres%'";
        }

        if(!empty($apellidos)){
            $where = empty($where) ? $where : "$where AND ";
            $where.= "c.apellidos like '%$apellidos%'";
        }

        if(!empty($fechaDe)){
            $fechaDe = DateTime::createFromFormat('d/m/Y', $fechaDe)->format('Y-m-d');
            $where = empty($where) ? $where : "$where AND ";
            $where.= "cast(v.fecha_creacion as date) between cast('$fechaDe' as date) ";

            if(!empty($fechaA)){
                $fechaA = DateTime::createFromFormat('d/m/Y', $fechaA)->format('Y-m-d');
                $where.= "AND cast('$fechaA' as date)";
            } else {
                $where.= "AND now()";
            }
        }
        if($this->user['FK_PROFILE'] == 1){
            $query = sprintf("SELECT v.id_venta, v.fecha_creacion, v.total, sum(vt.cantidad) piezas, c.nombres, c.apellidos, v.usuario_venta
                FROM trx_venta v
                JOIN clientes c on c.id_cliente=v.id_cliente
                JOIN trx_venta_detalle vt on vt.id_venta=v.id_venta
                WHERE %s
                AND es_anulado = 0
                GROUP BY v.id_venta
                ORDER BY v.fecha_creacion desc
            ", $where);
        } else {
            $query = sprintf("SELECT v.id_venta, v.fecha_creacion, v.total, sum(vt.cantidad) piezas, c.nombres, c.apellidos, v.usuario_venta
                FROM trx_venta v
                JOIN clientes c on c.id_cliente=v.id_cliente
                JOIN trx_venta_detalle vt on vt.id_venta=v.id_venta 
                WHERE %s
                AND v.usuario_venta='%s'
                AND es_anulado = 0
                GROUP BY v.id_venta
                ORDER by v.fecha_creacion desc
            ", $where, $user);
        }
        $result = sanitize_array_by_keys($this->db->queryToArray($query), ['nombres', 'apellidos', 'usuario_venta']);
        echo json_encode($result);
    }

    public function anularVenta(){
        $id = getParam("id_venta");
        $fecha = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $dsEmpleado = decode_email_address($this->user['ID']);
        $dsCuentaVenta = Collection::get($this->db, 'cuentas', 'lower(nombre) = "venta"')->single();
        $dsCuentaReingreso = Collection::get($this->db, 'cuentas', 'lower(nombre) = "reingreso"')->single();
        $dsMoneda = Collection::get($this->db, 'monedas', 'moneda_defecto = 1')->single();
        $venta = $this->db->query_select("trx_venta", sprintf("id_venta=%s", $id));
        if(count($venta) > 0){
            $this->db->query("START TRANSACTION");
            try {
                $venta = $venta[0];
                $detalles = $this->db->query_select("trx_venta_detalle", sprintf("id_venta=%s", $id));
                foreach($detalles as $prod){
                    $transaccion = [
                        'id_cuenta' => sqlValue($dsCuentaVenta['id_cuenta'], 'int'),
                        'id_sucursal' => sqlValue($prod['id_sucursal'], 'int'),
                        'descripcion' => sqlValue('Anulación pedido', 'text'),
                        'id_producto' => sqlValue($prod['id_producto'], 'int'),
                        'haber' => sqlValue($prod['cantidad'], 'float'),
                        'debe' => sqlValue('0', 'float'),
                        'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date')
                    ];

                    $this->db->query_insert('trx_transacciones', $transaccion);
                }
                $ventaUpd = [
                    "estado" => sqlValue('AV', 'text'),
                    'es_anulado' => 1
                ];
                $this->db->query_update("trx_venta", $ventaUpd, sprintf("id_venta=%s", $venta['id_venta']));
                $this->db->query("COMMIT");
                echo json_encode(['result' => 1]);
                
            }catch(Exception $e){
                $this->db->query("ROLLBACK");
                echo json_encode(['result' => 0]);
            }
        }
    }
    
    public function dataIsValid($data) {
        $comentario = $data["comentario"];
        if(isEmpty($comentario)){
            $this->r = 0;
            $this->msg = "El comentario no puede estar vacio";
        }
        if($this->r == 0){
            return false;
        }
        return true;
    }
    
    public function doSave($data){
        $this->r = 1;
        $this->msg = 'Usted envio ' . $data["comentario"];
    }
}
