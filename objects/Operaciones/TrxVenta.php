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
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.2/dist/sweetalert2.all.min.js"></script>
        <script>
            Array.prototype.keySort = function(keys){          
                        keys = keys || {};

                // via
                // https://stackoverflow.com/questions/5223/length-of-javascript-object-ie-associative-array
                var obLen = function(obj) {
                    var size = 0, key;
                    for (key in obj) {
                        if (obj.hasOwnProperty(key))
                            size++;
                    }
                    return size;
                };

                // avoiding using Object.keys because I guess did it have IE8 issues?
                // else var obIx = function(obj, ix){ return Object.keys(obj)[ix]; } or
                // whatever
                var obIx = function(obj, ix) {
                    var size = 0, key;
                    for (key in obj) {
                        if (obj.hasOwnProperty(key)) {
                            if (size == ix)
                                return key;
                            size++;
                        }
                    }
                    return false;
                };

                var keySort = function(a, b, d) {
                    d = d !== null ? d : 1;
                    // a = a.toLowerCase(); // this breaks numbers
                    // b = b.toLowerCase();
                    if (a == b)
                        return 0;
                    return a > b ? 1 * d : -1 * d;
                };

                var KL = obLen(keys);

                if (!KL)
                    return this.sort(keySort);

                for ( var k in keys) {
                    // asc unless desc or skip
                    keys[k] = 
                            keys[k] == 'desc' || keys[k] == -1  ? -1 
                        : (keys[k] == 'skip' || keys[k] === 0 ? 0 
                        : 1);
                }

                this.sort(function(a, b) {
                    var sorted = 0, ix = 0;

                    while (sorted === 0 && ix < KL) {
                        var k = obIx(keys, ix);
                        if (k) {
                            var dir = keys[k];
                            sorted = keySort(a[k], b[k], dir);
                            ix++;
                        }
                    }
                    return sorted;
                });
                return this;
            };
            //CONTROLLER
        app.controller('ModuleCtrl', ['$scope', '$http', '$rootScope' , '$timeout', '$filter', function ($scope, $http, $rootScope, $timeout, $filter) {
            Array.prototype.sum = function (prop) {
                var total = 0
                for ( var i = 0, _len = this.length; i < _len; i++ ) {
                    if(this[i].mostrar == 1){
                        total += parseFloat(this[i][prop])
                    }
                }
                return total
            };
            $scope.rand = Math.random();
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
                $scope.preventClienteChange = false;
                $scope.preventProductoSearch = false;

                //Devolucion
                $scope.devolucion = {items:[]};
                $scope.selectDevolucion = true;
                $scope.sortDevolucion = false;
                $scope.ventasPasadas = [];
                $scope.ventas = [];
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
                $scope.lastVentaSelected = null;
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
                            $scope.setBodega($scope.bodegas[0]);
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
                $scope.getVentas();
            }
            $scope.hasProductos = () => {
                return $scope.productos_facturar.filter(p => p.mostrar == 1).length
            }

            $scope.getVentas = _ => {
                $http.get($scope.ajaxUrl + '&act=getVentas&bod=' + $scope.bodegaSel.id_sucursal).success(function (response) {
                    $scope.ventas = response.data.map((v,i) => {
                        return {
                            ...v,
                            selected: false,
                            index: i
                        }
                    });
                    $("#bodegasModal").modal('hide');
                    if(!$scope.$$phase){
                        $scope.$apply();
                    }
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
                console.log("CHECKING PEDIDOS");
                $scope.getVentas();
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
                    console.log($scope.lastVentaSelected);
                    let cliente = $scope.clientes.find(c => c.id_cliente == $scope.lastVentaSelected.id_cliente);
                    console.log(cliente);
                    $scope.lastVentaSelected.esPedido = true;
                    $scope.selectClienteRow(cliente);
                    $scope.preventClienteChange = true;
                    $scope.preventProductoSearch = true;
                });
            };

            $scope.anularVenta = function(venta){
                swal({
                    title: "Anular pedido",
                    text: "¿Está seguro de anular el pedido?. Esta accion no es reversible",
                    type: "warning",
                    confirmButtonText: "Confirmar",
                    cancelButtonText: "Cancelar",
                    showCancelButton: true
                }).then(res => {
                    if(res.value === true){
                        $http.get($scope.ajaxUrl + '&act=anularVenta&id_venta=' + venta.id_venta).success(function (response) {
                            if(response.result == 1){
                                swal("Anular", "Se ha reingresado el inventario", "success");
                                $scope.startAgain();
                            } else {
                                swal("Oh oh", "Ocurrió un error al anular el pedido. Intente más tarde", "error");
                            }
                        });
                    }
                })
            }

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
                if(!$scope.lastVentaSelected || !$scope.lastVentaSelected.esPedido){
                    
                    $http.get($scope.ajaxUrl + "&act=getTipoPrecio&id=" + $scope.lastClienteSelected.id_tipo_precio).success(r => {
                        $scope.lastClienteSelected.tipo_precio = r;
                        if(r.porcentaje_descuento){
                            for(let i = 0; $scope.productos_facturar.length > i; i++){
                                $scope.productos_facturar[i] = $scope.applyDiscountProducto($scope.productos_facturar[i]);
                                $scope.productos_facturar[i].sub_total = $scope.productos_facturar[i].precio_venta * $scope.productos_facturar[i].cantidad;
                            }
                        }
                        $scope.total = $scope.productos_facturar.sum("sub_total");
                        if(!$scope.$$phase){
                            $scope.$apply();
                        }
                    })
                }
                $('#clientesModal').modal('hide');
            };

            $scope.mostrar_detalle = function(){
                $scope.show_detalle = true;
            };

            $scope.imprimir_detalle = function(){
                var id_venta = $scope.lastVentaSelected.id_venta;
                window.open("./?action=pdf&tmp=PD&id_venta=" + id_venta);
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

            $scope.saveRow = function(data, id, idSuc, rowform) {
                console.log("SAVING ROW");
                if(data['cantidad'] <= 0){
                    swal("Cantidad incorrecta", "La cantidad no puede ser menor a 1", "warning");
                    return false;
                } else {
                    $productos = $filter('filter')($scope.productos_facturar, {id_producto: id, id_sucursal: idSuc});
                    if(!$scope.lastVentaSelected){
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
                    } else {
                        if ($productos.length > 0) {
                            if(parseFloat(data.cantidad) <= parseFloat($productos[0].cantidad_original)) {
                                $productos[0].total_existencias = parseFloat($productos[0].total_existencias + $productos[0].cantidad) - data.cantidad;
                                $productos[0].cantidad = data.cantidad;
                                $productos[0].sub_total = parseFloat($productos[0].cantidad) * parseFloat($productos[0].precio_venta);
                                $scope.total = $scope.productos_facturar.sum("sub_total");
                                $scope.productos = [];
                                $scope.search_codigo_origen = '';
                                return true;
                            } else {
                                swal("Cantidad incorrecta", "La cantidad de pedidos solo puede ser menor a la solicitada", "warning");
                                return false;
                            }
                        }
                    }
                }
            };

            $scope.removeRow = function(index, idProd, idSuc){
                console.log(`Quitando prod ${idProd} y Suc ${idSuc}`);
                if(!$scope.lastVentaSelected){
                    var $r = $scope.productos_facturar.filter(p => p.id_producto !== idProd || p.id_sucursal !== idSuc);
                    $scope.productos_facturar = $r;
                    $scope.productos = [];
                    $scope.search_codigo_origen = "";
                    $scope.total = $scope.productos_facturar.sum("sub_total");
                } else {
                    var $r = $scope.productos_facturar.find(p => p.id_producto == idProd && p.id_sucursal == idSuc);
                    $r.mostrar = 0;
                    $scope.productos = [];
                    $scope.search_codigo_origen = "";
                    $scope.total = $scope.productos_facturar.sum("sub_total");
                }
            };

            $scope.$watch('search_codigo_origen', function(val){
                var search = val.toLowerCase();
                if (val.length >= 2 && $scope.bodegaSel) {
                    $scope.productos = [];
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
                            if(response.data.length <= 50){
                                let productos = response.data;
                                for(let i = 0;productos.length > i; i++){
                                    let $r = $scope.productos_facturar.filter(f => f.id_producto == productos[i].id_producto && f.id_sucursal == productos[i].id_sucursal);
                                    $r = $r.length ? $r.pop() : {cantidad: 0};
                                    productos[i].total_existencias -= $r.cantidad; 
                                }
                                $scope.productos = productos;
                                $scope.encontrados = productos.length;
                                if(!$scope.$$phase){
                                    $scope.$apply();
                                }
                            } else {
                                $scope.encontrados = response.data.length;
                            }
                            //$scope.productos.length == 1 && $scope.agregarUno($scope.productos[0], true);
                        }
                    });
                } else {
                    $scope.encontrados = 0;
                }
            });

            $("#producto").keyup(function(ev) {
                // 13 is ENTER
                if (ev.which === 13 && $scope.productos.length == 1) {
                    $scope.agregarUno($scope.productos[0], true);
                    if(!$scope.$$phase){
                        $scope.$apply();
                    }
                }
            });

            $scope.applyDiscountProducto = p => {
                if($scope.lastClienteSelected && $scope.lastClienteSelected.tipo_precio && $scope.lastClienteSelected.tipo_precio.descuentos && $scope.lastClienteSelected.tipo_precio.porcentaje_descuento){
                    $scope.lastClienteSelected.tipo_precio.descuentos = $scope.lastClienteSelected.tipo_precio.descuentos.keySort({id_producto: 'desc', id_tipo:'desc'});
					let pv = p.precio_original;
                    let des = $scope.lastClienteSelected.tipo_precio.porcentaje_descuento;
                    p.precio_venta = pv - (pv * (des / 100)); 
                    $scope.lastClienteSelected.tipo_precio.descuentos.forEach(d => {
                        console.log("DESCUENTO ADICIONAL APLICAR:", `${d.id_producto}:${p.id_producto},${d.id_tipo}:${p.id_tipo}`);
                        if(d.id_producto == p.id_producto){
                            p.precio_venta = d.cantidad ? p.precio_venta - d.cantidad : p.precio_venta - (pv * (d.porcentaje_descuento / 100));
                            p.cantidad_descuento = d.cantidad;
                            p.porcentaje_descuento = d.porcentaje_descuento;
                        } else if(d.id_tipo == p.id_tipo && (!p.cantidad_descuento && !p.porcentaje_descuento)){
                            p.precio_venta = d.cantidad ? p.precio_venta - d.cantidad : p.precio_venta - (pv * (d.porcentaje_descuento / 100));
                            p.cantidad_descuento = d.cantidad;
                            p.porcentaje_descuento = d.porcentaje_descuento;
                        } else if(!d.id_producto && !d.id_tipo && (!p.cantidad_descuento && !p.porcentaje_descuento)){
                            p.precio_venta = d.cantidad ? p.precio_venta - d.cantidad : p.precio_venta - (pv * (d.porcentaje_descuento / 100));
                            p.cantidad_descuento = d.cantidad;
                            p.porcentaje_descuento = d.porcentaje_descuento;
                        }
                    })
                    if(p.precio_venta == p.precio_original){
                        p.cantidad_descuento = 0;
                        p.porcentaje_descuento = 0;
                    }
				} else {
                        let pv = parseFloat(p.precio_original);
                        let dpc = parseFloat(p.descuento_producto_cantidad);
                        let dpp = parseFloat(p.descuento_producto_porcentaje);
                        let dcc = parseFloat(p.descuento_categoria_cantidad);
                        let dcp = parseFloat(p.descuento_categoria_porcentaje);
                        let dgc = parseFloat(p.descuento_general_cantidad);
                        let dgp = parseFloat(p.descuento_general_porcentaje);
						if(dpc || dpp){
							p.precio_venta = dpc ? pv - dpc : pv - (pv * (dpp / 100));
							p.porcentaje_descuento = dpp;
							p.cantidad_descuento = dpc;
						} else if(dcc || dcp){
							p.precio_venta = dcc ? pv - dcc : pv - (pv * (dcp / 100));	
							p.porcentaje_descuento = dcp;
							p.cantidad_descuento = dcc;
						} else if(dgc || dgp){
							p.precio_venta = dgc ? pv - dgc : pv - (pv * (dgp / 100));	
							p.porcentaje_descuento = dgp;
							p.cantidad_descuento = dgc;
						} else {
							p.porcentaje_descuento = 0;
							p.cantidad_descuento = 0;
						}
                }
                return p;
            }

            $scope.hasDescuento = p => {
                if($scope.lastClienteSelected && $scope.lastClienteSelected.tipo_precio && $scope.lastClienteSelected.tipo_precio.descuentos && $scope.lastClienteSelected.tipo_precio.porcentaje_descuento){
                    $scope.lastClienteSelected.tipo_precio.descuentos = $scope.lastClienteSelected.tipo_precio.descuentos.keySort({id_producto: 'desc', id_tipo:'desc'});
					for(let i = 0; $scope.lastClienteSelected.tipo_precio.descuentos.length > i; i++){
                        let d = $scope.lastClienteSelected.tipo_precio.descuentos[i];
                        if(d.id_producto == p.id_producto){
                            console.log("TIENE DESC PROD");
                            return true;
                        } else if(d.id_tipo == p.id_tipo){
                            console.log("TIENE DESC CAT");
                            return true;
                        } else if(!parseInt(d.id_producto) && !parseInt(d.id_tipo)){
                            console.log("TIENE DESC GEN");
                            return true;
                        }
                    }
                    return false;
				} else {
                    let pv = parseFloat(p.precio_original);
                    let dpc = parseFloat(p.descuento_producto_cantidad);
                    let dpp = parseFloat(p.descuento_producto_porcentaje);
                    let dcc = parseFloat(p.descuento_categoria_cantidad);
                    let dcp = parseFloat(p.descuento_categoria_porcentaje);
                    let dgc = parseFloat(p.descuento_general_cantidad);
                    let dgp = parseFloat(p.descuento_general_porcentaje);
                    return !!(dpc || dpp || dcc || dcp || dgc || dgp);
                }
            }

            $scope.getDescuento = p => {

                if($scope.lastClienteSelected && $scope.lastClienteSelected.tipo_precio && $scope.lastClienteSelected.tipo_precio.descuentos && $scope.lastClienteSelected.tipo_precio.porcentaje_descuento){
                    $scope.lastClienteSelected.tipo_precio.descuentos = $scope.lastClienteSelected.tipo_precio.descuentos.keySort({id_producto: 'desc', id_tipo:'desc'});
					for(let i = 0; $scope.lastClienteSelected.tipo_precio.descuentos.length > i; i++) {
                        let d = $scope.lastClienteSelected.tipo_precio.descuentos[i];
                        if(d.id_producto == p.id_producto){
                            let cant = parseFloat(d.cantidad);
                            let porc = parseFloat(d.porcentaje_descuento);
                            return `-${$filter('number')(cant || porc, 2)}${(porc ? '%' : '')} adicional!`;
                        } else if(d.id_tipo == p.id_tipo){
                            let cant = parseFloat(d.cantidad);
                            let porc = parseFloat(d.porcentaje_descuento);
                            return `-${$filter('number')(cant || porc, 2)}${(porc ? '%' : '')} adicional!`;
                        } else if(!d.id_producto && !d.id_tipo){
                            let cant = parseFloat(d.cantidad);
                            let porc = parseFloat(d.porcentaje_descuento);
                            return `-${$filter('number')(cant || porc, 2)}${(porc ? '%' : '')} adicional!`;
                        }
                    }
                    return null;
				} else {
                    let pv = parseFloat(p.precio_original);
                    let dpc = parseFloat(p.descuento_producto_cantidad);
                    let dpp = parseFloat(p.descuento_producto_porcentaje);
                    let dcc = parseFloat(p.descuento_categoria_cantidad);
                    let dcp = parseFloat(p.descuento_categoria_porcentaje);
                    let dgc = parseFloat(p.descuento_general_cantidad);
                    let dgp = parseFloat(p.descuento_general_porcentaje);
                    if(dpc || dpp){
                        return `-${$filter('number')(dpc || dpp, 2)}${(dpp ? '%' : '')}`;
                    } else if(dcc || dcp){
                        return `-${$filter('number')(dcc || dcp, 2)}${(dcp ? '%' : '')}`;
                    } else if(dgc || dgp){
                        return `-${$filter('number')(dgc || dgp, 2)}${(dgp ? '%' : '')}`;
                    } else {
                        return null;
                    }
                }
            }


            $scope.agregarUno = function(prod, resetAfter) {
                if(!$scope.preventProductoSearch){
                    if($scope.lastClienteSelected.id_cliente){
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
                                agregar = $scope.applyDiscountProducto(agregar);
                                /*
                                if($scope.lastClienteSelected.tipo_precio && $scope.lastClienteSelected.tipo_precio.porcentaje_descuento){
                                    agregar.precio_venta = agregar.precio_original - parseFloat(agregar.precio_original * ($scope.lastClienteSelected.tipo_precio.porcentaje_descuento/100))  
                                    agregar.sub_total = parseFloat(agregar.precio_venta) * parseInt(prod.cantidad);
                                } else {
                                    
                                }
                                */
                                agregar.sub_total = parseFloat(agregar.precio_venta) * parseInt(agregar.cantidad);
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
                        if(!$scope.$$phase){
                            $scope.$apply();
                        }
                        if(resetAfter){
                            //$scope.productos = [];
                            $("#producto").select();
                        }
                    } else {
                        $("#clientesModal").modal();
                    }
                }
                
            };

            $scope.agregarVarios = function(prod) {
                if(!$scope.preventProductoChange){
                    if($scope.lastClienteSelected.id_cliente && prod.cant_vender){
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
                                agregar = $scope.applyDiscountProducto(agregar);
                                /*
                                if($scope.lastClienteSelected.tipo_precio && $scope.lastClienteSelected.tipo_precio.porcentaje_descuento){
                                    agregar.precio_venta = agregar.precio_original - parseFloat(agregar.precio_original * ($scope.lastClienteSelected.tipo_precio.porcentaje_descuento/100))  
                                    agregar.sub_total = parseFloat(agregar.precio_venta) * parseInt(prod.cantidad);
                                } else {
                                    agregar.sub_total = parseFloat(prod.precio_venta) * parseInt(prod.cantidad);
                                }
                                */
                                agregar.sub_total = parseFloat(agregar.precio_venta) * parseInt(prod.cantidad);
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
                        if(!$scope.$$phase){
                            $scope.$apply();
                        }
                    } else {
                        $("#clientesModal").modal();
                    }
                }
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
                var noneReq = ["factura_nit", "factura_nombre", "factura_direccion", "id_pais", "id_usuario", "tipo_cliente"];
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
                        $scope.forma_pago.cantidad_efectivo = 0;
                        $scope.forma_pago.monto = 0;
                        /*
                        console.log("MONTO: ", $scope.forma_pago.monto)
                        $scope.forma_pago.cantidad_efectivo = parseFloat($scope.forma_pago.monto * $scope.tipo_cambio_actual.factor).toFixed(2); 
                        if($scope.devolucion.credito > 0){
                            $scope.forma_pago.cantidad_efectivo = Math.max(0, $scope.forma_pago.cantidad_efectivo - $scope.devolucion.credito);
                            $scope.forma_pago.monto = Math.max(0, $scope.forma_pago.monto - $scope.devolucion.credito);
                        }
                        */
                    } 
                } else {
                    $scope.showAlert('alert-warning', 'No hay tipo de cambio configurado para esa moneda', 2500);
                }
            };

            $("#tipoPagoModal").on('shown.bs.modal', function(){
                $("#monto_efectivo").focus();
            })

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
                let facturarAlert = {
                    title:'Finalizar venta', 
                    text: (cambio || "") || "Guardando..."
                }
                if(!cambio){
                    facturarAlert.timer = 3000;
                }
                swal(facturarAlert);
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
        echo json_encode(array('data' => sanitize_array_by_keys($resultSet, ['nombres', 'apellidos'])));
    }

    public function getTipoPrecio(){
        $id = getParam("id");
        $tipoPrecio = Collection::get($this->db, "clientes_tipos_precio")->where(["id_tipo_precio" => $id])->select(["id_tipo_precio","nombre", "porcentaje_descuento"], true)->single();
        $tipoPrecio['descuentos'] = Collection::get($this->db, 'descuentos',sprintf("id_tipo_precio=%s and activo = 1", $tipoPrecio['id_tipo_precio']))->toArray();
        echo json_encode($tipoPrecio);
    }

    public function getVentas()
    {
        $bod = getParam("bod");
        $queryVentas = "      
            SELECT    v.id_venta, v.total, CONCAT(c.nombres,' ',c.apellidos) AS nombre_cliente, v.fecha_creacion, v.id_cliente, v.estado
            FROM	  trx_venta v
            JOIN       clientes c on c.id_cliente=v.id_cliente 
            WHERE     v.estado = 'P'
            AND v.id_venta IN (select id_venta FROM trx_venta_detalle where id_sucursal=%s)" ;

        $ventas = $this->db->queryToArray(sprintf($queryVentas, $bod));

        echo json_encode(array('data' => sanitize_array_by_keys($ventas, ['nombre_cliente'])));
    }

    public function getDetalleVenta(){
        $id_venta = getParam("id_venta");
        $queryProductos = " SELECT	vd.id_venta_detalle, p.id_producto, p.nombre, p.descripcion, p.precio_venta precio_original, p.imagen,p.codigo,
                                    p.codigo_origen, vd.cantidad, vd.cantidad cantidad_original, (vd.cantidad * vd.precio_venta) AS sub_total,vd.precio_venta,
                                    t.total_existencias AS total_existencias, 1 AS mostrar, vd.id_sucursal, s.nombre nombre_sucursal
                            FROM	trx_venta_detalle vd
                                    LEFT JOIN producto p
                                    ON p.id_producto = vd.id_producto
                                    LEFT JOIN reporte_inventario t
                                    ON t.id_producto = vd.id_producto
                                    AND t.id_sucursal = vd.id_sucursal
                                    LEFT JOIN sucursales s on s.id_sucursal=vd.id_sucursal
                            WHERE	vd.id_venta = " . $id_venta; 

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
        $productos = [];
        if(!isEmpty($key)){
            $queryProductos = " 
                SELECT 
                    prod.*,
                    ifnull(dp.cantidad, 0) descuento_producto_cantidad,
                    ifnull(dp.porcentaje_descuento, 0) descuento_producto_porcentaje,
                    ifnull(dt.cantidad, 0) descuento_categoria_cantidad,
                    ifnull(dt.porcentaje_descuento, 0) descuento_categoria_porcentaje,
                    ifnull(dg.cantidad, 0) descuento_general_cantidad,
                    ifnull(dg.porcentaje_descuento, 0) descuento_general_porcentaje
                FROM
                    (SELECT	p.id_producto, p.nombre, p.descripcion, p.precio_venta precio_original, p.precio_venta, p.imagen, p.id_tipo,
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
                    GROUP BY p.id_producto, trx.id_sucursal) prod 
                LEFT JOIN descuentos dp on dp.id_producto=prod.id_producto 
                    AND dp.activo = 1 
                    AND dp.id_tipo_precio is null 
                    AND dp.id_tipo is null
                LEFT JOIN descuentos dt on dt.id_tipo=prod.id_tipo 
                    AND dt.activo = 1 
                    AND dt.id_tipo_precio is null 
                    AND dt.id_producto is null
                LEFT JOIN descuentos dg on 
                    dg.activo = 1 
                    AND dg.id_producto is null 
                    AND dg.id_tipo is null 
                    AND dg.id_tipo_precio is null";
        //                            HAVING	(sum(trx.haber) - sum(trx.debe)) > 0";

                $productos = $this->db->queryToArray($queryProductos);

                if(count($productos) <= 50){
                    for($i = 0; count($productos) > $i; $i++){
                        $productos[$i]['cantidad'] = 0;
                        $productos[$i]['sub_total'] = 0;
                        $productos[$i]['cant_vender'] = 0;
                    }
                }
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
                'id_tipo_precio' => isset($data['tipo_cliente']) && !isEmpty($data['tipo_cliente']) ? sqlValue($data['tipo_cliente'], 'int') : 'NULL',
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

    public function anularVenta(){
        $id = getParam("id_venta");
        $fecha = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $dsEmpleado = decode_email_address($this->user['ID']);
        $dsCuentaVenta = Collection::get($this->db, 'cuentas', 'lower(nombre) = "venta"')->single();
        $dsCuentaReingreso = Collection::get($this->db, 'cuentas', 'lower(nombre) = "reingreso"')->single();
        $dsMoneda = Collection::get($this->db, 'monedas', 'moneda_defecto = 1')->single();
        $venta = $this->db->query_select("trx_venta", sprintf("id_venta=%s", $id));
        if(count($venta) > 0){
            $this->db->query("START TRANSACTION");
            try {
                $venta = $venta[0];
                $detalles = $this->db->query_select("trx_venta_detalle", sprintf("id_venta=%s", $id));
                foreach($detalles as $prod){
                    $transaccion = [
                        'id_cuenta' => sqlValue($dsCuentaVenta['id_cuenta'], 'int'),
                        'id_sucursal' => sqlValue($prod['id_sucursal'], 'int'),
                        'descripcion' => sqlValue('Anulación pedido', 'text'),
                        'id_producto' => sqlValue($prod['id_producto'], 'int'),
                        'haber' => sqlValue($prod['cantidad'], 'float'),
                        'debe' => sqlValue('0', 'float'),
                        'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date')
                    ];

                    $this->db->query_insert('trx_transacciones', $transaccion);
                }
                $ventaUpd = [
                    "estado" => sqlValue('A', 'text'),
                    'es_anulado' => 1
                ];
                $this->db->query_update("trx_venta", $ventaUpd, sprintf("id_venta=%s", $venta['id_venta']));
                $this->db->query("COMMIT");
                echo json_encode(['result' => 1]);
                
            }catch(Exception $e){
                $this->db->query("ROLLBACK");
                echo json_encode(['result' => 0]);
            }
        }
    }

    public function doSave($data)
    {
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

                if($data['id_venta'] > 0 && isset($prod['id_venta_detalle']) && !isEmpty($prod['id_venta_detalle'])) {
                    $venta_detalle = [
                        'cantidad' => sqlValue($prod['cantidad'], 'float'),
                        'precio_venta' => sqlValue($prod['precio_venta'], 'float'),
                        'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                        'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
                    ];

                    $this->db->query_update('trx_venta_detalle', $venta_detalle, sprintf('id_venta_detalle = %s', $prod['id_venta_detalle']));
                    if($prod['cantidad_original'] > $prod['cantidad']){
                        $transaccion = [
                            'id_cuenta' => sqlValue($dsCuentaVenta['id_cuenta'], 'int'),
                            'id_sucursal' => sqlValue($prod['id_sucursal'], 'int'),
                            'descripcion' => sqlValue('devolución exceso pedido', 'text'),
                            'id_producto' => sqlValue($prod['id_producto'], 'int'),
                            'haber' => sqlValue($prod['cantidad_original'] - $prod['cantidad'], 'float'),
                            'debe' => sqlValue('0', 'float'),
                            'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date')
                        ];
    
                        $this->db->query_insert('trx_transacciones', $transaccion);
                    }
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

                    $this->db->query_insert('trx_venta_detalle', $venta_detalle);
                    $this->db->query_insert('trx_transacciones', $transaccion);
                }
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
                    'descripcion' => sqlValue('Devolucion eliminacion pedido', 'text'),
                    'id_moneda' => sqlValue($dsMoneda['id_moneda'], 'int'),
                    'id_producto' => sqlValue($prod['id_producto'], 'int'),
                    'debe' => sqlValue('0', 'float'),
                    'haber' => sqlValue($prod['cantidad_original'], 'float'),
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                    'id_cliente' => sqlValue($data['id_cliente'], 'int')
                ];

                $this->db->query_insert('trx_transacciones', $transaccion);
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