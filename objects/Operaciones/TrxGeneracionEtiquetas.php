<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 8/13/2018
 * Time: 7:02 AM
 */

class TrxGeneracionEtiquetas extends FastTransaction {
    protected $onlyEdit;
    protected $uploadPath;
    function __construct(){
        parent::__construct();
        $this->instanceName = 'TrxGeneracionEtiquetas';
        $this->table = 'producto';
        $this->setTitle('Generacion de Etiquetas');
        $this->hasCustomSave = true;

        $this->onlyEdit = false;
        $this->onlyNew = false;
        $this->uploadPath = PATH_UPLOAD_GENERAL . DS;

        $this->fields = array(
            new FastField('Nombre', 'nombre', 'text', 'text'),
            new FastField('Descripción', 'descripcion', 'text', 'text'),
            new FastField('Categoria', 'nombre_tipo', 'text', 'text'),
            new FastField('Codigo', 'codigo_origen', 'text', 'text'),
            new FastField('Bodega', 'nombre_bodega', 'text', 'text')
        );

        $this->gridCols = array(
            'Codigo' => 'codigo_origen',
            'Nombre' => 'nombre',
            'Descripción' => 'descripcion',
            'Categoria' => 'nombre_tipo',
            'Bodega' => 'nombre_bodega'
        );
    }

    protected function showModule() {
        include VIEWS . "/generacion_etiquetas.phtml";
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
                    $scope.porArchivo = false;
                    $scope.porFiltro = false;
                    $scope.lastSelected = {
                        file: ''
                    };
                    $scope.id_tipo = '';
                    $scope.id_sucursal = '';
                    angular.element("input[type='file']").val(null);

                    $http.get($scope.ajaxUrl + '&act=getCategorias').success(function (response) {
                        $scope.categorias = response.data;
                    });

                    $http.get($scope.ajaxUrl + '&act=getBodegas').success(function (response) {
                        $scope.sucursales = response.data;
                    });
                    $rootScope.callbacks = new Array();
                };

                $http.get($scope.ajaxUrl + '&act=getGridCols').success(function (response) {
                    $scope.gridCols = response.data;
                });

                $scope.uploadFile = function (files) {
                    var uploadUrl = $scope.ajaxUrl + '&act=uploadExcel';
                    var fd = new FormData();
                    fd.append('file', files[0]);
                    fd.append('name', files[0]['name']);

                    $http.post(uploadUrl, fd, {
                        transformRequest: angular.identity,
                        headers: {'Content-Type': undefined, 'Process-Data': false}
                    })
                        .success(function (response) {
                            $scope.disableBtn = false;
                            $scope.alerts.push({
                                type: "alert-success",
                                msg: "Carga Exitosa"
                            });
                        })
                        .error(function (response) {
                            $scope.disableBtn = true;
                            $scope.alerts.push({
                                type: 'alert-danger',
                                msg: response.msg
                            });
                        });
                };

                $scope.actualizarChkBxFiltro = function () {
                    $scope.porFiltro = false;
                };

                $scope.actualizarChkBxArchivo = function () {
                    $scope.porArchivo = false;
                };

                $scope.filtrar = function () {
                    var idSucursal = $scope.id_sucursal;
                    var idTipo = $scope.id_tipo;
                    $http.get($scope.ajaxUrl + '&act=filtrarProductos&idSucursal='+idSucursal+'&idTipo='+idTipo).success(function (response) {
                        $scope.rows = response.data;

                        $scope.disableBtn = !($scope.rows.length > 0)
                    });
                };

                $scope.finalizar = function () {

                    $scope.doSave();

                    if ($scope.porArchivo) {
                        $rootScope.addCallback(response => window.open('./?action=pdf&tmp=GEN_ET&GEN_ET=0'));
                    } else if ($scope.porFiltro) {
                        var idSucursal = $scope.id_sucursal;
                        var idTipo = $scope.id_tipo;

                        $rootScope.addCallback(response => window.open('./?action=pdf&tmp=GEN_ET&GEN_ET=1&idSucursal=' + idSucursal + '&idTipo=' + idTipo));
                    }
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

    public function getBodegas(){
        $resultSet = array();

        $dsSucursales = $this->db->query_select('sucursales');
        $resultSet[] = array('id_sucursal' =>'', 'nombre' => '-- Seleccione uno --');
        foreach($dsSucursales as $p){
            $resultSet[] = array('id_sucursal' => $p['id_sucursal'], 'nombre' => $p['nombre']);
        }

        echo json_encode(array('data' => $resultSet));
    }

    public function filtrarProductos(){
        $idSucursal = getParam("idSucursal");
        $idTipo = getParam("idTipo");

        $queryTransacciones ="  SELECT	p.codigo_origen, p.nombre, p.descripcion, t.haber, t.id_sucursal, p.id_tipo, ti.nombre as nombre_tipo, s.nombre as nombre_bodega
                                FROM	trx_transacciones t
                                        INNER JOIN producto p ON p.id_producto = t.id_producto
                                        INNER JOIN tipo ti ON ti.id_tipo = p.id_tipo
                                        INNER JOIN sucursales s ON s.id_sucursal = t.id_sucursal ";

        $where = 'WHERE 1 = 1';

        if($idSucursal != "")
            $where .=  " AND t.id_sucursal = " . $idSucursal;

        if($idTipo != "")
            $where .= " AND ti.id_tipo = " . $idTipo;

        $transacciones = $this->db->queryToArray($queryTransacciones . $where);

        echo json_encode(array('data' => $transacciones));
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

            $this->db->query_delete('generacion_etiquetas');

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

            //  Loop through each row of the worksheet in turn
            for ($row = 1; $row <= $highestRow; $row++) {
                //  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                    NULL,
                    TRUE,
                    FALSE);
                //  Insert row data array into your database of choice here
                $etiqueta = [
                    'codigo_origen' => sqlValue($rowData[0][0], 'text'),
                    'cantidad' => sqlValue($rowData[0][1], 'int'),
                ];

                $this->db->query_insert('generacion_etiquetas', $etiqueta);
            }
            echo json_encode(array('result' => 1, 'msg' => ''));

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

    }
}