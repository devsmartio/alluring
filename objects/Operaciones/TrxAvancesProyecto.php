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
class TrxAvancesProyecto extends FastTransaction {
    function __construct(){
        parent::__construct();
        $this->setTitle('Ingreso de avances de proyecto');
        $this->hasCustomSave = false;
    }
    
    protected function showModule() {
        include VIEWS . "/avances_proyecto.phtml";
    }
    
    public function myJavascript() {
        parent::myJavascript();
        ?>
    <script>
        app.controller('ModuleCtrl', function($scope, $http, $rootScope, $timeout){
            $scope.startAgain = function(){
               $scope.proyectos = [];
               $scope.proyectoSel = null;
               $scope.inList = true;
            };
            
            $scope.getProyectos = function(){
                $http.get($scope.ajaxUrl + '&act=getProyectos').success(function(response){
                    $scope.proyectos = response.data;
                });
            };
            
            $scope.seleccionarProyecto = proyecto => {
                $scope.proyectoSel = proyecto;
                $scope.inList = false;
            }
            
            $scope.regresarAListado = function(){
                $scope.proyectoSel = null;
                $scope.inList = true;
            }
            
            $scope.getTiposAvance = function(){
                $http.get($scope.ajaxUrl + '&act=getTiposAvance').success(function(response){
                    $scope.proyectos = response.data;
                });
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
    
    public function getProyectos(){
        
    }
    
    public function getTiposAvance(){
        Collection::get($this->db, 'tipos_avance_proyecto')
                ->select(['id_tipo_avance_proyecto', 'nombre'], true)
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
            $this->msg = 'Debe agregar un comentario al ingreso';
        }
        foreach($productos as $p){
            if($p['unidades'] == 0){
                $this->r = 0;
                $this->msg = 'No se puede ingresar un producto con cantidad 0';
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
        
        $ingreso = [
            'fecha_creacion' => sqlValue($fecha, 'date'),
            'id_empleado' => $empleado->get('id_empleado'),
            'comentario' => sqlValue($data['comentario'], 'text'),
            'id_sucursal' => $empleado->get('id_sucursal')
        ];
        $this->db->query_insert('trx_ingreso_inventario', $ingreso);
        $ingresoId = $this->db->max_id('trx_ingreso_inventario', 'id_ingreso_inventario');
        
        foreach($data['productos'] as $p){
            $transaccion = [
                'id_cuenta' => Catalogos::Cuentas_Inventario,
                'id_empleado' => $empleado->get('id_empleado'),
                'id_sucursal' => $empleado->get('id_sucursal'),
                'descripcion' => sqlValue('Ingreso inventario ' . $ingresoId, 'text'),
                'id_producto' => $p['id_producto'],
                'debe' => 0,
                'haber' => $p['unidades'],
                'fecha_creacion' => sqlValue($fecha, 'date')
            ];
            $this->db->query_insert('trx_transacciones', $transaccion);
            $transaccionId = $this->db->max_id('trx_transacciones', 'id_transaccion');
            
            $detalle = [
                'id_ingreso_inventario' => $ingresoId,
                'id_producto' => $p['id_producto'],
                'cantidad' => $p['unidades'],
                'costo_producto' => $p['costo'],
                'id_transaccion' => $transaccionId
            ];
            $this->db->query_insert('trx_ingreso_inventario_detalle', $detalle);
        }
        
        $this->r = 1;
        $this->msg = 'Producto ingresado con éxito';
    }
}
