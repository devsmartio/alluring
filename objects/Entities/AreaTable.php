<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AreaTable
 *
 * @author Bryan C
 */
class AreaTable extends MaintenanceTable{
    private static $ID = 'idArea';
    private static $NOM = 'nombre';
    private static $DESC = 'descripcion';
    private static $MEDIDA = 'medida_area';
    private static $CREADO_POR = 'creado_por';
    private static $CREADO = 'creado';
    private static $ACT = 'actualizado';
    private static $ACT_POR = 'actualizado_por';
    
    protected function init() {
        $this->table = 'area';
        $this->sanitizeCols = array(
            self::$NOM, 
            self::$DESC, 
            self::$CREADO_POR,
            self::$ACT_POR
        );
        $this->requiredFields = array(self::$NOM, self::$MEDIDA);
        $this->pkField = self::$ID;
        $this->insertUpdateFields = array(self::$NOM, self::$DESC, self::$MEDIDA);
        $this->gridCols = array(
            new GridColumn(self::$ID, "ID"),
            new GridColumn(self::$NOM, "NOMBRE"),
            new GridColumn(self::$MEDIDA, "MEDIDA_AREA"),
            new GridColumn(self::$CREADO, "CREADO"),
            new GridColumn(self::$CREADO_POR, "CREADO POR")
        );
    }
    
    protected function processBeforeInsert($toInsert) {
        $date = new DateTime();
        $toInsert[self::$CREADO_POR] = $this->user['FIRST_NAME'];
        $toInsert[self::$CREADO] = $date->format(SQL_DT_FORMAT);
        return $toInsert;
    }
    
    protected function processBeforeShow($resultSet) {
        for($i = 0; count($resultSet) > $i; $i++){
            if($resultSet[$i][self::$ACT] != null){
                $date = DateTime::createFromFormat(SQL_DT_FORMAT, $resultSet[$i][self::$ACT]);
                $resultSet[$i][self::$ACT] = $date->format(SHOW_DT_FORMAT);
            }
            $date = DateTime::createFromFormat(SQL_DT_FORMAT, $resultSet[$i][self::$CREADO]);
            $resultSet[$i][self::$CREADO] = $date->format(SHOW_DT_FORMAT);
        }
        return $resultSet;
    }
    
    protected function processBeforeUpdate($toUpdate) {
        $date = new DateTime();
        $toUpdate[self::$ACT_POR] = $this->user['FIRST_NAME'];
        $toUpdate[self::$ACT] = $date->format(SQL_DT_FORMAT);
        return $toUpdate;
    }
}
