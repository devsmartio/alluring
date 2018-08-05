<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 7/29/2018
 * Time: 12:07 PM
 */

class TrxCargaMasivaProductos extends FastTransaction {
    protected $onlyEdit;
    protected $uploadPath;
    function __construct(){
        parent::__construct();
        $this->instanceName = 'TrxCargaMasivaProductos';
        $this->table = 'producto';
        $this->setTitle('Carga Masiva de Productos');
        $this->hasCustomSave = true;

        $this->onlyEdit = false;
        $this->onlyNew = false;
        $this->uploadPath = PATH_UPLOAD_GENERAL . DS;

        $this->fields = array(
            new FastField('Nombre', 'nombre', 'text', 'text'),
            new FastField('Descripción', 'descripcion', 'text', 'text'),
            new FastField('Categoria', 'id_tipo', 'text', 'text'),
            new FastField('Precio venta al público', 'precio_venta', 'text', 'text'),
            new FastField('Costo', 'costo', 'text', 'text'),
            new FastField('Imagen', 'imagen', 'text', 'text'),
            new FastField('Codigo', 'codigo_origen', 'text', 'text'),
            new FastField('Catidad', 'cantidad', 'text', 'text')
        );

        $this->gridCols = array(
            'Nombre' => 'nombre',
            'Descripción' => 'descripcion',
            'Categoria' => 'nombre_tipo',
            'Precio venta al público' =>'precio_venta',
            'Costo' => 'costo',
            'Imagen' => 'imagen',
            'Codigo' => 'codigo_origen'
        );
    }

    protected function showModule() {
        include VIEWS . "/carga_productos.phtml";
    }

    public function myJavascript()
    {
        parent::myJavascript();
        ?>
        <script>
            app.controller('ModuleCtrl', function ($scope, $http, $rootScope) {
                $scope.startAgain = function () {
                    $scope.rows = [];
                    $scope.disableBtn = true;
                    $scope.lastSelected = {
                        file: ''
                    };
                    angular.element("input[type='file']").val(null);
                };
                $http.get($scope.ajaxUrl + '&act=getGridCols').success(function (response) {
                    $scope.gridCols = response.data;
                });

                $scope.uploadFile = function (files) {
                    var uploadUrl = $scope.ajaxUrl + '&act=uploadExcel';
                    var fd = new FormData();
                    fd.append('file', files[0]);
                    fd.append('name', files[0]['name']);
                    fd.append('old_name', ($scope.lastSelected.imagen == undefined) ? '' : $scope.lastSelected.imagen);

                    $http.post(uploadUrl, fd, {
                        transformRequest: angular.identity,
                        headers: {'Content-Type': undefined, 'Process-Data': false}
                    })
                        .success(function (response) {
                            console.log("Success");
                            $scope.lastSelected.file = files[0]['name'];
                            $scope.rows = response.data;
                            $scope.lastSelected.identificador_excel = response.identificador_excel;

                            if (response.identificador_excel == undefined) {
                                $scope.disableBtn = true;
                                $scope.alerts.push({
                                    type: 'alert-danger',
                                    msg: 'No se encontró el identificador de bodega en el archivo de excel. Favor de revisar e intentar de nuevo'
                                });
                            } else {
                                $scope.disableBtn = false;
                            }
                        })
                        .error(function (response) {
                            $scope.alerts.push({
                                type: 'alert-danger',
                                msg: response.msg
                            });
                        });
                };

                $scope.finalizar = function () {

                    var productos = JSON.stringify($scope.rows);
                    productos = productos.replace(/\\/g, "\\\\");

                    $rootScope.modData = {
                        file: $scope.lastSelected.file,
                        productos: JSON.parse(productos),
                        identificador_excel: $scope.lastSelected.identificador_excel,
                        mod: 1
                    };

                    $scope.doSave();
                };

                $scope.cancelar = function () {
                    $scope.cancel();
                };

                $scope.startAgain();
                $rootScope.addCallback(function () {
                    $scope.startAgain();
                });
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

    public function specialProcessBeforeShow($resultSet){
        $categorias = Collection::get($this->db, 'tipo');

        for($i = 0; count($resultSet) > $i; $i++){
            $cat = $categorias->where(['id_tipo' => $resultSet[$i]['id_tipo']])->single();
            if($cat)
                $resultSet[$i]['nombre_tipo'] = $cat['nombre'];
            else
                $resultSet[$i]['nombre_tipo'] = 'Favor Revisar el codigo de la Categoria';
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
//        foreach($this->requiredFields as $f) {
//            if(!array_key_exists($f->name, $data)) {
//                $this->r = 0;
//                $this->msg = sprintf('El campo %s es requerido ', $f->label);
//                break;
//            }
//        }

        if ($this->r == 0) {
            return false;
        }

        return true;
    }

    public function uploadExcel()
    {
        try {

            $target_file = $this->uploadPath . basename($_FILES["file"]["name"]);

            move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);

            $inputFileName = $target_file;

            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }

            //  Get worksheet dimensions
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $resultSet = [];

            $encabezados = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);
            $identificador_excel = $encabezados[0][count($this->fields)-1];

            //  Loop through each row of the worksheet in turn
            for ($row = 2; $row <= $highestRow; $row++) {
                //  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                    NULL,
                    TRUE,
                    FALSE);
                //  Insert row data array into your database of choice here
                $col = 0;
                foreach($this->fields as $f) {
                    $resultSet[($row-2)][$f->name] = self_escape_string($rowData[0][$col]);
                    $col++;
                }
            }

            $resultSet = $this->specialProcessBeforeShow($resultSet);

            echo json_encode(array('data' => $resultSet, 'identificador_excel' => $identificador_excel));;

//            unlink($target_file);
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

    public function doSave($data)
    {
        $fecha = new DateTime();
        $user = AppSecurity::$UserData['data'];

        $dsBodega = Collection::get($this->db, 'sucursales', sprintf('identificador_excel = "%s"', $data['identificador_excel']))->single();
        $dsCuenta = Collection::get($this->db, 'cuentas', 'lower(nombre) = "inventario"')->single();
        $dsEmpleado = Collection::get($this->db, 'empleados', sprintf('id_usuario = "%s"', $user['ID']))->single();
        $dsMoneda = Collection::get($this->db, 'monedas', 'moneda_defecto = 1')->single();

        foreach ($data['productos'] as $prod) {
            $id_producto = 0;
            $dsProducto = $this->db->query_select('producto', sprintf('codigo_origen = "%s"', $prod['codigo_origen']));

            if (count($dsProducto) > 0) {
                $producto = [
                    'costo' => sqlValue($prod['costo'], 'float'),
                    'precio_venta' => sqlValue($prod['precio_venta'], 'float')
                ];

                $this->db->query_update('producto', $producto, sprintf('id_producto = %s', $dsProducto[0]['id_producto']));

                $id_producto = $dsProducto[0]['id_producto'];
            } else {

                $producto = [
                    'nombre' => sqlValue($prod['nombre'], 'text'),
                    'descripcion' => sqlValue($prod['descripcion'], 'text'),
                    'costo' => sqlValue($prod['costo'], 'float'),
                    'id_tipo' => sqlValue($prod['id_tipo'], 'int'),
                    'precio_venta' => sqlValue($prod['precio_venta'], 'float'),
                    'imagen' => sqlValue($prod['imagen'], 'text'),
                    'codigo_origen' => sqlValue($prod['codigo_origen'], 'text'),
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                    'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
                ];

                $this->db->query_insert('producto', $producto);

                $id_producto = $this->db->max_id('producto', 'id_producto');
            }

            if (count($dsBodega) > 0 && count($dsCuenta) > 0 && count($dsEmpleado) > 0) {

                $transaccion = [
                    'id_cuenta' => sqlValue($dsCuenta['id_cuenta'], 'int'),
                    'id_empleado' => sqlValue($dsEmpleado['id_empleado'], 'int'),
                    'id_sucursal' => sqlValue($dsBodega['id_sucursal'], 'int'),
                    'descripcion' => sqlValue('Carga Masiva Productos', 'text'),
                    'id_moneda' => sqlValue($dsMoneda['id_moneda'], 'int'),
                    'id_producto' => sqlValue($id_producto, 'int'),
                    'debe' => sqlValue('0', 'float'),
                    'haber' => sqlValue($prod['cantidad'], 'float'),
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                    'id_cliente' => sqlValue(0, 'text')
                ];

                $this->db->query_insert('trx_transacciones', $transaccion);
            }
        }
        $this->r = 1;
        $this->msg = 'Producto ingresado con éxito';
    }
}