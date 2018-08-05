<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 8/3/2018
 * Time: 1:41 PM
 */

class TrxTrasladoBodegas   extends FastTransaction {
    function __construct(){
        parent::__construct();
        $this->instanceName = 'TrxTrasladoBodegas';
        $this->setTitle('Traslado de Bodegas');
        $this->hasCustomSave = true;

        $this->fields = array(
        );
    }

    protected function showModule() {
        include VIEWS . "/traslado_bodegas.phtml";
    }

    public function myJavascript()
    {
        parent::myJavascript();
        ?>
        <script>
            app.controller('ModuleCtrl', function ($scope, $http, $rootScope) {
                $scope.startAgain = function () {
                    $scope.idSucursalOrigen = 0;
                    $scope.idSucursalDestino = 0;
                    $http.get($scope.ajaxUrl + '&act=getSucursales').success(function (response) {
                        $scope.sucursalesOrigen = response.data;
                        $scope.sucursalesDestino = response.data;
                    });
                };

                $scope.finalizar = function () {
                    $rootScope.modData = {
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

    public function getSucursales(){
        $resultSet = array();

        $dsBodegas = Collection::get($this->db, 'sucursales')->toArray();

        $resultSet[] = array('id_sucursal' =>'', 'nombre' => '-- Seleccione uno --');
        foreach($dsBodegas as $p){
            $resultSet[] = array('id_sucursal' => $p['id_sucursal'], 'nombre' => $p['nombre']);
        }

        echo json_encode(array('data' => $resultSet));
    }

    public function specialProcessBeforeShow($resultSet){
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
        $fecha = new DateTime();
        $user = AppSecurity::$UserData['data'];

        $this->r = 1;
        $this->msg = 'Traslado realizado con éxito';
    }
}