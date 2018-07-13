<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GridColumn
 *
 * @author baci5
 */
class GridColumn {
    
    private $field;
    private $displayName;
    
    function __construct($field, $displayName){
        $this->field = $field;
        $this->displayName = $displayName;
    }
    
    public function getField(){
        return $this->field;
    }
    
    public function prepSerialize(){
        $map = get_object_vars($this);
        return $map;
    }
}
