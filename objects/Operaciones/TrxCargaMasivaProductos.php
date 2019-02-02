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
            new FastField('Codigo', 'codigo', 'text', 'text', false, null, [], false, null, true),
            new FastField('COdigo Origen', 'codigo_origen', 'text', 'text')
        );

        $this->gridCols = array(
            'Codigo' => 'codigo',
            'Nombre' => 'nombre',
            'Descripción' => 'descripcion',
            'Categoria' => 'nombre_tipo',
            'Precio venta al público' =>'precio_venta',
            'Costo' => 'costo',
            'Codigo origen' => 'codigo_origen'
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
                    $scope.bodegas = [];
                    $scope.disableBtn = true;
                    $scope.lastSelected = {
                        file: ''
                    };
                    angular.element("input[type='file']").val(null);
                    $('#loading').hide();
                };
                $http.get($scope.ajaxUrl + '&act=getGridCols').success(function (response) {
                    $scope.gridCols = response.data;
                });

                $scope.uploadFile = function (files) {
                    $('#loading').show();
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
                            $scope.bodegas = [];
                            $scope.lastSelected.file = files[0]['name'];
                            $scope.rows = response.data;
                            $scope.bodegas = response.bodegas;

                            if (($scope.bodegas == undefined) || ($scope.bodegas.length == 0)) {
                                $scope.disableBtn = true;
                                $scope.alerts.push({
                                    type: 'alert-danger',
                                    msg: 'No se encontró el identificador de bodega en el archivo de excel. Favor de revisar e intentar de nuevo'
                                });
                            } else {
                                $scope.disableBtn = false;
                            }
                            angular.element("input[type='file']").val(null);
                        })
                        .error(function (response) {
                            $scope.alerts.push({
                                type: 'alert-danger',
                                msg: response.msg
                            });
                        });
                    $('#loading').hide();
                };

                $scope.finalizar = function () {

                    var identificador_excel = $scope.lastSelected.identificador_excel;
                    var productos = JSON.stringify($scope.rows);
                    productos = productos.replace(/\\[^"]/g, "\\\\");

                    var bodega_cargar = JSON.stringify($scope.bodegas);
                    bodega_cargar = bodega_cargar.replace(/\\[^"]/g, "\\\\");
                    $rootScope.modData = {
                        file: $scope.lastSelected.file,
                        productos: JSON.parse(productos),
                        bodegas_cargar: JSON.parse(bodega_cargar),
                        mod: 1
                    };

                    $scope.doSave();
                };

                $scope.cancelar = function () {
                    $scope.cancel();
                };

                $scope.startAgain();
                $rootScope.addCallback(function (response) {

                    if ((response != undefined) && (response.result == 1)) {

                        //window.open("./?action=pdf&tmp=TRX");
                    }

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
            $bodegas = [];
            $countBodegas = 0;
            $bodegas_cargar = [];

            $encabezados = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);
            
            for ($i = 7; $i <= count($encabezados[0])-1; $i++) {
                if(isset($encabezados[0][$i]) && !isEmpty($encabezados[0][$i])){
                    $bodegas[$countBodegas++] = $encabezados[0][$i];
                } 
            }

            $countBodegas = 0;
            //  Loop through each row of the worksheet in turn
            for ($row = 2; $row <= $highestRow; $row++) {
                //  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                    NULL,
                    TRUE,
                    FALSE);
                //Si codigo_origen esta vacio y codigo tambien, nos saltamos esa fila
                if((!isset($rowData[0][6]) || isEmpty($rowData[0][6])) /*&& (!isset($rowData[0][5]) || isEmpty($rowData[0][5]))*/){
                    continue;
                }
                $col = 0;
                foreach($this->fields as $f) {
                    $resultSet[($row-2)][$f->name] = self_escape_string($rowData[0][$col]);
                    $col++;
                }

                $resultSet[$row - 2]['bodegas'] = [];
                for ($i = 1; $i <= count($bodegas); $i++) {
                    $bodegas_cargar[$countBodegas]['codigo'] = $rowData[0][5];
                    $bodegas_cargar[$countBodegas]['codigo_origen'] = $rowData[0][6];
                    $bodegas_cargar[$countBodegas]['cantidad'] = $rowData[0][(6+$i)];
                    $bodegas_cargar[$countBodegas]['bodega'] = $bodegas[($i-1)];
                    $resultSet[$row - 2]['bodegas'][$bodegas[$i - 1]] = $rowData[0][(6+$i)]; 
                    $countBodegas++;
                }
            }

            $resultSet = $this->specialProcessBeforeShow($resultSet);

            echo json_encode(array('data' => $resultSet, 'bodegas' => $bodegas_cargar));;

//            unlink($target_file);
        } catch (Exception $e) {
            $this->r = 0;
            if (DEBUG) {
                $this->msg = var_dump($e->getMessage());
            } else {
                error_log($e->getMessage());
                $this->msg = 'Error al cargar Excel. Intente de nuevo';
            }

            echo json_encode(array('result' => $this->r, 'msg' => $this->msg));
        }
    }

    public function doSave($data)
    {
        $data = inputStreamToArray(false);
        $data = $data['data'];
        $fecha = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $dsCuenta = Collection::get($this->db, 'cuentas', 'lower(nombre) = "inventario"')->single();
        $dsMoneda = Collection::get($this->db, 'monedas', 'moneda_defecto = 1')->single();
        foreach ($data['productos'] as $prod) {
            $id_producto = 0;
            $dsProducto = $this->db->query_select('producto', sprintf('codigo_origen = "%s"', $prod['codigo_origen']));
            if(count($dsProducto) == 0 && !isEmpty($prod['codigo'])){
                $dsProducto = $this->db->query_select('producto', sprintf('codigo = "%s"', $prod['codigo']));
            }
            if (count($dsProducto) > 0) {
                $producto = [];
                if(!isEmpty($prod['costo'])){
                    $producto['costo'] = sqlValue($prod['costo'], 'float');
                }
                if(!isEmpty($prod['precio_venta'])){
                    $producto['precio_venta'] = sqlValue($prod['precio_venta'], 'float');
                }
                if(count($producto) > 0){
                    $this->db->query_update('producto', $producto, sprintf('id_producto = %s', $dsProducto[0]['id_producto']));
                }
                

            } else {
                $ultimoCodigo = $this->db->queryToArray('SELECT	COALESCE(MAX(CAST(REPLACE(codigo,"Y","") AS UNSIGNED)),1) AS ultimo_codigo FROM producto');
                $ultimoCodigo = intval($ultimoCodigo[0]['ultimo_codigo']) + 1;
                $producto = [
                    'nombre' => sqlValue($prod['nombre'], 'text'),
                    'descripcion' => sqlValue($prod['descripcion'], 'text'),
                    'costo' => sqlValue($prod['costo'], 'float'),
                    'id_tipo' => sqlValue($prod['id_tipo'], 'int'),
                    'precio_venta' => sqlValue($prod['precio_venta'], 'float'),
                    'imagen' => sqlValue(strtolower($prod['codigo_origen']) . '.jpg', 'text'),
                    'codigo' => sqlValue('Y' . $ultimoCodigo, 'text'),
                    'codigo_origen' => sqlValue($prod['codigo_origen'], 'text'),
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                    'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
                ];

                $this->db->query_insert('producto', $producto);
            }
        }

        $this->db->query_delete('generacion_etiquetas');
        foreach($data['bodegas_cargar'] as $bodega){
            $dsBodega = Collection::get($this->db, 'sucursales', sprintf('LOWER(identificador_excel) = LOWER("%s")', $bodega['bodega']))->single();
            $dsProducto = Collection::get($this->db, 'producto', sprintf('LOWER(codigo_origen) = LOWER("%s")', $bodega['codigo_origen']))->single();
            if(count($dsProducto) == 0){
                $dsProducto = Collection::get($this->db, 'producto', sprintf('LOWER(codigo) = LOWER("%s")', $bodega['codigo']))->single();
            }
            if (count($dsBodega) > 0 && count($dsCuenta) > 0) {

                $transaccion = [
                    'id_cuenta' => sqlValue($dsCuenta['id_cuenta'], 'int'),
                    'id_sucursal' => sqlValue($dsBodega['id_sucursal'], 'int'),
                    'descripcion' => sqlValue('Carga Masiva Productos', 'text'),
                    'id_moneda' => sqlValue($dsMoneda['id_moneda'], 'int'),
                    'id_producto' => sqlValue($dsProducto['id_producto'], 'int'),
                    'debe' => sqlValue('0', 'float'),
                    'haber' => $bodega['cantidad'],
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                    'id_cliente' => sqlValue(0, 'text')
                ];

                $this->db->query_insert('trx_transacciones', $transaccion);

                $etiqueta = [
                    'codigo' => sqlValue($bodega['codigo'], 'text'),
                    'codigo_origen' => sqlValue($bodega['codigo_origen'], 'text'),
                    'cantidad' => $bodega['cantidad'],
                    'id_sucursal' => sqlValue($dsBodega['id_sucursal'], 'int'),
                ];

                $this->db->query_insert('generacion_etiquetas', $etiqueta);
            } else {
                $error = 'No se encuentra configurada la Bodega, Cuenta o Empleado, favor de revisar';
                throw new Exception($error . " " . $bodega['bodega']);
            }
        }

        $this->r = 1;
        $this->msg = 'Producto ingresado con éxito';
    }
}