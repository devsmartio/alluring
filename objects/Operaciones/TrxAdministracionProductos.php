<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 7/27/2018
 * Time: 8:09 AM
 */

class TrxAdministracionProductos  extends FastTransaction {
    protected $onlyEdit;
    protected $uploadPath;
    function __construct(){
        parent::__construct();
        $this->instanceName = 'TrxAdministracionProductos';
        $this->table = 'producto';
        $this->setTitle('Administracion de Productos');
        $this->hasCustomSave = true;

        $this->onlyEdit = false;
        $this->onlyNew = false;
        $this->uploadPath = PATH_UPLOAD_GENERAL . DS;

        $this->requiredFields = array(
            new FastField('Nombre', 'nombre', 'text', 'text'),
            new FastField('Descripción', 'descripcion', 'text', 'text'),
            new FastField('Categoria', 'id_tipo', 'text', 'text'),
            new FastField('Precio venta al público', 'precio_venta', 'text', 'text'),
            new FastField('Costo', 'costo', 'text', 'text'),
            new FastField('Imagen', 'imagen', 'text', 'text'),
            new FastField('Código Origen', 'codigo_origen', 'text', 'text'),
            new FastField('Código', 'codigo', 'text', 'text')
        );

        $this->gridCols = array(
            'Código producto' => 'codigo',
            'Código origen' => 'codigo_origen',
            'Nombre' => 'nombre',
            'Categoria' => 'nombre_tipo'
        );
    }

    protected function showModule() {
        include VIEWS . "/administracion_productos.phtml";
    }

    public function myJavascript()
    {
        parent::myJavascript();
        ?>
        <script>
            app.directive(
			"bnLazySrc",
			function( $window, $document ) {
 
 
				// I manage all the images that are currently being
				// monitored on the page for lazy loading.
				var lazyLoader = (function() {
 
					// I maintain a list of images that lazy-loading
					// and have yet to be rendered.
					var images = [];
 
					// I define the render timer for the lazy loading
					// images to that the DOM-querying (for offsets)
					// is chunked in groups.
					var renderTimer = null;
					var renderDelay = 100;
 
					// I cache the window element as a jQuery reference.
					var win = $( $window );
 
					// I cache the document document height so that
					// we can respond to changes in the height due to
					// dynamic content.
					var doc = $document;
					var documentHeight = doc.height();
					var documentTimer = null;
					var documentDelay = 2000;
 
					// I determine if the window dimension events
					// (ie. resize, scroll) are currenlty being
					// monitored for changes.
					var isWatchingWindow = false;
 
 
					// ---
					// PUBLIC METHODS.
					// ---
 
 
					// I start monitoring the given image for visibility
					// and then render it when necessary.
					function addImage( image ) {
 
						images.push( image );
 
						if ( ! renderTimer ) {
 
							startRenderTimer();
 
						}
 
						if ( ! isWatchingWindow ) {
 
							startWatchingWindow();
 
						}
 
					}
 
 
					// I remove the given image from the render queue.
					function removeImage( image ) {
 
						// Remove the given image from the render queue.
						for ( var i = 0 ; i < images.length ; i++ ) {
 
							if ( images[ i ] === image ) {
 
								images.splice( i, 1 );
								break;
 
							}
 
						}
 
						// If removing the given image has cleared the
						// render queue, then we can stop monitoring
						// the window and the image queue.
						if ( ! images.length ) {
 
							clearRenderTimer();
 
							stopWatchingWindow();
 
						}
 
					}
 
 
					// ---
					// PRIVATE METHODS.
					// ---
 
 
					// I check the document height to see if it's changed.
					function checkDocumentHeight() {
 
						// If the render time is currently active, then
						// don't bother getting the document height -
						// it won't actually do anything.
						if ( renderTimer ) {
 
							return;
 
						}
 
						var currentDocumentHeight = doc.height();
 
						// If the height has not changed, then ignore -
						// no more images could have come into view.
						if ( currentDocumentHeight === documentHeight ) {
 
							return;
 
						}
 
						// Cache the new document height.
						documentHeight = currentDocumentHeight;
 
						startRenderTimer();
 
					}
 
 
					// I check the lazy-load images that have yet to
					// be rendered.
					function checkImages() {
 
						// Log here so we can see how often this
						// gets called during page activity.
						console.log( "Checking for visible images..." );
 
						var visible = [];
						var hidden = [];
 
						// Determine the window dimensions.
						var windowHeight = win.height();
						var scrollTop = win.scrollTop();
 
						// Calculate the viewport offsets.
						var topFoldOffset = scrollTop;
						var bottomFoldOffset = ( topFoldOffset + windowHeight );
 
						// Query the DOM for layout and seperate the
						// images into two different categories: those
						// that are now in the viewport and those that
						// still remain hidden.
						for ( var i = 0 ; i < images.length ; i++ ) {
 
							var image = images[ i ];
 
							if ( image.isVisible( topFoldOffset, bottomFoldOffset ) ) {
 
								visible.push( image );
 
							} else {
 
								hidden.push( image );
 
							}
 
						}
 
						// Update the DOM with new image source values.
						for ( var i = 0 ; i < visible.length ; i++ ) {
 
							visible[ i ].render();
 
						}
 
						// Keep the still-hidden images as the new
						// image queue to be monitored.
						images = hidden;
 
						// Clear the render timer so that it can be set
						// again in response to window changes.
						clearRenderTimer();
 
						// If we've rendered all the images, then stop
						// monitoring the window for changes.
						if ( ! images.length ) {
 
							stopWatchingWindow();
 
						}
 
					}
 
 
					// I clear the render timer so that we can easily
					// check to see if the timer is running.
					function clearRenderTimer() {
 
						clearTimeout( renderTimer );
 
						renderTimer = null;
 
					}
 
 
					// I start the render time, allowing more images to
					// be added to the images queue before the render
					// action is executed.
					function startRenderTimer() {
 
						renderTimer = setTimeout( checkImages, renderDelay );
 
					}
 
 
					// I start watching the window for changes in dimension.
					function startWatchingWindow() {
 
						isWatchingWindow = true;
 
						// Listen for window changes.
						win.on( "resize.bnLazySrc", windowChanged );
						win.on( "scroll.bnLazySrc", windowChanged );
 
						// Set up a timer to watch for document-height changes.
						documentTimer = setInterval( checkDocumentHeight, documentDelay );
 
					}
 
 
					// I stop watching the window for changes in dimension.
					function stopWatchingWindow() {
 
						isWatchingWindow = false;
 
						// Stop watching for window changes.
						win.off( "resize.bnLazySrc" );
						win.off( "scroll.bnLazySrc" );
 
						// Stop watching for document changes.
						clearInterval( documentTimer );
 
					}
 
 
					// I start the render time if the window changes.
					function windowChanged() {
 
						if ( ! renderTimer ) {
 
							startRenderTimer();
 
						}
 
					}
 
 
					// Return the public API.
					return({
						addImage: addImage,
						removeImage: removeImage
					});
 
				})();
 
 
				// ------------------------------------------ //
				// ------------------------------------------ //
 
 
				// I represent a single lazy-load image.
				function LazyImage( element ) {
 
					// I am the interpolated LAZY SRC attribute of
					// the image as reported by AngularJS.
					var source = null;
 
					// I determine if the image has already been
					// rendered (ie, that it has been exposed to the
					// viewport and the source had been loaded).
					var isRendered = false;
 
					// I am the cached height of the element. We are
					// going to assume that the image doesn't change
					// height over time.
					var height = null;
 
 
					// ---
					// PUBLIC METHODS.
					// ---
 
 
					// I determine if the element is above the given
					// fold of the page.
					function isVisible( topFoldOffset, bottomFoldOffset ) {
 
						// If the element is not visible because it
						// is hidden, don't bother testing it.
						if ( ! element.is( ":visible" ) ) {
 
							return( false );
 
						}
 
						// If the height has not yet been calculated,
						// the cache it for the duration of the page.
						if ( height === null ) {
 
							height = element.height();
 
						}
 
						// Update the dimensions of the element.
						var top = element.offset().top;
						var bottom = ( top + height );
 
						// Return true if the element is:
						// 1. The top offset is in view.
						// 2. The bottom offset is in view.
						// 3. The element is overlapping the viewport.
						return(
								(
									( top <= bottomFoldOffset ) &&
									( top >= topFoldOffset )
								)
							||
								(
									( bottom <= bottomFoldOffset ) &&
									( bottom >= topFoldOffset )
								)
							||
								(
									( top <= topFoldOffset ) &&
									( bottom >= bottomFoldOffset )
								)
						);
 
					}
 
 
					// I move the cached source into the live source.
					function render() {
 
						isRendered = true;
 
						renderSource();
 
					}
 
 
					// I set the interpolated source value reported
					// by the directive / AngularJS.
					function setSource( newSource ) {
 
						source = newSource;
 
						if ( isRendered ) {
 
							renderSource();
 
						}
 
					}
 
 
					// ---
					// PRIVATE METHODS.
					// ---
 
 
					// I load the lazy source value into the actual
					// source value of the image element.
					function renderSource() {
 
						element[ 0 ].src = source;
 
					}
 
 
					// Return the public API.
					return({
						isVisible: isVisible,
						render: render,
						setSource: setSource
					});
 
				}
 
 
				// ------------------------------------------ //
				// ------------------------------------------ //
 
 
				// I bind the UI events to the scope.
				function link( $scope, element, attributes ) {
 
					var lazyImage = new LazyImage( element );
 
					// Start watching the image for changes in its
					// visibility.
					lazyLoader.addImage( lazyImage );
 
 
					// Since the lazy-src will likely need some sort
					// of string interpolation, we don't want to
					attributes.$observe(
						"bnLazySrc",
						function( newSource ) {
 
							lazyImage.setSource( newSource );
 
						}
					);
 
 
					// When the scope is destroyed, we need to remove
					// the image from the render queue.
					$scope.$on(
						"$destroy",
						function() {
 
							lazyLoader.removeImage( lazyImage );
 
						}
					);
 
				}
 
 
				// Return the directive configuration.
				return({
					link: link,
					restrict: "A"
				});
 
			}
		);
            app.controller('ModuleCtrl', function ($scope, $http, $rootScope, $timeout) {
                $scope.startAgain = function () {
                    $scope.goNoMode();
                    $scope.currentIndex = null;
                    $scope.lastSelected = {
                        id_producto: 0,
                        nombre: '',
                        descripcion: '',
                        id_tipo: 0,
                        precio_venta: 0,
                        costo: 0,
                        imagen: '',
                        codigo: ''
                    };
                    $scope.rand = Math.random();
                    angular.element("input[type='file']").val(null);

                    $http.get($scope.ajaxUrl + '&act=getRows').success(function (response) {
                        $scope.rows = response.data;
                        $scope.setRowSelected($scope.rows);
                        $scope.setRowIndex($scope.rows);
                    });
                    $http.get($scope.ajaxUrl + '&act=getGridCols').success(function (response) {
                        $scope.gridCols = response.data;
                    });
                    $http.get($scope.ajaxUrl + '&act=getCategorias').success(function (response) {
                        $scope.categorias = response.data;
                    });

                    $scope.inList = true;
                };
                $scope.goNew = function () {
                    $scope.lastSelected = new Array();
                    $scope.editMode = false;
                    $scope.newMode = true;
                    $scope.noMode = false;
                };
                $scope.goNoMode = function () {
                    $scope.editMode = false;
                    $scope.newMode = false;
                    $scope.noMode = true;
                };
                $scope.goEdit = function () {
                    $scope.editMode = true;
                    $scope.newMode = false;
                    $scope.noMode = false;
                };

                $scope.uploadFile = function(files) {
                    var uploadUrl = $scope.ajaxUrl + '&act=uploadImage';
                    var fd = new FormData();
                    fd.append('file', files[0]);
                    fd.append('name', files[0]['name']);
                    fd.append('old_name', (!$scope.lastSelected.codigo) ? '' : $scope.lastSelected.codigo.toString() + '.jpg');
                    fd.append('id_producto', $scope.lastSelected.id_producto || "");
                    

                    $http.post(uploadUrl, fd, {
                        transformRequest: angular.identity,
                        headers: {'Content-Type': undefined,'Process-Data': false}
                    })
                        .success(function(response){
                            console.log(response);
                            if(response.result == 1){
                                $scope.lastSelected.imagen = null;
                                $timeout(_ => {
                                    $scope.rand = Math.random();
                                    $scope.lastSelected.imagen = response.msg;
                                    $scope.lastSelected.codigo = response.msg.replace('.jpg','');
                                }, 2500)
                            } else {
                                $scope.alerts.push({
                                    type: 'alert-danger',
                                    msg: response.msg
                                })
                                angular.element("input[type='file']").val(null);
                                $timeout(_ => {
                                    $scope.alerts = [];
                                }, 2500)
                            }
                        })
                        .error(function(response){
                            $scope.alerts.push({
                                type: 'alert-danger',
                                msg: response.msg
                            });
                        });
                };

                $scope.finalizarEditado = function () {
                    $rootScope.modData = {
                        id_producto: $scope.lastSelected.id_producto,
                        nombre: $scope.lastSelected.nombre,
                        descripcion: $scope.lastSelected.descripcion,
                        costo: $scope.lastSelected.costo,
                        id_tipo: $scope.lastSelected.id_tipo,
                        precio_venta: $scope.lastSelected.precio_venta,
                        codigo: $scope.lastSelected.codigo,
                        imagen: $scope.lastSelected.imagen,
                        codigo_origen: !$scope.lastSelected.codigo_origen ? '' : $scope.lastSelected.codigo_origen,
                        mod: 2
                    };
                    console.log($rootScope.modData);
                    $scope.doSave();
                };

                $scope.finalizar = function () {
                    $rootScope.modData = {
                        id_producto: $scope.lastSelected.id_producto,
                        nombre: $scope.lastSelected.nombre,
                        descripcion: $scope.lastSelected.descripcion,
                        costo: $scope.lastSelected.costo,
                        id_tipo: $scope.lastSelected.id_tipo,
                        precio_venta: $scope.lastSelected.precio_venta,
                        imagen: $scope.lastSelected.imagen,
                        codigo: $scope.lastSelected.codigo,
                        codigo_origen: !$scope.lastSelected.codigo_origen ? '' : $scope.lastSelected.codigo_origen,
                        mod: 1
                    };
                    console.log($rootScope.modData);
                    $scope.doSave();
                };
                $scope.doDelete = function () {
                    if ($scope.editMode) {
                        if (confirm('¿Confirmas borrar este registro? Si el registro está en uso, la acción no se realizará.')) {
                            $rootScope.modData = {
                                id_producto: $scope.lastSelected.id_producto,
                                nombre: $scope.lastSelected.nombre,
                                descripcion: $scope.lastSelected.descripcion,
                                costo: $scope.lastSelected.costo,
                                id_tipo: $scope.lastSelected.id_tipo,
                                precio_venta: $scope.lastSelected.precio_venta,
                                imagen: $scope.lastSelected.imagen,
                                codigo_origen: $scope.lastSelected.codigo_origen,
                                mod: 3
                            };

                            $scope.doSave();
                        }
                    } else {
                        $scope.alerts.push({type: 'alert-warning', msg: 'Operación no permitida'});
                        $scope.startAgain();
                    }
                };

                $scope.cancelar = function () {
                    if($scope.lastSelected.imagen){
                        if($scope.editMode && confirm("¿Desea cancelar? Los cambios a imagenes no se revertiran")){
                            $scope.startAgain();
                        } else if($scope.newMode) {
                                $scope.alerts.push({type: "alert-info", msg: "Eliminando la imagen subida"});
                            $http.delete($scope.ajaxUrl + "&act=deleteImage&cod=" + $scope.lastSelected.codigo).success(r => {
                                $scope.alerts = [];
                                $scope.startAgain();
                            }).catch(err => {
                                $scope.alerts = [];
                                $scope.alerts.push({type: "alert-danger", msg: err});
                            });
                        } else {
                            $scope.startAgain();
                        }
                    }
                };

                $scope.selectRow = function(row){
                    $scope.lastSelected = row;
                    console.log(row);
                    $scope.currentIndex = row.index;
                    $scope.setRowSelected($scope.rows);
                    $scope.lastSelected.selected = true;
                    $scope.goEdit();
                };
                $scope.next = function(){
                    if($scope.currentIndex == ($scope.rows.length - 1)){
                        $scope.alerts.push({
                            type: 'alert-info',
                            msg: 'Ha llegado al último registro'
                        });
                    } else {
                        $scope.selectRow($scope.rows[parseInt($scope.currentIndex + 1)]);
                    }
                    $timeout(function(){
                        $scope.alerts = new Array();
                    }, 3000);
                };
                $scope.prev = function(){
                    if($scope.currentIndex == 0){
                        $scope.alerts.push({
                            type: 'alert-info',
                            msg: 'Ha llegado al primer registro'
                        });
                    } else {
                        $scope.selectRow($scope.rows[parseInt($scope.currentIndex - 1)]);
                    }
                    $timeout(function(){
                        $scope.alerts = new Array();
                    }, 2000);
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

                $scope.startAgain();
                $rootScope.addCallback(function () {
                    $scope.startAgain();
                });
            });
        </script>
    <?php
    }

    public function deleteImage(){
        $cod = getParam("cod");
        $r = 0;
        $mess = 0;
        if(isEmpty("cod")){
            http_response_code(400);
            return;
        }
        try {
            unlink($this->uploadPath . $cod . '.jpg');
            $r = 1;
            $mess = "Eliminado con éxito";
        } catch (Exception $e){
            $mess = DEBUG ? $e->getTraceAsString() : "Error inesperado al eliminar imagen";
            http_response_code(500);
        }
        echo json_encode(['r' => $r, 'mess' => $mess]);
    }

    public function getRows(){
        try {
            $resultSet = $this->db->query_select($this->table);
            foreach($this->fields as $f){
                $i = 0;
                while(count($resultSet) > $i){
                    if($f instanceof FastField){
                        if($f->valueType == 'text'){
                            $resultSet[$i][$f->name] = self_escape_string($resultSet[$i][$f->name]);
                        }
                    }
                    $i++;
                }
            }
            $resultSet = $this->specialProcessBeforeShow($resultSet);
        } catch(Exception $e){
            error_log($e->getTraceAsString());
        }
        echo json_encode(array('data' => sanitize_array_by_keys($resultSet, ['descripcion'])));
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

    public function specialProcessBeforeShow($resultSet){
        $categorias = Collection::get($this->db, 'tipo');

        for($i = 0; count($resultSet) > $i; $i++){
            $cat = $categorias->where(['id_tipo' => $resultSet[$i]['id_tipo']])->single();
            $resultSet[$i]['nombre_tipo'] = $cat['nombre'];
        }
        return sanitize_array_by_keys($resultSet, array('nombre_tipo'));
    }

    public function getCategorias(){
        $resultSet = array();

        $dsCategorias = $this->db->query_select('tipo');
        $resultSet[] = array('id_tipo' =>'', 'nombre' => '-- Seleccione uno --');
        foreach($dsCategorias as $p){
            $resultSet[] = array('id_tipo' => $p['id_tipo'], 'nombre' => $p['nombre']);
        }

        echo json_encode(array('data' => $resultSet));
    }

    public function dataIsValid($data)
    {
        foreach($this->requiredFields as $f) {
            if(!array_key_exists($f->name, $data)) {
                $this->r = 0;
                $this->msg = sprintf('El campo %s es requerido ', $f->label);
                break;
            }
        }

//        if (array_key_exists('correo', $data) && $this->r != 0) {
//            $correo = str_replace("'", "", $data['correo']);
//            if (!preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/', $correo, $matches)) {
//                $this->r = 0;
//                $this->msg = 'Correo inválido, favor de revisar e intentar de nuevo';
//            }
//        }
//        if (array_key_exists('tiene_credito', $data) && $this->r != 0) {
//            if ($data['tiene_credito'] && ($data['dias_credito'] == 0 || $data['dias_credito'] == '')) {
//                $this->r = 0;
//                $this->msg = 'Al momento de indicar que posee credito, debe indicar cuantos dias de credito posee el cliente';
//            }
//        }
//        if (array_key_exists('factura_nit', $data) && $this->r != 0 && (!array_key_exists('factura_nombre', $data) || !array_key_exists('factura_direccion', $data))) {
//            $this->r = 0;
//            $this->msg = 'Al momento de ingresar el Nit, debe indicar el nombre y direccion a facturar';
//
////            if (trim($data['factura_nit']) != '' && (trim($data['factura_nombre']) == '' || trim($data['factura_direccion']) == '')) {
////                $this->r = 0;
////                $this->msg = 'Al momento de ingresar el Nit, debe indicar el nombre y direccion a facturar';
////            }
//        }
//
//        if ($data['mod'] == 1 || $data['mod'] == 2 ) {
//            if (array_key_exists('catalogo_usuario', $data) && $this->r != 0) {
//                if (trim($data['catalogo_usuario']) != '') {
//
//                    $id = str_replace("'", "", trim($data['catalogo_usuario']));
//                    $id = sqlValue(encode_email_address($id), 'text');
//
//                    $result = $this->db->queryToArray(sprintf('select FIRST_NAME from app_user where ID=%s', $id));
//                    if (count($result) > 0) {
//                        $this->r = 0;
//                        $this->msg = 'El usuario ya existe, favor de corregir y volver a intentar';
//                    }
//                }
//            }
//        }
//
//        if ($data['mod'] == 1) {
//            if (array_key_exists('identificacion', $data) && $this->r != 0) {
//                if (trim($data['identificacion']) != '') {
//
//                    $identificacion = str_replace("'", "", trim($data['identificacion']));
//                    $idPais = str_replace("'", "", trim($data['id_pais']));
//
//                    $result = $this->db->queryToArray(sprintf("SELECT id_cliente FROM clientes WHERE identificacion = %s and id_pais = %s;", $identificacion, $idPais));
//                    if (count($result) > 0) {
//                        $this->r = 0;
//                        $this->msg = 'La identificacion ya existe, favor de corregir y volver a intentar';
//                    }
//                }
//            }
//        }

        if ($this->r == 0) {
            return false;
        }

        return true;
    }

    public function uploadImage()
    {
        try {
            $parts = pathinfo($_FILES["file"]["name"]);
            if(!isset($parts["extension"]) || strtolower($parts["extension"]) != "jpg"){
                echo json_encode(["r" => 0, "msg" => "La extension del archivo debe ser jpg"]);
            } else {
                if ($_POST['old_name'] == '') {
                    $ultimoCodigo = $this->db->queryToArray('  SELECT	COALESCE(MAX(CAST(REPLACE(codigo,"Y","") AS UNSIGNED)),1) AS ultimo_codigo
                                                               FROM	    producto');
    
                    $nombre_archivo = 'Y' . ($ultimoCodigo[0]['ultimo_codigo'] + 1) . '.jpg';
                } else {
                    $nombre_archivo = $_POST['old_name'];
                }
    
    //            $target_file = $this->uploadPath . basename($_FILES["file"]["name"]);
                $target_file = $this->uploadPath . basename($nombre_archivo);
                move_uploaded_file($_FILES["file"]["tmp_name"], $target_file . '.temp');
                $result = $this->resize_image($target_file . '.temp', 370, 277, $target_file);
                unlink($target_file . '.temp');

                if($result) {
                    if(isset($_POST["id_producto"]) && !isEmpty($_POST["id_producto"])){
                        $update = [
                            "imagen" => sqlValue($nombre_archivo, 'text')
                        ];
                        $this->db->query_update("producto", $update, sprintf("id_producto=%s", $_POST["id_producto"]));
                    }
                    echo json_encode(array('result' => 1, 'msg' => $nombre_archivo));
                } else {
                    echo json_encode(array('result' => 0, 'msg' => 'Ocurrio un error al momento de convertir la imagen'));
                }
            }
        } catch (Exception $e) {
            $this->r = 0;
            if (DEBUG) {
                $this->msg = var_dump($e->getMessage());
            } else {
                error_log($e->getMessage());
                $this->msg = 'Error al subir la imagen. Intente de nuevo';
            }

            echo json_encode(array('result' => $this->r, 'msg' => $this->msg));
        }
    }

    public function resize_image($filename, $thumb_width, $thumb_height, $newFile)
    {
        if (($img_info = getimagesize($filename)) === FALSE) {
            die("Image not found or not an image");
        }

        switch ($img_info[2]) {
            case IMAGETYPE_GIF  :
                $image = imagecreatefromgif($filename);
                break;
            case IMAGETYPE_JPEG :
                $image = imagecreatefromjpeg($filename);
                break;
            case IMAGETYPE_PNG  :
                $image = imagecreatefrompng($filename);
                break;
            default :
                die("Unknown filetype");
        }

        $owidth = $img_info[0];
        $oheight = $img_info[1];

        if (($owidth / $thumb_width) > ($oheight / $thumb_height)) {
            $y = 0;
            $x = $owidth - (($oheight * $thumb_width) / $thumb_height);
        } else {
            $x = 0;
            $y = $oheight - (($owidth * $thumb_height) / $thumb_width);
        }

        $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
        imagecopyresampled($thumb, $image, 0, 0, $x / 2, $y / 2, $thumb_width, $thumb_height, $owidth - $x, $oheight - $y);

        switch ( $img_info[2] ) {
            case IMAGETYPE_GIF  :
                imagegif( $thumb,  $newFile );
                break;
            case IMAGETYPE_JPEG :
                imagejpeg( $thumb, $newFile, 100 );
                break;
            case IMAGETYPE_PNG  :
                imagepng($thumb,  $newFile, 0 );
                break;
            default :
                die("Unknown filetype");
        }

        return true;
    }

    public function doSave($data)
    {
        $fecha = new DateTime();
        $user = AppSecurity::$UserData['data'];

        $producto = [
            'nombre' => sqlValue($data['nombre'], 'text'),
            'descripcion' => sqlValue($data['descripcion'], 'text'),
            'costo' => sqlValue($data['costo'], 'float'),
            'id_tipo' => sqlValue($data['id_tipo'], 'int'),
            'precio_venta' => sqlValue($data['precio_venta'], 'float'),
            'codigo_origen' => sqlValue(isEmpty($data['codigo_origen']) ? null : $data['codigo_origen'], 'text'),
            'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
            'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
        ];
        if ($data['mod'] == 1) {
            if(isEmpty($data['codigo'])){
                $ultimoCodigo = $this->db->queryToArray('  SELECT	COALESCE(MAX(CAST(REPLACE(codigo_origen,"Y","") AS UNSIGNED)),1) AS ultimo_codigo FROM producto');
                $nombre_archivo = 'Y' . ($ultimoCodigo[0]['ultimo_codigo'] + 1);
                $producto['codigo'] = $nombre_archivo;
            }
            $producto['codigo'] = sqlValue($data['codigo'], 'text');
            $producto['imagen'] = sqlValue($data['imagen'], 'text');
            $this->db->query_insert('producto', $producto);

            $this->msg = 'Producto ingresado con éxito';
        } else if ($data['mod'] == 2) {
            $this->db->query_update('producto', $producto, sprintf('id_producto = %s', $data['id_producto']));
            $this->msg = 'Producto actualizado con éxito';
        } else if ($data['mod'] == 3) {

            unlink($producto['imagen']);

            $this->db->query_delete('producto', sprintf('id_producto = %s', $data['id_producto']));

            $this->msg = 'Producto eliminado con éxito';
        }

        $this->r = 1;
    }
}