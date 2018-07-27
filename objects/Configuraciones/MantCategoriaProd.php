<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 7/26/2018
 * Time: 12:34 PM
 */

class MantCategoriaProd extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantCategoriaProd';
        $this->table = 'tipo';
        $this->setTitle('Mantenimiento Categoría de Productos');

        $this->fields = array(
            new FastField('Id', 'id_tipo', 'text', 'int', true, null, array(), FALSE, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true)
        );

        $this->gridCols = array(
            'Id' => 'id_tipo',
            'Nombre' => 'nombre'
        );
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