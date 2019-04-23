<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 8/3/2018
 * Time: 6:56 PM
 */

class TxCargaMasivaClientes  extends FastTransaction {
    protected $onlyEdit;
    protected $uploadPath;
    function __construct(){
        parent::__construct();
        $this->instanceName = 'TxCargaMasivaClientes';
        $this->table = 'clientes';
        $this->setTitle('Carga Masiva de Clientes');
        $this->hasCustomSave = true;
        $this->uploadPath = PATH_UPLOAD_GENERAL . DS;

        $this->fields = array(
            new FastField('Nombres', 'nombres', 'text', 'text'),
            new FastField('Apellidos', 'apellidos', 'text', 'text'),
            new FastField('Direccion', 'direccion', 'text', 'text'),
            new FastField('Identificacion', 'identificacion', 'text', 'text'),
            new FastField('Pais', 'id_pais', 'text', 'text'),
            new FastField('Departamento', 'id_departamento', 'text', 'text'),
            new FastField('Municipio', 'id_municipio', 'text', 'text'),
            new FastField('Correo', 'correo', 'text', 'text'),
            new FastField('Tipo Precio', 'id_tipo_precio', 'text', 'text'),
            new FastField('Vendedor', 'id_empleado', 'text', 'text'),
            new FastField('Referido', 'id_cliente_referido', 'text', 'text'),
            new FastField('Tiene Credito', 'tiene_credito', 'text', 'text'),
            new FastField('Dias Credito', 'dias_credito', 'text', 'text'),
            new FastField('factura_nit', 'factura_nit', 'text', 'text'),
            new FastField('factura_nombre', 'factura_nombre', 'text', 'text'),
            new FastField('factura_direccion', 'factura_direccion', 'text', 'text'),
            new FastField('catalogo_usuario', 'catalogo_usuario', 'text', 'text'),
            new FastField('catalogo_password_hash', 'catalogo_password_hash', 'text', 'text'),
            new FastField('observaciones', 'observaciones', 'text', 'text')
        );

        $this->gridCols = array(
            'Nombres' => 'nombres',
            'Apellidos' => 'apellidos',
            'Identificacion' => 'identificacion',
            'Correo' => 'correo',
            'Departamento' => 'nombre_depto'
        );
    }

    protected function showModule() {
        include VIEWS . "/carga_masiva_clientes.phtml";
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

                    $http.post(uploadUrl, fd, {
                        transformRequest: angular.identity,
                        headers: {'Content-Type': undefined, 'Process-Data': false}
                    })
                        .success(function (response) {
                            console.log("Success");
                            $scope.lastSelected.file = files[0]['name'];
                            $scope.rows = response.data;
                            $scope.disableBtn = false;


//                            if (response.identificador_excel == undefined) {
//                                $scope.disableBtn = true;
//                                $scope.alerts.push({
//                                    type: 'alert-danger',
//                                    msg: 'No se encontró el identificador de bodega en el archivo de excel. Favor de revisar e intentar de nuevo'
//                                });
//                            } else {
//                                $scope.disableBtn = false;
//                            }
                        })
                        .error(function (response) {
                            $scope.alerts.push({
                                type: 'alert-danger',
                                msg: response.msg
                            });
                        });
                };

                $scope.finalizar = function () {

                    var clientes = JSON.stringify($scope.rows);
                    clientes = clientes.replace(/\\/g, "\\\\");

                    $rootScope.modData = {
                        file: $scope.lastSelected.file,
                        clientes: JSON.parse(clientes),
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
        $departamentos = Collection::get($this->db, 'departamentos');

        for($i = 0; count($resultSet) > $i; $i++){
            $depto = $departamentos->where(['id_departamento' => $resultSet[$i]['id_departamento']])->single();
            if($depto)
                $resultSet[$i]['nombre_depto'] = $depto['nombre'];
            else
                $resultSet[$i]['nombre_depto'] = 'Favor Revisar el codigo de la Categoria';
        }
        return $resultSet;
    }

    public function dataIsValid($data)
    {
        foreach ($data['clientes'] as $cliente) {
            if(array_key_exists('catalogo_usuario', $cliente)) {
                $id = str_replace("'", "", trim($cliente['catalogo_usuario']));
                $id = sqlValue(encode_email_address($id), 'text');

                $result = $this->db->queryToArray(sprintf('select FIRST_NAME from app_user where ID=%s', $id));
                if (count($result) > 0) {
                    $this->r = 0;
                    $this->msg = sprintf('El usuario asignado al cliente %s ya existe, favor de corregir y volver a intentar',$cliente['nombres']);
                }
                break;
            }
        }

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

            //  Loop through each row of the worksheet in turn
            for ($row = 2; $row <= $highestRow; $row++) {
                //  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                    NULL,
                    TRUE,
                    FALSE);
                $col = 0;
                foreach($this->fields as $f) {
                    $resultSet[($row-2)][$f->name] = self_escape_string($rowData[0][$col]);
                    $col++;
                }
            }

            $resultSet = $this->specialProcessBeforeShow($resultSet);

            echo json_encode(array('data' => $resultSet));

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

        foreach ($data['clientes'] as $cli) {

                $cliente = [
                    'nombres' => sqlValue($cli['nombres'], 'text'),
                    'apellidos' => sqlValue($cli['apellidos'], 'text'),
                    'direccion' => sqlValue($cli['direccion'], 'text'),
                    'identificacion' => sqlValue($cli['identificacion'], 'text'),
                    'id_pais' => sqlValue($cli['id_pais'], 'int'),
                    'id_departamento' => sqlValue($cli['id_departamento'], 'int'),
                    'id_municipio' => sqlValue($cli['id_municipio'], 'int'),
                    'correo' => sqlValue($cli['correo'], 'text'),
                    'id_tipo_precio' => sqlValue($cli['id_tipo_precio'], 'int'),
                    'id_empleado' => sqlValue($cli['id_empleado'], 'int'),
                    'id_cliente_referido' => sqlValue($cli['id_cliente_referido'], 'int'),
                    'tiene_credito' => sqlValue($cli['tiene_credito'], 'int'),
                    'dias_credito' => sqlValue($cli['dias_credito'], 'int'),
                    'factura_nit' => sqlValue($cli['factura_nit'], 'text'),
                    'factura_nombre' => sqlValue($cli['factura_nombre'], 'text'),
                    'factura_direccion' => sqlValue($cli['factura_direccion'], 'text'),
                    'catalogo_usuario' => sqlValue($cli['catalogo_usuario'], 'text'),
                    'catalogo_password_hash' => sqlValue(md5($cli['catalogo_password_hash']), 'text'),
                    'observaciones' => sqlValue($cli['observaciones'], 'text'),
                    'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                    'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
                ];

                $this->db->query_insert('clientes', $cliente);
        }
        $this->r = 1;
        $this->msg = 'Clientes ingresados con éxito';
    }
}