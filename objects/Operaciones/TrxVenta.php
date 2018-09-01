<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 8/25/2018
 * Time: 9:18 PM
 */

class TrxVenta extends FastTransaction {
    function __construct(){
        parent::__construct();
        $this->instanceName = 'TrxVenta';
        $this->setTitle('Venta');
        $this->hasCustomSave = true;

        $this->fields = array(
        );

        $this->gridCols = array(
        );
    }

    protected function showModule() {
        include VIEWS . "/venta.phtml";
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

                $scope.currentVentaIndex = null;
                $scope.productos = {};
                $scope.productos_facturar = new Array();
                $scope.search_codigo_origen = '';
                $scope.formas_pago = new Array();
                $scope.forma_pago = {};
                $scope.forma_pago.tipo_pago = 1;
                $scope.forma_pago.id_moneda = '';
                $scope.id_moneda_defecto = 0;

                $('#loading').hide();

                $http.get($scope.ajaxUrl + '&act=getVentas').success(function (response) {
                    $scope.ventas = response.data;
                    $scope.setVentasRowSelected($scope.ventas);
                    $scope.setVentasRowIndex($scope.ventas);

                    $('#ventasModal').modal();
                });

                $http.get($scope.ajaxUrl + '&act=getClientes').success(function (response) {
                    $scope.clientes = response.data;
                    $scope.setClienteRowSelected($scope.rows);
                    $scope.setClienteRowIndex($scope.rows);
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
            };

            $scope.cancelar = function () {
                $scope.cancel();
            };

            $scope.selectVentaRow = function(row){
                $scope.lastVentaSelected = row;
                $scope.currentVentaIndex = row.index;
                $scope.setVentasRowSelected($scope.ventas);
                $scope.lastVentaSelected.selected = true;

                $http.get($scope.ajaxUrl + '&act=getDetalleVenta&id_venta=' + $scope.lastVentaSelected.id_venta).success(function (response) {
                    $scope.productos_facturar = response.data;
                });
            };

            $scope.setVentasRowIndex = function(rows){
                $index = 0;
                $.each(rows, function(e, row){
                    row.index = $index;
                    $index++;
                });
            };

            $scope.setVentasRowSelected = function(rows){
                $.each(rows, function(e, row){
                    row.selected = false;
                });
            };

            $scope.selectClienteRow = function(row){
                $scope.lastClienteSelected = row;
                $scope.currentClienteIndex = row.index;
                $scope.setClienteRowSelected($scope.ventas);
                $scope.lastClienteSelected.selected = true;
                $scope.cliente = $scope.lastClienteSelected.nombres + " " + $scope.lastClienteSelected.apellidos;
                $('#clientesModal').modal('hide');
            };

            $scope.setClienteRowIndex = function(rows){
                $index = 0;
                $.each(rows, function(e, row){
                    row.index = $index;
                    $index++;
                });
            };

            $scope.setClienteRowSelected = function(rows){
                $.each(rows, function(e, row){
                    row.selected = false;
                });
            };

            $scope.saveRow = function(data, id, rowform) {
                $productos = $filter('filter')($scope.productos_facturar, {id_producto: id});
                if ($productos.length > 0) {
                    if(parseFloat(data.cantidad) <= parseFloat($productos[0].total_existencias)) {
                        $productos[0].cantidad = data.cantidad;
                        $productos[0].sub_total = parseFloat($productos[0].cantidad) * parseFloat($productos[0].precio_venta);
                        return true;
                    } else {
                        $scope.alerts.push({
                            type: 'alert-warning',
                            msg: 'La cantidad de ' + data.cantidad + ' sobrepasa las existencias ' + $productos[0].total_existencias
                        });

                        return false;
                    }
                }
            };

            $scope.$watch('search_codigo_origen', function(val){
                if (val.length >= 3) {
                    $('#loading').show();
                    $http.get($scope.ajaxUrl + '&act=getProductos&key=' + val).success(function (response) {

                        $('#loading').hide();

                        if (response.data.length == 0) {
                            $scope.alerts.push({
                                type: 'alert-warning',
                                msg: 'El producto con el código ' + val + ' no se encuentra o no hay existencias'
                            });
                        } else {
                            $scope.productos = response.data;
                        }
                    });
                }
            });

            $scope.agregarUno = function(prod) {
                var restoExistencias = prod.total_existencias - prod.cantidad;
                if (restoExistencias > 0) {

                    $productos = $filter('filter')($scope.productos_facturar, {id_producto: prod.id_producto});

                    if ($productos.length > 0) {
                        $productos[0].cantidad = parseFloat($productos[0].cantidad) + 1;
                        $productos[0].sub_total = parseFloat($productos[0].cantidad) * parseFloat($productos[0].precio_venta);
                    } else {
                        prod.cantidad = parseFloat(prod.cantidad) + 1;
                        prod.sub_total = parseFloat(prod.cantidad) * parseFloat(prod.precio_venta);
                        $scope.productos_facturar.push(prod);
                    }
                } else {
                    $scope.alerts.push({
                        type: 'alert-danger',
                        msg: 'No puede vender mas de las unidades (' + restoExistencias + ') de este producto ' + prod.nombre
                    });
                }

                $scope.search_codigo_origen = '';
                $scope.productos = {};
            };

            $scope.agregarVarios = function(prod) {
                var restoExistencias = prod.total_existencias - prod.cant_vender;
                if (restoExistencias > 0) {

                    $productos = $filter('filter')($scope.productos_facturar, {id_producto: prod.id_producto});

                    if ($productos.length > 0) {
                        $productos[0].cantidad = parseFloat($productos[0].cantidad) + prod.cant_vender;
                        $productos[0].sub_total = parseFloat($productos[0].cantidad) * parseFloat($productos[0].precio_venta);
                    } else {
                        prod.cantidad = parseFloat(prod.cantidad) + prod.cant_vender;
                        prod.sub_total = parseFloat(prod.cantidad) * parseFloat(prod.precio_venta);
                        $scope.productos_facturar.push(prod);
                    }
                } else {
                    $scope.alerts.push({
                        type: 'alert-danger',
                        msg: 'No puede vender mas de las unidades (' + restoExistencias + ') de este producto ' + prod.nombre
                    });
                }

                $scope.search_codigo_origen = '';
                $scope.productos = {};
            };

            $scope.cambioMoneda = function() {
                $tipo_cambio = $filter('filter')($scope.tipo_cambio, {id_moneda_muchos: $scope.id_moneda_defecto, id_moneda_uno: $scope.forma_pago.id_moneda});

                if($tipo_cambio.length > 0)
                    $scope.forma_pago.monto = (parseFloat($scope.forma_pago.cantidad) / parseFloat($tipo_cambio[0].factor)).toFixed(2);
            };

            $scope.generar = function() {

                $scope.forma_pago.cantidad = $scope.productos_facturar.sum("sub_total").toFixed(2);
                $scope.forma_pago.monto = $scope.forma_pago.cantidad;

                $tipo_cambio = $filter('filter')($scope.tipo_cambio, {id_moneda_muchos: $scope.id_moneda_defecto, id_moneda_uno: $scope.forma_pago.id_moneda});

                if($tipo_cambio.length > 0)
                    $scope.forma_pago.monto = (parseFloat($scope.forma_pago.cantidad) / parseFloat($tipo_cambio[0].factor)).toFixed(2);
            };

            $scope.startAgain();

            $rootScope.addCallback(function (response) {

//                if ((response != undefined) && (response.result == 1)) {
//                    var id_venta = 0;
//
//                    if (response.data)
//                        id_venta = response.data.id_venta;
//
//                    window.open("./?action=pdf&tmp=VT&id_venta=" + id_venta);
//                }

                $scope.startAgain();
            });
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

    public function getVentas()
    {
        $queryVentas = "  SELECT    v.id_venta, v.total, CONCAT(e.nombres,' ',e.apellidos) AS nombre_empleado
                          FROM	    trx_venta v
                                    LEFT JOIN empleados e ON e.id_empleado = v.id_empleado
                          WHERE     v.estado = 'P'";

        $ventas = $this->db->queryToArray($queryVentas);

        echo json_encode(array('data' => $ventas));
    }

    public function getDetalleVenta(){
        $id_venta = getParam("id_venta");

        $queryProductos = " SELECT	p.id_producto, p.nombre, p.descripcion, p.precio_venta, p.imagen,
                                    p.codigo_origen, vd.cantidad, (vd.cantidad * p.precio_venta) AS sub_total,
                                    (sum(t.haber) - sum(t.debe)) AS total_existencias, 1 AS mostrar
                            FROM	trx_venta_detalle vd
                                    LEFT JOIN producto p
                                    ON p.id_producto = vd.id_producto
                                    LEFT JOIN trx_transacciones t
                                    ON t.id_producto = vd.id_producto
                                    AND t.id_sucursal = vd.id_sucursal
                            WHERE	vd.id_venta = " . $id_venta .
                          " GROUP BY
                                    vd.id_producto";

        $productos = $this->db->queryToArray($queryProductos);

        echo json_encode(array('data' => $productos));
    }

    public function getProductos(){
        $key = getParam("key");

        $queryProductos = " SELECT	p.id_producto, p.nombre, p.descripcion, p.precio_venta, p.imagen,
                                    p.codigo_origen, COALESCE((sum(trx.haber) - sum(trx.debe)),0) AS total_existencias,
                                    1 AS mostrar, t.nombre AS nombre_categoria, s.nombre AS nombre_sucursal
                            FROM	producto p
                                    LEFT JOIN tipo t
                                    ON t.id_tipo = p.id_tipo
                                    LEFT JOIN trx_transacciones trx
                                    ON trx.id_producto = p.id_producto
                                    LEFT JOIN sucursales s
                                    ON s.id_sucursal = trx.id_sucursal
                            WHERE	1 = 1
                            AND		(p.codigo_origen LIKE '%". $key . "%'
                                    OR
                                    p.nombre LIKE '%". $key . "%'
                                    OR
                                    p.descripcion LIKE '%". $key . "%'
                                    OR
                                    t.nombre LIKE '%". $key . "%'
                                    OR
                                    s.nombre LIKE '%". $key . "%'
                                    )
                            GROUP BY
                                    p.id_producto
                            HAVING	(sum(trx.haber) - sum(trx.debe)) > 0";

        $productos = $this->db->queryToArray($queryProductos);

        for($i = 0; count($productos) > $i; $i++){
            $productos[$i]['cantidad'] = 0;
            $productos[$i]['sub_total'] = 0;
            $productos[$i]['cant_vender'] = 0;
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
        $dsEmpleado = Collection::get($this->db, 'empleados', sprintf('id_usuario = "%s"', $user['ID']))->single();
        $dsCuentaVenta = Collection::get($this->db, 'cuentas', 'lower(nombre) = "venta"')->single();
        $dsCuentaReingreso = Collection::get($this->db, 'cuentas', 'lower(nombre) = "reingreso"')->single();
        $dsMoneda = Collection::get($this->db, 'monedas', 'moneda_defecto = 1')->single();

        $venta = [
            'total' => sqlValue($data['forma_pago']['cantidad'], 'float'),
            'id_cliente' => sqlValue($data['id_cliente'], 'int'),
            'id_empleado' => sqlValue($dsEmpleado['id_empleado'], 'int'),
            'id_sucursal' => sqlValue($data['consignaciones'][0]['id_sucursal_origen'], 'int'),
            'estado' => sqlValue('C', 'text'),
            'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
            'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
        ];

        $this->db->query_insert('trx_venta', $venta);

        $id_venta = $this->db->max_id('trx_venta', 'id_venta');

        foreach ($data['productos'] as $prod) {

            $venta_detalle = [
                'id_venta' => sqlValue($id_venta, 'int'),
                'id_producto' => sqlValue($prod['id_producto'], 'int'),
                'cantidad' => sqlValue($prod['cant_facturar'], 'float'),
                'precio_venta' => sqlValue($prod['precio_descuento'], 'float'),
                'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
            ];

            $this->db->query_insert('trx_venta_detalle', $venta_detalle);

            $transaccion = [
                'id_cuenta' => sqlValue($dsCuentaVenta['id_cuenta'], 'int'),
                'id_empleado' => sqlValue($dsEmpleado['id_empleado'], 'int'),
                'id_sucursal' => sqlValue($data['consignaciones'][0]['id_sucursal_origen'], 'int'),
                'descripcion' => sqlValue('Venta por Consignacion', 'text'),
                'id_moneda' => sqlValue($dsMoneda['id_moneda'], 'int'),
                'id_producto' => sqlValue($prod['id_producto'], 'int'),
                'debe' => sqlValue($prod['cant_facturar'], 'float'),
                'haber' => sqlValue('0', 'float'),
                'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                'id_cliente' => sqlValue($data['id_cliente'], 'int')
            ];

            $this->db->query_insert('trx_transacciones', $transaccion);

            $transaccion = [
                'id_cuenta' => sqlValue($dsCuentaVenta['id_cuenta'], 'int'),
                'id_empleado' => sqlValue($dsEmpleado['id_empleado'], 'int'),
                'id_sucursal' => sqlValue($data['consignaciones'][0]['id_sucursal_origen'], 'int'),
                'descripcion' => sqlValue('Venta por Consignacion', 'text'),
                'id_moneda' => sqlValue($dsMoneda['id_moneda'], 'int'),
                'id_producto' => sqlValue($prod['id_producto'], 'int'),
                'debe' => sqlValue('0', 'float'),
                'haber' => sqlValue($prod['cant_reingreso'], 'float'),
                'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                'id_cliente' => sqlValue(0, 'text')
            ];

            $this->db->query_insert('trx_transacciones', $transaccion);
        }

        $forma_pago = [
            'id_venta' => sqlValue($id_venta, 'int'),
            'id_forma_pago' => sqlValue($data['forma_pago']['tipo_pago'], 'int'),
            'id_moneda' => sqlValue(array_key_exists("id_moneda", $data['forma_pago']) ? $data['forma_pago']['id_moneda'] : 0, 'int'),
            'cantidad' => sqlValue(array_key_exists("cantidad", $data['forma_pago']) ? $data['forma_pago']['cantidad'] : 0, 'float'),
            'monto' => sqlValue(array_key_exists("monto", $data['forma_pago']) ? $data['forma_pago']['monto'] : 0, 'float'),
            'numero_cheque' => sqlValue(array_key_exists("numero_cheque", $data['forma_pago']) ? $data['forma_pago']['numero_cheque'] : '', 'text'),
            'id_banco' => sqlValue(array_key_exists("id_banco", $data['forma_pago']) ? $data['forma_pago']['id_banco'] : 0, 'int'),
            'numero_autorizacion' => sqlValue(array_key_exists("numero_autorizacion", $data['forma_pago']) ? $data['forma_pago']['numero_autorizacion'] : '', 'text'),
            'autorizado_por' => sqlValue(array_key_exists("autorizado_por", $data['forma_pago']) ? $data['forma_pago']['autorizado_por'] : '', 'text'),
            'numero_voucher' => sqlValue(array_key_exists("numero_voucher", $data['forma_pago']) ? $data['forma_pago']['numero_voucher'] : '', 'text'),
            'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
            'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
        ];

        $this->db->query_insert('trx_venta_formas_pago', $forma_pago);

        $this->r = 1;
        $this->msg = 'Traslado realizado con éxito';
        $this->returnData = array('id_venta' => $id_venta);
    }
}