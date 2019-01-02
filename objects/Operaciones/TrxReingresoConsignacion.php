<?php
/**
 * Created by PhpStorm.
 * User: bryan.cruz
 * Date: 8/25/2018
 * Time: 9:18 PM
 */

class TrxReingresoConsignacion extends FastTransaction {
    function __construct(){
        parent::__construct();
        $this->instanceName = 'TrxReingresoConsignacion';
        $this->setTitle('Reingreso Consignacion');
        $this->hasCustomSave = true;
        $this->showSideBar = false;

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
                        total += parseFloat(this[i][prop]) || 0
                    }
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
                $scope.productos = [];
                $scope.selectedConsignacion = null;
                $scope.productos_facturar = [];
                $scope.total = $scope.productos_facturar.sum("subtotal");
            };

            $scope.resetConsignacion = function(){
                $scope.productos_facturar = [];
                let c = {...$scope.selectedConsignacion};
                $scope.selectedConsignacion = null;
                $scope.preventProductoChange = false;
                $scope.show_detalle = false;
                if(c){
                    $scope.selectConsignacion(c);
                }
            }

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
                $scope.filtered =  [];
                $scope.totalDevuelto = 0;


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
                $scope.selectedConsignacion = null;
                $scope.totalConsignacion = 0;
                $scope.maxDevolucion = 0;
                $scope.totalDevuelto = 0;
                $scope.resetCliente();
                $('#loading').hide();

                $http.get($scope.ajaxUrl + '&act=getClientes').success(function (response) {
                    $scope.clientes = response.data;
                    $scope.setClienteRowSelected($scope.rows);
                    $scope.setClienteRowIndex($scope.rows);
                    $("#clientesModal").modal();
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

            

            

            $scope.getVendedor = function(){
                $http.get($scope.ajaxUrl + "&act=getVendedor").success(response => {
                    $scope.vendedor = response;
                })
            }

            //Solo ejecutamos una vez
            $scope.getVendedor();

            

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

            $scope.getConsignacionState = c => {
                return new Date(new Date().setDate(new Date(c.fecha_recepcion).getDate() + parseInt(c.dias_consignacion))) > new Date()
            }

            $scope.getFechaMaximaEntrega = c => {
                return new Date(new Date().setDate(new Date(c.fecha_recepcion).getDate() + parseInt(c.dias_consignacion))).toLocaleDateString();
            }

            $scope.selectClienteRow = function(row){
                $scope.lastClienteSelected = row;
                $scope.currentClienteIndex = row.index;
                $scope.setClienteRowSelected($scope.clientes);
                $scope.lastClienteSelected.selected = true;
                $scope.cliente = $scope.lastClienteSelected.nombres + " " + $scope.lastClienteSelected.apellidos;
                console.log($scope.lastClienteSelected);
                $scope.consignacionesPendientes = [];
                $http.get($scope.ajaxUrl + "&act=getTipoPrecio&id=" + $scope.lastClienteSelected.id_tipo_precio).success(r => {
                    $scope.lastClienteSelected.tipo_precio = r;
                    
                    $http.get($scope.ajaxUrl + "&act=getConsignacionesPendientes&id=" + $scope.lastClienteSelected.id_cliente).success(r => {
                        $scope.consignacionesPendientes = r;
                    })
                    /*
                    if(r.porcentaje_descuento){
                        for(let i = 0; $scope.productos_facturar.length > i; i++){
                            $scope.productos_facturar[i] = $scope.applyDiscountProducto($scope.productos_facturar[i]);
                            $scope.productos_facturar[i].sub_total = $scope.productos_facturar[i].precio_venta * $scope.productos_facturar[i].cantidad;
                        }
                    }
                    $scope.total = $scope.productos_facturar.sum("sub_total");
                    */
                })
                //$('#clientesModal').modal('hide');
            };

            $scope.selectConsignacion = c => {
                $scope.selectedConsignacion = c;
                let total = 0;
                $scope.selectedConsignacion.productos.forEach(p => {
                    p = $scope.applyDiscountProducto(p);
                    p.devuelto = 0;
                    total+=parseFloat(p.precio_venta * p.unidades);
                })
                console.log($scope.selectedConsignacion.productos);
                $scope.totalConsignacion = total;
                $scope.totalDevuelto = 0;
                $scope.productos_facturar = [];
                $scope.maxDevolucion = total * (1 - (parseFloat(c.porcentaje_compra)/100));
                $scope.productos = c.productos;
                $("#clientesModal").hide();
            }

            $scope.mostrar_detalle = function(){
                $scope.show_detalle = true;
            };

            /*
            $scope.imprimir_detalle = function(){
                var id_venta = $scope.lastVentaSelected.id_venta;
                window.open("./?action=pdf&tmp=PD&id_venta=" + id_venta);
            };
            */

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

            $scope.$watch('search_codigo_origen', function(val){
                var search = val.toLowerCase();
                if (val.length >= 2 && $scope.bodegaSel) {
                    //$scope.productos = [];
                    /*
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
                            //$scope.productos.length == 1 && $scope.agregarUno($scope.productos[0], true);
                        }
                    });
                    */
                    //TODO PISTOLEO
                }
            });

            $("#producto").keyup(function(ev) {
                // 13 is ENTER
                if (ev.which === 13 && $scope.filtered.length == 1) {
                    let toAdd = $scope.filtered[0];
                    toAdd.devuelto = (toAdd.devuelto || 0);
                    console.log(toAdd);
                    if(toAdd.devuelto < toAdd.unidades){
                        let nuevoTotalDevuelto = $scope.totalDevuelto + parseFloat(toAdd.precio_venta);
                        if(nuevoTotalDevuelto <= $scope.maxDevolucion){
                            $scope.totalDevuelto = nuevoTotalDevuelto;
                            toAdd.devuelto += 1;
                            $scope.$apply();
                        } else {
                            swal("Máximo alcanzado", `Ha alcanzado el máximo para devolver`, "warning");    
                        }
                    } else {
                        swal("Producto devuelto", `Ha alcanzado el máximo de ${toAdd.codigo} para devolver`, "warning");
                        return false;
                    }
                    $(this).select();
                }
            });

            $scope.generarVenta = () => {
                if($scope.selectedConsignacion){
                    console.log("GENERANDO VENTA");
                    $scope.total = 0;
                    $scope.selectedConsignacion.productos.forEach(p => {
                        let cantidad_vender = p.unidades - p.devuelto;
                        if(cantidad_vender){
                            let toAdd = {...p, cantidad: cantidad_vender, sub_total: p.precio_venta * cantidad_vender};
                            $scope.productos_facturar.push(toAdd);
                            $scope.total+=toAdd.sub_total;
                        }
                    })
                    console.log("MOSTRANDO");
                    $scope.preventProductChange = true;
                    $scope.show_detalle = true;
                }
            }

            $scope.applyDiscountProducto = p => {

                if($scope.lastClienteSelected && $scope.lastClienteSelected.tipo_precio && $scope.lastClienteSelected.tipo_precio.descuentos && $scope.lastClienteSelected.tipo_precio.porcentaje_descuento){
                    $scope.lastClienteSelected.tipo_precio.descuentos = $scope.lastClienteSelected.tipo_precio.descuentos.keySort({id_producto: 'desc', id_tipo:'desc'});
					let pv = p.precio_original;
                    let des = $scope.lastClienteSelected.tipo_precio.porcentaje_descuento;
                    p.precio_venta = pv - (pv * (des / 100));
                    /* 
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
                    */
				} else {
                    /*
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
                        */
                }
                return p;
            }

            $scope.hasDescuento = p => {
                /*
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
                    */
                    return false;
                    /*
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
                */
            }

            $scope.getDescuento = p => {
                /*
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
                    */
                    return null;
                    /*
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
                */
            }


            $scope.agregarUno = function(prod, resetAfter) {
                if(!$scope.preventProductoSearch){
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
                    if(resetAfter){
                        //$scope.productos = [];
                        $("#producto").select();
                    }
                }
                
            };

            $scope.agregarVarios = function(prod) {
                if(!$scope.preventProductoChange){
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

            

            $scope.generar = function() {
                $productos_calc = $filter('filter')($scope.productos_facturar);
                //$scope.total = $productos_calc.sum("sub_total");
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
                    consignacion: $scope.selectedConsignacion
                };
                swal({
                    title:'Generando venta', 
                    text: (cambio || "") || "Guardando...", 
                    timer: 3000
                });
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

        $dsClientes = $this->db->query_select('clientes', sprintf("id_usuario='%s'", $this->user['ID']));

        

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

    

    public function getConsignacionesPendientes(){
        $date = new DateTime();
        $cl = getParam("id");
        $sql = "
            SELECT ms.*,
            ms.porcetaje_compra_min porcentaje_compra,
            so.nombre sucursal_origen, 
            ifnull(sd.nombre,'') sucursal_destino
            FROM trx_movimiento_sucursales ms
            JOIN sucursales so on so.id_sucursal=ms.id_sucursal_origen
            LEFT JOIN sucursales sd on ms.id_sucursal_destino=sd.id_sucursal
            WHERE id_cliente_recibe=%s 
            AND es_devuelto = 0
        ";
        $ventas = sanitize_array_by_keys($this->db->queryToArray(sprintf($sql,$cl)),['sucursal_origen','sucursal_destino']);
        for($i = 0; count($ventas) > $i; $i++){
            $detalleSql = "
                SELECT 
                    msd.id_producto,
                    msd.unidades,
                    p.descripcion,
                    p.codigo,
                    p.imagen,
                    p.precio_venta precio_original,
                    p.precio_venta
                FROM trx_movimiento_sucursales_detalle msd
                JOIN producto p on p.id_producto=msd.id_producto
                WHERE id_movimiento_sucursales=%s
            ";
            $detalles = sanitize_array_by_keys($this->db->queryToArray(sprintf($detalleSql, $ventas[$i]['id_movimiento_sucursales'])), ['descripcion', 'codigo']);
            $ventas[$i]['productos'] = $detalles;
        }
        echo json_encode($ventas);
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
        //print_r($data);
        
        $fecha = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $dsCuentaVenta = Collection::get($this->db, 'cuentas', 'lower(nombre) = "venta"')->single();
        $dsCuentaReingreso = Collection::get($this->db, 'cuentas', 'lower(nombre) = "reingreso"')->single();
        $dsCuentaInventario = Collection::get($this->db, 'cuentas', 'lower(nombre) = "inventario"')->single();
        $dsMoneda = Collection::get($this->db, 'monedas', 'moneda_defecto = 1')->single();

        $venta = [
            'total' => sqlValue($data['forma_pago']['cantidad'], 'float'),
            'id_cliente' => sqlValue($data['id_cliente'], 'int'),
            'usuario_venta' => sqlValue($this->user['FIRST_NAME'] . " " . $this->user['LAST_NAME'], 'int'),
            'estado' => sqlValue('VC', 'text'),
            'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
            'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
        ];

        $this->db->query_insert('trx_venta', $venta);

        $id_venta = $this->db->max_id('trx_venta', 'id_venta');
        $devoluciones = [];
        foreach ($data['productos'] as $prod) {
            $devoluciones[] = $prod['id_producto'];
            $venta_detalle = [
                'id_venta' => sqlValue($id_venta, 'int'),
                'id_producto' => sqlValue($prod['id_producto'], 'int'),
                'id_sucursal' => sqlValue($data['consignacion']['id_sucursal_origen'], 'int'),
                'cantidad' => sqlValue($prod['cantidad'], 'float'),
                'precio_venta' => sqlValue($prod['precio_venta'], 'float'),
                'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
            ];

            $this->db->query_insert('trx_venta_detalle', $venta_detalle);
            if(isset($data['consignacion']['id_sucursal_destino']) && !isEmpty($data['consignacion']['id_sucursal_destino'])){
                $transaccion = [
                    'id_cuenta' => sqlValue($dsCuentaInventario['id_cuenta'], 'int'),
                    'id_sucursal' => sqlValue($data['consignacion']['id_sucursal_destino'], 'int'),
                    'descripcion' => sqlValue('Reingreso por consignacion', 'text'),
                    'id_producto' => sqlValue($prod['id_producto'], 'int'),
                    'debe' => sqlValue($prod['unidades'], 'float'),
                    'haber' => 0,
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                    'id_cliente' => sqlValue(0, 'text')
                ];
    
                $this->db->query_insert('trx_transacciones', $transaccion);
            }

            if($prod['devuelto'] > 0){
                $transaccion = [
                    'id_cuenta' => sqlValue($dsCuentaInventario['id_cuenta'], 'int'),
                    'id_sucursal' => sqlValue($data['consignacion']['id_sucursal_origen'], 'int'),
                    'descripcion' => sqlValue('Reingreso por consignacion', 'text'),
                    'id_producto' => sqlValue($prod['id_producto'], 'int'),
                    'debe' => sqlValue('0', 'float'),
                    'haber' => sqlValue($prod['devuelto'], 'float'),
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                    'id_cliente' => sqlValue(0, 'text')
                ];
    
                $this->db->query_insert('trx_transacciones', $transaccion);
    
           }
        }

        //PRODUCTOS DEVUELTOS EN SU TOTALIDAD
        foreach($data['consignacion']['productos'] as $prod){
            if($prod['devuelto'] == $prod['unidades']){
                if(isset($data['consignacion']['id_sucursal_destino']) && !isEmpty($data['consignacion']['id_sucursal_destino'])){
                    $transaccion = [
                        'id_cuenta' => sqlValue($dsCuentaInventario['id_cuenta'], 'int'),
                        'id_sucursal' => sqlValue($data['consignacion']['id_sucursal_destino'], 'int'),
                        'descripcion' => sqlValue('Reingreso por consignacion', 'text'),
                        'id_producto' => sqlValue($prod['id_producto'], 'int'),
                        'debe' => sqlValue($prod['unidades'], 'float'),
                        'haber' => sqlValue('0', 'float'),
                        'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                        'id_cliente' => sqlValue(0, 'text')
                    ];
        
                    $this->db->query_insert('trx_transacciones', $transaccion);
                }
    
                if($prod['devuelto'] > 0){
                    $transaccion = [
                        'id_cuenta' => sqlValue($dsCuentaInventario['id_cuenta'], 'int'),
                        'id_sucursal' => sqlValue($data['consignacion']['id_sucursal_origen'], 'int'),
                        'descripcion' => sqlValue('Reingreso por consignacion', 'text'),
                        'id_producto' => sqlValue($prod['id_producto'], 'int'),
                        'debe' => sqlValue('0', 'float'),
                        'haber' => sqlValue($prod['devuelto'], 'float'),
                        'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                        'id_cliente' => sqlValue(0, 'text')
                    ];
        
                    $this->db->query_insert('trx_transacciones', $transaccion);
        
               }    
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
        $upd = [
            "es_devuelto" => 1
        ];
        $this->db->query_update('trx_movimiento_sucursales', $upd,sprintf("id_movimiento_sucursales=%s", $data['consignacion']['id_movimiento_sucursales']));
        $this->r = 1;
        $this->msg = 'Reingreso realizado  con éxito';
        $this->returnData = array('id_venta' => $id_venta);
        
    }
}