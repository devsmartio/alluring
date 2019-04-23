<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PerfilesTable
 *
 * @author Edgar
 */
final class PerfilesTable extends MaintenanceTable{
    private static $ID = 'ID';
    private static $NOM = 'NAME';
    
    protected function init() {
        $this->gridCols = array(
            new AngularGridColumn(self::$ID, 'ID', false),
            new AngularGridColumn(self::$NOM, 'Nombre'),
            new AngularGridColumn(null, 'Opciones', false, '10%', true,
                new BootstrapBtnTemplate('Eliminar', 'btn-danger', 'doDelete(row)'))
        );
        $this->table = 'app_profile';
        $this->pkField = self::$ID;
        $this->insertUpdateFields = array(self::$NOM);
        $this->requiredFields = array(self::$NOM);
        $this->sanitizeCols = array(self::$NOM);
    }

}
