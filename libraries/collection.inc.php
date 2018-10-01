<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Collection
 *
 * @author baci5
 */
class Collection {
    
    private $items;
    
    public static function get(DbManager $db, $table, $where = null, $orderBy = null, $orderByType = 'DESC'){
        $items = $db->query_select($table, $where, $orderBy, null, null, $orderByType);
        return new static($items);
    }
    
    function __construct($items = array()){
        $this->items = is_array($items) ? $items : array();
    }
    
    public function where($conditions){
        $result = [];
        for($i = 0; count($this->items) > $i; $i++){
            $toAdd = true;
            foreach($conditions as $f => $v){
                if($this->items[$i][$f] != $v){
                    $toAdd = false;
                    break;
                } 
            }
            if($toAdd){
                $result[] = $this->items[$i];
            }
        }
        return new static(array_values($result));
    }
    
    public function toSelectList($idField, $nameField){
        $result = [];
        for($i = 0; count($this->items) > $i; $i++){
            $result[self_escape_string($this->items[$i][$nameField])] = $this->items[$i][$idField];
        }
        return $result;
    }
    
    public function select($fieldsToSelect, $sanitizeAll = false, $toSanitize = array()){
        $resultado = array();
        foreach($this->items as $i){
            $item = array();
            foreach($i as $k => $v){
                if(in_array($k, $fieldsToSelect)){
                    $item[$k] = $sanitizeAll ? self_escape_string($v) : $v;
                }
            }
            if(count($item) > 0){
                $resultado[] = $item;
            }
        }
        return new Collection(sanitize_array_by_keys($resultado, $toSanitize));
    }
    
    public function selectValue($field, $sanitize = false){
        
    }
    
    public function single(){
        if(count($this->items) > 0){
            return $this->items[0];
        }
        return array();
    }
    
    public function toArray(){
        return $this->items;
    }
    
    public function toJSON(){
        echo json_encode($this->items);
    }

    public function any(){
        return count($this->items) > 0;
    }
}
