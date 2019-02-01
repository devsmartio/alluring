<?php
/**
 * @author bcruz
 */

class MantTiposAgrupaciones extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantTiposAgrupaciones';
        $this->table = 'tipos_agrupaciones';
        $this->setTitle('Mantenimiento de Agrupaciones de catálogo');

        $this->fields = array(
            new FastField('Id', 'id_tipo_agrupacion', 'text', 'int', true, null, array(), FALSE, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true)
        );

        $this->gridCols = array(
            'Id' => 'id_tipo_agrupacion',
            'Nombre' => 'nombre'
        );
    }

    protected function validationBeforeDelete($r, $mess, $pkFields){
        $sql =  'SELECT * FROM tipo WHERE id_tipo_agrupación=%s';
        $bodegas = $this->db->queryToArray(sprintf($sql, $pkFields['id_tipo_agrupacion']));

        if(count($bodegas) > 0){
            $r = 0;
            $mess = "La agrupación no puede ser borrada ya que hay bodegas asociadas";
        }

        return array('r' => $r, 'mess' => $mess);
    }

    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(self_escape_string($user['FIRST_NAME']), 'text');
        return $updateData;
    }
}