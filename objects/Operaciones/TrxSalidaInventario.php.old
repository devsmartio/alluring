<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TrxSalidaInventario
 *
 * @author Usuario
 */
class TrxSalidaInventario extends FastTransaction{
    function __construct(){
        parent::__construct();
        $this->setTitle('Salida de inventario');
        $this->hasCustomSave = true;
    }
    
    protected function showModule() {
        include VIEWS . "/salida_inventario.phtml";
    }
    
    public function myJavascript() {
        parent::myJavascript();
        ?>
    <script>
        app.controller('ModuleCtrl', function($scope, $http, $rootScope, $timeout){
            $scope.startAgain = function(){
               $scope.productos = new Array();
               $scope.productosOr = new Array();
               $scope.productosSel = new Array();
               $scope.totalCosto = 0;
               $scope.comentario = '';
               $scope.getProductos();
            };
            
            $scope.getProductos = function(){
                $http.get($scope.ajaxUrl + '&act=getProductos').success(function(response){
                    $scope.productos = response.data;
                    $scope.productosOr = response.data;
                });
            };
            
            $scope.addProducto = function(producto){
                $scope.productosSel.push(producto);
                $scope.doCosto();
                $scope.doPrecio(producto);
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
            
            $("#btnClientesCat").click(function(){
                $scope.getClientes();
            });
            
            $scope.selectCliente = function(cliente){
                $scope.cliente = cliente;
                $scope.clienteSeleccionadoMode = true;
                $("#clientesCat").modal('hide');
                $("#filterProductos").focus();
            };
            
            $scope.doCosto = function(){
                $scope.totalCosto = 0;
                $.each($scope.productosSel, function($in, item){
                    $scope.totalCosto += (item.unidades * item.costo);
                });
                $scope.totalCosto = ($scope.totalCosto).toFixed(2);
            };
            
            $scope.doPrecio = function(p){
                p.costoTotal = (p.costo * p.unidades).toFixed(2);
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
            
            $scope.finalizar = function(){
                $rootScope.modData = {
                    comentario: $scope.comentario,
                    productos: $scope.productosSel
                };
                $scope.doSave();
            };
            
            $scope.cancelar = function(){
                $scope.cancel();
            };
            
            $scope.startAgain();
            $rootScope.addCallback(function(){
                $('#finModal').modal('hide');
                $scope.startAgain(); 
            });
        });
    </script>
        <?php
    }
    
    public function getProductos(){
        $user = AppSecurity::$UserData['data'];
        $marcas = Collection::get($this->db, 'marca');
        $productos = Collection::get($this->db, 'producto')->select(array('id_tipo', 'id_subtipo', 'id_marca', 'id_proveedor' ,'id_producto', 'nombre', 'descripcion', 'sku', 'minimo_inventario', 'costo'), true)->toArray();
        $proveedores = Collection::get($this->db, 'proveedor');
        for($i=0; count($productos) > $i; $i++){
            $inventario = ProductoUtil::getInventario($productos[$i], $this->db);
            $local = $inventario['haber_local'] - $inventario['debe_local'];
            $global = $inventario['haber_global'] - $inventario['debe_global'];
            $productos[$i]['stock'] = $local;
            $productos[$i]['stock_global'] = $global;
            $productos[$i]['unidades'] = 1;
            $productos[$i]['codigo_producto'] = ProductoUtil::getCodigoProducto($productos[$i], $this->db);
            $prov = $proveedores->where(array('id_proveedor' => $productos[$i]['id_proveedor']))->single();
            $productos[$i]['proveedor_label'] = $prov['nombre'];
            $marc = $marcas->where(['id_marca' => $productos[$i]['id_marca']])->single();
            $productos[$i]['nombre_marca'] = self_escape_string($marc['nombre']);
            unset($productos[$i]['id_tipo']);
            unset($productos[$i]['id_subtipo']);
            unset($productos[$i]['id_proveedor']);
            unset($productos[$i]['id_marca']);
        }
        $productos = sanitize_array_by_keys($productos, array('codigo_producto', 'proveedor_label'));
        echo json_encode(array('data' => $productos));
    }
    
    public function getSucursales(){
        Collection::get($this->db, 'sucursales')
                ->select(['id_sucursal', 'nombre'], true)
                ->toJSON();
    }
    
    public function dataIsValid($data) {
        $productos = $data['productos'];
        $empleado = EmpleadoUtil::getEmpleado();
        if(!$empleado){
            $this->r = 0;
            $this->msg = 'Debe configurar a un empleado con el usuario actual para continuar con la transaccion';
        }
        if(!isset($data['comentario']) || isEmpty($data['comentario'])){
            $this->r = 0;
            $this->msg = 'Debe agregar un comentario a la salida';
        }
        foreach($productos as $p){
            if($p['unidades'] == 0){
                $this->r = 0;
                $this->msg = 'No se puede ingresar un producto con cantidad 0';
            }
            if(!isset($p['comentarioDetalle']) || isEmpty($p['comentarioDetalle'])){
                $this->r = 0;
                $this->msg = 'Debe seleccionar un motivo en el detalle de salida';
            }
        }
        if($this->r == 0){
            return false;
        }
        return true;
    }
    
    public function doSave($data){
        $empleado = new Entity(EmpleadoUtil::getEmpleado());
        $fecha = (new DateTime())->format(SQL_DT_FORMAT);
        
        $salida = [
            'fecha_creacion' => sqlValue($fecha, 'date'),
            'id_empleado' => $empleado->get('id_empleado'),
            'comentario' => sqlValue($data['comentario'], 'text'),
            'id_sucursal' => $empleado->get('id_sucursal')
        ];
        $this->db->query_insert('trx_salida_inventario', $salida);
        $salidaId = $this->db->max_id('trx_salida_inventario', 'id_salida_inventario');
        
        foreach($data['productos'] as $p){
            $transaccion = [
                'id_cuenta' => Catalogos::Cuentas_Inventario,
                'id_empleado' => $empleado->get('id_empleado'),
                'id_sucursal' => $empleado->get('id_sucursal'),
                'descripcion' => sqlValue('Salida inventario ' . $salidaId, 'text'),
                'id_producto' => $p['id_producto'],
                'debe' => $p['unidades'],
                'haber' => 0,
                'fecha_creacion' => sqlValue($fecha, 'date')
            ];
            $this->db->query_insert('trx_transacciones', $transaccion);
            $transaccionId = $this->db->max_id('trx_transacciones', 'id_transaccion');
            
            $costo = ProductoUtil::getCosto($p, $empleado->toArray(), $this->db);
            
            $detalle = [
                'id_salida_inventario' => $salidaId,
                'id_producto' => $p['id_producto'],
                'cantidad' => $p['unidades'],
                'costo_producto' => $costo,
                'id_transaccion' => $transaccionId,
                'comentario' => sqlValue($p['comentarioDetalle'], 'text')    
            ];
            $this->db->query_insert('trx_salida_inventario_detalle', $detalle);
        }
        
        $this->r = 1;
        $this->msg = 'Producto sacado con éxito';
    }
}
