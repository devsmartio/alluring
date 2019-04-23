<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantProductos
 *
 * @author Bryan Cruz
 */
class MantModulos extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantModulos';
        $this->table = 'app_modules';
        $this->setTitle('Modulos');
        $this->gridCols = array(
            'Nombre' => 'NAME',
            'Categoria' => 'CATEGORY_LABEL'
        );
        $profiles = $this->db->query_select('app_module_category');
        $prof = array();
        foreach($profiles as $p){
                $prof[self_escape_string($p['NAME'])] = $p['ID'];
        }
        $this->fields = array(
            new FastField(null, 'ID', 'hidden', 'int', false, null, array(), FALSE, null, true),
            new FastField('Nombre', 'NAME', 'text', 'text', true),
            new FastField('Ruta', 'PATH', 'text', 'text'),
            new FastField('Categoria', 'FK_MODULE_CATEGORY', 'select', 'int', true, null, $prof)
        );
    }
    
    protected function specialProcessBeforeShow($rows){
        for($i = 0; count($rows) > $i; $i++){
            $cat = $this->db->query_select('app_module_category', sprintf('ID=%s', $rows[$i]['FK_MODULE_CATEGORY']));
            $rows[$i]['CATEGORY_LABEL'] = $cat[0]['NAME']; 
        }
        return $rows;
    }
}