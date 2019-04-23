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
class MantDepartamentos extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantDepartamentos';
        $this->table = 'departamentos';
        $this->setTitle('Mantenimiento de departamentos');
        $this->gridCols = array(
            'Id' => 'id_departamento',
            'Nombre' => 'nombre',
            'Pais' => 'nombre_pais',
            'Creado por' => 'usuario_creacion',
            'Fecha creación' => 'fecha_creacion'
        );
        
        $paises = Collection::get($this->db, 'paises')->toSelectList('id_pais', 'nombre');
        
        $this->fields = array(
            new FastField('Id', 'id_departamento', 'text', 'int', true, null, array(), FALSE, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true),
            new FastField('Pais', 'id_pais', 'select', 'int', true, null, $paises)
        );
    }
    
    protected function specialProcessBeforeShow($rows){
        $paises = Collection::get($this->db, 'paises');
        for($i = 0; count($rows) > $i; $i++){
            $pais = $paises->where(["id_pais" => $rows[$i]['id_pais']])->single();
            $rows[$i]['nombre_pais'] = $pais['nombre'];
        }
        return sanitize_array_by_keys($rows, ['nombre','usuario_creacion',"nombre_pais"]);
    }
    
    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(self_escape_string($user['FIRST_NAME']), 'text');
        return $updateData;
    }
}
