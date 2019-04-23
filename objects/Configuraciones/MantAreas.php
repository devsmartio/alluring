<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantDepartamentos
 *
 * @author baci5
 */
class MantAreas extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantAreas';
        $this->table = 'areas';
        $this->setTitle('Mantenimiento de areas');
        
        $this->fields = array(
            new FastField('Id', 'id_area', 'text', 'int', TRUE, null, array(), false, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true)
        );
        $this->gridCols = array(
            'ID' => 'id_area',
            'Nombre' => 'nombre',
            'Creado' => 'fecha_creacion',
            'Creado por' => 'usuario_creacion'
        );
    }
    
    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(self_escape_string($user['FIRST_NAME']), 'text');
        return $updateData;
    }
}
