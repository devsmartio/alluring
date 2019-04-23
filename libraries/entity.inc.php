<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of entity
 *
 * @author baci5
 */
class Entity {
    private $entityFields;
    
    function __construct($entityFields){
        if(is_array($entityFields)){
            $this->entityFields = $entityFields;
        } else {
            throw new Exception('The entity must be an array');
        }
    }
    
    public function exists($key){
        return array_key_exists($key, $this->entityFields);
    }
    
    public function existsAndNotEmpty($key){
        return $this->exists($key) && !isEmpty($this->entityFields[$key]);
    }
    
    public function get($key){
        if($this->exists($key)){
            return $this->entityFields[$key];
        }
        return false;
    }
    
    public function getOrDefault($key, $default){
        $get = $this->get($key);
        if($get === false){
            return $default;
        }
        return $get;
    }
    
    public function toArray(){
        return $this->entityFields;
    }
}
