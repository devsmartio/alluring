<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 7/17/2018
 * Time: 10:55 AM
 */

class MantTipoCambio extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantTipoCambio';
        $this->table = 'tipo_cambio';
        $this->setTitle('Mantenimiento de Tipos de Cambio');

        $monedas = $this->db->query_select('monedas');

        $monedaBase = [];
        foreach($monedas as $m){
            $monedaBase[self_escape_string($m['nombre'])] = $m['id_moneda'];
        }

        $monedaConvertir = [];
        foreach($monedas as $m){
            $monedaConvertir[self_escape_string($m['nombre'])] = $m['id_moneda'];
        }

        $this->fields = array(
            new FastField('Id', 'id_tipo_cambio', 'text', 'int', true, null, array(), false, null, true),
            new FastField('Moneda Base', 'id_moneda_muchos', 'select', 'int', true, null, $monedaBase, true),
            new FastField('Moneda a Convertirse', 'id_moneda_uno', 'select', 'int', true, null, $monedaConvertir, true),
            new FastField('Factor de conversion', 'factor', 'text', 'float', true),
        );

        $this->gridCols = array(
            'Moneda Base' => 'nombre_moneda_base',
            'Moneda a Convertirse' => 'nombre_moneda_convertir',
            'Factor de conversion' => 'factor'
        );
    }

    protected function specialValidation($fields, $r, $mess, $pkFields)
    {
        if (array_key_exists('factor', $fields)) {
            $factor = str_replace("'", "", $fields['factor']);
            if (!preg_match('/^[1-9]\d*(\.\d{1,2})?$/', $factor, $matches)){
                $r = 0;
                $mess = 'Factor invalido (##.##), favor de revisar e intentar de nuevo';
            }
        }

        if (array_key_exists('id_moneda_muchos', $fields) && array_key_exists('id_moneda_uno', $fields)) {
            $tipo_cambio = new Entity($fields);
            $w = sprintf("id_moneda_muchos = " . $tipo_cambio->get('id_moneda_muchos') . " and id_moneda_uno = " . $tipo_cambio->get('id_moneda_uno'));
            $count = $this->db->query_select("tipo_cambio", $w);
            if(count($count) == 1){
                $r = 0;
                $mess = "Esta tipo de cambio ya existe.";
            }
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

    protected function specialProcessBeforeUpdate($updateData, $pkFields = array()){
        return $updateData;
    }

    protected function specialProcessBeforeShow($resultSet){

        $monedas = Collection::get($this->db, 'monedas');

        for($i = 0; count($resultSet) > $i; $i++){
            $monedaBase = $monedas->where(['id_moneda' => $resultSet[$i]['id_moneda_muchos']])->single();
            $resultSet[$i]['nombre_moneda_base'] = $monedaBase['nombre'];

            $monedaConvertir = $monedas->where(['id_moneda' => $resultSet[$i]['id_moneda_uno']])->single();
            $resultSet[$i]['nombre_moneda_convertir'] = $monedaConvertir['nombre'];
        }
        return sanitize_array_by_keys($resultSet, array('nombre_moneda_base', 'nombre_moneda_convertir'));
    }
}
