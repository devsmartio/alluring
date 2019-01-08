<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GeoAdmin
 *
 * @author baci5
 */
class GeoAdmin implements BaseMod {
    private static $selfInstance = null;
    
    public static function getMe(){
        if(self::$selfInstance == null){
            self::$selfInstance = new self();
        }
        return self::$selfInstance;
    }
    
    private $type;
    
    function __construct() {
        $this->instanceName = "GeoModError";
        $this->type = getParam("mode");
    }
    public function init() {
        $this->alertMe();
    }

    public function myJavascript() {
        
    }

    public function myStyle() {
        
    }

    public function myTitle() {
        return "Aviso del administrador";
    }

    public function alertMe() {
        switch($this->type){
            default: {
                ?>
                <div class="alert alert-warning">Su licencia ha vencido. Comuníquese con el administrador del sistema.</div>
                <?php
                break;
            }
                
        }
    }

    public function showSideBar(){
        return true;
    }
}
