<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of typeaheadgen
 *
 * @author GeoDeveloper
 */
class TypeaheadGen {
    private static $selfInstance = null;
    private $db;
    
    public static function getMe(){
        if(self::$selfInstance == null){
            self::$selfInstance = new self();
        }
        return self::$selfInstance;
    }
    
    function __construct() {
        $this->db = DbManager::getMe();
    }
    
    public function getTypeahead($tag){
        switch($tag){
            case 'gentil':{
                echo $this->genTypeahead('PER_NATION', 'user_personal');
                break;
            }
            case 'prof':{
                echo $this->genTypeahead('MY_PROFESSION', 'user_professional');
                break;
            }
            case 'degrees':{
                echo $this->genTypeahead('DEGREE', 'user_academics');
                break;
            }
            case 'schools':{
                echo $this->genTypeahead('INSTITUTION', 'user_academics');
                break;
            }
            case 'positions':{
                echo $this->genTypeahead('POSITION', 'user_professional_exp');
                break;
            }
            case 'religion':{
                echo $this->genTypeahead('PER_RELIGION', 'user_personal');
                break;
            }
        }
    }
    
    private function genTypeahead($field, $table){
        $rows = $this->db->queryToArray(sprintf('SELECT DISTINCT %s FROM %s', $field, $table));
        $resultSet = array();
        foreach($rows as $row){
            $resultSet[] = self_escape_string($row[$field]);
        }
        return json_encode($resultSet);
    }
}

?>
