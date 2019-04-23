<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantMunicipios
 *
 * @author baci5
 */
class MantMunicipios extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantMunicipios';
        $this->table = 'municipios';
        $this->setTitle('Mantenimiento de municipios');
        $this->gridCols = array(
            'Id' => 'id_municipio',
            'Nombre' => 'nombre',
            'Departamento' => 'nombre_departamento',
            'Creado por' => 'usuario_creacion',
            'Fecha creación' => 'fecha_creacion'
        );
        
        $departamentos = Collection::get($this->db, 'departamentos')->toSelectList('id_departamento', 'nombre');
        
        $this->fields = array(
            new FastField('Id', 'id_municipio', 'text', 'int', true, null, array(), FALSE, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true),
            new FastField('Departamento', 'id_departamento', 'select', 'int', true, null, $departamentos)
        );
    }
    
    protected function specialProcessBeforeShow($rows){
        $deptos = Collection::get($this->db, 'departamentos');
        for($i = 0; count($rows) > $i; $i++){
            $dep = $deptos->where(["id_departamento" => $rows[$i]['id_departamento']])->single();
            $rows[$i]['nombre_departamento'] = $dep['nombre'];
        }
        return sanitize_array_by_keys($rows, ['nombre','usuario_creacion',"nombre_departamento"]);
    }
    
    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(self_escape_string($user['FIRST_NAME']), 'text');
        return $updateData;
    }
}
