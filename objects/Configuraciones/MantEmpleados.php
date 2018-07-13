<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantEmpleados
 *
 * @author baci5
 */
class MantEmpleados extends FastMaintenance {
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantEmpleados';
        $this->table = 'empleados';
        $this->setTitle('Mantenimiento de empleados');
        $usuarios = $this->db->query_select('app_user');
        $sucursales = $this->db->query_select('sucursales');
        $users = array();
        $sucs = array();
        
        foreach($usuarios as $u){
            $users[self_escape_string($u['FIRST_NAME'])] = $u['ID'];
        }
        
        foreach($sucursales as $s){
            $sucs[self_escape_string($s['nombre'])] = $s['id_sucursal'];
        }
        
        $this->fields = array(
            new FastField('Id', 'id_empleado', 'hidden', 'int', TRUE, null, array(), false, null, true),
            new FastField('ID Interno', 'id_interno', 'text', 'text', true),
            new FastField('Nombres', 'nombres', 'text', 'text', true),
            new FastField('Apellidos', 'apellidos', 'text', 'text', true),
            new FastField('Usuario', 'id_usuario', 'select', 'text', TRUE, null, $users),
            new FastField('Sucursal', 'id_sucursal', 'select', 'int', true, null, $sucs)
        );
        
        $this->gridCols = array(
            'ID' => 'id_empleado',
            'Nombre' => 'nombres',
            'Apellidos' => 'apellidos',
            'Sucursal' => 'sucursal_label',
            'Creado' => 'fecha_creacion',
            'Creado por' => 'usuario_creacion',
        );
    }
    
    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(self_escape_string($user['FIRST_NAME']), 'text');
        return $updateData;
    }
    
    protected function specialProcessBeforeShow($rows){
        for($i = 0; count($rows) > $i; $i++){
            $cat = $this->db->query_select('sucursales', sprintf('id_sucursal=%s', $rows[$i]['id_sucursal']));
            $rows[$i]['sucursal_label'] = $cat[0]['nombre']; 
        }
        return sanitize_array_by_keys($rows, array('sucursal_label'));
    }
}