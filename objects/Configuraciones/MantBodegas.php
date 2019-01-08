<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 7/26/2018
 * Time: 1:50 PM
 */

class MantBodegas extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantBodegas';
        $this->table = 'sucursales';
        $this->setTitle('Mantenimiento de Bodegas');

        $this->fields = array(
            new FastField('Id', 'id_sucursal', 'text', 'int', true, null, array(), FALSE, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true),
            new FastField('Identificador Excel', 'identificador_excel', 'text', 'text', true, null, array(), false)
        );

        $this->gridCols = array(
            'Id' => 'id_sucursal',
            'Nombre' => 'nombre',
            'Identificador Excel' => 'identificador_excel'
        );
    }

    protected function validationBeforeDelete($r, $mess, $pkFields){

        $sql =  'SELECT  COUNT(1) AS sucursales ' .
                'FROM    sucursales s ' .
                '        INNER JOIN trx_transacciones t ON t.id_sucursal = s.id_sucursal ' .
                'WHERE s.id_sucursal = %s';
        $sucursales = $this->db->query_toArray(sprintf($sql, $pkFields['id_sucursal']));

        if($sucursales[0]['sucursales'] > 0){
            $r = 0;
            $mess = "La sucursal no puede ser borrada. Debido a que tiene existencias";
        }

        return array('r' => $r, 'mess' => $mess);
    }

    protected function specialProcessBeforeShow($resultSet){
        return $resultSet;
    }

    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(self_escape_string($user['FIRST_NAME']), 'text');
        return $updateData;
    }
}