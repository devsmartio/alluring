<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SelectTemplate
 *
 * @author Edgar
 */
class SelectTemplate extends AngularGridTemplate{
    
    private $data;
    private $val;
    private $label;
    private $isEntityData;
    private $entity;
    
    function __construct($val, $label, $entity, $isEntityData = false, $data = array()) {
        $this->val = $val;
        $this->label = $label;
        $this->isEntityData = $isEntityData;
        if($this->isEntityData){
            $this->entity = $entity instanceof MaintenanceTable ? $entity : null;
            $this->data = $this->entity->getAll(true);
        } else {
            $this->data = $data;
        }
    }
    
    public function renderTemplate() {
        ?>
        <select ng-model="row.entity.<?php echo $this->parent->getField() ?>">
            <?php 
            foreach($this->data as $option):
                ?>
            <option value="<?php echo $option[$this->val] ?>"><?php echo $option[$this->label] ?></option>
                <?php
            endforeach;
            ?>
        </select>
        <?php
    }
}
