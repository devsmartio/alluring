<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 7/18/2018
 * Time: 9:03 PM
 * TrxAdministracionClientes
 */

class TrxAdministracionClientes extends FastTransaction {
    protected $onlyEdit;
    function __construct(){
        parent::__construct();
        $this->instanceName = 'TrxAdministracionClientes';
        $this->table = 'clientes';
        $this->setTitle('Administracion de Clientes');
        $this->hasCustomSave = true;

        $this->onlyEdit = false;
        $this->onlyNew = false;
        $dsClientes = $this->db->query_select('clientes');
        $clientes = array();
        foreach($dsClientes as $p){
            $clientes[self_escape_string($p['nombres'] . ' '. $p['apellidos'])] = $p['id_cliente'];
        }
        $this->requiredFields = array(
            new FastField('Nombres', 'nombres', 'text', 'text'),
            new FastField('Apellidos', 'apellidos', 'text', 'text'),
            new FastField('Direccion', 'direccion', 'text', 'text'),
            new FastField('Identificacion', 'identificacion', 'text', 'text'),
            new FastField('Pais', 'id_pais', 'text', 'text'),
            new FastField('Departamento', 'id_departamento', 'text', 'text'),
            new FastField('Municipio', 'id_municipio', 'text', 'text'),
            new FastField('Correo', 'correo', 'text', 'text'),
            new FastField('Tipo Precio', 'id_tipo_precio', 'text', 'text'),
            new FastField('Vendedor', 'id_usuario', 'text', 'text')
        );

        $this->gridCols = array(
            'ID' => 'id_cliente',
            'Nombres' => 'nombres',
            'Apellidos' => 'apellidos',
            'Identificacion' => 'identificacion',
            'Nit' => 'factura_nit',
            'Departamento' => 'nombre_depto'
        );
    }

    protected function showModule() {
        include VIEWS . "/administracion_clientes.phtml";
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
                        id_cliente: 0,
                        nombres: '',
                        apellidos: '',
                        direccion: '',
                        identificacion: '',
                        id_pais: 0,
                        id_departamento: 0,
                        id_municipio: 0,
                        correo: '',
                        id_tipo_precio: 0,
                        id_usuario: 0,
                        tiene_credito: false,
                        dias_credito: '0',
                        id_cliente_referido: 0,
                        factura_nit: '',
                        factura_direccion: '',
                        factura_nombre: '',
                        observaciones: '',
                        catalogo_usuario: '',
                        catalogo_password_hash: '',
                        telefonos : [],
                        bodegas : []
                    };
                    $scope.telefono = '';
                    $scope.telefonoPattern = /^[0-9]{8}$/;
                    $scope.error = '';
                    $scope.id_sucursal = '';
                    $scope.error_bodega = '';
                    $scope.todos_departamentos = [];
                    $scope.carga_desde_edit = false;

                    $http.get($scope.ajaxUrl + '&act=getRows').success(function (response) {
                        $scope.rows = response.data;
                        $scope.setRowSelected($scope.rows);
                        $scope.setRowIndex($scope.rows);
                    });

                    $http.get($scope.ajaxUrl + '&act=getGridCols').success(function (response) {
                        $scope.gridCols = response.data;
                    });
                    $http.get($scope.ajaxUrl + '&act=getPaises').success(function (response) {
                        $scope.paises = response.data;
                    });
                    $http.get($scope.ajaxUrl + '&act=getDepartamentos').success(function (response) {
                        $scope.todos_departamentos = response.data;
                    });
                    $http.get($scope.ajaxUrl + '&act=getMunicipios').success(function (response) {
                        $scope.todos_municipios = response.data;
                    });
                    $http.get($scope.ajaxUrl + '&act=getTiposCliente').success(function (response) {
                        $scope.tiposCliente = response.data;
                    });
                    $http.get($scope.ajaxUrl + '&act=getVendedores').success(function (response) {
                        $scope.vendedores = response.data;
                    });
                    $http.get($scope.ajaxUrl + '&act=getClientes').success(function (response) {
                        $scope.clientes = response.data;
                    });
                    $http.get($scope.ajaxUrl + '&act=getSucursales').success(function (response) {
                        $scope.sucursales = response.data;
                    });

                    $scope.inList = true;
                };

                $scope.$watch('lastPaisSelected', function () {
                    if (!$scope.carga_desde_edit) {
                        if ($scope.todos_departamentos.length > 0 && $scope.lastPaisSelected) {
                            $scope.departamentos = $scope.todos_departamentos.filter(function (s) {
                                return s.id_pais == $scope.lastPaisSelected.id_pais;
                            });
                            $scope.lastMunicipioSelected = {};
                            $scope.lastDepartamentoSelected = {};
                            $scope.municipios = [];
                        }
                    }
                });

                $scope.$watch('lastDepartamentoSelected', function () {
                    if (!$scope.carga_desde_edit) {
                        if ($scope.todos_municipios && $scope.lastDepartamentoSelected && $scope.lastDepartamentoSelected.id_departamento > 0) {
                            $scope.municipios = $scope.todos_municipios.filter(function (s) {
                                return s.id_departamento == $scope.lastDepartamentoSelected.id_departamento;
                            });
                            $scope.lastMunicipioSelected = {};
                        }
                    }
                    $scope.carga_desde_edit = false;
                });

                $scope.goNew = function () {
                    $scope.lastSelected = new Array();
                    $scope.Init();
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
                    $scope.carga_desde_edit = true;
                    $scope.editMode = true;
                    $scope.newMode = false;
                    $scope.noMode = false;

                    var pais = $scope.paises.filter(function (s) {
                        return s.id_pais == $scope.lastSelected.id_pais;
                    });

                    if (pais.length > 0)
                        $scope.lastPaisSelected = pais[0];

                    $scope.departamentos = $scope.todos_departamentos.filter(function (s) {
                        return s.id_pais == $scope.lastPaisSelected.id_pais;
                    });

                    var departamento = $scope.departamentos.filter(function (s) {
                        return s.id_departamento == $scope.lastSelected.id_departamento;
                    });

                    if (departamento.length > 0)
                        $scope.lastDepartamentoSelected = departamento[0];

                    $scope.municipios = $scope.todos_municipios.filter(function (s) {
                        return s.id_departamento == $scope.lastDepartamentoSelected.id_departamento;
                    });

                    var municipio = $scope.todos_municipios.filter(function (s) {
                        return s.id_municipio == $scope.lastSelected.id_municipio;
                    });

                    if (municipio.length > 0)
                        $scope.lastMunicipioSelected = municipio[0];


                };
                $scope.Init = function () {
                    $scope.lastSelected.tiene_credito = false;
                    $scope.lastSelected.dias_credito = 0;
                    $scope.lastSelected.telefonos = [];
                    $scope.lastSelected.bodegas = []
                };

                $scope.agregarTelefono = function () {
                    if($scope.lastSelected.telefonos.filter(tel => tel.numero == $scope.telefono).length > 0) {
                        $scope.mantForm.telefono.$setValidity("invalid", false);
                        $scope.error = 'El teléfono ya fue ingresado';
                    }else {
                        var tel = {numero: $scope.telefono};
                        $scope.lastSelected.telefonos.push(tel);
                        $scope.telefono = '';
                        $scope.error = '';
                    }
                };

                $scope.borrarTelefono = function (tel) {
                    var index = $scope.lastSelected.telefonos.indexOf(tel);
                    $scope.lastSelected.telefonos.splice(index, 1);
                };

                $scope.agregarBodega = function () {
                    if($scope.lastSelected.bodegas.filter(bodega => bodega.id_sucursal == $scope.id_sucursal).length > 0) {
                        $scope.alerts.push({
                            type: 'alert-danger',
                            msg: 'La bodega con ID: ' + $scope.id_sucursal + ' seleccionada ya existe'
                        });
                    }else {
                        var bodega = $scope.sucursales.filter(sucursal => sucursal.id_sucursal == $scope.id_sucursal);
                        if(bodega.length > 0) {
                            $scope.lastSelected.bodegas.push(bodega[0]);
                            $scope.id_sucursal = '';
                            $scope.error_bodega = '';
                        }
                    }
                    $scope.selectBodegaRowSelected($scope.sucursales);
                };

                $scope.borrarBodega = function (bodega) {
                    var index = $scope.lastSelected.bodegas.indexOf(bodega);
                    $scope.lastSelected.bodegas.splice(index, 1);
                };

                $scope.finalizarEditado = function () {
                    $rootScope.modData = {
                        id_cliente: $scope.lastSelected.id_cliente,
                        nombres: $scope.lastSelected.nombres,
                        apellidos: $scope.lastSelected.apellidos,
                        direccion: $scope.lastSelected.direccion,
                        identificacion: $scope.lastSelected.identificacion,
                        id_pais: ($scope.lastPaisSelected == undefined) ? 0 : $scope.lastPaisSelected.id_pais,
                        id_departamento: ($scope.lastDepartamentoSelected == undefined) ? 0 : $scope.lastDepartamentoSelected.id_departamento,
                        id_municipio: ($scope.lastMunicipioSelected == undefined) ? 0 : $scope.lastMunicipioSelected.id_municipio,
                        correo: $scope.lastSelected.correo,
                        id_tipo_precio: $scope.lastSelected.id_tipo_precio,
                        id_usuario: $scope.lastSelected.id_usuario,
                        tiene_credito: $scope.lastSelected.tiene_credito,
                        dias_credito: $scope.lastSelected.dias_credito,
                        id_cliente_referido: ($scope.lastSelected.id_cliente_referido == undefined) ? 0 : $scope.lastSelected.id_cliente_referido,
                        factura_nit: ($scope.lastSelected.factura_nit == undefined) ? '' : $scope.lastSelected.factura_nit,
                        factura_nombre: ($scope.lastSelected.factura_nombre == undefined) ? '' : $scope.lastSelected.factura_nombre,
                        factura_direccion: ($scope.lastSelected.factura_direccion == undefined) ? '' : $scope.lastSelected.factura_direccion,
                        observaciones: ($scope.lastSelected.observaciones == undefined) ? '' : $scope.lastSelected.observaciones,
                        catalogo_usuario: ($scope.lastSelected.catalogo_usuario == undefined) ? '' : $scope.lastSelected.catalogo_usuario,
                        catalogo_password_hash: ($scope.lastSelected.catalogo_password_hash == undefined) ? '' : $scope.lastSelected.catalogo_password_hash,
                        telefonos: $scope.lastSelected.telefonos,
                        bodegas: $scope.lastSelected.bodegas,
                        mod: 2
                    };

                    $scope.doSave();
                };

                $scope.finalizar = function () {
                    $rootScope.modData = {
                        id_cliente: $scope.lastSelected.id_cliente,
                        nombres: $scope.lastSelected.nombres,
                        apellidos: $scope.lastSelected.apellidos,
                        direccion: $scope.lastSelected.direccion,
                        identificacion: $scope.lastSelected.identificacion,
                        id_pais: ($scope.lastPaisSelected == undefined) ? 0 : $scope.lastPaisSelected.id_pais,
                        id_departamento: ($scope.lastDepartamentoSelected == undefined) ? 0 : $scope.lastDepartamentoSelected.id_departamento,
                        id_municipio: ($scope.lastMunicipioSelected == undefined) ? 0 : $scope.lastMunicipioSelected.id_municipio,
                        correo: $scope.lastSelected.correo,
                        id_tipo_precio: $scope.lastSelected.id_tipo_precio,
                        id_usuario: $scope.lastSelected.id_usuario,
                        tiene_credito: $scope.lastSelected.tiene_credito,
                        dias_credito: $scope.lastSelected.dias_credito,
                        id_cliente_referido: ($scope.lastSelected.id_cliente_referido == undefined) ? 0 : $scope.lastSelected.id_cliente_referido,
                        factura_nit: ($scope.lastSelected.factura_nit == undefined) ? '' : $scope.lastSelected.factura_nit,
                        factura_nombre: ($scope.lastSelected.factura_nombre == undefined) ? '' : $scope.lastSelected.factura_nombre,
                        factura_direccion: ($scope.lastSelected.factura_direccion == undefined) ? '' : $scope.lastSelected.factura_direccion,
                        observaciones: ($scope.lastSelected.observaciones == undefined) ? '' : $scope.lastSelected.observaciones,
                        catalogo_usuario: ($scope.lastSelected.catalogo_usuario == undefined) ? '' : $scope.lastSelected.catalogo_usuario,
                        catalogo_password_hash: ($scope.lastSelected.catalogo_password_hash == undefined) ? '' : $scope.lastSelected.catalogo_password_hash,
                        telefonos: $scope.lastSelected.telefonos,
                        bodegas: $scope.lastSelected.bodegas,
                        mod: 1
                    };

                    $scope.doSave();
                };
                $scope.doDelete = function () {
                    if ($scope.editMode) {
                        if (confirm('¿Confirmas borrar este registro? Si el registro está en uso, la acción no se realizará.')) {
                            $rootScope.modData = {
                                id_cliente: $scope.lastSelected.id_cliente,
                                nombres: $scope.lastSelected.nombres,
                                apellidos: $scope.lastSelected.apellidos,
                                direccion: $scope.lastSelected.direccion,
                                identificacion: $scope.lastSelected.identificacion,
                                id_pais: $scope.lastSelected.id_pais,
                                id_departamento: $scope.lastSelected.id_departamento,
                                id_municipio: $scope.lastSelected.id_municipio,
                                correo: $scope.lastSelected.correo,
                                id_tipo_precio: $scope.lastSelected.id_tipo_precio,
                                id_usuario: $scope.lastSelected.id_usuario,
                                tiene_credito: $scope.lastSelected.tiene_credito,
                                dias_credito: $scope.lastSelected.dias_credito,
                                id_cliente_referido: $scope.lastSelected.id_cliente_referido,
                                factura_nit: $scope.lastSelected.factura_nit,
                                factura_nombre: $scope.lastSelected.factura_nombre,
                                factura_direccion: $scope.lastSelected.factura_direccion,
                                observaciones: $scope.lastSelected.observaciones,
                                catalogo_usuario: $scope.lastSelected.catalogo_usuario,
                                catalogo_password_hash: $scope.lastSelected.catalogo_password_hash,
                                telefonos: $scope.lastSelected.telefonos,
                                bodegas: $scope.lastSelected.bodegas,
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

                $scope.selectBodegaRow = function(row){
                    $scope.lastBodegaSelected = row;
                    $scope.currentBodegaIndex = row.index;
                    $scope.selectBodegaRowSelected($scope.sucursales);
                    $scope.lastBodegaSelected.selected = true;
                    $scope.id_sucursal = $scope.lastBodegaSelected.id_sucursal;
                };

                $scope.selectBodegaRowSelected = function(rows){
                    $.each(rows, function(e, row){
                        row.selected = false;
                    });
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
        $departamentos = Collection::get($this->db, 'departamentos');
        $dsClientesBodegas = Collection::get($this->db, 'clientes_bodegas');

        for($i = 0; count($resultSet) > $i; $i++){
            if(isset($resultSet[$i]['id_departamento']) && !isEmpty($resultSet[$i]['id_departamento'])) {
                $depto = $departamentos->where(['id_departamento' => $resultSet[$i]['id_departamento']])->single();
                $resultSet[$i]['nombre_depto'] = self_escape_string($depto['nombre']);
            }

            $resultSet[$i]['tiene_credito'] = ($resultSet[$i]['tiene_credito'] == '1') ? true : false ;

            $telefonos = $this->db->queryToArray(sprintf('select numero from clientes_telefonos where id_cliente=%s', $resultSet[$i]['id_cliente']));
            $resultSet[$i]['telefonos'] = $telefonos;


            $bodegas = $this->db->queryToArray(sprintf('SELECT cl.id_sucursal, nombre FROM clientes_bodegas cl LEFT JOIN sucursales s ON s.id_sucursal = cl.id_sucursal WHERE id_cliente=%s;', $resultSet[$i]['id_cliente']));

            $resultSet[$i]['bodegas'] = sanitize_array_by_keys($bodegas, ['nombre']);
        }
        return $resultSet;
    }

    public function getPaises(){

        $paises = Collection::get($this->db, 'paises')->select(['id_pais','nombre'], true)->toArray();

        echo json_encode(array('data' => $paises));
    }

    public function getDepartamentos(){
        $deptos = Collection::get($this->db, 'departamentos')->select(['id_departamento','nombre','id_pais'], true)->toArray();

        echo json_encode(array('data' => $deptos));
    }

    public function getMunicipios(){
        $municipios = Collection::get($this->db, 'municipios')->select(['id_municipio','nombre','id_departamento'], true)->toArray();

        echo json_encode(array('data' => $municipios));
    }

    public function getTiposCliente(){
        $resultSet = array();

        $dsTipoCliente = $this->db->query_select('clientes_tipos_precio');

        $resultSet[] = array('id_tipo_precio' =>'', 'nombre' => '-- Seleccione uno --');

        foreach($dsTipoCliente as $p){
            $resultSet[] = array('id_tipo_precio' => $p['id_tipo_precio'], 'nombre' => $p['nombre']);
        }

        echo json_encode(array('data' => $resultSet));
    }

    public function getVendedores(){
        $resultSet = array();

        $dsVendedores = $this->db->query_select('app_user', sprintf("is_seller = 1"));

        $resultSet[] = array('id_usuario' => '', 'nombre' => '-- Seleccione uno --');

        foreach($dsVendedores as $p){
            $resultSet[] = array('id_usuario' => $p['ID'], 'nombre' => $p['FIRST_NAME'] . ' ' .$p['LAST_NAME']);
        }

        echo json_encode(array('data' => $resultSet));
    }

    public function getClientes(){
        $resultSet = array();

        $dsClientes = $this->db->query_select('clientes');

        $resultSet[] = array('id_cliente' =>'', 'nombre' => '-- Seleccione uno --');

        foreach($dsClientes as $p){
            $resultSet[] = array('id_cliente' => $p['id_cliente'], 'nombre' => $p['nombres'] . ' ' . $p['apellidos']);
        }

        echo json_encode(array('data' => $resultSet));
    }

    public function getSucursales(){

        $bodegas = Collection::get($this->db, 'sucursales')->select(['id_sucursal','nombre','identificador_excel'], true)->toArray();
        echo json_encode(array('data' => $bodegas));
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

        if (array_key_exists('correo', $data) && $this->r != 0) {
            $correo = str_replace("'", "", $data['correo']);
            if (!preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/', $correo, $matches)) {
                $this->r = 0;
                $this->msg = 'Correo inválido, favor de revisar e intentar de nuevo';
            }
        }
        if (array_key_exists('tiene_credito', $data) && $this->r != 0) {
            if ($data['tiene_credito'] && ($data['dias_credito'] == 0 || $data['dias_credito'] == '')) {
                $this->r = 0;
                $this->msg = 'Al momento de indicar que posee credito, debe indicar cuantos dias de credito posee el cliente';
            }
        }
        if (array_key_exists('factura_nit', $data) && $this->r != 0 && (trim($data['factura_nit']) != '')) {
            if ((array_key_exists('factura_nombre', $data)  && (trim($data['factura_nombre']) == ''))) {
                $this->r = 0;
                $this->msg = 'Al momento de ingresar el Nit, debe indicar el nombre y direccion a facturar';
            }
            if ((array_key_exists('factura_direccion', $data)  && (trim($data['factura_direccion']) == ''))) {
                $this->r = 0;
                $this->msg = 'Al momento de ingresar el Nit, debe indicar el nombre y direccion a facturar';
            }
        }

        if ($data['mod'] == 1 || $data['mod'] == 2 ) {
            if (array_key_exists('catalogo_usuario', $data) && $this->r != 0) {
                if (trim($data['catalogo_usuario']) != '') {

                    $id = str_replace("'", "", trim($data['catalogo_usuario']));
                    $id = sqlValue(encode_email_address($id), 'text');

                    $result = $this->db->queryToArray(sprintf('select FIRST_NAME from app_user where ID=%s', $id));
                    if (count($result) > 0) {
                        $this->r = 0;
                        $this->msg = 'El usuario ya existe, favor de corregir y volver a intentar';
                    }
                }
            }
        }

        if ($data['mod'] == 1) {
            if (array_key_exists('identificacion', $data) && $this->r != 0) {
                if (trim($data['identificacion']) != '') {

                    $identificacion = str_replace("'", "", trim($data['identificacion']));
                    $id_pais = str_replace("'", "", trim($data['id_pais']));

                    $result = $this->db->queryToArray(sprintf("SELECT id_cliente FROM clientes WHERE identificacion = %s and id_pais = %s;", $identificacion, $id_pais));
                    if (count($result) > 0) {
                        $this->r = 0;
                        $this->msg = 'La identificacion ya existe, favor de corregir y volver a intentar';
                    }
                }
            }
        }

        if ($this->r == 0) {
            return false;
        }

        return true;
    }

    public function doSave($data)
    {
        $fecha = new DateTime();
        $user = AppSecurity::$UserData['data'];
        
        $cliente = [
            'nombres' => sqlValue($data['nombres'], 'text'),
            'apellidos' => sqlValue($data['apellidos'], 'text'),
            'direccion' => sqlValue($data['direccion'], 'text'),
            'identificacion' => sqlValue($data['identificacion'], 'text'),
            'id_pais' => sqlValue($data['id_pais'], 'int'),
            'id_departamento' => sqlValue($data['id_departamento'], 'number'),
            'id_municipio' => sqlValue($data['id_municipio'], 'number'),
            'correo' => sqlValue($data['correo'], 'text'),
            'id_tipo_precio' => sqlValue($data['id_tipo_precio'], 'number'),
            'id_usuario' => sqlValue($data['id_usuario'], 'text'),
            'tiene_credito' => sqlValue(($data['tiene_credito'] == false) ? 0 : 1, 'number'),
            'dias_credito' => sqlValue($data['dias_credito'], 'number'),
            'id_cliente_referido' => sqlValue($data['id_cliente_referido'], 'number'),
            'factura_nit' => sqlValue($data['factura_nit'], 'text'),
            'factura_nombre' => sqlValue($data['factura_nombre'], 'text'),
            'factura_direccion' => sqlValue($data['factura_direccion'], 'text'),
            'observaciones' => sqlValue($data['observaciones'], 'text'),
            'catalogo_usuario' => sqlValue($data['catalogo_usuario'], 'text'),
            'catalogo_password_hash' => sqlValue(md5($data['catalogo_password_hash']), 'text'),
            'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
            'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
        ];
        

        if ($data['mod'] == 1) {
            $this->db->query_insert('clientes', $cliente);
            $id_cliente = $this->db->max_id('clientes', 'id_cliente');

            if($data['telefonos']){
                foreach($data['telefonos'] as $tel){
                    $telefono = [
                        'id_cliente' => sqlValue($id_cliente, 'number'),
                        'numero' => sqlValue($tel['numero'], 'number'),
                        'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                        'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
                    ];
                    $this->db->query_insert('clientes_telefonos', $telefono);
                }
            }

            if($data['bodegas']){
                foreach($data['bodegas'] as $bodega){
                    $cliente_bodega = [
                        'id_cliente' => sqlValue($id_cliente, 'number'),
                        'id_sucursal' => sqlValue($bodega['id_sucursal'], 'id_sucursal'),
                        'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                        'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
                    ];
                    $this->db->query_insert('clientes_bodegas', $cliente_bodega);
                }
            }

            $this->msg = 'Cliente ingresado con éxito';
        } else if ($data['mod'] == 2) {

            $this->db->query_update('clientes', $cliente, sprintf('id_cliente = %s', $data['id_cliente']));

            $this->db->query_delete('clientes_telefonos', sprintf('id_cliente = %s', $data['id_cliente']));

            if($data['telefonos']){
                foreach($data['telefonos'] as $tel){
                    $telefono = [
                        'id_cliente' => sqlValue($data['id_cliente'], 'number'),
                        'numero' => sqlValue($tel['numero'], 'number'),
                        'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                        'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
                    ];
                    $this->db->query_insert('clientes_telefonos', $telefono);
                }
            }

            $this->db->query_delete('clientes_bodegas', sprintf('id_cliente = %s', $data['id_cliente']));

            if($data['bodegas']){
                foreach($data['bodegas'] as $bodega){
                    $cliente_bodega = [
                        'id_cliente' => sqlValue($data['id_cliente'], 'number'),
                        'id_sucursal' => sqlValue($bodega['id_sucursal'], 'id_sucursal'),
                        'fecha_creacion' => sqlValue($fecha->format('Y-m-d H:i:s'), 'date'),
                        'usuario_creacion' => sqlValue(self_escape_string($user['FIRST_NAME']), 'text')
                    ];
                    $this->db->query_insert('clientes_bodegas', $cliente_bodega);
                }
            }

            $this->msg = 'Cliente actualizado con éxito';
        } else if ($data['mod'] == 3) {

            $this->db->query_delete('clientes_telefonos', sprintf('id_cliente = %s', $data['id_cliente']));
            $this->db->query_delete('clientes_bodegas', sprintf('id_cliente = %s', $data['id_cliente']));
            $this->db->query_delete('clientes', sprintf('id_cliente = %s', $data['id_cliente']));

            $this->msg = 'Cliente eliminado con éxito';
        }

        $this->r = 1;
    }
}