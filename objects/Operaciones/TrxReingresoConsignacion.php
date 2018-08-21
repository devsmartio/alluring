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

                Array.prototype.sum = function (prop) {
                    var total = 0
                    for ( var i = 0, _len = this.length; i < _len; i++ ) {
                        total += parseFloat(this[i][prop])
                    }
                    return total
                };

                $scope.startAgain = function () {
                    $scope.cliente = '';
                    $scope.productos_facturar = new Array();
                    $scope.lastSelected = new Array();
                    $scope.consignaciones = new Array();
                    $scope.productos = new Array();
                    $scope.vender = false;
                    $scope.formas_pago = new Array();
                    $scope.forma_pago = new Array();
                    $scope.forma_pago.tipo_pago = 1;
                    $scope.forma_pago.id_moneda = '';
                    $scope.id_moneda_defecto = 0;

                    $http.get($scope.ajaxUrl + '&act=getClientes').success(function (response) {
                        $scope.rows = response.data;
                        $scope.setRowSelected($scope.rows);
                        $scope.setRowIndex($scope.rows);
                    });

                    $http.get($scope.ajaxUrl + '&act=getFormasPago').success(function (response) {
                        $scope.formas_pago = response.data;
                    });

                    $http.get($scope.ajaxUrl + '&act=getMonedas').success(function (response) {
                        $scope.monedas = response.data;
                        $moneda = $filter('filter')($scope.monedas, {selected: true});
                        if ($moneda.length > 0) {
                            $scope.id_moneda_defecto = $moneda[0].id_moneda;
                            $scope.forma_pago.id_moneda = $scope.id_moneda_defecto;
                        }
                    });

                    $http.get($scope.ajaxUrl + '&act=getTipoCambio').success(function (response) {
                        $scope.tipo_cambio = response.data;
                    });

                    $http.get($scope.ajaxUrl + '&act=getBancos').success(function (response) {
                        $scope.bancos = response.data;
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
                    $scope.cliente = $scope.lastSelected.nombres + " " + $scope.lastSelected.apellidos;
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

                $scope.reIngresarUno = function(prod) {
                    if ((prod.cant_reingreso + 1) <= prod.unidades) {
                        var total_reingreso_temp = $scope.lastSelectedConsig.total_reingreso + 1;
                        if (total_reingreso_temp <= $scope.lastSelectedConsig.compra_minima) {
                            $scope.lastSelectedConsig.total_reingreso++;

                            prod.cant_reingreso = prod.cant_reingreso + 1;
                            prod.total_reingreso = prod.cant_reingreso;

                        } else {
                            $scope.alerts.push({
                                type: 'alert-danger',
                                msg: 'Ha llegado al maximo de la compra minima'
                            });
                        }
                    } else {
                        $scope.alerts.push({
                            type: 'alert-danger',
                            msg: 'No puede reingresar mas de las unidades (' + prod.unidades + ') de este producto'
                        });
                    }
                };

                $scope.reIngresar = function(prod) {
                    if(prod.cant_reingreso > 0) {
                        if (prod.cant_reingreso <= prod.unidades) {

                            var total_reingreso_temp = 0;
                            angular.forEach($scope.productos, function(obj, key){
                                total_reingreso_temp += parseInt(obj['cant_reingreso']);
                            });

                            if (total_reingreso_temp <= $scope.lastSelectedConsig.compra_minima) {
                                $scope.lastSelectedConsig.total_reingreso = total_reingreso_temp;
                                prod.total_reingreso = prod.cant_reingreso;

                            } else {
                                prod.cant_reingreso = prod.total_reingreso;
                                $scope.productos_vender = $scope.productos;

                                $scope.alerts.push({
                                    type: 'alert-danger',
                                    msg: 'Ha llegado al maximo de la compra minima'
                                });
                            }
                        } else {
                            prod.cant_reingreso = prod.total_reingreso;
                            $scope.alerts.push({
                                type: 'alert-danger',
                                msg: 'No puede reingresar mas de las unidades (' + prod.unidades + ') de este producto'
                            });
                        }
                    }
                };

                $scope.cambioMoneda = function() {
                    $tipo_cambio = $filter('filter')($scope.tipo_cambio, {id_moneda_muchos: $scope.id_moneda_defecto, id_moneda_uno: $scope.forma_pago.id_moneda});

                    if($tipo_cambio.length > 0)
                        $scope.forma_pago.monto = (parseFloat($scope.forma_pago.cantidad) / parseFloat($tipo_cambio[0].factor)).toFixed(2);
                };

                $scope.generar = function() {
                    angular.forEach($scope.productos, function (prod, key) {
                        if ((prod.unidades - prod.cant_reingreso) > 0) {
                            prod.cant_facturar = prod.unidades - prod.cant_reingreso;
                            prod.precio_descuento = parseFloat(prod.precio_descuento).toFixed(2);
                            prod.sub_total = ((prod.unidades - prod.cant_reingreso) * prod.precio_descuento).toFixed(2);
                            $scope.productos_facturar.push(prod);
                        }
                    });
                    $scope.forma_pago.cantidad = $scope.productos_facturar.sum("sub_total").toFixed(2);
                    $scope.forma_pago.monto = $scope.forma_pago.cantidad;

                    $tipo_cambio = $filter('filter')($scope.tipo_cambio, {id_moneda_muchos: $scope.id_moneda_defecto, id_moneda_uno: $scope.forma_pago.id_moneda});

                    if($tipo_cambio.length > 0)
                        $scope.forma_pago.monto = (parseFloat($scope.forma_pago.cantidad) / parseFloat($tipo_cambio[0].factor)).toFixed(2);

                    $scope.vender = true;
                };

                $scope.facturar = function() {

                    var productos = JSON.stringify($scope.productos);
//                    productos = productos.replace(/\\/g, "\\\\");
//
//                    $rootScope.modData = {
//                        productos: JSON.parse(productos),
//                        id_cliente: $scope.lastSelected.id_cliente
//                    };
//
//                    $scope.doSave();

//                    $rootScope.addCallback(response =>
//                    window.open("./?action=pdf&tmp=TRX&identificador_excel=" + identificador_excel));
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

    public function getFormasPago()
    {
        $resultSet = array();

        $ds = $this->db->query_select('formas_pago');

        foreach ($ds as $p) {
            $resultSet[] = array('id_forma_pago' => $p['id_forma_pago'], 'nombre' => $p['nombre']);
        }

        echo json_encode(array('data' => $resultSet));
    }

    public function getBancos()
    {
        $resultSet = array();
        $resultSet[] = array('id_banco' => '', 'nombre' => '-- Seleccione uno --');

        $ds = $this->db->query_select('bancos');

        foreach ($ds as $p) {
            $resultSet[] = array('id_banco' => $p['id_banco'], 'nombre' => $p['nombre']);
        }

        echo json_encode(array('data' => $resultSet));
    }

    public function getTipoCambio()
    {
        $resultSet = array();

        $ds = $this->db->query_select('tipo_cambio');

        foreach ($ds as $p) {
            $resultSet[] = array('id_moneda_muchos' => $p['id_moneda_muchos'], 'id_moneda_uno' => $p['id_moneda_uno'], 'factor' => $p['factor']);
        }

        echo json_encode(array('data' => $resultSet));
    }

    public function getMonedas()
    {
        $resultSet = array();

        $ds = $this->db->query_select('monedas');

        foreach ($ds as $p) {
            if ($p['moneda_defecto'] == 1) {
                $resultSet[] = array('id_moneda' => $p['id_moneda'], 'nombre' => $p['nombre'], 'selected' => true);
            } else {
                $resultSet[] = array('id_moneda' => $p['id_moneda'], 'nombre' => $p['nombre'], 'selected' => false);
            }
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

        $queryProductos = " SELECT	p.id_producto, p.nombre, p.descripcion, p.precio_venta, (p.precio_venta * (ctp.porcentaje_descuento/100)) AS precio_descuento, p.imagen, p.codigo_origen, tmsd.unidades, ctp.porcentaje_descuento, 0 AS cant_reingreso, 0 AS total_reingreso
                            FROM	trx_movimiento_sucursales tms
                                    INNER JOIN trx_movimiento_sucursales_detalle tmsd ON tmsd.id_movimiento_sucursales = tms.id_movimiento_sucursales
                                    INNER JOIN producto p ON p.id_producto = tmsd.id_producto
                                    INNER JOIN clientes c ON c.id_cliente = tms.id_cliente_recibe
                                    INNER JOIN clientes_tipos_precio ctp ON ctp.id_tipo_precio = c.id_tipo_precio
                            WHERE	tmsd.id_movimiento_sucursales = " . $id_movimiento_sucursales;

        $productos = $this->db->queryToArray($queryProductos);

        for($i = 0; count($productos) > $i; $i++){
            $productos[$i]['unidades'] = (int)$productos[$i]['unidades'];
            $productos[$i]['cant_reingreso'] = (int)$productos[$i]['cant_reingreso'];
            $productos[$i]['total_reingreso'] = (int)$productos[$i]['total_reingreso'];
        }

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



        foreach ($data['productos'] as $prod) {

        }

        $this->r = 1;
        $this->msg = 'Traslado realizado con éxito';
    }
}