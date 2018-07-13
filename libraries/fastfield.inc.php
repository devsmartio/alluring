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
class FastField {
    
     public $label;
     public $name;
     public $type;
     public $dataArray;
     public $required;
     public $placeholder;
     private $hasImageUpload;
     private $specialModel;
     private $parent;
     private $hasLabel;
	 public $valueType;
     public $storedFunc;
     public $isPk;
     public $isCreatablePk;
     
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
     function __construct($label, $name, $type, $valueType, $hasLabel = true, $specialModel = null, $dataArray = array(), $required = true, $storedFunc = null, $isPk = false, $isCreatablePk = false) {
        $this->label = $label;
        $this->name = $name;
        $this->type = $type;
        $this->dataArray = $dataArray;
        $this->required = $required;
        $this->valueType = $valueType;
        $this->specialModel = $specialModel;
        $this->parent = null;
        $this->hasLabel = $hasLabel;
        $this->storedFunc = $storedFunc;
        $this->isPk = $isPk;
        $this->isCreatablePk = $isCreatablePk;
     }
     
     public function setParent($parent){
         $this->parent = $parent;
     }
     
     public function build(){
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
            case "textarea": {
                $this->buildTextarea();
                break;
            }
            case "hidden": {
                    $this->buildHidden();
                    break;
            }
            default:{
                $this->buildInput();
                break;
            }
        }
     }
     
     private function buildInput(){
        ?>
        <div class='form-group form-group-sm'>
        <?php
            if($this->hasLabel){
                ?>
            <label for='<?php echo $this->name ?>' class='control-label'><?php echo $this->label ?></label>
                <?php
            }
            ?>
            <input
                ng-model="<?php echo ($this->specialModel == null ? 'lastSelected.' .  $this->name  : $this->specialModel)?>" 
                type="<?php echo $this->type ?>"
                <?php echo $this->required ? "required" : ""?>
                class='form-control'
                name='<?php echo $this->name ?>'
                placeholder='<?php echo $this->placeholder ?>'
                ng-readonly='(editMode&&<?php echo $this->isPk ? 'true' : 'false'?>)||(newMode&&<?php echo ($this->isPk && !$this->isCreatablePk) ? 'true' : 'false' ?>)'
            />
        </div>
        <?php
     }
	 
	 private function buildHidden(){
        ?>
        <div class='form-group form-group-sm'>
            <input
                ng-value="<?php echo ($this->specialModel == null ? 'lastSelected.' .  $this->name  : $this->specialModel)?>" 
                type="hidden"
                name='<?php echo $this->name ?>'
            />
        </div>
        <?php
     }
     
     private function buildTextarea(){
        ?>
        <div class='form-group form-group-sm'>
        <?php
            if($this->hasLabel){
                ?>
            <label for='<?php echo $this->name ?>' class='control-label'><?php echo $this->label ?></label>
                <?php
            }
            ?>
            <textarea
                ng-model="<?php echo ($this->specialModel == null ? 'lastSelected.' .  $this->name  : $this->specialModel)?>" 
                type="<?php echo $this->type ?>"
                <?php echo $this->required ? "required" : ""?>
                class='form-control'
                name='<?php echo $this->name ?>'
                placeholder='<?php echo $this->placeholder ?>'
            ></textarea>
        </div>
        <?php
     }
     
     private function buildDatepicker(){
        ?>
        <div class='form-group form-group-sm'>
        <?php
            if($this->hasLabel){
                ?>
            <label for='<?php echo $this->name ?>' class='control-label'><?php echo $this->label ?></label>
                <?php
            }
            ?>
            <input
                ng-model="<?php echo ($this->specialModel == null ? 'lastSelected.' .  $this->name  : $this->specialModel)?>" 
                type="<?php echo $this->type ?>"
                <?php echo $this->required ? "required" : ""?>
                class='form-control dateMe'
                name='<?php echo $this->name ?>'
                placeholder='<?php echo $this->placeholder ?>'
                ng-readonly='(editMode&&<?php echo $this->isPk ? 'true' : 'false'?>)||(newMode&&<?php echo ($this->isPk && !$this->isCreatablePk) ? 'true' : 'false' ?>)'
            />
        </div>
        <?php
     }
     
     private function buildTypeahead(){
         $this->buildInput();
     }
     
    private function buildCheck(){
        ?>
        <div class='checkbox'>
            <label>
                <input
                    ng-model="<?php echo ($this->specialModel == null ? 'lastSelected.' .  $this->name  : $this->specialModel)?>" 
                    type="<?php echo $this->type ?>"
                    <?php echo $this->required ? "required" : ""?>
                    name='<?php echo $this->name ?>'
                    placeholder='<?php echo $this->placeholder ?>'
                />
                <?php
                if($this->hasLabel){
                    echo $this->label;
                }
                ?>
            </label>
        </div>
        <?php
    }
     
     private function buildCombo(){
		?>
        <div class='form-group form-group-sm'>
        <?php
            if($this->hasLabel){
                ?>
            <label for='<?php echo $this->name ?>' class='control-label'><?php echo $this->label ?></label>
                <?php
            }
            ?>
            <select
                ng-model="<?php echo ($this->specialModel == null ? 'lastSelected.' .  $this->name  : $this->specialModel)?>" 
                type="<?php echo $this->type ?>"
                <?php echo $this->required ? "required" : ""?>
                class='form-control'
                name='<?php echo $this->name ?>'
                placeholder='<?php echo $this->placeholder ?>'
            >
				<option value="">-- Seleccione uno --</option>
				<?php
				foreach($this->dataArray as $k => $v){
					?>
				<option value="<?php echo $v ?>"><?php echo self_escape_string($k) ?></option>
					<?php
				}				
				?>
			</select>
        </div>
        <?php
     }
     
     public function getSqlValue(){
         return sqlValue(getParam($this->name), $this->valueType);
     }
     
     /**
      * 
      * @return mixed
      */
     public function getValue(){
         return getParam($this->name);
     }
     
     public function exists(){
         return !getParam($this->name, false) ? false : true; 
     }
     
    public function getDefaultValue($def = null){
        switch ($this->valueType){
            case "text":
                $value = 'NULL';
                break;
            case "int":
                $value = 0;
                break;
            case "float":
            case "double":
                $value = 0.00;
                break;
            case "date":
                $date = new DateTime();
                $value = $date->format('Y-m-d H:i:s');
                break;
            default:
                $value = $def;
                break;
        }
        return $value;
    }
}

?>
