<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantMonedas
 *
 * @author baci5
 */
class MantMonedas extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantMonedas';
        $this->table = 'monedas';
        $this->setTitle('Mantenimiento de monedas');
        
        $this->fields = array(
            new FastField('Id', 'id_moneda', 'text', 'int', TRUE, null, array(), false, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true),
            new FastField('Simbolo', 'simbolo', 'text', 'text', true),
            new FastField('Moneda por defecto', 'moneda_defecto', 'checkbox', 'text', true, null, array(), false)
        );
        $this->gridCols = array(
            'ID' => 'id_moneda',
            'Nombre' => 'nombre',
            'Creado' => 'fecha_creacion',
            'Creado por' => 'usuario_creacion'
        );
    }

    protected function specialValidation($fields, $r, $mess, $pkFields)
    {
        $w = sprintf("moneda_defecto=1");
        $count = $this->db->query_select("monedas", $w);
        if(count($count) == 0 && !$fields['moneda_defecto'] ){
            $r = 0;
            $mess = "Esta moneda debe ser seleccionada por defecto, debido a que no existe una";
        }

        if (array_key_exists('simbolo', $fields)) {
            $simbolo = strtoupper(str_replace("'", "", $fields['simbolo']));
            if (!preg_match('/^[a-zA-Z€£$]+$/', $simbolo, $matches)){
                $r = 0;
                $mess = 'Simbolo invalido, favor de revisar e intentar de nuevo';
            }
        }

        return array('r' => $r, 'mess' => $mess);
    }
    
    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(self_escape_string($user['FIRST_NAME']), 'text');
        $moneda_defecto = str_replace("'", "", $updateData['moneda_defecto']);
        $updateData['simbolo'] = strtoupper($updateData['simbolo']);

        try {
            if(array_key_exists('moneda_defecto', $updateData) && $moneda_defecto == "on") {
                $updateData['moneda_defecto'] = 1;

                $values = array('moneda_defecto' => 0);
                $this->db->query_update('monedas', $values);

            } else {
                $updateData['moneda_defecto'] = 0;
            }
        } catch (Exception $e){
            $r = 0;
            $mess = 'Error desconocido. Contacte a soporte';
            var_dump($e->getTraceAsString());
        }

        return $updateData;
    }

    protected function specialProcessBeforeUpdate($updateData, $pkFields = array()){

        $moneda_defecto = str_replace("'", "", $updateData['moneda_defecto']);
        $updateData['simbolo'] = strtoupper($updateData['simbolo']);

        if(array_key_exists('moneda_defecto', $updateData) && $moneda_defecto == "on"){
            $updateData['moneda_defecto'] = 1;
            try {
                $values = array('moneda_defecto' => 0);
                $this->db->query_update('monedas', $values);
            } catch (Exception $e){
                $r = 0;
                $mess = 'Error desconocido. Contacte a soporte';
                var_dump($e->getTraceAsString());
            }
        }else{
            $updateData['moneda_defecto'] = 0;
        }

        return $updateData;
    }

    protected function specialProcessBeforeShow($resultSet){
        $i = 0;
        while(count($resultSet) > $i){
            $resultSet[$i]['moneda_defecto'] = ($resultSet[$i]['moneda_defecto'] == '1') ? true : false ;
            $i++;
        }
        return $resultSet;
    }
}
