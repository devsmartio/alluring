<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantProductoTipo
 *
 * @author baci5
 */
class MantTipoProducto extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantTipoProducto';
        $this->table = 'tipo';
        $this->setTitle('Mantenimiento de tipos de producto');
        $this->gridCols = array(
            'Id' => 'id_tipo',
            'Nombre' => 'nombre',
            'Prefijo' => 'prefijo',
            'Categoría' => 'categoria_nombre',
            'Creado por' => 'usuario_creacion',
            'Fecha creación' => 'fecha_creacion'
        );
        $categorias = $this->db->query_select('categorias');
        $cats = array();
        foreach($categorias as $p){
                $cats[self_escape_string($p['nombre'])] = $p['id_categoria'];
        }
        $this->fields = array(
            new FastField('Id', 'id_tipo', 'text', 'int', true, null, array(), FALSE, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true),
            new FastField('Prefijo', 'prefijo', 'text', 'text', true),
            new FastField('Categoria', 'id_categoria', 'select', 'int', true, null, $cats)
        );
    }
    
    protected function specialValidation($fields, $r, $mess, $pkFields) {
        $marca = new Entity($fields);
        $prefijo = strtoupper(str_replace("'", "", $marca->get('prefijo')));
        if(count($pkFields) > 0){
            if( count($this->db->query_select('tipo', sprintf('prefijo="%s" and id_tipo!=%s', $prefijo, $pkFields['id_tipo']))) > 0){
                $r = 0;
                $mess = 'El prefijo ' . strtoupper($fields['prefijo']) . ' ya existe. Intente ingresar otro';
            }
        } else {
            if(count($this->db->query_select('tipo', sprintf('prefijo="%s"', $prefijo))) > 0){
                $r = 0;
                $mess = 'El prefijo ' . strtoupper($fields['prefijo']) . ' ya existe. Intente ingresar otro';
            }
        }
        return array('r' => $r, 'mess' => $mess);
    }
    
    protected function specialProcessBeforeShow($rows){
        $cats = Collection::get($this->db, 'categorias');
        for($i = 0; count($rows) > $i; $i++){
            $cat = $cats->where(array('id_categoria' => $rows[$i]['id_categoria']))->single();
            $rows[$i]['categoria_nombre'] = $cat['nombre']; 
        }
        return sanitize_array_by_keys($rows, array('categoria_nombre', 'nombre', 'usuario_creacion'));
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
