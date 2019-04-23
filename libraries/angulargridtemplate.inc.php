<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of angulargridtemplate
 *
 * @author Bryan C
 */
abstract class AngularGridTemplate {
    protected $parent;
    
    abstract function renderTemplate();
    
    public function setParent(AngularGridColumn $parent){
        $this->parent = $parent;
    }
}
