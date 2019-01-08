<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CategoriasModulosTable
 *
 * @author Edgar
 */
final class CategoriasModulosTable extends MaintenanceTable{
    private static $ID = 'ID';
    private static $NOMBRE = 'NAME';
    private static $ICONO = 'ICON';
    
    protected function init() {
        $this->table = 'app_module_category';
        $this->gridCols = array(
            new AngularGridColumn(self::$ID, 'ID', false),
            new AngularGridColumn(self::$NOMBRE, 'NOMBRE'),
            new AngularGridColumn(self::$ICONO, 'ICONO'),
            new AngularGridColumn(null, 'Opciones', false, '10%', true,
                new BootstrapBtnTemplate('Eliminar', 'btn-danger', 'doDelete(row)'))
        );
        $this->insertUpdateFields = array(self::$ICONO, self::$NOMBRE);
        $this->pkField = self::$ID;
        $this->sanitizeCols = $this->requiredFields = $this->insertUpdateFields;
    }
}
