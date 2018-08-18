<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 8/14/2018
 * Time: 8:41 AM
 */

class TrxReingresoConsignacion extends FastTransaction {
    function __construct(){
        parent::__construct();
        $this->instanceName = 'TrxReingresoConsignacion';
        $this->setTitle('Reingreso de Consignacion');
        $this->hasCustomSave = true;

        $this->fields = array(
        );

        $this->gridCols = array(
        );
    }

    protected function showModule() {
        include VIEWS . "/reingreso_consignacion.phtml";
    }

    public function myJavascript()
    {
        parent::myJavascript();
        ?>
        <script>
            app.controller('ModuleCtrl', ['$scope', '$http', '$rootScope' , '$timeout', '$filter', function ($scope, $http, $rootScope, $timeout, $filter) {
                $scope.startAgain = function () {
                    $scope.cliente = '';
                    $scope.productos_reingreso = new Array();

                    $http.get($scope.ajaxUrl + '&act=getClientes').success(function (response) {
                        $scope.rows = response.data;
                        $scope.setRowSelected($scope.rows);
                        $scope.setRowIndex($scope.rows);
                    });

                    $('#clientesModal').modal();
                    $('#clientesModal').on('show.bs.modal', function (event) {
                        var modal = $(this)
                        modal.find('.modal-body input#identificacion').focus();
                    })
                };

                $scope.finalizar = function () {
                };

                $scope.cancelar = function () {
                    $scope.cancel();
                };

                $scope.selectRow = function(row){
                    $scope.lastSelected = row;
                    $scope.currentIndex = row.index;
                    $scope.setRowSelected($scope.rows);
                    $scope.lastSelected.selected = true;
                    $('#clientesModal').modal('hide');

                    $http.get($scope.ajaxUrl + '&act=getConsignaciones&id_cliente=' + $scope.lastSelected.id_cliente).success(function (response) {
                        $scope.consignaciones = response.data;
                        $scope.setRowSelectedConsignaciones($scope.consignaciones);
                        $scope.setRowIndexConsignaciones($scope.consignaciones);
                        $('#consignacionesModal').modal();
                    });
                };

                $scope.selectRowConsignaciones = function(row){
                    $scope.lastSelectedConsig = row;
                    $scope.currentIndexConsig = row.index;
                    $scope.setRowSelectedConsignaciones($scope.consignaciones);
                    $scope.lastSelectedConsig.selected = true;
                    $scope.cliente = $scope.lastSelected.nombres + " " + $scope.lastSelected.apellidos
                    $('#consignacionesModal').modal('hide');

                    $http.get($scope.ajaxUrl + '&act=getProductos&id_movimiento_sucursales=' + $scope.lastSelectedConsig.id_movimiento_sucursales).success(function (response) {
                        $scope.productos = response.data;
                        $scope.setRowSelectedConsignaciones($scope.productos);
                        $scope.setRowIndexConsignaciones($scope.productos);
                    });
                };

                $scope.selectRowProd = function(row){
                    $scope.lastSelectedProd = row;
                    $scope.currentIndexProd = row.index;
                    $scope.setRowSelectedProd($scope.productos);
                    $scope.lastSelectedProd.selected = true;
                };

                $scope.setRowIndex = function(rows){
                    $index = 0;
                    $.each(rows, function(e, row){
                        row.index = $index;
                        $index++;
                    });
                };

                $scope.setRowSelected = function(rows){
                    $.each(rows, function(e, row){
                        row.selected = false;
                    });
                };

                $scope.setRowIndexConsignaciones = function(rows){
                    $index = 0;
                    $.each(rows, function(e, row){
                        row.index = $index;
                        $index++;
                    });
                };

                $scope.setRowSelectedConsignaciones = function(rows){
                    $.each(rows, function(e, row){
                        row.selected = false;
                    });
                };

                $scope.setRowIndexProd = function(rows){
                    $index = 0;
                    $.each(rows, function(e, row){
                        row.index = $index;
                        $index++;
                    });
                };

                $scope.setRowSelectedProd = function(rows){
                    $.each(rows, function(e, row){
                        row.selected = false;
                    });
                };

                $scope.startAgain();
                $rootScope.addCallback(function () {
                    $scope.startAgain();
                });

                $scope.reIngresar = function(prod) {
                    var total_reingreso_temp = $scope.lastSelectedConsig.total_reingreso + (prod.cant_reingreso > 0 ? prod.cant_reingreso : 1);
                    if (total_reingreso_temp <= $scope.lastSelectedConsig.compra_minima) {
                        $scope.lastSelectedConsig.total_reingreso = $scope.lastSelectedConsig.total_reingreso + (prod.cant_reingreso > 0 ? prod.cant_reingreso : 1);

//                        $filter('filter')($scope.productos_reingreso, {id_producto: prod.id_producto})[0].price+=999;

                        $producto = $filter('filter')($scope.productos_reingreso, {id_producto: prod.id_producto});

                        if ($producto.length > 0) {
                            $producto[0].cant_reingreso = (prod.cant_reingreso > 0 ? prod.cant_reingreso : 1)
                        } else {
                            $scope.productos_reingreso.push(prod);
                        }

                    } else {
                        $scope.alerts.push({
                            type: 'alert-danger',
                            msg: 'Ha llegado al maximo de la compra minima'
                        });
                    }
                };
            }]);
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

    public function getClientes()
    {
        $resultSet = array();

        $dsClientes = $this->db->query_select('clientes');

        foreach ($dsClientes as $p) {
            $resultSet[] = array('id_cliente' => $p['id_cliente'], 'identificacion' => $p['identificacion'], 'nombres' => $p['nombres'], 'apellidos' => $p['apellidos']);
        }

        echo json_encode(array('data' => $resultSet));
    }

    public function getConsignaciones()
    {
        $id_cliente = getParam("id_cliente");

        $queryConsignaciones = " SELECT	tms.id_movimiento_sucursales, tms.comentario_envio, tms.fecha_creacion, tms.dias_consignacion, tms.porcetaje_compra_min, (SUM(tmsd.unidades)*(tms.porcetaje_compra_min/100)) AS compra_minima, tms.id_sucursal_origen, 0 AS total_reingreso, SUM(tmsd.unidades) AS total_entregado
                                 FROM	trx_movimiento_sucursales tms
                                        INNER JOIN trx_movimiento_sucursales_detalle tmsd
                                 WHERE	tms.es_consignacion = 1
                                 AND	tms.id_cliente_recibe = " . $id_cliente .
                               " LIMIT 	10 ";

        $consignaciones = $this->db->queryToArray($queryConsignaciones);

        for($i = 0; count($consignaciones) > $i; $i++){
            $consignaciones[$i]['compra_minima'] = ceil($consignaciones[$i]['compra_minima']);
            $consignaciones[$i]['dias_consignacion'] = (int)($consignaciones[$i]['dias_consignacion']);
            $consignaciones[$i]['total_reingreso'] = (int)($consignaciones[$i]['total_reingreso']);
        }

        echo json_encode(array('data' => $consignaciones));
    }

    public function getProductos(){
        $id_movimiento_sucursales = getParam("id_movimiento_sucursales");

        $queryProductos = " SELECT	p.id_producto, p.nombre, p.descripcion, p.precio_venta, (p.precio_venta * (ctp.porcentaje_descuento/100)) AS precio_descuento, p.imagen, p.codigo_origen, tmsd.unidades, ctp.porcentaje_descuento, 0 AS cant_reingreso
                            FROM	trx_movimiento_sucursales tms
                                    INNER JOIN trx_movimiento_sucursales_detalle tmsd ON tmsd.id_movimiento_sucursales = tms.id_movimiento_sucursales
                                    INNER JOIN producto p ON p.id_producto = tmsd.id_producto
                                    INNER JOIN clientes c ON c.id_cliente = tms.id_cliente_recibe
                                    INNER JOIN clientes_tipos_precio ctp ON ctp.id_tipo_precio = c.id_tipo_precio
                            WHERE	tmsd.id_movimiento_sucursales = " . $id_movimiento_sucursales;

        $productos = $this->db->queryToArray($queryProductos);

//        for($i = 0; count($consignaciones) > $i; $i++){
//            $consignaciones[$i]['compra_minima'] = ceil($consignaciones[$i]['compra_minima']);
//        }

        echo json_encode(array('data' => $productos));
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
        $fecha = new DateTime();
        $user = AppSecurity::$UserData['data'];


        $this->r = 1;
        $this->msg = 'Traslado realizado con éxito';
    }
}