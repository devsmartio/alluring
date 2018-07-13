<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of angulargridcolumn
 *
 * @author Bryan C.
 */
class AngularGridColumn {
    private $field;
    private $displayName;
    private $enableCellEdit;
    private $width;
    private $hasCustomTemp;
    private $cellTemplate;
    public $isTableColumn;
    
    /**
     * 
     * @param type $field
     * @param type $displayName
     * @param type $enableCellEdit
     * @param type $size
     * @param type $hasCustomTemp
     * @param AngularGridTemplate $cellTemplate
     * @param type $isTableColumn
     */
    function __construct($field, $displayName, $enableCellEdit = true, $size = '*', 
            $hasCustomTemp = false, $cellTemplate = null, $isTableColumn = true) {
        $this->field = $field;
        $this->displayName = $displayName;
        $this->enableCellEdit = (bool) $enableCellEdit;
        $this->width = $size;
        $this->hasCustomTemp = $hasCustomTemp;
        if($this->hasCustomTemp){
            if($cellTemplate instanceof AngularGridTemplate){
                ob_start();
                $cellTemplate->setParent($this);
                $cellTemplate->renderTemplate();
                $this->cellTemplate = ob_get_clean();
            }
        }
        $this->isTableColumn = $isTableColumn;
    }
    
    /**
     * Returns custom template object
     * @return AngularGridTemplate
     */
    public function getTemplate(){
        return $this->cellTemplate;
    }
    /**
     * Setter for custom size
     * @param String $size
     */
    public function setCustomSize($size){
        $this->width = $size;
    }
    
    /**
     * Returns TRUE if it is an editable column. FALSE if not
     * @return boolean
     */
    public function isEditable(){
        return (bool) $this->enableCellEdit;
    }
    
    /**
     * Return TRUE if its custom col, FALSE if not
     * @return boolean
     */
    public function isCustomCol(){
        return (bool) $this->hasCustomTemp;
    }

    /**
     * Returns array of object variables for JSON serialization
     * @return array
     */
    public function prepSerialize(){
        $map = get_object_vars($this);
        if(!$this->hasCustomTemp){
            unset($map['cellTemplate']);
        }
        unset($map['hasCustomTemp']);
        return $map;
    }
    
    /**
     * Returns field name
     * @return String
     */
    public function getField(){
        return $this->field;
    }
}
