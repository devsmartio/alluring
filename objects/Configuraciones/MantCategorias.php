<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantCategorias
 *
 * @author baci5
 */
class MantCategorias extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantCategorias';
        $this->table = 'categorias';
        $this->setTitle('Mantenimiento de categorias de productos');
        
        $this->fields = array(
            new FastField(null, 'id_categoria', 'hidden', 'int', TRUE, null, array(), false, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true),
            new FastField('Prefijo', 'prefijo', 'text', 'text', true)
        );
        $this->gridCols = array(
            'ID' => 'id_categoria',
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
    
    protected function specialProcessBeforeShow($rows){
        return sanitize_array_by_keys($rows, ['nombre','usuario_creacion']);
    }
}
