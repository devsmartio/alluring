<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileManager
 *
 * @author Bryan Cruz
 */
abstract class FileManager {
    private $db;
    private $user;
    private $tempPath;
    private $currFile;
    private $table;
    private $specialDataToSave = array();
    protected $r;
    protected $msg;
    protected $allowedExt = array();
    private static $NO_MOVE = 'no_move';
    private static $ALREADY_EXISTS = 'already_ex';
    private static $EXCEEDS = 'exceeds';
    private static $NOT_VALID = 'not_valid';
    private static $UNKNOWN = 'unknown';
    private static $SUCCESS = 'fileUploadSuccess';
    
    function __construct() {
        $this->db = DbManager::getMe();
        $this->user = AppSecurity::$UserData['data'];
        $this->r = 1;
        $this->msg = "";
    }
    
    protected function setUploadTable($table){
        $this->table = $table;
    }
    
    private function setTempPath($tempPath){
        $this->tempPath = $tempPath;
        return $this;
    }
    
    private function setCurrentFile($filename){
        $this->currFile = escape_filename($filename);
    }
    
    public function setSpecialDataToSave($fields){
        $this->specialDataToSave = $fields;
    }

    public function sortUpload($file, $hasResponse = true, $isReturnable = false){
        $this->r = 1;
        try {
            if($this->isValidFile($file)){
                $this->setTempPath($file['tmp_name']);
                $this->setCurrentFile($file['name']);
                $this->handleFile();
            }
        } catch(Exception $e){
            error_log($e->getTraceAsString());
            $this->r = 0;
            $this->msg = self::$UNKNOWN;
        }
        if($hasResponse){
            $this->throwResponse();
        } elseif($isReturnable){
            return $this->throwResponse();
        }
    }
    
    private function upload(){
         $uploadPath = PATH_UPLOAD_GENERAL . DS . $this->currFile;
         if(!file_exists($uploadPath)){
             if(!move_uploaded_file($this->tempPath, $uploadPath)){
                $this->r = 0;
                $this->msg = self::$NO_MOVE;
                return false;
            }
         } else {
            $this->r = 0;
            $this->msg = self::$ALREADY_EXISTS;
            return false;
         }
         return true;
    }
    
    private function handleFile(){
        if($this->upload()){
            $insert = array(
                'NAME' => sqlValue($this->currFile, 'text'),
                'FK_USER' => sqlValue($this->user['ID'], 'text')
            );
            $insert = $this->processSpecialData($insert);
            $this->db->query_insert($this->table, $insert);
            $this->msg = self::$SUCCESS;
        }
    }
    
    private function processSpecialData($insert){
        if(count($this->specialDataToSave) > 0){
            foreach ($this->specialDataToSave as $key => $value) {
                $insert[$key] = sqlValue($value, 'text');
            }
        }
        return $insert;
    }
    
    private function isValidFile($file){
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if($file['size'] > MAX_FILE_SIZE){
            $this->r = 0;
            $this->msg =  self::$EXCEEDS;
            return false;
        } elseif (!in_array(strtolower($ext), $this->allowedExt)) {
            $this->r = 0;
            $this->msg = self::$NOT_VALID;
            return false;
        }
        return true;
    }
    
    protected function throwResponse(){
        echo json_encode(array('result' => $r, 'msg' => $this->msg));
    }
    
    protected function throwJsonResponse(){
        echo json_encode(array('result' => $this->r, 'msg' => $this->msg));
    }
    
    public function removeFiles($conditions = array()){
        try {
            $path = PATH_UPLOAD_GENERAL . DS;
            foreach($this->getFiles($conditions) as $file){
                if(!unlink($path . $file['NAME'])){
                    $this->r = 0;
                    $this->msg = 'No se puede remover el archivo solicitado. Verifique que exista y no esté en uso';
                } else {
                    $this->removeRecord($file['ID']);
                }
            }
        } catch (Exception $e){
            $this->r = 0;
            $this->msg = 'Error desconocido, contacte a soporte';
            error_log($e->getTraceAsString());
        }
        $this->throwJsonResponse();
    }
    
    private function removeRecord($id){
        $this->db->query_delete($this->table, sprintf('ID="%s"', $id));
        $this->msg = 'Eliminado con éxito';
    }
    
    public function getFiles($conditions = array()){
        $w = null;
        foreach($conditions as $key => $value){
            $w = $w == null ? "" : sprintf("%s and ", $w);
            $w.= sprintf('%s="%s"', $key, $value);
        }
        $resultSet = $this->db->query_select($this->table, $w);
        return $resultSet;
    }
    
    public function getErr($id){
        switch ($id){
            case self::$NO_MOVE:{
                $err = "El archivo no se encuentra disponible para subir. Por favor intente de nuevo";
                break;
            }
            case self::$ALREADY_EXISTS:{
                $err = "El archivo que desea subir ya existe. Si desea subirlo de todas formas, cambie el nombre";
                break;
            }
            case self::$EXCEEDS:{
                $err = "El archivo excede el máximo permitido";
                break;
            }
            case self::$NOT_VALID:{
                $err = sprintf("No es un tipo de extensión válido: %s", $this->getValidExtensionString()); 
                break;
            }
            case self::$SUCCESS:{
                $err = "Subido con éxito";
                break;
            }
            default: {
                echo 'default';
                return;
            }
        }
        ?>
        <div class="alert alert-dismissible alert-info" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">x</span>
                <span class="sr-only">Cerrar</span>
            </button>
            <?php echo $err ?>
        </div>
        <?php
    }
    
    private function getValidExtensionString(){
        $validExt = "";
        foreach ($this->allowedExt as $ext){
            $validExt = !isEmpty($validExt) ? "$validExt, " : "(";
            $validExt.= $ext;
        }
        $validExt.= count($this->allowedExt) > 0 ? ")" : "No hay extensiones válidas por el momento";
        return $validExt;
    }
    
    public function updateFile($file, $keysToUpdate){
        foreach($keysToUpdate as $k){
            $update[$k] = sqlValue($file[$k], 'text');
        }
        $this->db->query_update($this->table, $update, sprintf('ID=%s', $file['ID']));
    }
}