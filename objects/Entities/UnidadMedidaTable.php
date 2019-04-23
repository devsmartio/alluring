<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UnidadMedidaTable
 *
 * @author Bryan C
 */
class UnidadMedidaTable extends MaintenanceTable{
    private static $ID = 'idUnidad_Medida';
    private static $NOM = 'nombre';
    private static $SIM = 'simbolo';
    private static $UNIDAD_MEDIDA_COL = 'Unidad_Medidacol';
    
    protected function init() {
        $this->table = 'unidad_medida';
        $this->pkField = self::$ID;
        $this->gridCols = array(
            new AngularGridColumn(self::$ID, 'ID', false, '5%', FALSE),
            new AngularGridColumn(self::$NOM, 'Nombre'),
            new AngularGridColumn(self::$SIM, 'Símbolo'),
            new AngularGridColumn(self::$UNIDAD_MEDIDA_COL, 'Unidad medida'),
            new AngularGridColumn(null, 'Opciones', false, '*', true, 
                new BootstrapBtnTemplate('Eliminar', 'btn-danger', 'doDelete(row)'))
        );
        $this->insertUpdateFields = array(self::$NOM, self::$SIM, self::$UNIDAD_MEDIDA_COL);
        $this->requiredFields = array(self::$NOM);
        $this->sanitizeCols = $this->insertUpdateFields;
    }
}
