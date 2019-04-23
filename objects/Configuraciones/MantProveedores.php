<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantProveedores
 *
 * @author baci5
 */
class MantProveedores extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantProveedores';
        $this->table = 'proveedor';
        $this->setTitle('Mantenimiento de provedores');
        
        $this->fields = array(
            new FastField('Id', 'id_proveedor', 'text', 'int', TRUE, null, array(), false, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true),
            new FastField('NIT', 'nit', 'text', 'text', TRUE, null, array(), false),
            new FastField('Fecha pago', 'fecha_pago', 'text', 'text', true, null, array(), false),
            new FastField('Telefono', 'telefono', 'text', 'int', true, null, array(), false),
            new FastField('Dirección', 'direccion', 'textarea', 'text', true, null, array(), false),
            new FastField('Es Internacional', 'es_internacional', 'select', 'int', true, null, ['SI' => 1, 'NO' => 0], false)
        );
        $this->gridCols = array(
            'ID' => 'id_proveedor',
            'Nombre' => 'nombre',
            'Telefono' => 'telefono',
            'Creado' => 'fecha_creacion',
            'Creado por' => 'usuario_creacion',
        );
    }
    
    protected function specialProcessBeforeShow($resultSet) {
        return sanitize_array_by_keys($resultSet, ['direccion', 'usuario_creacion']);
    }
    
    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(self_escape_string($user['FIRST_NAME']), 'text');
        return $updateData;
    }
}
