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
            new FastField('Imagen', 'imagen', 'text', 'text')
        );

        $this->gridCols = array(
            'Código producto' => 'id_producto',
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
                        imagen: ''
                    };

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
                    fd.append('old_name', ($scope.lastSelected.imagen == undefined) ? '' : $scope.lastSelected.imagen);

                    $http.post(uploadUrl, fd, {
                        transformRequest: angular.identity,
                        headers: {'Content-Type': undefined,'Process-Data': false}
                    })
                        .success(function(response){
                            console.log("Success");
                            $scope.lastSelected.imagen = response.msg;
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
                        imagen: $scope.lastSelected.imagen,
                        codigo_origen: ($scope.lastSelected.codigo_origen == undefined) ? ' ' : $scope.lastSelected.codigo_origen,
                        mod: 2
                    };

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
                        codigo_origen: ($scope.lastSelected.codigo_origen == undefined) ? ' ' : $scope.lastSelected.codigo_origen,
                        mod: 1
                    };

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
                    $scope.cancel();
                };

                $scope.selectRow = function(row){
                    $scope.lastSelected = row;
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
        echo json_encode(array('data' => $resultSet));
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

            if ($_POST['old_name'] == '') {
                $id_producto = $this->db->max_id('producto', 'id_producto');

                if (!$id_producto)
                    $id_producto = 1;
                else {
                    $id_producto += 1;
                }

                $nombre_archivo = 'Y' . $id_producto . '.jpg';
            } else
                $nombre_archivo = $_POST['old_name'];

//            $target_file = $this->uploadPath . basename($_FILES["file"]["name"]);
            $target_file = $this->uploadPath . basename($nombre_archivo);

            move_uploaded_file($_FILES["file"]["tmp_name"], $target_file . '.temp');


            $result = $this->resize_image($target_file . '.temp', 370, 277, $target_file);
            unlink($target_file . '.temp');

            if($result)
                echo json_encode(array('result' => 1, 'msg' => $nombre_archivo));
            else
                echo json_encode(array('result' => 0, 'msg' => 'Ocurrio un error al momento de convertir la imagen'));
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
            'imagen' => sqlValue($data['imagen'], 'text'),
            'codigo_origen' => sqlValue($data['codigo_origen'], 'text'),
            'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
            'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
        ];

        if ($data['mod'] == 1) {
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