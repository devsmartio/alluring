<?php
/**
 * Description of Table
 *
 * @author Bryan C.
 */
abstract class MaintenanceTable {
    private $db;
    protected $pkField = null;
    protected $table;
    protected $sanitizeCols;
    protected $gridCols;
    protected static $sInstance = null;
    protected $requiredFields = array();
    protected $insertUpdateFields = array();
    protected $user;
    private $lastError;
    protected $parent;
    
    public static function getMe(){
        if(static::$sInstance == null){
            static::$sInstance = new static();
        }
        return static::$sInstance;
    }
    
    private function __construct() {
        $this->db = DbManager::getMe();
        $this->user = AppSecurity::$UserData['data'];
        $this->init();
    }
    
    abstract protected function init();
    
    public function getAll($filterByGridCols = false){
        $result = array();
        $resultSet = $this->db->query_select($this->table);
        if($filterByGridCols){
            foreach($resultSet as $row){
                $result[] = $this->filterByGridCols($row);
            }
        } else {
            $result = $resultSet;
        }
        $sanitized = sanitize_array_by_keys($result, $this->sanitizeCols);
        return $this->processBeforeShow($sanitized);
    }
    
    private function filterByGridCols($row){
        $result = array();
        foreach($this->gridCols as $col){
            if($col->getField() != null){
                $k = $col->getField();
                $result[$k] = $row[$k];
            }
        }
        return $result;
    }
    
    public function getCols(){
        return $this->gridCols;
    }
    
    public function deleteRow($pkValue){
        if($this->pkField == null){
            $this->lastError = 'Se ha detectado una configuración incorrecta. '
                    . 'Comuníquese con el programador';
            return false;
        } else {
            if(!$this->getRow($pkValue)){
                return false;
            } else {
                $w = sprintf('%s="%s"', $this->pkField, $pkValue);
                $this->db->query_delete($this->table, $w);
                return true;
            }
        }
    }
    
    public function getRow($pkValue){
        $w = sprintf('%s="%s"', $this->pkField, $pkValue);
        $result = $this->db->query_select($this->table, $w);
        if(count($result) == 0){
            $this->lastError = "¡La fila solicitada no existe!";
            return false;
        } else {
            return $result[0];
        }
    }
    
    public function getPkField(){
        return $this->pkField;
    }
    
    public function getLastError(){
        return $this->lastError;
    }
    
    public function saveTable($rows){
        if($this->pkField == null){
            $this->lastError = 'Se ha detectado una configuración incorrecta. '
                    . 'Comuníquese con el programador';
            return false;
        } else {
            if($this->rowsAreValid($rows)){
                $this->doSave($rows);
            } else {
                return false;
            }
        }
        return true;
    }
    
    private function rowsAreValid($rows){
        print_r($rows);
        foreach($rows as $r){
            foreach($this->requiredFields as $f){
                if(!isset($r[$f]) || isEmpty($r[$f])){
                    $this->lastError = sprintf('El campo %s es requerido', $f);
                    return false;
                }
            }
        }
        return true;
    }
    
    private function doSave($rows){
        foreach($rows as $r){
            if(!isset($r[$this->pkField]) || isEmpty($r[$this->pkField])){
                $this->doInsert($r);
            } else {
                $this->doUpdate($r);
            }
        }
    }
    
    private function doInsert($r){
        $insert = array();
        foreach($this->insertUpdateFields as $f){
            $insert[$f] = isset($r[$f]) ? $r[$f] : "";
        }
        $processedInsert = $this->processBeforeInsert($insert);
        $preparedForQuery = $this->prepareRowForQuery($processedInsert);
        $this->db->query_insert($this->table, $preparedForQuery);
    }

    private function doUpdate($r){
        $insert = array();
        foreach($this->insertUpdateFields as $f){
            $insert[$f] = isset($r[$f]) ? $r[$f] : "";
        }
        $processedInsert = $this->processBeforeUpdate($insert);
        $preparedForQuery = $this->prepareRowForQuery($processedInsert);
        $w = sprintf('%s="%s"', $this->pkField, $r[$this->pkField]);
        $this->db->query_update($this->table, $preparedForQuery, $w);
    }
    
    protected function prepareRowForQuery($row){
        $keys = array_keys($row);
        for($i = 0;count($keys) > $i; $i++){
            $row[$keys[$i]] = sqlValue($row[$keys[$i]], 'text');
        }
        return $row;
    }

    protected function processBeforeInsert($toInsert){
        return $toInsert;
    }
    
    protected function processBeforeUpdate($toUpdate){
        return $toUpdate;
    }
    
    protected function processPkFields($pkFields){
        return $pkFields;
    }
    
    protected function processBeforeShow($resultSet){
        return $resultSet;
    }
}
