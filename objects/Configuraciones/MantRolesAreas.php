<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantRolesDepartamentos
 *
 * @author baci5
 */
class MantRolesAreas extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantRolesAreas';
        $this->table = 'perfiles_areas';
        $this->setTitle('Mantenimiento de perfiles y areas');
        $this->gridCols = array(
            'ID' => 'id_perfil_area',
            'Área' => 'nombre_area',
            'Perfil' => 'nombre_perfil',
            'Creado por' => 'usuario_creacion',
            'Fecha creación' => 'fecha_creacion'
        );
        
        $perfiles = $this->db->query_select('app_profile');
        $perfs = array();
        foreach($perfiles as $p){
            $perfs[self_escape_string($p['NAME'])] = $p['ID'];
        }
        
        $deptos = $this->db->query_select('areas');
        $dpts = array();
        foreach($deptos as $d){
            $dpts[self_escape_string($d['nombre'])] = $d['id_area'];
        }
        
        $this->fields = array(
            new FastField('ID', 'id_perfil_area', 'text', 'int', true, null, array(), FALSE, null, true),
            new FastField('Área', 'id_area', 'select', 'int', true, null, $dpts),
            new FastField('Perfil', 'id_perfil', 'select', 'int', true, null, $perfs)
        );
    }
    
    protected function specialValidation($fields, $r, $mess, $pkFields) {
        $w = "";
        if(isset($pkFields['id_perfil_area']) && !isEmpty($pkFields['id_perfil_area'])){
            $w = sprintf("id_perfil=%s and id_perfil_area != %s", $fields['id_perfil'], $pkFields['id_perfil_area']);
        } else {
            $w = sprintf("id_perfil=%s", $fields['id_perfil']);
        }
        $count = $this->db->query_select("perfiles_areas", $w);
        if(count($count) > 0){
            $r = 0;
            $mess = "Este perfil ya están asociado a un área. Revise su configuración";
        }
        return ['r' => $r, 'mess' => $mess];
    }
    
    protected function specialProcessBeforeShow($rows){
        $depts = Collection::get($this->db, 'areas');
        $profiles = Collection::get($this->db, 'app_profile');
        for($i = 0; count($rows) > $i; $i++){
            $currentDept = $depts->where(["id_area" => $rows[$i]['id_area']])->single();
            $rows[$i]['nombre_area'] = $currentDept['nombre'];
            $currentProf = $profiles->where(["ID" => $rows[$i]['id_perfil']])->single();
            $rows[$i]['nombre_perfil'] = $currentProf['NAME'];
        }
        return sanitize_array_by_keys($rows, ["nombre_area", "nombre_perfil", "usuario_creacion"]);
    }
    
    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(self_escape_string($user['FIRST_NAME']), 'text');
        return $updateData;
    }
    
    
}
