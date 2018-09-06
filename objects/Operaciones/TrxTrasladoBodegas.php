<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 8/3/2018
 * Time: 1:41 PM
 */

class TrxTrasladoBodegas extends FastTransaction {
    function __construct(){
        parent::__construct();
        $this->instanceName = 'TrxTrasladoBodegas';
        $this->table = 'producto';
        $this->setTitle('Traslado de Bodegas');
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
        include VIEWS . "/traslado_bodegas.phtml";
    }

    public function myJavascript()
    {
        parent::myJavascript();
        ?>
        <script>
            app.controller('ModuleCtrl', function ($scope, $http, $rootScope) {
                $scope.startAgain = function () {
                    $scope.idSucursalOrigen = '';
                    $scope.idSucursalDestino = '';
                    $scope.rows = [];
                    $scope.esConsignacion = false;
                    $scope.diasConsignar = 0;
                    $scope.porcentajeCompraMinima = 0;
                    $scope.idClienteRecibe = '';
                    $scope.clientesBodegas = [];

                    $http.get($scope.ajaxUrl + '&act=getGridCols').success(function (response) {
                        $scope.gridCols = response.data;
                    });

                    $http.get($scope.ajaxUrl + '&act=getSucursales').success(function (response) {
                        $scope.sucursalesOrigen = response.data;
                        $scope.sucursalesDestino = response.data;
                    });
                };

                $scope.finalizar = function () {
                    var found = $scope.rows.filter(r => r.cantidad != 0);

                    if ($scope.esConsignacion && ($scope.diasConsignar == 0 || $scope.porcentajeCompraMinima == 0)) {

                        $scope.alerts.push({
                            type: 'alert-danger',
                            msg: 'Si el traslado es consignación debe colocar los dias a consignar y el porcentaje de compra mínima.'
                        });
                        return;
                    }

                    if(found.length == 0){
                        $scope.alerts.push({
                            type: 'alert-danger',
                            msg: 'Debe seleccionar al menos un producto para realizar el traslado.'
                        });
                        return;
                    }

                    if ($scope.idSucursalOrigen == '' || $scope.idSucursalDestino == '') {
                        $scope.alerts.push({
                            type: 'alert-danger',
                            msg: 'La bodega origen y la bodega destino son requeridas para realizar un traslado.'
                        });
                        return;
                    }

                    if (confirm('¿Está seguro que desea transladar los productos seleccionados?')) {
                        var productos = JSON.stringify($scope.rows);
                        productos = productos.replace(/\\/g, "\\\\");

                        $rootScope.modData = {
                            idSucursalOrigen: $scope.idSucursalOrigen,
                            idSucursalDestino: $scope.idSucursalDestino,
                            esConsignacion: $scope.esConsignacion,
                            diasConsignar: $scope.diasConsignar,
                            porcentajeCompraMinima: $scope.porcentajeCompraMinima,
                            idClienteRecibe: $scope.idClienteRecibe,
                            productos: JSON.parse(productos),
                            mod: 1
                        };

                        $scope.doSave();
                    }
                };

                $scope.cancelar = function () {
                    $scope.cancel();
                };

                $scope.startAgain();
                $rootScope.addCallback(function () {
                    $scope.startAgain();
                });

                $scope.filtarProductos = function() {
                    $http.get($scope.ajaxUrl + '&act=getProductos&id_sucursal='+$scope.idSucursalOrigen).success(function (response) {
                        $scope.rows = response.data;
                    });

                    $http.get($scope.ajaxUrl + '&act=getClientesBodegas&id_sucursal='+$scope.idSucursalOrigen).success(function (response) {
                        $scope.clientesBodegas = response.data;
                    });
                };

                $scope.selBodegaDestino = function() {
                    if ($scope.idSucursalOrigen == $scope.idSucursalDestino) {
                        $scope.idSucursalDestino = '';
                        $scope.alerts.push({
                            type: 'alert-danger',
                            msg: 'La bodega destino no puede ser igual a la bodega origen.'
                        });
                    }
                };

                $scope.trasladarTodo = function() {
                    angular.forEach($scope.rows, function(itm){ itm.cantidad = parseInt(itm.haber) });
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

        $dsBodegas = Collection::get($this->db, 'sucursales')->toArray();

        $resultSet[] = array('id_sucursal' =>'', 'nombre' => '-- Seleccione uno --');
        foreach($dsBodegas as $p){
            $resultSet[] = array('id_sucursal' => $p['id_sucursal'], 'nombre' => $p['nombre']);
        }

        echo json_encode(array('data' => $resultSet));
    }

    public function getProductos(){
        $resultSet = [];
        try {
            $id_sucursal = getParam('id_sucursal');
            $resultSet = $this->db->query_toArray('select trx.*, p.*, t.nombre as nombre_tipo, COALESCE((sum(trx.haber) - sum(trx.debe)),0) AS total_existencias from trx_transacciones trx inner join producto p ON p.id_producto = trx.id_producto inner join tipo t ON t.id_tipo = p.id_tipo where trx.id_sucursal = ' . $id_sucursal . ' GROUP BY p.id_producto HAVING	(sum(trx.haber) - sum(trx.debe)) > 0');

            for($i = 0; count($resultSet) > $i; $i++){
                $resultSet[$i]['cantidad'] = 0;
            }
        } catch(Exception $e){
            error_log($e->getTraceAsString());
        }
        echo json_encode(array('data' => $resultSet));
    }

    public function getClientesBodegas(){
        $resultSet = array();
        try {
            $resultSet[] = array('id_cliente' =>'', 'cliente_nombre' => '-- Seleccione uno --');
            $id_sucursal = getParam('id_sucursal');
            $resultSet = $this->db->query_toArray('SELECT c.id_cliente, CONCAT(c.nombres , " ", c.apellidos) AS cliente_nombre FROM clientes_bodegas cb INNER JOIN clientes c ON c.id_cliente = cb.id_cliente WHERE cb.id_sucursal = ' . $id_sucursal);

        } catch(Exception $e){
            error_log($e->getTraceAsString());
        }
        echo json_encode(array('data' => $resultSet));
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
        $dsCuenta = Collection::get($this->db, 'cuentas', 'lower(nombre) = "inventario"')->single();
        $dsMoneda = Collection::get($this->db, 'monedas', 'moneda_defecto = 1')->single();

        try {

            $trx_movimiento_sucursales = [
                'id_movimiento_sucursales_estado' => sqlValue('2', 'int'),
                'id_empleado_envia' => sqlValue($dsEmpleado['id_empleado'], 'int'),
                'id_sucursal_origen' => sqlValue($data['idSucursalOrigen'], 'int'),
                'id_sucursal_destino' => sqlValue($data['idSucursalDestino'], 'int'),
                'comentario_envio' => sqlValue('', 'text'),
                'comentario_recepcion' => sqlValue('', 'text'),
                'id_empleado_recibe' => sqlValue('', 'text'),
                'id_cliente_recibe' => sqlValue($data['idClienteRecibe'], 'int'),
                'es_consignacion' => sqlValue(($data['esConsignacion'] == false) ? 0 : 1, 'number'),
                'dias_consignacion' => sqlValue($data['diasConsignar'], 'int'),
                'porcetaje_compra_min' => sqlValue($data['porcentajeCompraMinima'], 'float'),
                'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                'fecha_recepcion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date')
            ];

            $this->db->query_insert('trx_movimiento_sucursales', $trx_movimiento_sucursales);

            $id_movimiento_sucursales = $this->db->max_id('trx_movimiento_sucursales', 'id_movimiento_sucursales');

            foreach ($data['productos'] as $prod) {

                $transaccion = [
                    'id_cuenta' => sqlValue($dsCuenta['id_cuenta'], 'int'),
                    'id_empleado' => sqlValue($dsEmpleado['id_empleado'], 'int'),
                    'id_sucursal' => sqlValue($data['idSucursalOrigen'], 'int'),
                    'descripcion' => sqlValue('Traslado Productos', 'text'),
                    'id_moneda' => sqlValue($dsMoneda['id_moneda'], 'int'),
                    'id_producto' => sqlValue($prod['id_producto'], 'int'),
                    'debe' => sqlValue($prod['cantidad'], 'float'),
                    'haber' => sqlValue('0', 'float'),
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                    'id_cliente' => sqlValue(0, 'text')
                ];

                $this->db->query_insert('trx_transacciones', $transaccion);

                $id_transaccion_origen = $this->db->max_id('trx_transacciones', 'id_transaccion');

                $transaccion = [
                    'id_cuenta' => sqlValue($dsCuenta['id_cuenta'], 'int'),
                    'id_empleado' => sqlValue($dsEmpleado['id_empleado'], 'int'),
                    'id_sucursal' => sqlValue($data['idSucursalDestino'], 'int'),
                    'descripcion' => sqlValue('Traslado Productos', 'text'),
                    'id_moneda' => sqlValue($dsMoneda['id_moneda'], 'int'),
                    'id_producto' => sqlValue($prod['id_producto'], 'int'),
                    'debe' => sqlValue('0', 'float'),
                    'haber' => sqlValue($prod['cantidad'], 'float'),
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                    'id_cliente' => sqlValue(0, 'text')
                ];

                $this->db->query_insert('trx_transacciones', $transaccion);

                $id_transaccion_destino = $this->db->max_id('trx_transacciones', 'id_transaccion');

                $trx_movimiento_sucursales_detalle = [
                    'id_movimiento_sucursales' => sqlValue($id_movimiento_sucursales, 'int'),
                    'id_producto' => sqlValue($prod['id_producto'], 'int'),
                    'unidades' => sqlValue($prod['cantidad'], 'int'),
                    'id_transaccion' => sqlValue($id_transaccion_origen, 'int'),
                    'id_transaccion_destino' => sqlValue($id_transaccion_destino, 'int')
                ];

                $this->db->query_insert('trx_movimiento_sucursales_detalle', $trx_movimiento_sucursales_detalle);
            }

            $this->r = 1;
            $this->msg = 'Traslado realizado con éxito';
        } catch(Exception $e) {
            $this->r = 0;
            $this->msg = $e->getMessage();
        }
    }
}