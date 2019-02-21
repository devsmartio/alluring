<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 8/3/2018
 * Time: 1:41 PM
 */

class TrxSalidaInventario extends FastTransaction {
    function __construct(){
        parent::__construct();
        $this->instanceName = 'TrxSalidaInventario';
        $this->table = 'producto';
        $this->setTitle('Salida de inventario');
        $this->hasCustomSave = true;

        $this->fields = array(
        );

        $this->gridCols = array(
            'Código producto' => 'codigo_origen',
            'Nombre' => 'nombre',
            'Descripcion' => 'descripcion',
            'Categoria' => 'nombre_tipo',
            'Existencia' => 'haber',
            'Cantidad' => 'cantidad'
        );
    }

    protected function showModule() {
        include VIEWS . "/salida_inventario.phtml";
    }

    public function myJavascript()
    {
        parent::myJavascript();
        ?>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.2/dist/sweetalert2.all.min.js"></script>
        <script>

            app.controller('ModuleCtrl', function ($scope, $http, $rootScope) {
                $scope.rand = Math.random();
                $scope.encontrados = 0;
                $scope.$on('emptylistfilter.found', function(e, found){
                    $scope.encontrados = found;
                })
                
                $scope.startAgain = function () {
                    $scope.comentario = '';
                    $scope.idSucursalOrigen = '';
                    $scope.rows = [];
                    $scope.search = '';
                    $scope.filteredProd = [];

                    $http.get($scope.ajaxUrl + '&act=getGridCols').success(function (response) {
                        $scope.gridCols = response.data;
                    });

                    $http.get($scope.ajaxUrl + '&act=getSucursales').success(function (response) {
                        $scope.sucursalesOrigen = response.data;
                        $scope.sucursalesDestino = response.data;
                    });
                };

                $scope.$watch("rows", function(){
                    console.log("ROWS CAMBIO");
                    $scope.total = 0;
                    $scope.piezas = 0;
                    $scope.rows.forEach(r => {
                        $scope.total+= r.cantidad * (r.precio_descuento || r.precio_venta);
                        $scope.piezas+= r.cantidad;	
                    })
                }, true)

                $scope.finalizar = function () {
                    var found = $scope.rows.filter(r => r.cantidad != 0);

                    if(found.length == 0){
                        swal("Oh oh", 'Debe seleccionar al menos un producto a sacar.', "warning");
                        return;
                    }

                    if(!$scope.comentario){
                        swal("Oh oh", 'Debe ingresar un comentario de la salida', "warning");
                        return;
                    }

                    if ($scope.idSucursalOrigen == '') {
                        swal("Oh oh", 'La bodega origen es requerida para realizar un traslado.', "warning");
                        return;
                    }

                    let piezas = 0;
                    angular.forEach(found, i => piezas += i.cantidad);
                    swal({
                        title: "Confirmar salida",
                        text: `¿Desea confirmar la salida de ${piezas} piezas de producto?`,
                        showCancelButton: true,
                        confirmButtonText: "Confirmar",
                        cancelButtonText: "Cancelar"
                    }).then(val => {
                        if(val.value == true){
                            
                            let dataAEnviar = {
                                idSucursalOrigen: $scope.idSucursalOrigen,
                                productos: found,
                                comentario: $scope.comentario
                            }
                            $rootScope.modData = dataAEnviar;
				            console.log(dataAEnviar);
                            $scope.doSave();
                        }
                    })
                };
                $("#pistolearItem").keyup(function(ev) {
                    let val = $(this).val();
                    if (ev.which === 13 && val) {
                        if($scope.filteredProd.length == 1){
                            let item = $scope.filteredProd[0];
                            if(item.cantidad + 1 > item.total_existencias){
                                swal("Oh oh", `Ya tiene la existencia máxima de ${val} `, "warning");
                            } else {
                                item.cantidad = Math.min(item.total_existencias, item.cantidad + 1);
                                $scope.$apply();
                                $(this).select();
                            }
                        } else {
                            let item = $scope.filteredProd.find(i => i.codigo.toLowerCase() == val.toLowerCase());
                            if(item){
                                if(item.cantidad + 1 > item.total_existencias){
                                    swal("Oh oh", `Ya tiene la existencia máxima de ${val} `, "warning");
                                } else {
                                    item.cantidad = Math.min(item.total_existencias, item.cantidad + 1);
                                    $scope.$apply();
                                    $(this).select();
                                }
                            } else {
                                swal("Oh oh", `Item ${val} no encontrado`, "warning");
                            }
                        }
                    } 
                });
                    

                $scope.cancelar = function () {
                    $scope.cancel();
                };

                $scope.startAgain();
                $rootScope.addCallback(function (response) {
                    if ((response != undefined) && (response.result == 1)) {
                        if (response.data) {
                            //id_movimiento_sucursales = response.data.id_movimiento_sucursales;
                            //window.open("./?action=pdf&tmp=CON&id_movimiento_sucursales=" + id_movimiento_sucursales);
                        }
                    }
                    $scope.startAgain();
                });


                $scope.filtarProductos = function() {
                    $http.get($scope.ajaxUrl + '&act=getProductos&id_sucursal='+$scope.idSucursalOrigen).success(function (response) {
                        $scope.rows = response.data;
                    });
                };

                $scope.trasladarTodo = function() {
                    angular.forEach($scope.rows, function(itm){ itm.cantidad = parseInt(itm.total_existencias) });
                };
            });
        </script>
    <?php
    }

    public function getGridCols(){
        $resultSet = array();
        foreach($this->gridCols as $colLabel => $colValue){
            $toAdd = array(
                'LABEL' => $colLabel,
                'VALOR' => $colValue
            );
            $resultSet[] = $toAdd;
        }
        echo json_encode(array('data' => $resultSet));
    }

    public function getSucursales(){
        $resultSet = array();
        $accesos = $this->db->query_select("usuarios_bodegas", sprintf("id_usuario='%s'", $this->user['ID']));
        $i = 1;
        $strAccesos = "";
        foreach($accesos as $a){
            $strAccesos .= $a["id_bodega"] . (count($accesos) > $i ? "," : "");
            $i++;
        };

        $dsBodegas = Collection::get($this->db, 'sucursales', sprintf('id_sucursal in (%s)', $strAccesos))->toArray();

        foreach($dsBodegas as $p){
            $resultSet[] = array('id_sucursal' => $p['id_sucursal'], 'nombre' => $p['nombre']);
        }

        echo json_encode(array('data' => sanitize_array_by_keys($resultSet, ['nombre'])));
    }

    public function getProductos(){
        $resultSet = [];
        try {
            $id_sucursal = getParam('id_sucursal');
            $resultSet = $this->db->query_toArray('
            select	max(trx.haber) AS haber, 
                p.codigo, 
                p.nombre, 
                p.descripcion,
                p.id_producto,
                p.precio_venta,
                p.imagen,
                COALESCE((sum(trx.haber) - sum(trx.debe)),0) AS total_existencias
            from 	trx_transacciones trx
            inner join producto p ON p.id_producto = trx.id_producto
            inner join tipo t ON t.id_tipo = p.id_tipo
            where 	trx.id_sucursal = ' . $id_sucursal . '
            GROUP BY
                p.id_producto
            HAVING	(sum(trx.haber) - sum(trx.debe)) > 0;');

            for($i = 0; count($resultSet) > $i; $i++){
                $resultSet[$i]['cantidad'] = 0;
            }
        } catch(Exception $e){
            error_log($e->getTraceAsString());
        }
        echo json_encode(array('data' => sanitize_array_by_keys($resultSet, ['nombre','descripcion' ])));
    }

    public function dataIsValid($data)
    {
        if ($this->r == 0) {
            return false;
        }

        return true;
    }

    public function doSave($data)
    {
        $data = inputStreamToArray(false);
        $data = $data['data'];
        $fecha = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $dsEmpleado = Collection::get($this->db, 'empleados', sprintf('id_usuario = "%s"', $user['ID']))->single();
        $dsCuenta = Collection::get($this->db, 'cuentas', 'lower(nombre) = "inventario"')->single();
	//print_r($data);
        try {

            $trx_movimiento_sucursales = [
                'id_movimiento_sucursales_estado' => sqlValue('2', 'int'),
                'id_empleado_envia' => sqlValue($this->user['ID'], 'text'),
                'id_sucursal_origen' => sqlValue($data['idSucursalOrigen'], 'int'),
                'comentario_envio' => sqlValue($data['comentario'], 'text'),
                'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date')
            ];
            

            $this->db->query_insert('trx_movimiento_sucursales', $trx_movimiento_sucursales);

            $id_movimiento_sucursales = $this->db->max_id('trx_movimiento_sucursales', 'id_movimiento_sucursales');

            foreach ($data['productos'] as $prod) {

                $transaccion = [
                    'id_cuenta' => sqlValue($dsCuenta['id_cuenta'], 'int'),
                    'usuario_creacion' => sqlValue($this->user['ID'], 'text'),
                    'id_sucursal' => sqlValue($data['idSucursalOrigen'], 'int'),
                    'descripcion' => sqlValue('Traslado Productos', 'text'),
                    'id_producto' => sqlValue($prod['id_producto'], 'int'),
                    'debe' => sqlValue($prod['cantidad'], 'float'),
                    'haber' => sqlValue('0', 'float'),
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date')
                ];

                $this->db->query_insert('trx_transacciones', $transaccion);

                $id_transaccion_origen = $this->db->max_id('trx_transacciones', 'id_transaccion');

                $trx_movimiento_sucursales_detalle = [
                    'id_movimiento_sucursales' => sqlValue($id_movimiento_sucursales, 'int'),
                    'id_producto' => sqlValue($prod['id_producto'], 'int'),
                    'unidades' => sqlValue($prod['cantidad'], 'int'),
                    'id_transaccion' => sqlValue($id_transaccion_origen, 'int')
                ];

                
                $this->db->query_insert('trx_movimiento_sucursales_detalle', $trx_movimiento_sucursales_detalle);
            }

            $this->r = 1;
            $this->msg = 'Traslado realizado con éxito';
            $this->returnData = array('id_movimiento_sucursales' => $id_movimiento_sucursales);
        } catch(Exception $e) {
            $this->r = 0;
            $this->msg = $e->getMessage();
        }
    }
}
