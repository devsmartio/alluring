<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TrxMovimientoSucursales
 *
 * @author baci5
 */
class TrxMovimientoSucursales extends FastTransaction{
    function __construct(){
        parent::__construct();
        $this->setTitle('Movimiento de producto');
        $this->hasCustomSave = true;
    }
    
    protected function showModule() {
        include VIEWS . "/movimiento_sucursales.phtml";
    }
    
    public function myJavascript() {
        parent::myJavascript();
        ?>
    <script>
        app.controller('ModuleCtrl', function($scope, $http, $rootScope, $timeout){
            $scope.startAgain = function(){
               $scope.productos = [];
               $scope.productosOr = [];
               $scope.productosSel = [];
               $scope.envios = [];
               $scope.porRecibir = [];
               $scope.sucursales = [];
               $scope.sucursal = { id_sucursal: ''};
               $scope.inRecibirEnvio = false;
               $scope.inNuevoEnvio = false;
               $scope.inDetalle = false;
               $scope.esSuperUsuario = false;
               $scope.envioActual = {
                   sucursalOrigen: '', sucursalDestino: '', comentario_envio: '', comentario_destino: ''
               };
               $scope.checkSuper();
               $scope.getEnvios();
               $scope.getSucursales();
            };
            
            $scope.checkSuper = function(){
                $http.get($scope.ajaxUrl + '&act=checkSuper').success(function(response){
                    $scope.esSuperUsuario = response.result;
                });   
            }
            
            $scope.getEnvios = function(){
                $http.get($scope.ajaxUrl + '&act=getEnvios').success(function(response){
                    if(response.result == 2){
                        $scope.alerts.push({
                            type: 'alert-warning',
                            msg: 'El usuario debe tener configurado un empleado para usar esta pantalla'
                        });
                    } else if(response.result == 1){
                        $scope.recibir = response.recibir;
                        $scope.enviados = response.enviados;
                    }
                });
            };
            
            $scope.getProductos = function(){
                $http.get($scope.ajaxUrl + '&act=getProductos').success(function(response){
                    $scope.productos = response.data;
                    $scope.productosOr = response.data;
                });
            };
            
            $scope.getSucursales = function(){
                $http.get($scope.ajaxUrl + '&act=getSucursales').success(function(response){
                    if(response.result == 2){
                        $scope.alerts.push({
                            type: 'alert-warning',
                            msg: 'El usuario debe tener configurado un empleado para usar esta pantalla'
                        });
                    } else if(response.result == 1){
                        $.each(response.sucursales, function($in, item){
                            if(item.es_actual){
                                $scope.sucursal = item;
                            }
                        });
                        $scope.sucursales = response.sucursales;
                    }
                });
            };
            
            $scope.doNew = function(){
                $scope.getProductos();
                $scope.inNuevoEnvio = true;
                $scope.inRecibirEnvio = false;
                $scope.inDetalle = false;
                $scope.envioActual.id_sucursal_origen = $scope.sucursal.id_sucursal;
            };
            
            $scope.doList = function(){
                $scope.envioActual = {
                   id_sucursal_origen: '', id_sucursal_destino: '', comentario_envio: '', comentario_destino: ''
               };
                $scope.inNuevoEnvio = false;
                $scope.inRecibirEnvio = false;
                $scope.inDetalle = false;
            };
            
            $scope.doRecibir = function(envio){
                $scope.envioActual = envio;
                $scope.inNuevoEnvio = false;
                $scope.inRecibirEnvio = true;
                $scope.inDetalle = false;
            };
            
            $scope.doDetalle = function(envio){
                $scope.envioActual = envio;
                $scope.inNuevoEnvio = false;
                $scope.inRecibirEnvio = false;
                $scope.inDetalle = true;
            }
            
            $scope.addProducto = function(producto){
                $scope.productosSel.push(producto);
                $scope.filterOriginal();
            };
            
            $scope.filterOriginal = function(){
                currentIds = $scope.getIdProductosSel();
                $r = $scope.productosOr.filter(function(p){
                    return !currentIds.includes(p.id_producto);
                });
                $scope.productos = $r;
            };
            
            $scope.getIdProductosSel = function(){
                ids = new Array();
                $.each($scope.productosSel, function($in, item){
                    ids.push(item.id_producto); 
                });
                return ids;
            };
            
            $scope.clearFilter = function(){
                $scope.search.codigo_producto = '';
                $scope.search.nombre = '';
                $scope.search.descripcion = '';
                $scope.search.sku = '';
                $scope.search.proveedor_label = '';
            }
            
            $scope.removeItem = function(p){
                $r = $scope.productosSel.filter(function(item){
                    return item.id_producto != p.id_producto;
                });
                $scope.productosSel = $r;
                $scope.filterOriginal();
                $scope.doCosto();
            };
            
            $scope.finalizarModal = function(){
                if($scope.productosSel.length == 0){
                    $timeout(function(){
                        $scope.alerts.push({type: 'alert-danger', msg: 'Debe agregar al menos un producto'});
                    }, 2000);
                } else {
                    $("#finModal").modal();
                }
            };
            
            $scope.finalizarEnvio = function(){
                $rootScope.modData = {
                    comentario: $scope.comentario,
                    envio: $scope.envioActual,
                    productos: $scope.productosSel,
                    mod: 1
                };
                $scope.doSave();
            };
            
            $scope.finalizarRecepcion = function(){
                $rootScope.modData = {
                    envio: $scope.envioActual,
                    mod: 2
                };
                $scope.doSave();
            }
            
            $scope.cancelar = function(){
                $scope.cancel();
            };

            $scope.startAgain();
            $rootScope.addCallback(function(response){
                $('#finModal').modal('hide');
                if(response.data.id_movimiento_sucursales){
                    window.open("./?action=pdf&tmp=TS&id_movimiento_sucursales="+response.data.id_movimiento_sucursales, "_blank");
                }                
                $scope.startAgain(); 
            });            
        });
    </script>
        <?php
    }
    
    public function checkSuper(){
        $result = EmpleadoUtil::esSuperUsuario();
        echo json_encode(['result' => $result]);
    }
    
    protected function dataIsValid($data){
        $mod = $data['mod'];
        switch ($mod){
            case 1: {
                if(isEmpty($data['envio']['comentario_envio'])){
                    $this->r = 0;
                    $this->msg = 'Debe ingresar un comentario de envio';
                    return false;
                } else if(count($data['productos']) == 0){
                    $this->r = 0;
                    $this->msg = 'No puede enviar 0 productos';
                    return false;
                } else if($data['envio']['id_sucursal_origen']  == $data['envio']['id_sucursal_destino']){
                    $this->r = 0;
                    $this->msg = 'El origen y destino no debe ser el mismo';
                    return false;
                } else {
                    foreach($data['productos'] as $p){
                        if(isEmpty($p['unidades']) || $p['unidades'] == 0){
                            $this->r = 0;
                            $this->msg = 'No puede enviar 0 unidades del producto [' . $p['codigo_producto'] . ']';
                            return false;
                        }
                    }
                }
                break;
            }
            case 2: {
                if(isEmpty($data['envio']['comentario_destino'])){
                    $this->r = 0;
                    $this->msg = 'Debe ingresar un comentario de ingreso';
                    return false;
                }
            }
                
        }
        return true;
    }
    
    protected function doSave($data){
        if($data['mod'] == 1){
            $this->doNew($data);
        } else if($data['mod'] == 2){
            $this->doRecibir($data);
        }
    }
    
    private function doRecibir($data){
        $date = (new DateTime())->format(SQL_DT_FORMAT);
        $empleado = EmpleadoUtil::getEmpleado();
        $envio = $data['envio'];
        $detalles = $this->db->query_select('trx_movimiento_sucursales_detalle', sprintf('id_movimiento_sucursales=%s', $envio['id_movimiento_sucursales']));
        foreach($detalles as $d){
            $transaccionOrigen = [
                'id_cuenta' => Catalogos::Cuentas_Inventario,
                'id_empleado' => $envio['id_empleado_envia'],
                'id_sucursal' => $envio['id_sucursal_origen'],
                'descripcion' => sqlValue('Traslado sucursales ' . $envio['id_movimiento_sucursales'], 'text'),
                'id_producto' => $d['id_producto'],
                'debe' => $d['unidades'],
                'haber' => 0,
                'fecha_creacion' => sqlValue($date, 'date')
            ];
            $this->db->query_insert('trx_transacciones', $transaccionOrigen);
            $transaccionOrigenId = $this->db->max_id('trx_transacciones', 'id_transaccion');
            
            $transaccionDestino = [
                'id_cuenta' => Catalogos::Cuentas_Inventario,
                'id_empleado' => $empleado['id_empleado'],
                'id_sucursal' => $envio['id_sucursal_destino'],
                'descripcion' => sqlValue('Traslado sucursales ' . $envio['id_movimiento_sucursales'], 'text'),
                'id_producto' => $d['id_producto'],
                'debe' => 0,
                'haber' => $d['unidades'],
                'fecha_creacion' => sqlValue($date, 'date')
            ];
            $this->db->query_insert('trx_transacciones', $transaccionDestino);
            $transaccionDestinoId = $this->db->max_id('trx_transacciones', 'id_transaccion');
            
            $this->db->query_update('trx_movimiento_sucursales_detalle', ['id_transaccion' => $transaccionOrigenId, 'id_transaccion_destino' => $transaccionDestinoId], sprintf('id_movimiento_sucursales_detalle=%s', $d['id_movimiento_sucursales_detalle']));
        }
        
        $updateMovimiento = [
            'comentario_recepcion' => sqlValue($envio['comentario_destino'], 'text'),
            'id_empleado_recibe' => $empleado['id_empleado'],
            'fecha_recepcion' => sqlValue($date, 'date'),
            'id_movimiento_sucursales_estado' => Catalogos::MovimientoSucursalesEstado_Entregada
        ];
        
        $this->db->query_update('trx_movimiento_sucursales', $updateMovimiento, sprintf('id_movimiento_sucursales=%s', $envio['id_movimiento_sucursales']));
        
        $this->r = 1;
        $this->msg = count($detalles) . ' productos ingresados con éxito';
    }
    
    private function doNew($data){
        $date = (new DateTime())->format(SQL_DT_FORMAT);
        $empleado = EmpleadoUtil::getEmpleado();
        $envio = $data['envio'];
        $movimiento = [
            'id_movimiento_sucursales_estado' => Catalogos::MovimientoSucursalesEstado_EnRuta,
            'id_empleado_envia' => $empleado['id_empleado'],
            'id_sucursal_origen' => $envio['id_sucursal_origen'],
            'id_sucursal_destino' => $envio['id_sucursal_destino'],
            'comentario_envio' => sqlValue($envio['comentario_envio'], 'text'),
            'fecha_creacion' => sqlValue($date, 'date')
        ];
        
        $this->db->query_insert('trx_movimiento_sucursales', $movimiento);
        $id_movimiento = $this->db->max_id('trx_movimiento_sucursales', 'id_movimiento_sucursales');
        if(!$id_movimiento){
            $this->r = 0;
            $this->msg = 'Error Inesperado';
        } else {
            foreach($data['productos'] as $p){
                $detalle = [
                    'id_movimiento_sucursales' => $id_movimiento,
                    'id_producto' => $p['id_producto'],
                    'unidades' => $p['unidades']
                ];
                
                $this->db->query_insert('trx_movimiento_sucursales_detalle', $detalle);
            }
            
            $this->r = 1;
            $this->msg = 'Productos enviados con éxito. En espera de confirmación.';
            $this->returnData['id_movimiento_sucursales'] = $id_movimiento;
            
        }
    }
    
    public function getEnvios(){
        $empleado = EmpleadoUtil::getEmpleado();
        $porRecibir = [];
        $enviados = [];
        $r = 1;
        
        if(!$empleado){
            $r = 2;
        } else {
            $queryRecibir = 'select m.*, s.nombre origen_nombre, e.nombres empleado_origen '
                    . 'from trx_movimiento_sucursales m '
                    . 'join sucursales s on s.id_sucursal=m.id_sucursal_origen '
                    . 'join empleados e on e.id_empleado=m.id_empleado_envia '
                    . 'where m.id_sucursal_destino=%s && m.id_movimiento_sucursales_estado=%s';
            
            $porRecibir = $this->db->queryToArray(sprintf($queryRecibir, $empleado['id_sucursal'], Catalogos::MovimientoSucursalesEstado_EnRuta));
            
            $queryEnviados = 'select m.*, s.nombre destino_nombre, e.nombres empleado_origen '
                    . 'from trx_movimiento_sucursales m '
                    . 'join sucursales s on s.id_sucursal=m.id_sucursal_destino '
                    . 'join empleados e on e.id_empleado=m.id_empleado_envia '
                    . 'where m.id_sucursal_origen=%s && m.id_movimiento_sucursales_estado=%s';
            $enviados = $this->db->queryToArray(sprintf($queryEnviados, $empleado['id_sucursal'], Catalogos::MovimientoSucursalesEstado_EnRuta));
            
            for($i = 0; count($porRecibir) > $i; $i++){
                $queryProductos = 'select p.nombre, d.unidades, m.nombre nombre_marca '
                        . 'from trx_movimiento_sucursales_detalle d '
                        . 'join producto p on p.id_producto=d.id_producto '
                        . 'join marca m on m.id_marca=p.id_marca '
                        . 'where d.id_movimiento_sucursales=%s';
                $productos = $this->db->queryToArray(sprintf($queryProductos, $porRecibir[$i]['id_movimiento_sucursales']));
                $porRecibir[$i]['productos'] = sanitize_array_by_keys($productos, ['nombre']);
            }
            
            for($i = 0; count($enviados) > $i; $i++){
                $queryProductos = 'select p.nombre, d.unidades, m.nombre nombre_marca '
                        . 'from trx_movimiento_sucursales_detalle d '
                        . 'join producto p on p.id_producto=d.id_producto '
                        . 'join marca m on m.id_marca=p.id_marca '
                        . 'where d.id_movimiento_sucursales=%s';
                $productos = $this->db->queryToArray(sprintf($queryProductos, $enviados[$i]['id_movimiento_sucursales']));
                $enviados[$i]['productos'] = sanitize_array_by_keys($productos, ['nombre']);
            }
        }
        $enviados = sanitize_array_by_keys($enviados, ['comentario_envio', 'comentario_recepcion', 'destino_nombre', 'empleado_origen']);
        $porRecibir = sanitize_array_by_keys($porRecibir, ['comentario_envio', 'comentario_recepcion', 'origen_nombre', 'empleado_origen']);
        echo json_encode(['result' => $r, 'recibir' => $porRecibir, 'enviados' => $enviados]);
    }
    
    public function getProductos(){
        $user = AppSecurity::$UserData['data'];
        $productos = Collection::get($this->db, 'producto')->select(array('id_tipo', 'id_subtipo', 'id_marca', 'id_proveedor' ,'id_producto', 'nombre', 'descripcion', 'sku', 'minimo_inventario', 'costo'), true)->toArray();
        $proveedores = Collection::get($this->db, 'proveedor');
        for($i=0; count($productos) > $i; $i++){
            $inventario = ProductoUtil::getInventario($productos[$i], $this->db);
            $local = $inventario['haber_local'] - ($inventario['debe_local'] + $inventario['en_traslado']);
            $global = $inventario['haber_global'] - ($inventario['debe_global'] + $inventario['en_traslado']);
            $productos[$i]['stock'] = $local;
            $productos[$i]['stock_global'] = $global;
            $productos[$i]['unidades'] = 1;
            $productos[$i]['codigo_producto'] = ProductoUtil::getCodigoProducto($productos[$i], $this->db);
            $prov = $proveedores->where(array('id_proveedor' => $productos[$i]['id_proveedor']))->single();
            $productos[$i]['proveedor_label'] = $prov['nombre'];
            unset($productos[$i]['id_tipo']);
            unset($productos[$i]['id_subtipo']);
            unset($productos[$i]['id_proveedor']);
            unset($productos[$i]['id_marca']);
        }
        $productos = sanitize_array_by_keys($productos, array('codigo_producto', 'proveedor_label'));
        echo json_encode(array('data' => $productos));
    }
    
    public function getSucursales(){
        $r = 1;
        $empleado = EmpleadoUtil::getEmpleado();
        if(!$empleado){
            $r = 2;
        } else {
            $sucursales = Collection::get($this->db, 'sucursales')->select(['nombre', 'id_sucursal'], true)->toArray();
            for($i = 0; count($sucursales) > $i; $i++){
                if($sucursales[$i]['id_sucursal'] == $empleado['id_sucursal']){
                    $sucursales[$i]['es_actual'] = 1;
                } else {
                    $sucursales[$i]['es_actual'] = 0;
                }
            }
        }
        echo json_encode(['result' => $r, 'sucursales' => $sucursales]);
    }
}
