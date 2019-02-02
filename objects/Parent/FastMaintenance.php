<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FastMantenance
 *
 * @author Bryan Cruz
 */
abstract class FastMaintenance extends FastModWrapper{
    protected $table;
    protected $gridCols;
    protected $fields;
    protected $pkFields;
    protected $name;
    protected $view;
    protected $onlyEdit;
    
    function __construct() {
        parent::__construct();
        $this->view = 'fast_maintenance.phtml';
        $this->fields = array();
        $this->onlyEdit = false;
        $this->onlyNew = false;
    }
    
    protected function showMiddle() {
        $this->showModule();
    }
    
    private function showModule() {
        include VIEWS . DS . $this->view;
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
	
    protected function specialProcessBeforeShow($resultSet){
        return $resultSet;
    }
    
    public function doSave(){
        $type = getParam('type');
        switch($type){
            case 'upd':{
                $this->doUpd();
                break;
            }
            case 'new':{
                $this->doNew();
                break;
            }
        }
    }
    
    private function doUpd(){
        $r = 1;
        $mess = 'Guardado';
        $update = array();
        $pkFields = array();
        $date = new DateTime();
        $where = '';
        foreach($this->fields as $f){
            if($f instanceof FastField){
                if($f->required){
                    if(isEmpty($f->getValue())){
                        $r = 0;
                        $mess = sprintf('El campo %s es requerido', $f->label);
                        break;
                    } elseif($f->isPk) {
                        $pkFields[$f->name] = $f->getValue();
                    } else {
                        $update[$f->name] = $f->getSqlValue();
                    }
                } elseif($f->storedFunc != null) {
                    switch($f->storedFunc){
                        case 'DATE':{
                            $val = $date->format('Y-m-d H:i:s');
                        }
                    }
                    $update[$f->name] = $val;
                } elseif($f->isPk){
                    $pkFields[$f->name] = $f->getValue();
                } else{
                    if(!$f->exists()){
                        $update[$f->name] = $f->getDefaultValue();
                    }
                    if(!isEmpty($f->getValue())){
                        $update[$f->name] = $f->getSqlValue();
                    }
                }
            }
        }
        if($r == 1){
            $specialValidation = $this->specialValidation($update, $r, $mess, $pkFields);
            $r = $specialValidation['r'];
            $mess = $specialValidation['mess'];
            if($r == 1){
                $pkFields = $this->processPkFields($pkFields);
                foreach($pkFields as $k => $v){
                    $where = isEmpty($where) ? $where : sprintf('%s AND ', $where);
                    $where.= sprintf('%s="%s"', $k, $v);
                }
                try {
                    $update = $this->specialProcessBeforeUpdate($update, $pkFields);
                    $this->db->query_update($this->table, $update, $where);
                } catch (Exception $e){
                    $r = 0;
                    $mess = 'Error desconocido. Contacte a soporte';
                var_dump($e->getTraceAsString());
                }
            }
        }
        echo json_encode(array('result' => $r, 'msg' => $mess));
    }
	
    protected function processPkFields($pkFields){
        return $pkFields;
    }

    protected function specialProcessBeforeUpdate($updateData, $pkFields = array()){
        return $updateData;
    }
    
    protected function specialValidation($fields, $r, $mess, $pkFields){
        return array('r' => $r, 'mess' => $mess);
    }
	
    private function doNew(){
        $r = 1;
        $mess = 'Guardado';
        $insert = array();
        $pkFields = array();
        $date = new DateTime();
        foreach($this->fields as $f){
            if($f instanceof FastField){
                if($f->required){
                    if(isEmpty($f->getValue())){
                        $r = 0;
                        $mess = sprintf('El campo %s es requerido', $f->label);
                        break;
                    } elseif($f->isPk && !isEmpty($f->getValue())) {
                        $insert[$f->name] = $f->getSqlValue();
                    } else {
                        $insert[$f->name] = $f->getSqlValue();
                    }
                } elseif($f->storedFunc != null) {
                    switch($f->storedFunc){
                        case 'DATE':{
                            $val = $date->format('Y-m-d H:i:s');
                        }
                    }
                    $insert[$f->name] = $val;
                } else {
                    if(!isEmpty($f->getValue())){
                        $insert[$f->name] = $f->getSqlValue();
                    }
                }
            }
        }
        
        
        if($r == 1){
            $specialValidation = $this->specialValidation($insert, $r, $mess, $pkFields);
            $r = $specialValidation['r'];
            $mess = $specialValidation['mess'];
            if($r == 1){
                try {
                    $insert = $this->specialProcessBeforeInsert($insert);
                    $this->db->query_insert($this->table, $insert);
                } catch (Exception $e){
                    $r = 0;
                    $mess = 'Error desconocido. Contacte a soporte';
                    var_dump($e->getTraceAsString());
                }
            }
        }
        echo json_encode(array('result' => $r, 'msg' => $mess));
    }
	
	protected function specialProcessBeforeInsert($insertData){
            return $insertData;
	}

    protected function validationBeforeDelete($r, $mess, $pkFields){
        return array('r' => $r, 'mess' => $mess);
    }
    
	public function doDelete(){
            $r = 1;
            $msg = 'Eliminado';
            $pkFields = array();
            $where = '';
            try {
                $pkFields = array();
                foreach($this->fields as $f){
                    if($f instanceof FastField){
                        if($f->isPk){
                            $pkFields[$f->name] = $f->getValue();
                        }
                    }
                }
                $pkFields = $this->processPkFields($pkFields);
                foreach($pkFields as $k => $v){
                    $where = isEmpty($where) ? $where : sprintf('%s AND ', $where);
                    $where.= sprintf('%s="%s"', $k, $v);
                }

                $specialValidation = $this->validationBeforeDelete($r, $msg, $pkFields);
                $r = $specialValidation['r'];
                $msg = $specialValidation['mess'];
                if($r == 1) {
                    $this->db->query_delete($this->table, $where);
                } else {
                    echo json_encode(array('result' => $r, 'msg' => $msg));
                }
            } catch (Exception $e){
                $r = 0;
                $msg = 'Error desconocido, contacte a soporte';
                error_log($e->getTraceAsString());
            }
            echo json_encode(array('result' => $r, 'msg' => $msg));
        }
	
    public function myJavascript() {
        parent::myJavascript();
        ?>
        <script type='text/javascript'>
            app.controller('WrapperCtrl', function($scope,$http,$rootScope, $timeout){
                $scope.ajaxUrl = './?<?php echo AJAX ?>=true&mod=<?php echo $this->instanceName ?>';
                $rootScope.callbacks = new Array();
				$scope.alerts = new Array();
                $scope.startAgain = function(){
                    $scope.goNoMode();
                    $scope.currentIndex = null;
                    $http.get($scope.ajaxUrl + '&act=getRows').success(function(response){
                        $scope.rows = response.data;
                        $scope.setRowSelected($scope.rows);
                        $scope.setRowIndex($scope.rows);
                    });
                    $http.get($scope.ajaxUrl + '&act=getGridCols').success(function(response){
                        $scope.gridCols = response.data;
                    });
                };
                $rootScope.addCallback = function($cb){
                    $rootScope.callbacks.push($cb);
                };
                $rootScope.doCallbacks = function(){
                    $.each($rootScope.callbacks, function($id, $cb){
                        $cb();
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
                $scope.goEdit = function(){
                    $scope.editMode = true;
                    $scope.newMode = false;
                    $scope.noMode = false;
                };
                $scope.goNew = function(){
                    $scope.lastSelected = new Array();
                    $scope.editMode = false;
                    $scope.newMode = true;
                    $scope.noMode = false;
                };
                $scope.goNoMode = function(){
                    $scope.editMode = false;
                    $scope.newMode = false;
                    $scope.noMode = true;
                };
                $scope.cancel = function(){
                    $scope.startAgain();
                };
                $scope.refresh = function(){
                    $scope.startAgain();
                };
                $scope.doSave = function(){
                    isOnlyNew = <?php echo $this->onlyNew ? 'true' : 'false' ?>;
                    if(isOnlyNew && $scope.editMode){
                        $scope.alerts = new Array();
                            $scope.alerts.push({
                                type: "alert-danger",
                                msg: 'Este elemento solo puede ser eliminado.'
                            });
                            $timeout(function(){
                                $scope.alerts = new Array();
                            }, 3500);
                    }
                    type = $scope.editMode ? 'upd' : 'new';
                    $scope.save($scope.ajaxUrl + '&act=doSave&type=' + type, $('#mantForm').serialize());
                };
                $scope.doSaveNoChange = function(){
                    isOnlyNew = <?php echo $this->onlyNew ? 'true' : 'false' ?>;
                    if(isOnlyNew && $scope.editMode){
                        $scope.alerts = new Array();
                            $scope.alerts.push({
                                type: "alert-danger",
                                msg: 'Este elemento solo puede ser eliminado.'
                            });
                            $timeout(function(){
                                $scope.alerts = new Array();
                            }, 3500);
                    }
                    type = $scope.editMode ? 'upd' : 'new';
                    $scope.saveNoCb($scope.ajaxUrl + '&act=doSave&type=' + type, $('#mantForm').serialize());
                };
                $scope.doDelete = function(){
                    if($scope.editMode){
                        if(confirm('¿Confirmas borrar este registro? Si el registro está en uso, la acción no se realizará.')){
                            $scope.save($scope.ajaxUrl + '&act=doDelete', $('#mantForm').serialize());
                        }
                    } else {
                        $scope.alerts.push({type: 'alert-warning',msg: 'Operación no permitida'});
                        $scope.startAgain();
                    }
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
                            $rootScope.doCallbacks();
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

                $scope.saveNoCb = function(url, data){
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
                            //$rootScope.doCallbacks();
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
                $scope.startAgain();
                $rootScope.addCallback(function(){
                    $scope.startAgain();
                });
            });
        </script>
        <?php
    }
}

?>
