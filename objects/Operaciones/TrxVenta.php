<?php
/**
 * Created by PhpStorm.
 * User: bryan.cruz
 * Date: 8/25/2018
 * Time: 9:18 PM
 */

class TrxVenta extends FastTransaction {
    function __construct(){
        parent::__construct();
        $this->instanceName = 'TrxVenta';
        $this->setTitle('Venta');
        $this->hasCustomSave = true;
        $this->showSideBar = false;

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
            
            $scope.resetCliente = function(){
                $scope.lastClienteSelected = {
                    nombres: null,
                    apellidos: null,
                    identificacion: null,
                    correo: null,
                    factura_nit: null,
                    factura_nombre: null,
                    factura_direccion: null,
                    tipo_cliente: null,
                    id_pais: null,
                    id_departamento: null,
                    id_pais: null,
                    id_usuario: null,
                    telefono: null
                }
            };

            //GLOBALS THAT DONT RESET ON START AGAIN
            $scope.invalidSale = false;
            $scope.invalidSaleMsg = "";
            $scope.bodegas = [];
            $scope.bodegaSel = null;

            $scope.startAgain = function () {
                $scope.currentVentaIndex = null;
                $scope.productos = [];
                

                //Devolucion
                $scope.devolucion = {items:[]};
                $scope.selectDevolucion = true;
                $scope.sortDevolucion = false;
                $scope.ventasPasadas = [];
                $scope.ventaPasadaSelected = null;

                $scope.productos_facturar = new Array();
                $scope.search_codigo_origen = '';
                $scope.formas_pago = new Array();
                $scope.tipos_clientes = [];
                $scope.forma_pago = {};
                $scope.forma_pago.tipo_pago = 1;
                $scope.forma_pago.id_moneda = '';
                $scope.id_moneda_defecto = 0;
                $scope.cliente = '';
                $scope.show_nuevo_cliente = false;
                $scope.total = 0;
                $scope.show_detalle = false;
                $scope.vendedor = {};
                $scope.paises = [];
                $scope.departamentos = [];
                $scope.resetCliente();
                $('#loading').hide();

                $http.get($scope.ajaxUrl + '&act=getPaises').success(function (response) {
                    $scope.paises = response;
                });

                $http.get($scope.ajaxUrl + '&act=getClientes').success(function (response) {
                    $scope.clientes = response.data;
                    $scope.setClienteRowSelected($scope.rows);
                    $scope.setClienteRowIndex($scope.rows);
                });

                $http.get($scope.ajaxUrl + '&act=getTiposClientes').success(function (response) {
                    $scope.tipos_clientes = response;
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
                if(!$scope.bodegaSel) {
                    $http.get($scope.ajaxUrl + '&act=getBodegasAut').success(function (response) {
                        $scope.bodegas = response;
                        if($scope.bodegas.length == 1){
                            $scope.bodegaSel = $scope.bodegas[0];
                            $scope.show_detalle = true;
                        } else if(!$scope.bodegas.length) {
                            $scope.invalidSale = true;
                            $scope.invalidSaleMsg = "El usuario no tiene bodegas asignadas"
                        } else {
                            $("#bodegasModal").modal();
                        }
                    });
                } else {
                    $scope.setBodega($scope.bodegaSel);
                }
            };

            $scope.setBodega = bod => {
                $scope.bodegaSel = bod;
                $scope.devolucion = {};
                $scope.nueva_venta();
                $http.get($scope.ajaxUrl + '&act=getVentas&bod=' + $scope.bodegaSel.id_sucursal).success(function (response) {
                    $scope.ventas = response.data;
                    $scope.setVentasRowSelected($scope.ventas);
                    $scope.setVentasRowIndex($scope.ventas);
                    $("#bodegasModal").modal('hide');
                });
            }

            $scope.getVentasPasadas = _ => {
                $scope.ventasPasadas = [];
                $idCliente = $scope.lastClienteSelected.id_cliente;
                $bod = $scope.bodegaSel.id_sucursal;
                $http.get($scope.ajaxUrl + "&act=getVentasPasadas&bod=" + $bod + "&cl=" + $idCliente).success(response => {
                    $scope.ventasPasadas = response;
                    $scope.setVentasRowIndex($scope.ventasPasadas);
                    $("#devolucionModal").modal();
                })
            }

            $scope.selectVentaPasada = venta => {
                $scope.ventaPasadaSelected = venta;
                if(!$scope.devolucion.id_venta || $scope.devolucion.id_venta != venta.id_venta){
                    $scope.devolucion = {};
                    $scope.devolucion.id_venta = venta.id_venta;
                    $scope.devolucion.items = [];
                    $scope.devolucion.total = venta.total;
                    $scope.devolucion.maximo = venta.total * 0.2;
                    $scope.devolucion.credito = $scope.devolucion.items.sum("subtotal");
                } 
                $scope.selectDevolucion = false;
                $scope.sortDevolucion = true;
                $("#itemDevolucion").select();
            }

            $scope.checkItemDevolucion = _ => {
                if($scope.searchDevolucion){
                    let detalle = $scope.ventaPasadaSelected.detalles.find(i => i.codigo_producto.toLowerCase() == $scope.searchDevolucion.toLowerCase());
                    if(detalle){
                        let aDevolver = detalle.precio_venta;
                        console.log("A DEVOLVER:" , aDevolver);
                        if((parseFloat($scope.devolucion.credito) + parseFloat(aDevolver)) > $scope.devolucion.maximo){
                            $scope.showAlert('alert-warning', `El valor del producto (${$filter('currency')(aDevolver, 'Q', 2)}) sobrepasa el máximo a devolver`, 2500);    
                        } else {
                            var detDevolucion = $scope.devolucion.items.find(i => i.codigo_producto.toLowerCase() == detalle.codigo_producto.toLowerCase());
                            if(detDevolucion) {
                                detDevolucion.cantidad += 1;
                                detDevolucion.subtotal = detDevolucion.cantidad * detDevolucion.precio_venta; 
                            } else {
                                let itemDevolucion = {
                                    codigo_producto: detalle.codigo_producto,
                                    id_producto: detalle.id_producto,
                                    id_sucursal: detalle.id_sucursal,
                                    cantidad: 1,
                                    precio_venta: detalle.precio_venta,
                                    subtotal: parseFloat(detalle.precio_venta)
                                }
                                $scope.devolucion.items.push(itemDevolucion);
                            } 
                            $scope.devolucion.credito = $scope.devolucion.items.sum("subtotal");
                            $("#itemDevolucion").select();
                        }
                        
                    } else {
                        $scope.showAlert('alert-warning', 'No se encontró ese item en la venta', 2500);
                    }
                    $scope.searchDevolucion = "";
                }
            }

            $scope.agregarCreditoVenta = _ => {
                $scope.forma_pago.cantidad_credito_devolucion = $scope.devolucion.credito;
                $scope.selectDevolucion = true;
                $scope.sortDevolucion = false;
            }

            $scope.quitarItemDev = item => {
                let $r = $scope.devolucion.items.filter(i => i.id_producto !== item.id_producto || item.id_sucursal !== i.id_sucursal);
                $scope.devolucion.items = $r;
                $scope.devolucion.credito = $scope.devolucion.items.sum("subtotal");
            }

            $scope.showBodegaCat = _ => {
                if($scope.bodegas.length > 1){
                    $("#bodegasModal").modal();
                }
            }

            $scope.checkPedidos = _ => {
                $('#ventasModal').modal();
            }

            $scope.getVendedor = function(){
                $http.get($scope.ajaxUrl + "&act=getVendedor").success(response => {
                    $scope.vendedor = response;
                })
            }

            //Solo ejecutamos una vez
            $scope.getVendedor();

            $scope.getDepartamentos = function(){
                let id = $scope.lastClienteSelected.id_pais;
                $scope.departamentos = [];
                $http.get($scope.ajaxUrl + "&act=getDepartamentos&id=" + id).success(response => {
                    $scope.departamentos = response;
                })
            }

            $scope.cancelar = function () {
                $scope.cancel();
            };

            $scope.selectVentaRow = function(row){
                $scope.lastVentaSelected = row;
                $scope.currentVentaIndex = row.index;
                $scope.setVentasRowSelected($scope.ventas);
                $scope.lastVentaSelected.selected = true;
                $scope.show_detalle = false;
                $http.get($scope.ajaxUrl + '&act=getDetalleVenta&id_venta=' + $scope.lastVentaSelected.id_venta).success(function (response) {
                    $scope.productos_facturar = response.data;
                    $scope.total = $scope.productos_facturar.sum("sub_total");
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
                $scope.setClienteRowSelected($scope.clientes);
                $scope.lastClienteSelected.selected = true;
                $scope.cliente = $scope.lastClienteSelected.nombres + " " + $scope.lastClienteSelected.apellidos;
                console.log($scope.lastClienteSelected);
                $http.get($scope.ajaxUrl + "&act=getTipoPrecio&id=" + $scope.lastClienteSelected.id_tipo_precio).success(r => {
                    $scope.lastClienteSelected.tipo_precio = r;
                    if(r.porcentaje_descuento){
                        for(let i = 0; $scope.productos_facturar.length > i; i++){
                            $scope.productos_facturar[i].precio_venta = $scope.productos_facturar[i].precio_original - parseFloat($scope.productos_facturar[i].precio_original * (r.porcentaje_descuento/100))  
                            $scope.productos_facturar[i].sub_total = $scope.productos_facturar[i].precio_venta * $scope.productos_facturar[i].cantidad;
                        }
                    }
                    $scope.total = $scope.productos_facturar.sum("sub_total");
                })
                $('#clientesModal').modal('hide');
            };

            $scope.mostrar_detalle = function(){
                $scope.show_detalle = true;
            };

            $scope.imprimir_detalle = function(){
                var id_venta = $scope.lastVentaSelected.id_venta;
                window.open("./?action=pdf&tmp=VT&id_venta=" + id_venta);
            };

            $scope.nueva_venta = function(){
                $scope.productos_facturar = new Array();
                $scope.show_detalle = true;
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
                console.log("SAVING ROW");
                if(data['cantidad'] <= 0){
                    $scope.showAlert('alert-warning','La cantidad no puede ser menor que 1', 2500);
                    return false;
                } else {
                    $productos = $filter('filter')($scope.productos_facturar, {id_producto: id});
                    if ($productos.length > 0) {
                        if(parseFloat(data.cantidad) <= parseFloat($productos[0].total_existencias + $productos[0].cantidad)) {
                            $productos[0].total_existencias = parseFloat($productos[0].total_existencias + $productos[0].cantidad) - data.cantidad;
                            $productos[0].cantidad = data.cantidad;
                            $productos[0].sub_total = parseFloat($productos[0].cantidad) * parseFloat($productos[0].precio_venta);
                            $scope.total = $scope.productos_facturar.sum("sub_total");
                            $scope.productos = [];
                            $scope.search_codigo_origen = '';
                            return true;
                        } else {
                            $scope.showAlert('alert-warning','La cantidad de ' + data.cantidad + ' sobrepasa las existencias ' + ($productos[0].total_existencias + $productos[0].cantidad), 2500);
                            return false;
                        }
                    }
                }
            };

            $scope.removeRow = function(index, idProd, idSuc){
                console.log(`Quitando prod ${idProd} y Suc ${idSuc}`);
                console.log($scope.productos_facturar);
                var $r = $scope.productos_facturar.filter(p => p.id_producto !== idProd || p.id_sucursal !== idSuc);
                $scope.productos_facturar = $r;
                $scope.productos = [];
                $scope.search_codigo_origen = "";
                $scope.total = $scope.productos_facturar.sum("sub_total");
            };

            $scope.$watch('search_codigo_origen', function(val){
                var search = val.toLowerCase();
                $scope.productos = [];
                if (val.length >= 2 && $scope.bodegaSel) {
                    $('#loading').show();
                    $http.get($scope.ajaxUrl + '&act=getProductos&key=' + search + '&bod=' + $scope.bodegaSel.id_sucursal).success(function (response) {

                        $('#loading').hide();
                        if (response.data.length == 0) {
                            $scope.alerts = [];
                            $scope.alerts.push({
                                type: 'alert-warning',
                                msg: 'El producto con el código ' + val + ' no se encuentra o no hay existencias'
                            });
                        } else {
                            let productos = response.data;
                            for(let i = 0;productos.length > i; i++){
                                let $r = $scope.productos_facturar.filter(f => f.id_producto == productos[i].id_producto && f.id_sucursal == productos[i].id_sucursal);
                                $r = $r.length ? $r.pop() : {cantidad: 0};
                                productos[i].total_existencias -= $r.cantidad; 
                            }
                            $scope.productos = productos;
                            $scope.productos.length == 1 && $scope.agregarUno($scope.productos[0], true);
                        }
                    });
                }
            });

            $scope.agregarUno = function(prod, resetAfter) {
                var restoExistencias = prod.total_existencias - prod.cantidad;
                if (restoExistencias > 0) {

                    $productos = $filter('filter')($scope.productos_facturar, {id_producto: prod.id_producto, id_sucursal: prod.id_sucursal});

                    if ($productos.length > 0) {
                        $productos[0].total_existencias -= 1;
                        $productos[0].cantidad = parseFloat($productos[0].cantidad) + 1;
                        $productos[0].sub_total = parseFloat($productos[0].cantidad) * parseFloat($productos[0].precio_venta);
                    } else {
                        let agregar = Object.assign({}, prod);
                        agregar.total_existencias -= 1;
                        agregar.cantidad = 1;
                        if($scope.lastClienteSelected.tipo_precio && $scope.lastClienteSelected.tipo_precio.porcentaje_descuento){
                            agregar.precio_venta = agregar.precio_original - parseFloat(agregar.precio_original * ($scope.lastClienteSelected.tipo_precio.porcentaje_descuento/100));
                            agregar.sub_total = parseFloat(agregar.precio_venta);  
                        } else {
                            agregar.sub_total = parseFloat(prod.precio_venta);
                        }
                        console.log("Agregando prod");
                        console.log("TotalExistencias");
                        $scope.productos_facturar.push(agregar);
                    }
                    prod.total_existencias -= 1;
                } else {
                    console.log("No más unidades");
                    $scope.showAlert('alert-danger', 'No puede vender mas de las unidades (' + prod.total_existencias + ') de este producto ' + prod.nombre, 2500);
                }
                $productos_calc = $filter('filter')($scope.productos_facturar, {mostrar: 1});
                $scope.total = $productos_calc.sum("sub_total");
                if(resetAfter){
                    $scope.productos = [];
                    $("#producto").select();
                }
            };

            $scope.agregarVarios = function(prod) {
                var restoExistencias = prod.total_existencias - prod.cant_vender;
                console.log(restoExistencias);
                if (restoExistencias >= 0) {

                    $productos = $filter('filter')($scope.productos_facturar, {id_producto: prod.id_producto});

                    if ($productos.length > 0) {
                        $productos[0].total_existencias -= prod.cant_vender;
                        $productos[0].cantidad = parseFloat($productos[0].cantidad) + prod.cant_vender;
                        $productos[0].sub_total = parseFloat($productos[0].cantidad) * parseFloat($productos[0].precio_venta);
                    } else {
                        let agregar = Object.assign({}, prod);
                        agregar.total_existencias -= prod.cant_vender;
                        agregar.cantidad = parseInt(prod.cantidad);
                        if($scope.lastClienteSelected.tipo_precio && $scope.lastClienteSelected.tipo_precio.porcentaje_descuento){
                            agregar.precio_venta = agregar.precio_original - parseFloat(agregar.precio_original * ($scope.lastClienteSelected.tipo_precio.porcentaje_descuento/100))  
                            agregar.sub_total = parseFloat(agregar.precio_venta) * parseInt(prod.cantidad);
                        } else {
                            agregar.sub_total = parseFloat(prod.precio_venta) * parseInt(prod.cantidad);
                        }
                        $scope.productos_facturar.push(agregar);
                    }
                    prod.total_existencias -= prod.cant_vender;
                } else {
                    $scope.showAlert('alert-danger','No puede vender mas de las unidades (' + prod.total_existencias + ') de este producto ' + prod.nombre, 2500);
                }

                if($scope.lastClienteSelected.tipo_precio && $scope.lastClienteSelected.tipo_precio.porcentaje_descuento){
                    for(let i = 0; $scope.productos_facturar.length > i; i++){
                        $scope.productos_facturar[i].precio_venta = $scope.productos_facturar[i].precio_original - parseFloat($scope.productos_facturar[i].precio_original * (r.porcentaje_descuento/100))  
                        $scope.productos_facturar[i].sub_total = $scope.productos_facturar[i].precio_venta * $scope.productos_facturar[i].cantidad;
                    }
                }
                $productos_calc = $filter('filter')($scope.productos_facturar, {mostrar: 1});
                $scope.total = $productos_calc.sum("sub_total");
            };

            $scope.cambioMoneda = function() {
                $tipo_cambio = $filter('filter')($scope.tipo_cambio, {id_moneda_muchos: $scope.id_moneda_defecto, id_moneda_uno: $scope.forma_pago.id_moneda});
                if($tipo_cambio.length > 0){
                    $scope.tipo_cambio_actual = $tipo_cambio[0];
                    if($scope.forma_pago.monto && $scope.tipo_cambio_actual.factor){
                        $scope.forma_pago.cantidad_efectivo = parseFloat($scope.forma_pago.monto * $scope.tipo_cambio_actual.factor).toFixed(2); 
                    } 
                } else {
                    $scope.showAlert('alert-warning', 'No hay tipo de cambio configurado para esa moneda', 2500);
                }
            };

            $scope.cambioMonto = function(){
                $scope.forma_pago.monto = Math.max(0,$scope.forma_pago.monto || 0);
                if($scope.tipo_cambio_actual && $scope.tipo_cambio_actual.factor){
                    $scope.forma_pago.cantidad_efectivo = (parseFloat($scope.forma_pago.monto) * parseFloat($scope.tipo_cambio_actual.factor)).toFixed(2);
                } 
            }

            $scope.nuevo_cliente = function(){
                $scope.resetCliente();
                $scope.show_nuevo_cliente = true;
            };

            $scope.cerrar_nuevo_cliente = function(){
                $scope.show_nuevo_cliente = false;
            };

            $scope.validarCamposCliente = function(){
                var noneReq = ["factura_nit", "factura_nombre", "factura_direccion", "id_pais", "id_usuario"];
                for(i in $scope.lastClienteSelected){
                    if(noneReq.indexOf(i) == -1 && $scope.lastClienteSelected.hasOwnProperty(i) && !$scope.lastClienteSelected[i]){
                        console.log("Campo invalido cliente", i); 
                        return false;
                    }
                }
                return true;
            }

            $scope.guardar_nuevo_cliente = function() {
                if($scope.validarCamposCliente()){
                    let url = $scope.ajaxUrl + "&act=guardarCliente";
                    $scope.lastClienteSelected.id_usuario = $scope.vendedor.id_usuario;
                    console.log("Cliente", $scope.lastClienteSelected);
                    $http.post(url, {cliente:$scope.lastClienteSelected}, {
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    })
                    .success(function (response) {
                        if(response.r == 1) {
                            $http.get($scope.ajaxUrl + '&act=getClientes').success(function (response) {
                                $scope.clientes = response.data;
                                $scope.setClienteRowSelected($scope.rows);
                                $scope.setClienteRowIndex($scope.rows);
                            });

                            $scope.cliente = $scope.lastClienteSelected.nombres + " " + $scope.lastClienteSelected.apellidos;
                            $scope.lastClienteSelected.id_cliente = response.id_cliente;

                            $http.get($scope.ajaxUrl + "&act=getTipoPrecio&id=" + $scope.lastClienteSelected.id_tipo_precio).success(r => {
                                $scope.lastClienteSelected.tipo_precio = r;
                                if(r.porcentaje_descuento){
                                    for(let i = 0; $scope.productos_facturar.length > i; i++){
                                        $scope.productos_facturar[i].precio_venta = $scope.productos_facturar[i].precio_original - parseFloat($scope.productos_facturar[i].precio_original * (r.porcentaje_descuento/100))  
                                        $scope.productos_facturar[i].sub_total = $scope.productos_facturar[i].precio_venta * $scope.productos_facturar[i].cantidad;
                                    }
                                }
                                $scope.total = $scope.productos_facturar.sum("sub_total");
                            })

                            $scope.show_nuevo_cliente = false;
                            $('#clientesModal').modal('hide');
                        } else {
                            $scope.alerts = [];
                            $scope.alerts.push({
                                type: 'alert-danger',
                                msg: response.mess
                            });
                        }
                    })
                    .error(function (response) {
                        $scope.alerts = [];
                        $scope.alerts.push({
                            type: 'alert-danger',
                            msg: response.msg
                        });
                    });
                } else {
                    $scope.showAlert("Todos los campos de cliente son requeridos", "alert-danger", 2000);
                }
            };

            $scope.generar = function() {
                $productos_calc = $filter('filter')($scope.productos_facturar, {mostrar: 1});
                $scope.total = $productos_calc.sum("sub_total");
                $scope.forma_pago.monto = $scope.total.toFixed(2);

                $tipo_cambio = $filter('filter')($scope.tipo_cambio, {id_moneda_muchos: $scope.id_moneda_defecto, id_moneda_uno: $scope.forma_pago.id_moneda});

                if($tipo_cambio.length > 0){
                    $scope.tipo_cambio_actual = $tipo_cambio[0];
                    if($scope.forma_pago.monto && $scope.tipo_cambio_actual.factor){
                        console.log("MONTO: ", $scope.forma_pago.monto)
                        $scope.forma_pago.cantidad_efectivo = parseFloat($scope.forma_pago.monto * $scope.tipo_cambio_actual.factor).toFixed(2); 
                        if($scope.devolucion.credito > 0){
                            $scope.forma_pago.cantidad_efectivo = Math.max(0, $scope.forma_pago.cantidad_efectivo - $scope.devolucion.credito);
                            $scope.forma_pago.monto = Math.max(0, $scope.forma_pago.monto - $scope.devolucion.credito);
                        }
                    } 
                } else {
                    $scope.showAlert('alert-warning', 'No hay tipo de cambio configurado para esa moneda', 2500);
                }
            };

            $scope.tipoPagoFormValid = function(){
                var efectivo = false;
                var cheque = false;
                var voucher = false;
                if(parseFloat($scope.forma_pago.cantidad_efectivo)){
                    efectivo = !!($scope.forma_pago.id_moneda && $scope.forma_pago.monto); 
                } else {
                    efectivo = true;
                }

                if(parseFloat($scope.forma_pago.cantidad_cheque)){
                    cheque = !!($scope.forma_pago.id_banco && $scope.forma_pago.numero_autorizacion && $scope.forma_pago.autorizado_por);
                } else {
                    cheque = true;
                }

                if(parseFloat($scope.forma_pago.cantidad_voucher)){
                    voucher = !!$scope.forma_pago.numero_voucher;
                } else {
                    voucher = true;
                }
                console.log("EFECTIVO: ", efectivo);
                console.log("CHEQUE: ", cheque);
                console.log("DEPOSITO: ", voucher);

                return efectivo && cheque && voucher;
            }

            $scope.facturar = function() {

                if(!$scope.total){
                    return false;
                }

                if(!$scope.tipoPagoFormValid()){
                    $scope.showAlert('alert-warning', 'Hay hay campos requeridos pendientes. Porfavor verificar', 2500);
                    return false;
                }

                console.log("CLIENTE VENTA:", $scope.lastClienteSelected);
                if(!$scope.lastClienteSelected || !$scope.lastClienteSelected.id_cliente) {
                    $scope.showAlert('alert-warning', 'Debe seleccionar un cliente para la venta', 2500);
                    $('#tipoPagoModal').modal('hide');
                    return false;
                }

                var cantidad_pagada = parseFloat($scope.forma_pago.cantidad_cheque || 0) + parseFloat($scope.forma_pago.cantidad_efectivo || 0) + parseFloat($scope.forma_pago.cantidad_voucher || 0) + parseFloat($scope.devolucion.credito || 0);
                console.log("pAGADO", cantidad_pagada);
                console.log(cantidad_pagada);
                if(cantidad_pagada < $scope.total){
                    $scope.showAlert('alert-warning', 'Hay ' + $filter('currency')(parseFloat($scope.total - cantidad_pagada),'Q', 2) + ' pendientes de pago', 2500);
                    return false;
                }

                var cambio = null;
                if(cantidad_pagada > $scope.total){
                    var excedente = cantidad_pagada - $scope.total;
                    if(excedente <= $scope.forma_pago.cantidad_efectivo){
                        cambio = 'Favor entregar ' + $filter('currency')(parseFloat(cantidad_pagada - $scope.total), 'Q', 2) + ' de cambio';
                        $scope.forma_pago.cantidad_efectivo -= excedente;
                    } else {
                        $scope.showAlert('alert-warning', 'Hay excendente de ' + $filter('currency')(parseFloat(cantidad_pagada - $scope.total), 'Q', 2) + ' que no se puede dar de cambio', 2500);
                        return false;
                    }
                }

                $scope.forma_pago.cantidad = $scope.total;
                var productos = JSON.stringify($scope.productos_facturar);
                productos = productos.replace(/\\/g, "\\\\");

                var forma_pago = JSON.stringify($scope.forma_pago);
                forma_pago = forma_pago.replace(/\\/g, "\\\\");

                console.log("FORMA PAGO: ", $scope.forma_pago);
                $rootScope.modData = {
                    productos: JSON.parse(productos),
                    id_cliente: $scope.lastClienteSelected.id_cliente,
                    forma_pago: JSON.parse(forma_pago),
                    id_venta: ($scope.lastVentaSelected == undefined) ? 0 : $scope.lastVentaSelected.id_venta,
                    devolucion: $scope.devolucion
                };
                $scope.showAlert('alert-info', 'Guardando... ' + (cambio || ""), 3000);
                //console.log($rootScope.modData);
                $scope.doSave();
            };

            $scope.startAgain();
            
            $rootScope.addCallback(function (response) {
                $scope.alerts = [];
                if ((response != undefined) && (response.result == 1)) {
                    var id_venta = 0;

                    if (response.data)
                        id_venta = response.data.id_venta;

                    window.open("./?action=pdf&tmp=VT&id_venta=" + id_venta);
                }
                $('#tipoPagoModal').modal('hide');
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

    public function getVendedor()
    {
        $vendedor = [
            'nombres' => self_escape_string($this->user['FIRST_NAME'] . " " . $this->user['LAST_NAME']),
            'id_usuario' => $this->user['ID']
        ];
        echo json_encode($vendedor);
    }

    public function getPaises()
    {
        Collection::get($this->db, "paises")->select(["id_pais", "nombre"], true)->toJson();
    }

    public function getDepartamentos()
    {
        $id = getParam("id");
        $deptos = Collection::get($this->db, "departamentos")->select(["id_pais","id_departamento","nombre"], true);
        if(!isEmpty($id)){
            $deptos = $deptos->where(["id_pais" => $id]);
        }
        $deptos->toJson();
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

    public function getTiposClientes(){
        Collection::get($this->db, "clientes_tipos_precio")->select(["id_tipo_precio","nombre"])->toJson();
    }

    public function getClientes()
    {
        $resultSet = array();

        $dsClientes = $this->db->query_select('clientes');

        foreach ($dsClientes as $p) {
            $resultSet[] = array('id_tipo_precio' => $p['id_tipo_precio'], 'id_cliente' => $p['id_cliente'], 'identificacion' => $p['identificacion'], 'nombres' => $p['nombres'], 'apellidos' => $p['apellidos']);
        }

        echo json_encode(array('data' => $resultSet));
    }

    public function getTipoPrecio(){
        $id = getParam("id");
        echo json_encode(Collection::get($this->db, "clientes_tipos_precio")->where(["id_tipo_precio" => $id])->select(["id_tipo_precio","nombre", "porcentaje_descuento"], true)->single());
    }

    public function getVentas()
    {
        $bod = getParam("bod");
        $queryVentas = "      
            SELECT    v.id_venta, v.total, CONCAT(c.nombres,' ',c.apellidos) AS nombre_cliente, v.fecha_creacion
            FROM	  trx_venta v
            JOIN       clientes c on c.id_cliente=v.id_cliente 
            WHERE     v.estado = 'P'
            AND v.id_venta IN (select id_venta FROM trx_venta_detalle where id_sucursal=%s)" ;

        $ventas = $this->db->queryToArray(sprintf($queryVentas, $bod));
        echo json_encode(array('data' => $ventas));
    }

    public function getDetalleVenta(){
        $id_venta = getParam("id_venta");
        $queryProductos = " SELECT	vd.id_venta_detalle, p.id_producto, p.nombre, p.descripcion, p.precio_venta, p.imagen,
                                    p.codigo_origen, vd.cantidad, (vd.cantidad * p.precio_venta) AS sub_total,
                                    (sum(t.haber) - sum(t.debe)) AS total_existencias, 1 AS mostrar, t.id_sucursal
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

    public function getBodegasAut(){
        $accesos = $this->db->query_select("usuarios_bodegas", sprintf("id_usuario='%s'", $this->user['ID']));
        $i = 1;
        $strAccesos = "";
        foreach($accesos as $a){
            $strAccesos .= $a["id_bodega"] . (count($accesos) > $i ? "," : "");
            $i++;
        };
        Collection::get($this->db, "sucursales", sprintf("id_sucursal in (%s)", $strAccesos))->select(['id_sucursal', 'nombre'], true)->toJson();
    }

    public function getVentasPasadas(){
        $date = new DateTime();
        $monthAgo = $date->sub(new DateInterval('P1M'));
        $bod = getParam("bod");
        $cl = getParam("cl");
        $sql = "
            SELECT v.id_venta, v.total, v.id_cliente, CONCAT(c.nombres, ' ', c.apellidos) nombre_cliente, v.fecha_creacion 
            FROM trx_venta v
            JOIN clientes c on c.id_cliente=v.id_cliente
            WHERE v.id_venta in (SELECT id_venta FROM trx_venta_detalle where id_sucursal = %s) 
            AND v.fecha_creacion > '%s'
            AND v.estado = 'V'
            AND v.id_cliente=%s
        ";
        $ventas = $this->db->queryToArray(sprintf($sql, $bod, $monthAgo->format(SQL_DT_FORMAT), $cl));
        for($i = 0; count($ventas) > $i; $i++){
            $detalleSql = "
                SELECT vd.id_producto, vd.id_sucursal, vd.cantidad, vd.precio_venta, vd.id_producto, vd.id_sucursal, p.nombre nombre_producto, p.codigo codigo_producto, s.nombre nombre_bodega 
                FROM trx_venta_detalle vd
                JOIN producto p on p.id_producto=vd.id_producto
                JOIN sucursales s on s.id_sucursal=vd.id_sucursal
                WHERE id_venta = %s
            ";
            $detalles = sanitize_array_by_keys($this->db->queryToArray(sprintf($detalleSql, $ventas[$i]['id_venta'])), ['nombre_producto', 'codigo_producto', 'nombre_bodega']);
            $ventas[$i]['detalles'] = $detalles;
        }
        echo json_encode($ventas);
    }

    public function getProductos(){
        $key = getParam("key");
        $bod = getParam("bod");
        $accesos = [$bod];
        $strAccesos = join(",",$accesos);
        $queryProductos = " 
        SELECT	p.id_producto, p.nombre, p.descripcion, p.precio_venta precio_original, p.precio_venta, p.imagen,
                p.codigo, FLOOR(COALESCE((sum(trx.haber) - sum(trx.debe)),0)) AS total_existencias,
                1 AS mostrar, max(t.nombre) AS nombre_categoria, max(s.nombre) AS nombre_sucursal,  max(s.id_sucursal) AS id_sucursal
        FROM	producto p
        LEFT JOIN tipo t ON t.id_tipo = p.id_tipo
        LEFT JOIN trx_transacciones trx ON trx.id_producto = p.id_producto AND trx.id_sucursal in (" . $strAccesos . ")
        LEFT JOIN sucursales s ON s.id_sucursal = trx.id_sucursal 
        WHERE	(
            p.codigo LIKE '%". $key . "%'
            OR
            p.codigo_origen LIKE '%". $key . "%'
            OR
            p.nombre LIKE '%". $key . "%'
            OR
            p.descripcion LIKE '%". $key . "%'
            OR
            t.nombre LIKE '%". $key . "%'
            OR
            s.nombre LIKE '%". $key . "%'
        )
        GROUP BY p.id_producto, trx.id_sucursal";
//                            HAVING	(sum(trx.haber) - sum(trx.debe)) > 0";

        $productos = $this->db->queryToArray($queryProductos);

        for($i = 0; count($productos) > $i; $i++){
            $productos[$i]['cantidad'] = 0;
            $productos[$i]['sub_total'] = 0;
            $productos[$i]['cant_vender'] = 0;
        }

        echo json_encode(array('data' => $productos));
    }

    public function guardarCliente()
    {
        $data = inputStreamToArray();
        $data = $data['cliente'];
        $r = 0;
        $mess = "";
        $fecha = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $check = $this->db->query_select("clientes", sprintf("identificacion='%s' and id_pais=%s", $data["identificacion"], $data['id_pais']));
        if(count($check) > 0) {
            $r = 0;
            $mess = sprintf("La identificacion %s ya existe en ese país", $data['identificacion']);
        } else {
            $cliente = [
                'nombres' => sqlValue($data['nombres'], 'text'),
                'apellidos' => sqlValue($data['apellidos'], 'text'),
                'direccion' => sqlValue(' ', 'text'),
                'identificacion' => sqlValue($data['identificacion'], 'text'),
                'correo' => sqlValue($data['correo'], 'text'),
                'id_tipo_precio' => sqlValue($data['tipo_cliente'], 'int'),
                'id_pais' => sqlValue($data['id_pais'], 'int'),
                'id_departamento' => sqlValue($data['id_departamento'], 'int'),
                'id_usuario' => sqlValue($data['id_usuario'], 'text'),
                'tiene_credito' => sqlValue(0, 'number'),
                'dias_credito' => sqlValue(0, 'number'),
                'id_cliente_referido' => sqlValue(0, 'number'),
                'factura_nit' => sqlValue($data['factura_nit'], 'text'),
                'factura_nombre' => sqlValue($data['factura_nombre'], 'text'),
                'factura_direccion' => sqlValue($data['factura_direccion'], 'text'),
                'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
            ];
    
            $this->db->query_insert('clientes', $cliente);
            $id_cliente = $this->db->max_id('clientes', 'id_cliente');
            $date = new Datetime();
            $telefono = [
                "id_cliente" => $id_cliente,
                "numero" => $data['telefono'],
                "usuario_creacion" => sqlValue($this->user['ID'], 'text'),
                "fecha_creacion" => sqlValue($date->format(SQL_DT_FORMAT), 'date')
            ];

            $this->db->query_insert('clientes_telefonos', $telefono);
            $r = 1;
            $mess = "Cliente guardado";
        }
        echo json_encode(array('r' => $r, 'mess' => $mess, 'id_cliente' => isset($id_cliente) ? $id_cliente : 0));
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
        //print_r($data);
        $fecha = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $dsEmpleado = decode_email_address($this->user['ID']);
        $dsCuentaVenta = Collection::get($this->db, 'cuentas', 'lower(nombre) = "venta"')->single();
        $dsCuentaReingreso = Collection::get($this->db, 'cuentas', 'lower(nombre) = "reingreso"')->single();
        $dsMoneda = Collection::get($this->db, 'monedas', 'moneda_defecto = 1')->single();

        $venta = [
            'total' => sqlValue($data['forma_pago']['cantidad'], 'float'),
            'id_cliente' => sqlValue($data['id_cliente'], 'int'),
            'usuario_venta' => sqlValue($dsEmpleado, 'text'),
            'estado' => sqlValue('V', 'text'),
            'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
            'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
        ];

        if($data['id_venta'] > 0) {
            $this->db->query_update('trx_venta', $venta, sprintf('id_venta = %s', $data['id_venta']));

            $id_venta = $data['id_venta'];
        } else {
            $this->db->query_insert('trx_venta', $venta);
            $id_venta = $this->db->max_id('trx_venta', 'id_venta');
        }

        foreach ($data['productos'] as $prod) {

            if($prod['mostrar'] == "1") {

                if($data['id_venta'] > 0) {

                    $venta_detalle = [
                        'cantidad' => sqlValue($prod['cantidad'], 'float'),
                        'precio_venta' => sqlValue($prod['precio_venta'], 'float'),
                        'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                        'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
                    ];

                    $this->db->query_update('trx_venta_detalle', $venta_detalle, sprintf('id_venta_detalle = %s', $prod['id_venta_detalle']));
                } else {
                    $venta_detalle = [
                        'id_venta' => sqlValue($id_venta, 'int'),
                        'id_producto' => sqlValue($prod['id_producto'], 'int'),
                        'id_sucursal' => sqlValue($prod['id_sucursal'], 'int'),
                        'cantidad' => sqlValue($prod['cantidad'], 'float'),
                        'precio_venta' => sqlValue($prod['precio_venta'], 'float'),
                        'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                        'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
                    ];

                    $this->db->query_insert('trx_venta_detalle', $venta_detalle);
                }

                $transaccion = [
                    'id_cuenta' => sqlValue($dsCuentaVenta['id_cuenta'], 'int'),
                    'id_sucursal' => sqlValue($prod['id_sucursal'], 'int'),
                    'descripcion' => sqlValue('Venta', 'text'),
                    'id_moneda' => sqlValue($dsMoneda['id_moneda'], 'int'),
                    'id_producto' => sqlValue($prod['id_producto'], 'int'),
                    'debe' => sqlValue($prod['cantidad'], 'float'),
                    'haber' => sqlValue('0', 'float'),
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                    'id_cliente' => sqlValue($data['id_cliente'], 'int')
                ];

                $this->db->query_insert('trx_transacciones', $transaccion);
                $trxId = $this->db->max_id('trx_transacciones', 'id_transaccion');
                $ventaUpdate = [
                    "id_transaccion" => sqlValue($trxId, "int")
                ];
                $this->db->query_update("trx_venta", $ventaUpdate, sprintf("id_venta=%s", $id_venta)); 
            } else {

                $venta_detalle = [
                    'cantidad' => sqlValue('0', 'float'),
                    'precio_venta' => sqlValue('0', 'float'),
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                    'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
                ];

                $this->db->query_update('trx_venta_detalle', $venta_detalle, sprintf('id_venta_detalle = %s', $prod['id_venta_detalle']));

                $transaccion = [
                    'id_cuenta' => sqlValue($dsCuentaVenta['id_cuenta'], 'int'),
                    'id_sucursal' => sqlValue($prod['id_sucursal'], 'int'),
                    'descripcion' => sqlValue('Ingreso de venta', 'text'),
                    'id_moneda' => sqlValue($dsMoneda['id_moneda'], 'int'),
                    'id_producto' => sqlValue($prod['id_producto'], 'int'),
                    'debe' => sqlValue('0', 'float'),
                    'haber' => sqlValue($prod['cantidad'], 'float'),
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                    'id_cliente' => sqlValue($data['id_cliente'], 'int')
                ];

                $this->db->query_insert('trx_transacciones', $transaccion);
                $trxId = $this->db->max_id('trx_transacciones', 'id_transaccion');
                $ventaUpdate = [
                    "id_transaccion" => sqlValue($trxId, "int")
                ];
                $this->db->query_update("trx_venta", $ventaUpdate, sprintf("id_venta=%s", $id_venta)); 
            }
        }

        if(isset($data['forma_pago']['cantidad_efectivo']) && !isEmpty($data['forma_pago']['cantidad_efectivo'])){
            $forma_pago = [
                'id_venta' => sqlValue($id_venta, 'int'),
                'id_forma_pago' => sqlValue(1, 'int'),
                'id_moneda' => sqlValue(array_key_exists("id_moneda", $data['forma_pago']) ? $data['forma_pago']['id_moneda'] : 0, 'int'),
                'cantidad' => sqlValue(array_key_exists("cantidad_efectivo", $data['forma_pago']) ? $data['forma_pago']['cantidad_efectivo'] : 0, 'float'),
                'monto' => sqlValue(array_key_exists("monto", $data['forma_pago']) ? $data['forma_pago']['monto'] : 0, 'float'),
                'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
            ];
            $this->db->query_insert('trx_venta_formas_pago', $forma_pago);    
        }

        if(isset($data['forma_pago']['cantidad_cheque']) && !isEmpty($data['forma_pago']['cantidad_cheque'])){
            $forma_pago = [
                'id_venta' => sqlValue($id_venta, 'int'),
                'id_forma_pago' => sqlValue(2, 'int'),
                'cantidad' => sqlValue(array_key_exists("cantidad_cheque", $data['forma_pago']) ? $data['forma_pago']['cantidad_cheque'] : 0, 'float'),
                'id_moneda' => sqlValue(1, 'int'),
                'monto' => sqlValue(array_key_exists("cantidad_cheque", $data['forma_pago']) ? $data['forma_pago']['cantidad_cheque'] : 0, 'float'),
                'numero_cheque' => sqlValue(array_key_exists("numero_cheque", $data['forma_pago']) ? $data['forma_pago']['numero_cheque'] : '', 'text'),
                'id_banco' => sqlValue(array_key_exists("id_banco", $data['forma_pago']) ? $data['forma_pago']['id_banco'] : 0, 'int'),
                'numero_autorizacion' => sqlValue(array_key_exists("numero_autorizacion", $data['forma_pago']) ? $data['forma_pago']['numero_autorizacion'] : '', 'text'),
                'autorizado_por' => sqlValue(array_key_exists("autorizado_por", $data['forma_pago']) ? $data['forma_pago']['autorizado_por'] : '', 'text'),
                'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
            ];
            $this->db->query_insert('trx_venta_formas_pago', $forma_pago);    
        }

        if(isset($data['forma_pago']['cantidad_voucher']) && !isEmpty($data['forma_pago']['cantidad_voucher'])){
            $forma_pago = [
                'id_venta' => sqlValue($id_venta, 'int'),
                'id_forma_pago' => sqlValue(3, 'int'),
                'cantidad' => sqlValue(array_key_exists("cantidad_voucher", $data['forma_pago']) ? $data['forma_pago']['cantidad_voucher'] : 0, 'float'),
                'id_moneda' => sqlValue(1, 'int'),
                'monto' => sqlValue(array_key_exists("cantidad_voucher", $data['forma_pago']) ? $data['forma_pago']['cantidad_voucher'] : 0, 'float'),
                'numero_voucher' => sqlValue(array_key_exists("numero_voucher", $data['forma_pago']) ? $data['forma_pago']['numero_voucher'] : '', 'text'),
                'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
            ];
            $this->db->query_insert('trx_venta_formas_pago', $forma_pago);    
        }

        //DEVOLUCION
        if(isset($data['devolucion']['id_venta']) && floatval($data['devolucion']['credito']) > 0){
            //MARCANDO LA VENTA ANTERIOR
            $devUpdate = [
                "credito_devolucion" => sqlValue($data['devolucion']['credito'], 'float'),
                "estado" => sqlValue('D', 'text'),
                "fecha_devolucion" => sqlValue($fecha->format('Y-m-d H:i:s'), 'date')
            ];
            $this->db->query_update('trx_venta', $devUpdate, sprintf('id_venta=%s', $data['devolucion']['id_venta']));

            //FORMA DE PAGO VENTA ACTUAL
            $forma_pago = [
                'id_venta' => sqlValue($id_venta, 'int'),
                'id_forma_pago' => sqlValue(4, 'int'),
                'id_moneda' => sqlValue($dsMoneda['id_moneda'], 'int'),
                'cantidad' => sqlValue($data['devolucion']['credito'], 'float'),
                'monto' => sqlValue($data['devolucion']['credito'], 'float'),
                'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
            ];
            $this->db->query_insert('trx_venta_formas_pago', $forma_pago);

            foreach($data['devolucion']['items'] as $prod){

                $venta_detalle = [
                    'cantidad_devolucion' => sqlValue($prod['cantidad'], 'float')
                ];

                $this->db->query_update('trx_venta_detalle', $venta_detalle, sprintf('id_producto = %s AND id_sucursal=%s', $prod['id_producto'],$prod['id_sucursal']));
              

                $transaccion = [
                    'id_cuenta' => sqlValue($dsCuentaVenta['id_cuenta'], 'int'),
                    'id_sucursal' => sqlValue($prod['id_sucursal'], 'int'),
                    'descripcion' => sqlValue('Venta', 'text'),
                    'id_producto' => sqlValue($prod['id_producto'], 'int'),
                    'debe' => sqlValue('0', 'float'),
                    'haber' => sqlValue($prod['cantidad'], 'float'),
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                    'id_cliente' => sqlValue($data['id_cliente'], 'int')
                ];

                $this->db->query_insert('trx_transacciones', $transaccion);
            }
        }
        //throw new Exception("ROLLING BACK FOR TESTING");
        $this->r = 1;
        $this->msg = 'Venta realizada con éxito';
        $this->returnData = array('id_venta' => $id_venta);
    }
}