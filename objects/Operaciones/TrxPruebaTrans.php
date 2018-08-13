<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TrxAvancesProyecto
 *
 * @author baci5
 */
class TrxPruebaTrans extends FastTransaction {
    function __construct(){
        parent::__construct();
        $this->setTitle('Pruebas');
        $this->hasCustomSave = true;
    }
    
    protected function showModule() {
        include VIEWS . "/pruebas_trans.phtml";
    }
    
    public function myJavascript() {
        parent::myJavascript();
        ?>
    <script>
        app.controller('ModuleCtrl', function($scope, $http, $rootScope, $timeout){
            $scope.startAgain = function(){
              $scope.hola = "Hola prueba";
            };
            $scope.finalizar = function(){
                console.log("Enviando");
                $rootScope.modData = {
                    comentario: $scope.hola
                };
                $scope.doSave();
            };
            
            $scope.cancelar = function(){
                $scope.cancel();
            };
            
            $scope.startAgain();
            $rootScope.addCallback(function(){
                $scope.startAgain(); 
            });
        });
    </script>
        <?php
    }
    
    public function dataIsValid($data) {
        $comentario = $data["comentario"];
        if(isEmpty($comentario)){
            $this->r = 0;
            $this->msg = "El comentario no puede estar vacio";
        }
        if($this->r == 0){
            return false;
        }
        return true;
    }
    
    public function doSave($data){
        $this->r = 1;
        $this->msg = 'Usted envio ' . $data["comentario"];
    }
}
