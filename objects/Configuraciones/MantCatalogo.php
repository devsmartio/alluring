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
class MantCatalogo extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantCatalogo';
        $this->table = 'catalogo';
        $this->setTitle('Mantenimiento de categorias de productos');
        
        $this->fields = array(
            new FastField(null, 'CatalogId', 'hidden', 'int', TRUE, null, array(), false, null, true),
            new FastField('Nombre', 'Name', 'text', 'text', true),
            new FastField('Descripción', 'Description', 'text', 'text', true, null, array(), false),
            new FastField('Lista', 'list', 'select', 'text', true, null, array("Opcion 1" => "Opcion 1", "Opcion 2" => "Opcion 2"), true)
        );
        $this->gridCols = array(
            'ID' => 'CatalogId',
            'Nombre' => 'Name'
        );
    }
    
}
