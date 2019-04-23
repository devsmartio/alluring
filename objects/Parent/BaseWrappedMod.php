<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseWrappedMod
 *
 * @author Bryan Cruz
 */
abstract class BaseWrappedMod implements BaseMod{
    
    private $fields;
    public $tabLabel;
    protected $sampleImg;
    public $id;
    public $icon;
    public $iconDesc;
    protected $user;
    protected $db;
    protected $table;
    protected $fieldPack;
    public $reqLevel;
    
    function __construct() {
        $this->icon = "";
        $this->iconLocked = "";
        $this->iconDesc = "";
        if(count($this->fields) > 0){
            $this->setArrParent($this->fields);
        };
        $user = AppSecurity::$UserData;
        $this->user = $user['data'];
        $this->db = DbManager::getMe();
        $this->gallery = GalleryManager::getMe();
    }
    
    public function showModule(){
        ?>
        <div class="alert alert-info">Estará listo pronto</div>
        <?php
    }
    
    public function init() {
        $this->showPage();
    }
    
    protected function setArrParent($fields){
        foreach($fields as $fd){
            if($fd instanceof FastMaintField){
                $fd->setParent($this);
            }
        }
    }
    
    public function myIcon(){
        ?>
        <img class='pointMe moduleIcon' ng-click="setMod('<?php echo $this->id ?>')" src="media/img/<?php echo $this->icon ?>" />
        <?php
    }

    public function myJavascript() {}

    public function myStyle() {}

    public function myTitle() {}    
    
    protected function myModal($sampleImg){
        ?>
        <div class="modal fade" id="uploader<?php echo $this->id ?>">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style='background-color: <?php echo COHEX ?>; color: white'>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                        <h4 class="modal-title">{{!editMode ? "Galería" : "Suba su imagen"}}</h4>
                    </div>
                    <div class="modal-body">
                        <img style='width: 100%; height: auto' src="media/img/<?php echo $sampleImg ?>" ng-show='!editMode' alt="Su imagen va aquí"/>
                        <form ng-show='editMode' enctype='multipart/form-data'>
                            <input type='file' name='myImage' />
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <?php
    }
    
    public function save(){
        $mode = getParam("mode");
        switch($mode){
            case "new" : {
                $this->doNew();
                break;
            }
            case "upd" : {
                $this->doUpd();
                break;
            }
        }
    }
    
     protected function doUpd(){
        $update = array();
        if($this->fieldsAreValid()){
            foreach($this->fieldPack as $pack){
                foreach($pack as $fld){
                    if($fld instanceof FastMaintField){
                        if(!$fld->isJustImage()){
                            if($fld->type == 'checkbox'){
                                if($fld->getValue() == 'on'){
                                    $update[$fld->name] = 1;
                                } else {
                                    $update[$fld->name] = 0;
                                }
                            } else {
                                $update[$fld->name] = $fld->getSqlValue();
                            }
                        }
                    }
                }
            }
            try {
                $this->db->query_update($this->table, $update, sprintf("FK_USER='%s'", $this->user['ID']));
                echo json_encode(array("result" => 1));
            } catch (Exception $e){
                echo $e->getTraceAsString();
            }
        } else {
            $fields = $this->getNonValidFields();
            echo json_encode(array("result" => 0, "error" => $fields));
        }
    }
    
    protected function doNew(){
        $insert = array();
        if($this->fieldsAreValid()){
            foreach($this->fieldPack as $pack){
                foreach($pack as $fld){
                    if($fld instanceof FastMaintField){
                        if(!$fld->isJustImage()){
                            if($fld->type == 'checkbox'){
                                if($fld->getValue() == 'on'){
                                    $insert[$fld->name] = 1;
                                } else {
                                    $insert[$fld->name] = 0;
                                }
                            } else {
                                $insert[$fld->name] = $fld->getSqlValue();
                            }
                        }
                    }
                }
            }
            try {
                $insert['FK_USER'] = sqlValue($this->user['ID'], "text");
                $this->db->query_insert($this->table, $insert);
                echo json_encode(array("result" => 1));
            } catch (Exception $e){
                echo $e->getTraceAsString();
            }
        } else {
            $fields = $this->getNonValidFields();
            echo json_encode(array("result" => 0, "error" => $fields));
        }
    }
    
    protected function fieldsAreValid(){
        foreach($this->fieldPack as $pack){
            foreach($pack as $f){
                if($f->required){
                    if($f->type == "text" && isEmpty($f->getValue())){
                        return false;
                    } else if($f->type == "select"){
                        if($f->getValue() == "" || $f->getValue() == null){
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }
    
    protected function getNonValidFields(){
        $fields = "";
        foreach($this->fieldPack as $pack){
            foreach($pack as $f){
                if($f->required){
                    if($f->type == "text" && isEmpty($f->getValue())){
                        if(!isEmpty($fields)){
                            $fields = "$fields, ";
                        }
                        $fields.= $f->label;
                    } else if($f->type == "select") {
                        if($f->getValue() == "" || $f->getValue() == null){
                            if(!isEmpty($fields)){
                                $fields = "$fields, ";
                            }
                            $fields.= $f->label;
                        }
                    }
                }
            }
        }
        return "¡Ups! Aún hay campos requeridos que no se han llenado... Presiona anterior o siguiente para revisar que todo esté lleno";
    } 
    
    public function alertMe() {}
    
    public function manageUploads(){
        $imgTag = getParam('imgTag');
        $row = getParam('row');
        $this->gallery->sortUpload($imgTag, $_FILES['file'], $row);
    }
    
    public function removeImg(){
        $id = getParam('id');
        $this->gallery->removeImg($id);
    }
    
    public function isDeletedRow($row, $newRows, $pkField){
        foreach ($newRows as $new) {
            if($row[$pkField] == $new[$pkField]){
                return false;
            }
        }
        return true;
    }
    
    public function getTypeahead(){
        $tag = getParam('tag');
        TypeaheadGen::getMe()->getTypeahead($tag);
    }
}

?>
