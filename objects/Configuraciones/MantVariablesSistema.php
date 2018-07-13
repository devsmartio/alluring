<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantVariablesSistema
 *
 * @author baci5
 */
class MantVariablesSistema extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantVariablesSistema';
        $this->table = 'variables_sistema';
        $this->setTitle('Variables del sistema');
        $this->onlyEdit = true;
        
        $this->fields = array(
            new FastField('Nombre variable', 'nombre', 'text', 'int', TRUE, null, array(), false, null, true),
            new FastField('Valor', 'valor', 'text', 'text', true)
        );
        $this->gridCols = array(
            'Nombre' => 'nombre',
            'Valor' => 'valor',
            'Creado' => 'fecha_creacion',
            'Creado por' => 'usuario_creacion'
        );
    }
}
