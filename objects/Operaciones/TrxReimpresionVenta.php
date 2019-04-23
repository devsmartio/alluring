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
class TrxReimpresionVenta extends FastTransaction {
    function __construct(){
        parent::__construct();
        $this->setTitle('Reimpresión venta');
        $this->hasCustomSave = true;
    }
    
    protected function showModule() {
        include VIEWS . "/reimpresion_venta.phtml";
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
                GROUP BY v.id_venta
                ORDER by v.fecha_creacion desc
            ", $where, $user);
        }
        $result = sanitize_array_by_keys($this->db->queryToArray($query), ['nombres', 'apellidos', 'usuario_venta']);
        echo json_encode($result);
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
