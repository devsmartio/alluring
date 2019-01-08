<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FastTransaction
 *
 * @author Bryan Cruz
 */
abstract class FastTransaction extends FastModWrapper{
    protected $view;
    protected $r;
    protected $msg;
    protected $hasCustomSave;
    protected $returnData;
    function __construct() {
        parent::__construct();
        $this->instanceName = get_called_class();
        $this->fields = array();
        $this->r = 1;
        $this->msg = 'Operado con éxito';
        $this->view = 'fast_transaction.phtml';
        $this->hasCustomSave = false;
        $this->returnData = [];
    }
    
    protected function showMiddle() {
        include VIEWS . DS . $this->view;
    }
    
    protected function showModule() {
        echo 'Hola probardor!';
    }
	
    public function saveTransaction(){
        $data = inputStreamToArray();
        $data = $data['data'];
        if($this->dataIsValid($data)){
            try {
                $this->db->query('START TRANSACTION');
                $this->doSave($data);
                $this->db->query('COMMIT');
            } catch (Exception $e){
                $this->db->query('ROLLBACK');
                $this->r = 0;
                if(DEBUG){
                    $this->msg = var_dump($e->getMessage());
                } else {
                    error_log($e->getMessage());
                    $this->msg = 'Error inesperado. Intente de nuevo';
                }
            }
        }
        $this->throwResponse();
    }

    private function throwResponse(){
        echo json_encode(array('result' => $this->r, 'msg' => $this->msg, 'data' => $this->returnData));
    }

    protected function doSave($data){
        $this->r = 0;
        $this->msg = 'La data es válida y está lista para ser guardada';
    }

    protected function dataIsValid($data){
        $data = $data;
        return true;
    }

    public function myJavascript() {
        parent::myJavascript();
        ?>
        <script type='text/javascript'>
            app.controller('WrapperCtrl', ['$scope', '$http', '$rootScope' , '$timeout', '$filter', function($scope, $http, $rootScope, $timeout, $filter){
                $scope.ajaxUrl = './?<?php echo AJAX ?>=true&mod=<?php echo $this->instanceName ?>';
                $rootScope.callbacks = new Array();
                $rootScope.modData = new Array();
                $scope.alerts = new Array();
                $rootScope.addCallback = function($cb){
                    $rootScope.callbacks.push($cb);
                };
                $rootScope.doCallbacks = function(response){
                    $.each($rootScope.callbacks, function($id, $cb){
                        $cb(response);
                    });
                };
                $scope.cancel = function(){
                    if(confirm('¿Está seguro que desea cancelar la transacción?')){
                        $rootScope.doCallbacks();
                    }
                };
                $scope.doSave = function(){
                    $scope.save($scope.ajaxUrl + '&act=saveTransaction', {data: $rootScope.modData});
                };
                $scope.send = function(url, data, $cb){
                    $scope.loading();
                    $http.post(url, {data: data}, {
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }).success(function(response) {
                        if(response.result == 1){
                            $scope.alerts = new Array();
                            $scope.alerts.push({
                                type: "alert-success",
                                msg: response.msg
                            });
                            $scope.doneLoading();
                            $timeout(function(){
                                $cb();
                            }, 5000);
                        } else if((response.result == 0)){
                            $scope.doneLoading();
                            $scope.alerts = new Array();
                            $scope.alerts.push({
                                type: "alert-danger",
                                msg: response.msg
                            });
                            $timeout(function(){
                                $scope.alerts = new Array();
                            }, 3500);
                        }                       
                    });
                     $scope.doneLoading();
                };
                $scope.request = function(url, data, $cb){
                    $scope.loading();
                    $http.post(url, {data: data}, {
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }).success(function(response) {
                        if(response.result == 1){
                            $scope.alerts = new Array();
                            $scope.alerts.push({
                                type: "alert-success",
                                msg: response.msg
                            });
                            $cb(response);
                        } else if((response.result == 0)){
                            $scope.doneLoading();
                            $scope.alerts = new Array();
                            $scope.alerts.push({
                                type: "alert-danger",
                                msg: response.msg
                            });
                        }
                        $timeout(function(){
                            $scope.alerts = new Array();
                        }, 2000);
                    });
                     $scope.doneLoading();
                };
                
                $scope.cbRequest = function(url, data, $cb){
                    $scope.loading();
                    $http.post(url, {data: data}, {
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }).success(function(response) {
                        if(response.result == 1){
                            $cb(response);
                        } else if((response.result == 0)){
                            $scope.doneLoading();
                            $scope.alerts = new Array();
                            $scope.alerts.push({
                                type: "alert-danger",
                                msg: response.msg
                            });
                        }
                        $timeout(function(){
                            $scope.alerts = new Array();
                        }, 2000);
                    });
                     $scope.doneLoading();
                };
                
                $scope.save = function(url, data){
                    $scope.loading(); 
                    $http.post(url, data, {
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }).success(function(response) {
                        if(response.result == 1){
                            $scope.alerts = new Array();
                            $scope.alerts.push({
                                type: "alert-success",
                                msg: response.msg
                            });
                            $scope.doneLoading();
                            $rootScope.doCallbacks(response);
                        } else if((response.result == 0)){
                            $scope.doneLoading();
                            $scope.alerts = new Array();
                            $scope.alerts.push({
                                type: "alert-danger",
                                msg: response.msg
                            });
                        }
                        $timeout(function(){
                            $scope.alerts = new Array();
                        }, 5000);
                    });
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
                          
                $scope.setSelectedItem = function(collection, item, field){
                    $.each(collection, function($in, i){
                        if(i[field] == item[field]){
                            i.selected = true;
                        } else {
                            i.selected = false;
                        }
                    })
                };
                
                $scope.showAlert = function(type, msg, timeout){
                    $scope.alerts = new Array();
                    $scope.alerts.push({
                        type: type,
                        msg: msg
                    });
                    $timeout(function(){
                        $scope.alerts = new Array();
                    }, timeout);
                };
            }]);
        </script>
        <?php
    }
}
?>