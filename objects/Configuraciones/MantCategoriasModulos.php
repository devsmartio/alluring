<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantCategoriasModulos
 *
 * @author Bryan C
 */
class MantCategoriasModulos extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantCategoriasModulos';
        $this->table = 'app_module_category';
        $this->setTitle('Categorías de Módulos');
        $this->gridCols = array(
            'Id' => 'ID',
            'Nombre' => 'NAME',
            'Ícono' => 'ICON'
        );
        $this->fields = array(
            new FastField(null, 'ID', 'hidden', 'int', false, null, array(), FALSE, null, true),
            new FastField('Nombre', 'NAME', 'text', 'text', true),
            new FastField('Ícono', 'PATH', 'text', 'text')
        );
    }
}
