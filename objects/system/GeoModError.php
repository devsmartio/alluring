<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GeoModError
 *
 * @author Bryan Cruz
 */

class GeoModError implements BaseMod {
    
    private static $selfInstance = null;
    
    public static function getMe(){
        if(self::$selfInstance == null){
            self::$selfInstance = new self();
        }
        return self::$selfInstance;
    }
    
    function __construct() {
        $this->instanceName = "GeoModError";
    }
    public function init() {
        $this->alertMe();
    }

    public function myJavascript() {
        
    }

    public function myStyle() {
        
    }

    public function myTitle() {
        return "Oh no!";
    }

    public function alertMe() {
        ?>
        <div class="alert alert-danger">UPS... Parece que la página que buscas no existe</div>
        <?php
    }
    
    public function showSideBar(){
        return true;
    }
}

?>
