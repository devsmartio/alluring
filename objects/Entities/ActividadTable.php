<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ActividadTable
 *
 * @author Edgar
 */
class ActividadTable extends MaintenanceTable{
    private static $ID = 'idActividad';
    private static $NOM = 'nombre';
    private static $CREADO = 'creado';
    private static $CREADO_POR = 'creado_por';
    private static $HORAS = 'horas_hombre';
    
    protected function init() {
        $this->table = 'actividad';
        $this->pkField = self::$ID;
        $this->gridCols = array(
            new AngularGridColumn(self::$ID, 'ID', false),
            new AngularGridColumn(self::$NOM, 'Nombre'),
            new AngularGridColumn(self::$HORAS, 'Horas hombre'),
            new AngularGridColumn(self::$CREADO, 'Creado', false),
            new AngularGridColumn(self::$CREADO_POR, 'Creado por', false),
            new AngularGridColumn(null, 'Options', false, '*', true, 
                new BootstrapBtnTemplate('Eliminar', "btn-danger", 'doDelete(row)'))
        );
        $this->sanitizeCols = array(self::$NOM);
        $this->insertUpdateFields = array(self::$NOM, self::$HORAS);
        $this->requiredFields = $this->insertUpdateFields;  
    }
    
    protected function processBeforeInsert($toInsert) {
        $date = new DateTime();
        $toInsert[self::$CREADO_POR] = $this->user['FIRST_NAME'];
        $toInsert[self::$CREADO] = $date->format(SQL_DT_FORMAT);
        return $toInsert;
    }
    
    protected function processBeforeShow($resultSet) {
        for($i = 0; count($resultSet) > $i;$i++){
            $date = DateTime::createFromFormat(SQL_DT_FORMAT, $resultSet[$i][self::$CREADO]);
            $resultSet[$i][self::$CREADO] = $date->format(SHOW_DT_FORMAT);
        }
        return $resultSet;
    }
}
