<?php
/**
 * Description of ProductoTable
 *
 * @author Bryan C.
 */
final class ProductoTable extends MaintenanceTable{
    private static $ID = 'idProducto';
    private static $NOM = 'nombre';
    private static $DESC = 'descripcion';
    private static $CREADO_POR = 'creado_por';
    private static $CREADO = 'creado';
    private static $ACT = 'actualizado';
    private static $ACT_POR = 'actualizado_por';
    
    protected function init() {
        $this->table = 'producto';
        $this->sanitizeCols = array(
            self::$NOM, 
            self::$DESC, 
            self::$CREADO_POR,
            self::$ACT_POR
        );
        $this->requiredFields = array(self::$NOM);
        $this->pkField = self::$ID;
        $this->insertUpdateFields = array(self::$NOM, self::$DESC);
        $this->gridCols = array(
            new AngularGridColumn(self::$ID, 'ID', false),
            new AngularGridColumn(self::$NOM, 'Nombre'),
            new AngularGridColumn(self::$DESC, 'Descripcion', true, '40%'),
            new AngularGridColumn(self::$CREADO, 'Creado', false),
            new AngularGridColumn(self::$CREADO_POR, 'Creado Por', false),
            new AngularGridColumn(self::$ACT, 'Última actualización', false),
            new AngularGridColumn(self::$ACT_POR, 'Actualizado por', false),
            new AngularGridColumn(null, 'Opciones', false, '10%', true,
                new BootstrapBtnTemplate('Eliminar', 'btn-danger', 'doDelete(row)'))
        );
    }
    
    protected function processBeforeInsert($toInsert) {
        $date = new DateTime();
        $toInsert[self::$CREADO_POR] = $this->user['FIRST_NAME'];
        $toInsert[self::$CREADO] = $date->format(SQL_DT_FORMAT);
        return $toInsert;
    }
    
    protected function processBeforeShow($resultSet) {
        for($i = 0; count($resultSet) > $i;$i++){
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
