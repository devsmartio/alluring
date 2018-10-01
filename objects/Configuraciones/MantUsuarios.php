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
            new FastField('Correo', 'EMAIL', 'text', 'text', true, null, array(), true),
            new FastField('Teléfono', 'PHONE', 'text', 'text', true, null, array(), true),
            new FastField('Perfil', 'FK_PROFILE', 'select', 'int', true, null, $prof, true),
            new FastField('Es vendedor?', 'is_seller', 'checkbox', 'text', true, null, [], false),
            new FastField('Nueva Contraseña ("Solo cambiar para reseteo")', 'PASSWORD', 'password', 'text')
        );
        $this->gridCols = array(
            'ID' => 'ID',
            'Nombres' => 'FIRST_NAME',
            'Apellidos' => 'LAST_NAME',
            'Perfil' => 'PROFILE_NAME',
            'Correo' => 'EMAIL',
            'Teléfono' => 'PHONE'
        );
    }

    protected function specialValidation($fields, $r, $mess, $pkFields)
    {
        $usuario = new Entity($fields);
        $id = str_replace("'", "", $usuario->get('ID'));
        $id = sqlValue(encode_email_address($id), 'text');
        $email = str_replace("'", "", $usuario->get('EMAIL'));
        if(!isEmpty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
            $r = 0;
            $mess = "El correo ingresado no tiene el formato correcto";
        }

        $result = $this->db->queryToArray(sprintf('select FIRST_NAME from app_user where ID=%s', $id));
        if (count($result) > 0) {
            $r = 0;
            $mess = 'El usuario ya existe, favor de corregir y volver a intentar';
        }
        return array('r' => $r, 'mess' => $mess);
    }
	
    protected function specialProcessBeforeInsert($insertData){
        //print_r($insertData);
        $date = new DateTime();
        $insertData['ID'] = str_replace("'", "", $insertData['ID']);
        $insertData['PASSWORD'] = str_replace("'", "", $insertData['PASSWORD']);
        $insertData['ID'] = sqlValue(encode_email_address($insertData['ID']), 'text');
        $insertData['CREATED'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $insertData['LAST_LOGIN'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $insertData['PASSWORD'] = sqlValue(md5($insertData['PASSWORD']), 'text');
        if(isset($insertData['is_seller'])){
            $insertData['is_seller'] = 1;
        } else {
            $insertData['is_seller'] = 0;
        }
        return $insertData;
    }
	
    protected function specialProcessBeforeUpdate($updateData, $pkFields = array()){
        //print_r($updateData);
        $updateData['PASSWORD'] = str_replace("'", "", $updateData['PASSWORD']);
        $pass = $this->getPass($pkFields['ID']);
        if($pass != $updateData['PASSWORD']){
                $updateData['PASSWORD'] = md5($updateData['PASSWORD']);
        }
        $updateData['PASSWORD'] = sqlValue($updateData['PASSWORD'], 'text');
        if(isset($updateData['is_seller']) && $updateData['is_seller'] == "'on'"){
            $updateData['is_seller'] = 1;
        } else {
            $updateData['is_seller'] = 0;
        }
        return $updateData;
    }
	
    protected function specialProcessBeforeShow($resultSet){
        $i = 0;
        $profiles = Collection::get($this->db, "app_profile")->select(["ID", "NAME"], true);

        while(count($resultSet) > $i){
            $profile = $profiles->where(["ID" => $resultSet[$i]['FK_PROFILE']]);
            $resultSet[$i]['PROFILE_NAME'] = $profile->any() ? $profile->single()['NAME'] : "Sin perfil asignado";
            $resultSet[$i]['ID'] = decode_email_address($resultSet[$i]['ID']); 
            $resultSet[$i]['is_seller'] = $resultSet[$i]['is_seller'] == 0 ? false : true;
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