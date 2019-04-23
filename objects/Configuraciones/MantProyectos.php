<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantProyectos
 *
 * @author baci5
 */
class MantProyectos extends NonDeleteCatalog {
    function __construct() {
        parent::__construct();
        $this->deleteFlagName = "eliminado";
        $this->instanceName = 'MantProyectos';
        $this->table = 'proyectos';
        $this->setTitle('Proyectos');
        $this->gridCols = array(
            'Id' => 'id_proyecto',
            'Nombre' => 'nombre',
            'SNIP' => 'snip',
            'Tipo' => 'estado_proyecto',
            'Código Fodes' => 'codigo_fodes',
            'Creado por' => 'usuario_creacion',
            'Fecha creación' => 'fecha_creacion',
            'Modificado por' => 'usuario_modificacion',
            'Última modificación' => 'fecha_modificacion'
        );
        
        $estados = Collection::get($this->db, 'estados_proyecto')->toSelectList('id_estado_proyecto', 'nombre');
        $tipo = Collection::get($this->db, 'tipos_proyecto')->toSelectList('id_tipo_proyecto', 'nombre');
        $departamentos = Collection::get($this->db, 'departamentos')->toSelectList('id_departamento', 'nombre');
        $municipios = Collection::get($this->db, 'municipios')->toSelectList('id_municipio', 'nombre');
        $ubicaciones = Collection::get($this->db, 'ubicaciones_expediente_proyecto')->toSelectList('id_ubicacion_expendiente', 'nombre');
        
        $this->fields = array(
            new FastField('Id', 'id_proyecto', 'text', 'int', true, null, array(), FALSE, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true),
            new FastField('SNIP', 'snip', 'text', 'text', true, null, [], false),
            new FastField('Código FODES', 'codigo_fodes', 'text', 'text', true),
            new FastField('Clase proyecto', 'id_tipo_proyecto', 'select', 'int', true, null, $tipo),
            new FastField('Departamento', 'id_departamento', 'select', 'int', true, null, $departamentos),
            new FastField('Municipio', 'id_municipio', 'select', 'int', true, null, $municipios),
            new FastField('Comunidad', 'comunidad', 'text', 'text', true),
            new FastField('Ubicación expediente', 'id_ubicacion_expediente', 'select', 'int', true, null, $ubicaciones),
            new FastField('Tipo', 'id_estado_proyecto', 'select', 'int', true, null, $estados),
            new FastField('Monto', 'monto', 'text', 'text', true, null, [], false),
            new FastField('Aprobado SEGEPLAN?', 'aprobado_segeplan', 'select', 'int', true, null, ["Si" => 1, "No" => 0] ),
            new FastField('Activo', 'activo', 'select', 'int', true, null, ["Si" => 1, "No" => 0] )
        );
    }
    
    protected function specialProcessBeforeShow($rows){
        $deptos = Collection::get($this->db, 'departamentos');
        $municipios = Collection::get($this->db, 'municipios');
        $ubicaciones = Collection::get($this->db, 'ubicaciones_expediente_proyecto');
        $estados = Collection::get($this->db, 'estados_proyecto');
        $tipos = Collection::get($this->db, 'tipos_proyecto');
        for($i = 0; count($rows) > $i; $i++){
            $dep = $deptos->where(["id_departamento" => $rows[$i]['id_departamento']])->single();
            $rows[$i]['nombre_departamento'] = $dep['nombre'];
            $mun = $municipios->where(["id_municipio" => $rows[$i]['id_municipio']])->single();
            $rows[$i]['nombre_municipio'] = $mun['nombre'];
            $ub = $ubicaciones->where(["id_ubicacion_expendiente" => $rows[$i]['id_ubicacion_expediente']])->single();
            $rows[$i]['nombre_ubicacion'] = $ub['nombre'];
            $est = $estados->where(["id_estado_proyecto" => $rows[$i]['id_estado_proyecto']])->single();
            $rows[$i]['nombre_estado'] = $est['nombre'];
            $ti = $tipos->where(["id_tipo_proyecto" => $rows[$i]['id_tipo_proyecto']])->single();
            $rows[$i]['nombre_tipo'] = $ti['nombre'];
        }
        return sanitize_array_by_keys($rows, ['nombre','usuario_creacion',"nombre_departamento", "nombre_municipio", "nombre_ubicacion", "nombre_estado", "nombre_tipo"]);
    }
    
    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(decode_email_address($user['ID']), 'text');
        return $updateData;
    }
    
    protected function specialProcessBeforeUpdate($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_modificacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_modificacion'] = sqlValue(decode_email_address($user['ID']), 'text');
        return $updateData;
    }
}
