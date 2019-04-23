<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantEmpresa
 *
 * @author baci5
 */
class MantEmpresa extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantEmpresa';
        $this->table = 'empresa';
        $this->setTitle('Mantenimiento de empresas');
        
        $this->fields = array(
            new FastField('Id', 'id_empresa', 'text', 'int', TRUE, null, array(), false, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true)
        );
        $this->gridCols = array(
            'ID' => 'id_empresa',
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
