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
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.2/dist/sweetalert2.all.min.js"></script>
        <script>

            app.controller('ModuleCtrl', function ($scope, $http, $rootScope) {
                $scope.rand = Math.random();
                $scope.encontrados = 0;
                $scope.$on('emptylistfilter.found', function(e, found){
                    $scope.encontrados = found;
                })
                $scope.startAgain = function () {
                    $scope.idSucursalOrigen = '';
                    $scope.idSucursalDestino = '';
                    $scope.rows = [];
                    $scope.search = '';
                    $scope.esConsignacion = false;
                    $scope.diasConsignar = 0;
                    $scope.porcentajeCompraMinima = 0;
                    $scope.idClienteRecibe = '';
                    $scope.tipo = '';
                    $scope.clientesBodegas = [];
                    $scope.clientes = [];
                    $scope.filteredProd = [];

                    $http.get($scope.ajaxUrl + '&act=getGridCols').success(function (response) {
                        $scope.gridCols = response.data;
                    });

                    $http.get($scope.ajaxUrl + '&act=getSucursales').success(function (response) {
                        $scope.sucursalesOrigen = response.data;
                        $scope.sucursalesDestino = response.data;
                    });

                    $http.get($scope.ajaxUrl + '&act=getClientes').success(function (response) {
                        $scope.clientes = response.data;
                    });
                };

                $scope.setBodega = () => {
                    console.log("SETEANDO BODEGA")
                    if($scope.clienteSelected.bodegas.length == 1){
                        $scope.idSucursalDestinoCliente = $scope.clienteSelected.bodegas[0].id_sucursal
                    }
                    $scope.applyDiscount();
                }

                $scope.applyDiscount = () => {
                    if($scope.clienteSelected && $scope.clienteSelected.cliente.porcentaje_descuento){
                        let des = $scope.clienteSelected.cliente.porcentaje_descuento;
                        $scope.rows.forEach(r => {
                            r.precio_descuento = r.precio_venta * ((100 - des)/100);
                        })        
                    }
                }

                $scope.finalizar = function () {
                    var found = $scope.rows.filter(r => r.cantidad != 0);

                    if ($scope.tipo == 'cliente' && !$scope.clienteSelected) {
                        swal("Oh oh", 'Debe elegir a un cliente para este tipo de traslado', "warning");
                        return;
                    } else if($scope.tipo == 'bodega' && !$scope.idSucursalDestino){
                        swal("Oh oh", 'Debe elegir una bodega para este tipo de traslado', "warning");
                        return;
                    }

                    if ($scope.tipo == 'cliente' && ($scope.diasConsignar == 0 || $scope.porcentajeCompraMinima == 0)) {

                        swal("Oh oh", "Si el traslado es consignación debe colocar los dias a consignar y el porcentaje de compra mínima.", "warning");
                        return;
                    }

                    if ($scope.tipo == 'cliente' && $scope.clienteSelected.bodegas.length && !$scope.idSucursalDestinoCliente) {

                        swal("Oh oh", "Debe Elegir la bodega cliente a trasladar", "warning");
                        return;
                    }

                    if(found.length == 0){
                        swal("Oh oh", 'Debe seleccionar al menos un producto para realizar el traslado.', "warning");
                        return;
                    }

                    if ($scope.idSucursalOrigen == '') {
                        swal("Oh oh", 'La bodega origen es requerida para realizar un traslado.', "warning");
                        return;
                    }

                    if ($scope.idSucursalOrigen == $scope.idSucursalDestino) {
                        swal("Oh oh", 'La bodega origen y destino no puede ser la misma', "warning");
                        return;
                    }

                    let piezas = 0;
                    angular.forEach(found, i => piezas += i.cantidad);
                    swal({
                        title: "Confirmar traslado",
                        text: `¿Desea confirmar el traslado de ${piezas} piezas de producto?`,
                        showCancelButton: true,
                        confirmButtonText: "Confirmar",
                        cancelButtonText: "Cancelar"
                    }).then(val => {
                        if(val.value == true){
                            
                            let dataAEnviar = {
                                idSucursalOrigen: $scope.idSucursalOrigen,
                                idSucursalDestino: $scope.idSucursalDestino,
                                idSucursalDestinoCliente: $scope.idSucursalDestinoCliente,
                                diasConsignar: $scope.diasConsignar,
                                porcentajeCompraMinima: $scope.porcentajeCompraMinima,
                                tipo: $scope.tipo,
                                idClienteRecibe: $scope.tipo == 'cliente' ? $scope.clienteSelected.cliente.id_cliente : "",
                                productos: found,
                                mod: 1
                            }
                            $rootScope.modData = dataAEnviar;
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
                            swal("Oh oh", `Item ${val} no encontrado`, "warning");
                        }
                    } 
                });
                    

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
                        $scope.applyDiscount();
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

        echo json_encode(array('data' => $resultSet));
    }

    public function getClientes(){
        $resultSet = array();
        $query = "
            SELECT
                c.id_cliente,
                c.nombres,
                c.apellidos,
                t.porcentaje_descuento 
            FROM clientes c
            LEFT JOIN clientes_tipos_precio t ON c.id_tipo_precio=t.id_tipo_precio
            WHERE id_usuario = '%s'
        ";
        $clientes = $this->db->queryToArray(sprintf($query, $this->user['ID']));
        foreach($clientes as $c){
            $query = "
                SELECT s.nombre, cb.id_sucursal
                FROM clientes_bodegas cb
                JOIN sucursales s on s.id_sucursal=cb.id_sucursal
                WHERE id_cliente = %s
            ";
            $bodegas = sanitize_array_by_keys($this->db->queryToArray(sprintf($query, $c['id_cliente'])), ['nombre']);
            $resultSet[] = [
                'cliente' => [
                    'id_cliente' => $c['id_cliente'],
                    'nombres' => self_escape_string($c['nombres']),
                    'apellidos' => self_escape_string($c['apellidos']),
                    'nombre_completo' => self_escape_string($c['nombres'] . " " . $c["apellidos"]),
                    'porcentaje_descuento' => !isEmpty($c['porcentaje_descuento']) ? floatval($c['porcentaje_descuento']) : 0
                ],
                'bodegas' => $bodegas
            ];
        }
        echo json_encode(array('data' => $resultSet));
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

        try {

            $trx_movimiento_sucursales = [
                'id_movimiento_sucursales_estado' => sqlValue('2', 'int'),
                'id_empleado_envia' => sqlValue($this->user['ID'], 'text'),
                'id_sucursal_origen' => sqlValue($data['idSucursalOrigen'], 'int'),
                'comentario_envio' => sqlValue('', 'text'),
                'comentario_recepcion' => sqlValue('', 'text'),
                'id_empleado_recibe' => sqlValue('', 'text'),
                'id_cliente_recibe' => sqlValue($data['idClienteRecibe'], 'int'),
                'es_consignacion' => sqlValue(($data['tipo'] == 'cliente') ? 1 : 0, 'number'),
                'dias_consignacion' => sqlValue($data['diasConsignar'], 'int'),
                'porcetaje_compra_min' => sqlValue($data['porcentajeCompraMinima'], 'float'),
                'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                'fecha_recepcion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date')
            ];
            if($data['tipo'] == 'cliente'){
                if(isset($data['idSucursalDestinoCliente']) && !isEmpty($data['idSucursalDestinoCliente'])){
                    $trx_movimiento_sucursales['id_sucursal_destino'] = sqlValue($data['idSucursalDestinoCliente'], 'int');
                }
                $trx_movimiento_sucursales['id_cliente_recibe'] = sqlValue($data['idClienteRecibe'], 'int');
            } else {
                $trx_movimiento_sucursales['id_sucursal_destino'] = sqlValue($data['idSucursalDestino'], 'int');
            }

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
                if($data["tipo"] == 'cliente'){
                    $transaccion['id_cliente'] = sqlValue($data['idClienteRecibe'], 'int');
                }

                $this->db->query_insert('trx_transacciones', $transaccion);

                $id_transaccion_origen = $this->db->max_id('trx_transacciones', 'id_transaccion');

                $transaccionDes = [
                    'id_cuenta' => sqlValue($dsCuenta['id_cuenta'], 'int'),
                    'usuario_creacion' => sqlValue($this->user['ID'], 'text'),
                    'id_sucursal' => sqlValue($data['tipo'] == 'cliente' ? (isset($data['idSucursalDestinoCliente']) ? $data['idSucursalDestinoCliente'] : 'NULL') : $data['idSucursalDestino'], 'int'),
                    'descripcion' => sqlValue('Traslado Productos', 'text'),
                    'id_producto' => sqlValue($prod['id_producto'], 'int'),
                    'debe' => sqlValue('0', 'float'),
                    'haber' => sqlValue($prod['cantidad'], 'float'),
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date')
                ];

                if($data['tipo'] == 'bodega' || (isset($data["idSucursalDestinoCliente"]) && !isEmpty($data["idSucursalDestinoCliente"]))){
                    $this->db->query_insert('trx_transacciones', $transaccionDes);
                    $id_transaccion_destino = $this->db->max_id('trx_transacciones', 'id_transaccion');
                }

                $trx_movimiento_sucursales_detalle = [
                    'id_movimiento_sucursales' => sqlValue($id_movimiento_sucursales, 'int'),
                    'id_producto' => sqlValue($prod['id_producto'], 'int'),
                    'unidades' => sqlValue($prod['cantidad'], 'int'),
                    'id_transaccion' => sqlValue($id_transaccion_origen, 'int')
                ];

                if(isset($id_transaccion_destino) && !isEmpty($id_transaccion_destino)){
                    $trx_movimiento_sucursales_detalle['id_transaccion_destino'] = sqlValue($id_transaccion_destino, 'int');
                }
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