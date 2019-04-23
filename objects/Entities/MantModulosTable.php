<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantModulosTable
 *
 * @author Bryan C
 */
class MantModulosTable extends MaintenanceTable{
    private static $ID = 'ID';
    private static $NOM = 'NAME';
    private static $OBJ_NAME = 'PATH';
    private static $ORDEN_CARGA = 'LOAD_SEQ';
    private static $CATEGORIA = 'FK_MODULE_CATEGORY';
    
    protected function init() {
        $this->table = 'app_modules';
        $this->pkField = self::$ID;
        $this->gridCols = array(
            new AngularGridColumn(self::$ID, 'ID', false, '5%', FALSE),
            new AngularGridColumn(self::$NOM, 'Título'),
            new AngularGridColumn(self::$OBJ_NAME, 'Nombre'),
            new AngularGridColumn(self::$ORDEN_CARGA, 'Orden de carga', '10%'),
            new AngularGridColumn(self::$CATEGORIA, 'Categoría', false, '*', true, 
                new SelectTemplate('ID', 'NAME', CategoriasModulosTable::getMe(), true, array())),
            new AngularGridColumn(null, 'Opciones', false, '*', true, 
                new BootstrapBtnTemplate('Eliminar', 'btn-danger', 'doDelete(row)'))
        );
        $this->insertUpdateFields = array(self::$NOM, self::$OBJ_NAME, 
            self::$ORDEN_CARGA, self::$CATEGORIA);
        $this->requiredFields = $this->insertUpdateFields;
        $this->sanitizeCols = array(self::$NOM, self::$OBJ_NAME);
    }
}
