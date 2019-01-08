<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 7/26/2018
 * Time: 11:53 AM
 */

class MantTipoCliente extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantTipoCliente';
        $this->table = 'clientes_tipos_precio';
        $this->setTitle('Mantenimiento de tipos de Cliente');

        $this->fields = array(
            new FastField('Id', 'id_tipo_precio', 'text', 'int', true, null, array(), FALSE, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true),
            new FastField('Porcentaje de Descuento', 'porcentaje_descuento', 'text', 'int', true)
        );

        $this->gridCols = array(
            'Id' => 'id_tipo_precio',
            'Nombre' => 'nombre',
            'Porcentaje de Descuento' => 'porcentaje_descuento'
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

    protected function specialValidation($fields, $r, $mess, $pkFields)
    {
        if (array_key_exists('porcentaje_descuento', $fields)) {
            $porcentaje = str_replace("'", "", $fields['porcentaje_descuento']);
            if (!preg_match('/^[0-9]+$/', $porcentaje, $matches)){
                $r = 0;
                $mess = 'Porcentaje descuento inválido (0..100), favor de revisar e intentar de nuevo';
            }
            if ($porcentaje < 0 || $porcentaje > 100){
                $r = 0;
                $mess = 'Porcentaje descuento inválido (0..100), favor de revisar e intentar de nuevo';
                $fields['porcentaje_descuento'] = '0';
            }
        }

        return array('r' => $r, 'mess' => $mess);
    }
}
