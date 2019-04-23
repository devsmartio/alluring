<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantMarcas
 *
 * @author baci5
 */
class MantMarcas extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantMarcas';
        $this->table = 'marca';
        $this->setTitle('Mantenimiento de Marcas');
        $this->gridCols = array(
            'Id' => 'id_marca',
            'Nombre' => 'nombre',
            'Prefijo' => 'prefijo',
            'Creado por' => 'usuario_creacion',
            'Fecha creación' => 'fecha_creacion'
        );
        
        $this->fields = array(
            new FastField('Id', 'id_marca', 'text', 'int', true, null, array(), FALSE, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true),
            new FastField('Prefijo', 'prefijo', 'text', 'text', true)
        );
    }
    
    protected function specialValidation($fields, $r, $mess, $pkFields) {
        $marca = new Entity($fields);
        $prefijo = strtoupper(str_replace("'", "", $marca->get('prefijo')));
        if(count($pkFields) > 0){
            if( count($this->db->query_select('marca', sprintf('prefijo="%s" and id_marca!=%s', $prefijo, $pkFields['id_marca']))) > 0){
                $r = 0;
                $mess = 'El prefijo ' . strtoupper($fields['prefijo']) . ' ya existe. Intente ingresar otro';
            }
        } else {
            if(count($this->db->query_select('marca', sprintf('prefijo="%s"', $prefijo))) > 0){
                $r = 0;
                $mess = 'El prefijo ' . strtoupper($fields['prefijo']) . ' ya existe. Intente ingresar otro';
            }
        }
        return array('r' => $r, 'mess' => $mess);
    }
    
    protected function specialProcessBeforeShow($rows){
        return sanitize_array_by_keys($rows, ['nombre','usuario_creacion']);
    }
    
    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['prefijo'] = strtoupper($updateData['prefijo']);
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(self_escape_string($user['FIRST_NAME']), 'text');
        return $updateData;
    }
    
    protected function specialProcessBeforeUpdate($updateData, $pkFields = array()) {
        $updateData['prefijo'] = strtoupper($updateData['prefijo']);
        return $updateData;
    }
}
