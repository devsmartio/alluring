<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProgramaTable
 *
 * @author Bryan C
 */
class ProgramaTable extends MaintenanceTable{
    private static $ID = 'idPrograma';
    private static $NOMBRE = 'nombre';
    private static $DESC = 'descripcion';
    private static $CREADO = 'creado';
    private static $CREADO_POR = 'creado_por';
    private static $SIEMBRA = 'TipoSiembra_idSiembra';
    private static $VER = 'version';
    private static $VER_PADRE = 'version_padre';
    
    
    protected function init() {
        $this->table = 'programa';
        $this->pkField = self::$ID;
        $this->gridCols = array(
            new AngularGridColumn(self::$ID, 'ID', false),
            new AngularGridColumn(self::$NOMBRE, 'Nombre', true, '15%'),
            new AngularGridColumn(self::$CREADO, 'Creado', false, '15%'),
            new AngularGridColumn(self::$CREADO_POR, 'Creado por', false),
            new AngularGridColumn(self::$DESC, 'Descripción', true, '23%'),
            new AngularGridColumn(self::$SIEMBRA, 'Siembra', false, '*', true, 
                new SelectTemplate('id_siembra', 'nombre', TipoSiembraTable::getMe(), true)),
            new AngularGridColumn(self::$VER, 'Versión'),
            new AngularGridColumn(self::$VER_PADRE, 'Versión Padre', true, '10%'),
            new AngularGridColumn(null, 'Opciones', false, '*', true, 
                new BootstrapBtnTemplate('Eliminar', 'btn-danger', 'doDelete(row)'))
        );
        $this->sanitizeCols = array(self::$NOMBRE, self::$DESC, self::$VER, 
            self::$VER_PADRE, self::$CREADO_POR);
        $this->requiredFields = array(self::$NOMBRE, self::$SIEMBRA, self::$VER);
        $this->insertUpdateFields = array(self::$NOMBRE, self::$DESC, self::$VER, 
            self::$VER_PADRE, self::$SIEMBRA);
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
