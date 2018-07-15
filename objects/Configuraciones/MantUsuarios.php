<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantCentrosConsumo
 *
 * @author Bryan Cruz
 */
class MantUsuarios extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantUsuarios';
        $this->table = 'app_user';
        $this->setTitle('Mantenimiento de Usuarios');
        $profiles = $this->db->query_select('app_profile');
        $prof = array();
        foreach($profiles as $p){
            $prof[self_escape_string($p['NAME'])] = $p['ID'];
        }
        $this->fields = array(
            new FastField('Usuario', 'ID', 'text', 'text', true, null, array(), true, null, true, true),
            new FastField('Nombres', 'FIRST_NAME', 'text', 'text', true, null, array(), true, null, false, false),
            new FastField('Apellidos', 'LAST_NAME', 'text', 'text'),
            new FastField('Perfil', 'FK_PROFILE', 'select', 'int', true, null, $prof),
            new FastField('Nueva Contraseña ("Solo cambiar para reseteo")', 'PASSWORD', 'password', 'text')
        );
        $this->gridCols = array(
            'ID' => 'ID',
            'Nombres' => 'FIRST_NAME',
            'Apellidos' => 'LAST_NAME',
        );
    }

    protected function specialValidation($fields, $r, $mess, $pkFields)
    {
        $usuario = new Entity($fields);
        $id = str_replace("'", "", $usuario->get('ID'));
        $id = sqlValue(encode_email_address($id), 'text');

        $result = $this->db->queryToArray(sprintf('select FIRST_NAME from app_user where ID=%s', $id));
        if (count($result) > 0) {
            $r = 0;
            $mess = 'El usuario ya existe, favor de corregir y volver a intentar';
        }
        return array('r' => $r, 'mess' => $mess);
    }
	
    protected function specialProcessBeforeInsert($insertData){
        $date = new DateTime();
        $insertData['ID'] = str_replace("'", "", $insertData['ID']);
        $insertData['PASSWORD'] = str_replace("'", "", $insertData['PASSWORD']);
        $insertData['ID'] = sqlValue(encode_email_address($insertData['ID']), 'text');
        $insertData['CREATED'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $insertData['LAST_LOGIN'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $insertData['PASSWORD'] = sqlValue(md5($insertData['PASSWORD']), 'text');
        return $insertData;
    }
	
    protected function specialProcessBeforeUpdate($updateData, $pkFields = array()){
        $updateData['PASSWORD'] = str_replace("'", "", $updateData['PASSWORD']);
        $pass = $this->getPass($pkFields['ID']);
        if($pass != $updateData['PASSWORD']){
                $updateData['PASSWORD'] = md5($updateData['PASSWORD']);
        }
        $updateData['PASSWORD'] = sqlValue($updateData['PASSWORD'], 'text');
        return $updateData;
    }
	
    protected function specialProcessBeforeShow($resultSet){
        $i = 0;
        while(count($resultSet) > $i){
            $resultSet[$i]['ID'] = decode_email_address($resultSet[$i]['ID']); 
            $i++;
        }
        return $resultSet;
    }
	
    protected function processPkFields($pkFields){
        $pkFields['ID'] = encode_email_address($pkFields['ID']);
        return $pkFields;
    }
	
    private function getPass($id){
        $result = $this->db->queryToArray(sprintf('select PASSWORD from app_user where ID="%s"', $id));
        if(count($result) > 0){
            return $result[0]['PASSWORD'];
        }
    }
}

?>