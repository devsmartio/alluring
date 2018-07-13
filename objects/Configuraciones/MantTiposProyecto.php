<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantTiposProyecto
 *
 * @author baci5
 */
class MantTiposProyecto extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantTiposProyecto';
        $this->table = 'tipos_proyecto';
        $this->setTitle('Mantenimiento de clases de proyecto');
        $this->gridCols = array(
            'Id' => 'id_tipo_proyecto',
            'Nombre' => 'nombre',
            'Creado por' => 'usuario_creacion',
            'Fecha creación' => 'fecha_creacion'
        );
        
        $this->fields = array(
            new FastField('Id', 'id_tipo_proyecto', 'text', 'int', true, null, array(), FALSE, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true)
        );
    }
    
    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(self_escape_string(decode_email_address($user['ID'])), 'text');
        return $updateData;
    }
}
