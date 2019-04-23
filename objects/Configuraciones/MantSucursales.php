<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantSucursales
 *
 * @author baci5
 */
class MantSucursales extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantSucursales';
        $this->table = 'sucursales';
        $this->setTitle('Mantenimiento de sucursales');
        $empresas = $this->db->query_select('empresa');
        $emps = array();
        foreach($empresas as $e){
            $emps[self_escape_string($e['nombre'])] = $e['id_empresa'];
        }
        $this->fields = array(
            new FastField('Id', 'id_sucursal', 'text', 'int', TRUE, null, array(), false, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true),
            new FastField('Empresa', 'id_empresa', 'select', 'int', true, null, $emps)
        );
        $this->gridCols = array(
            'ID' => 'id_sucursal',
            'Nombre' => 'nombre',
            'Empresa' => 'empresa_label',
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
    
    protected function specialProcessBeforeShow($rows){
        for($i = 0; count($rows) > $i; $i++){
            $cat = $this->db->query_select('empresa', sprintf('id_empresa=%s', $rows[$i]['id_empresa']));
            $rows[$i]['empresa_label'] = $cat[0]['nombre']; 
        }
        return sanitize_array_by_keys($rows, ['empresa_label', 'nombre', 'usuario_creacion']);
    }
}
