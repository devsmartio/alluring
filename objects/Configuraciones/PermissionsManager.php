<?php

class PermissionsManager extends FastModWrapper{
    
    function __construct() {
        parent::__construct();
        $this->setTitle('Manejo de Permisos');
    }
    public function myStyle() {
        parent::myStyle();
        ?>
        <link rel="stylesheet" href="media/css/PermissionsManager.css" />
        <?php
    }  
    protected function showMiddle() {
        include VIEWS . '/permissions_manager.phtml';
    }
    public function myJavascript() {
        ?>
        <script src="media/js/permissions_manager.js" type='text/javascript'></script>
        <?php
    } 
    
    public function getData(){
        $result = $this->db->query_select("app_profile");
        $i = 0;
        while(count($result) > $i){
            $result[$i]['ALLOWED'] = array();
            $result[$i]['DENIED'] = array();
            foreach($this->getPermissions($result[$i]['ID']) as $p){
                if($p['TAG'] == 1){
                    $result[$i]['ALLOWED'][] = $p;
                } elseif($p['TAG'] == 0){
                    $result[$i]['DENIED'][] = $p;
                }
            }
            $i++;
        }
        echo json_encode(array('data' => $result));
    }
    
    private function getPermissions($id){
        $permissionsSql = sprintf('select a.*, 1 as TAG from app_modules as a'
                . ' where id in ('
                . ' select FK_MODULE from app_profile_access where FK_PROFILE=%s'
                . ' )'
                . ' union '
                . 'select a.*, 0 as TAG from app_modules as a'
                . ' where id not in ('
                . ' select FK_MODULE from app_profile_access where FK_PROFILE=%s'
                . ')', $id, $id);
        $result = $this->db->queryToArray($permissionsSql);
        return sanitize_array_by_keys($result, array('NAME')); 
    } 
    
    public function savePermissions(){
        $r = 1;
        $msg = "";
        $data = inputStreamToArray();
        $data = $data['data'];
        foreach($data as $profile){            
            $this->db->query_delete('app_profile_access',  sprintf('FK_PROFILE="%s"',$profile['ID']));
            foreach($profile['ALLOWED'] as $module){
                try {
                $insert = array(
                    'FK_PROFILE' => sqlValue($profile['ID'], 'int'),
                    'FK_MODULE' => sqlValue($module['ID'], 'int'),
                );
                $this->db->query_insert('app_profile_access', $insert);
                $r = 1;                
                } catch (Exception $e) {
                    $r = 0;
                    $msg = '¡Error al guardar datos!';
                    var_dump($e->getTraceAsString());
                }
            }
        }  
        echo json_encode(array('result' => $r, 'msg' => $msg));        
    }
}



