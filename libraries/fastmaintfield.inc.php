<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FastMaintField
 *
 * @author Bryan Cruz
 */
class FastMaintField {
    
     public $label;
     public $name;
     public $type;
     public $input_w;
     public $label_w;
     public $dataArray;
     public $required;
     public $placeholder;
     private $hasImageUpload;
     private $justImage;
     private $specialModel;
     private $parent;
     private $hasLabel;
     private $hasTag;
     
     /**
      * 
      * @param type $label
      * @param type $name
      * @param type $type
      * @param type $placeholder
      * @param type $hasLabel
      * @param type $input_w
      * @param type $label_w
      * @param type $justImg
      * @param type $hasImageUpload
      * @param type $specialModel
      * @param type $dataArray
      * @param type $required
      * @param type $hasTag
      */
     function __construct($label, $name, $type, $placeholder, $hasLabel = false, $input_w = "39", $label_w = "100px", $justImg = false, $hasImageUpload = false, $specialModel = null, $dataArray = array(), $required = false, $hasTag = true) {
         $this->label = $label;
         $this->name = $name;
         $this->type = $type;
         $this->input_w = $input_w;
         $this->label_w = $label_w;
         $this->dataArray = $dataArray;
         $this->hasImageUpload = $hasImageUpload;
         $this->required = $required;
         $this->placeholder = $placeholder;
         $this->justImage = $justImg;
         $this->specialModel = $specialModel;
         $this->parent = null;
         $this->hasLabel = $hasLabel;
         $this->hasTag = $hasTag;
     }
     
     public function setParent($parent){
         $this->parent = $parent;
     }
     
     public function build(){
         ?>
        <div style="margin-bottom: 5px;" class="control_group">
            <?php 
            switch($this->type){
                case "checkbox": {
                    $this->buildCheck();
                    break;
                }
                case "select": {
                    $this->buildCombo();
                    break;
                }
                case "datepicker": {
                    $this->buildDatepicker();
                    break;
                }
                case "typeahead": {
                    $this->buildTypeahead();
                    break;
                }
                default:{
                    $this->buildInput();
                    break;
                }
            }
            ?>
        </div>
         <?php
     }
     
     private function buildInput(){
        if(!$this->justImage){
            ?>
            <input
                ng-model="<?php echo ($this->specialModel == null ? 'modData.' .  $this->name  : $this->specialModel)?>" 
                type="<?php echo $this->type ?>"
                <?php echo $this->required ? "required" : ""?>
                size="<?php echo $this->input_w ?>"
                class='modField'
                name='<?php echo $this->name ?>'
                placeholder='<?php echo $this->placeholder ?>'
            />
            <?php
        }
        if($this->hasImageUpload || $this->justImage){
            $this->setUploadModal();
        }
        if($this->hasLabel){
            ?>
            <label for='<?php echo $this->name ?>'><?php echo $this->label ?></label>
            <?php
        }
     }
     
     private function buildTypeahead(){
        if(!$this->justImage){
            ?>
            <input
                typeahead="row for row in <?php echo $this->dataArray ?> | filter:$viewValue | limitTo:5"
                ng-model="<?php echo ($this->specialModel == null ? 'modData.' .  $this->name  : $this->specialModel)?>" 
                type="<?php echo $this->type ?>"
                <?php echo $this->required ? "required" : ""?>
                size="<?php echo $this->input_w ?>"
                class='modField'
                name='<?php echo $this->name ?>'
                placeholder='<?php echo $this->placeholder ?>'
            />
            <?php
        }
        if($this->hasImageUpload || $this->justImage){
            $this->setUploadModal();
        }
        if($this->hasLabel){
            ?>
            <label for='<?php echo $this->name ?>'><?php echo $this->label ?></label>
            <?php
        }
     }
     
     private function buildDatepicker(){
        if(!$this->justImage){
            ?>
            <input
                ng-model="<?php echo ($this->specialModel == null ? 'modData.' .  $this->name  : $this->specialModel)?>" 
                type="<?php echo $this->type ?>"
                <?php echo $this->required ? "required" : ""?>
                size="<?php echo $this->input_w ?>"
                class='modField dateMe'
                name='<?php echo $this->name ?>'
                placeholder='<?php echo $this->placeholder ?>'
                readonly
            />
            <?php
        }
        if($this->hasImageUpload || $this->justImage){
            $this->setUploadModal();
        }
        if($this->hasLabel){
            ?>
            <label for='<?php echo $this->name ?>'><?php echo $this->label ?></label>
            <?php
        }
     }
     
     private function buildCheck(){
         if(!$this->justImage){
            ?>
            <input
                ng-checked="<?php echo ($this->specialModel == null ? 'modData.' .  $this->name  . '==1': $this->specialModel   . '==1')?>" 
                ng-model="<?php echo ($this->specialModel == null ? 'modData.' .  $this->name  : $this->specialModel)?>"
                type="<?php echo $this->type ?>"
                ng-false-value="0"
                ng-true-value="1"
                <?php echo $this->required ? "required" : ""?>
                name='<?php echo $this->name ?>'
            />
            <?php
        }
        if($this->hasImageUpload || $this->justImage){
            $this->setUploadModal();
        }
        if($this->hasLabel){
            ?>
            <label for='<?php echo $this->name ?>'><?php echo $this->label ?></label>
            <?php
        }
     }
     
     private function buildCombo(){
        if(!$this->justImage){
            ?>
            <select
                ng-model="<?php echo ($this->specialModel == null ? 'modData.' .  $this->name  : $this->specialModel)?>" 
                type="<?php echo $this->type ?>"
                <?php echo $this->required ? "required" : ""?>
                name='<?php echo $this->name ?>'
                class='modField'
            >
                <option value=""></option>
                <?php 
                foreach ($this->dataArray as $k => $v) {
                    ?>
                <option value='<?php echo $v ?>'><?php echo $k ?></option>
                    <?php
                }
                ?>
            </select>
            &nbsp;
            <span class=''><?php echo $this->placeholder ?></span>
            <?php
        }
        if($this->hasImageUpload || $this->justImage){
            $this->setUploadModal();
        }
     }
     
     public function buildTag($spanWidth = "175px"){
         if($this->hasTag){
             ?>
            <div
                <?php 
                if($this->type == 'checkbox'){
                    if (!$this->isJustImage()) : 
                        ?>
                        ng-hide='<?php echo ($this->specialModel == null ? 'modData.' .  $this->name . '==0' : $this->specialModel . '==0'); ?>'
                        <?php  
                    endif;
                } else {
                    if (!$this->isJustImage()) : 
                        ?>
                        ng-hide="<?php echo ($this->specialModel == null ? 'modData.' .  $this->name . '.length==0' : $this->specialModel . '.length==0')?>"  
                        <?php  
                    endif;
                }
                ?>
                style="margin-bottom: 5px; border-bottom: 1px solid #113F5C" class="control_group">
                <?php 
                if($this->type != 'checkbox'){
                    if(!$this->justImage){
                        ?>
                        <span   
                           type="<?php echo $this->type ?>"
                           <?php echo $this->required ? "required" : ""?> 
                           style="color: #113F5C;
                                  font-size: 25px;
                                  display: inline-block;
                                  width: <?php echo $spanWidth ?>;
                                  margin-right: 5px;">
                            <?php
                            echo ($this->specialModel == null ? '{{modData.' .  $this->name . '}}' : '{{' . $this->specialModel . '}}');
                            ?>
                        </span>
                        <?php
                    }
                }
                if($this->hasImageUpload || $this->justImage){
                    $this->setShowerModal();
                }
                if($this->hasLabel){
                    ?>
                    <label>
                        <?php 
                        if($this->type != 'checkbox'):
                            ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php 
                        endif;
                        echo $this->label ?>
                    </label>
                    <?php
                }
                ?>
            </div>
             <?php
         }
     }
     
     private function setUploadModal(){
         ?>
        <img class="pointMe" alt="subir" src="media/img/icono_subirimagen.png" ng-click="getUploader('<?php echo $this->name ?>', '<?php echo $this->label ?>')" />
         <?php
     }
     
     private function setShowerModal(){
         ?>
        <img class="pointMe" alt="subir" src="media/img/icono_verimagen.png" ng-click="getUploader('<?php echo $this->name ?>', '<?php echo $this->label ?>')" />
         <?php
     }
     
     public function getSqlValue(){
         return sqlValue(getParam($this->name), "text");
     }
     
     /**
      * 
      * @return mixed
      */
     public function getValue(){
         return getParam($this->name);
     }
     
     public function isJustImage(){
         return $this->justImage;
     }
}

?>
