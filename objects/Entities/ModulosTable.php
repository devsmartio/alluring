<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ModulosEntity
 *
 * @author Edgar
 */
class ModulosTable{
    private $db;
    private static $sInstance = null;
    
    function __construct() {
        $this->db = DbManager::getMe();
    }
    
    public static function getMe(){
        if(self::$sInstance == null){
            self::$sInstance = new self();
        }
        return self::$sInstance;
    }
    
    public function getModules(){
        $user = AppSecurity::$UserData['data'];
        $sql = sprintf('SELECT *
                        FROM app_modules
                        WHERE ID IN (
                            SELECT FK_MODULE
                            FROM app_profile_access
                            WHERE FK_PROFILE=%s)', $user['FK_PROFILE']);
        $modules = $this->db->queryToArray($sql);
        return $modules;
    }
    
    public function getModulesByCategory(){
        $user = AppSecurity::$UserData['data'];
        $categories = $this->db->query_select('app_module_category');
        $i = 0;
        while (count($categories) > $i) {
            $sql = sprintf('SELECT *
                            FROM app_modules
                            WHERE FK_MODULE_CATEGORY=%s
                            AND ID IN (
                                SELECT FK_MODULE
                                FROM app_profile_access
                                WHERE FK_PROFILE=%s)', $categories[$i]['ID'], $user['FK_PROFILE']);
            $categories[$i]['MODULES'] = $this->db->queryToArray($sql);
            $i++;
        }
        return $categories;
    }
    
    public function loadAdmitted(){
        $sql = 'SELECT A.PATH AS MODULE, B.NAME AS CATEGORY
                FROM app_modules AS A
                LEFT JOIN app_module_category AS B ON A.FK_MODULE_CATEGORY=B.ID
                WHERE FK_MODULE_CATEGORY IN (
                    SELECT FK_MODULE_CATEGORY
                    FROM app_profile_access
                    WHERE FK_PROFILE="%s")
                ORDER BY LOAD_SEQ ASC';
        $modules = $this->db->queryToArray(sprintf($sql, $_SESSION[USER_TYPE]));
        return $modules;
    }
}
