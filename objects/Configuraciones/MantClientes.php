<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantClientes
 *
 * @author baci5
 */
class MantClientes extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantClientes';
        $this->table = 'clientes';
        $this->setTitle('Mantenimiento de clientes');
        
        $this->fields = array(
            new FastField('Id', 'id_cliente', 'hidden', 'int', TRUE, null, array(), false, false, true),
            new FastField('Identificación', 'id_personal', 'text', 'text', true, null, array(), false),
            new FastField('Nombre', 'nombres', 'text', 'text', true),
            new FastField('Apellidos', 'apellidos', 'text', 'text', true),
            new FastField('NIT', 'nit', 'text', 'text', TRUE, null, array(), false),
            new FastField('Telefono', 'telefono', 'text', 'int', true, null, array(), false),
            new FastField('Dirección', 'direccion', 'textarea', 'text', true, null, array(), false),
            new FastField('Aprobar crédito?', 'tiene_credito', 'select', 'int', true, null, array('Si' => 1, 'No' => 0), true),
            new FastField('Días crédito', 'dias_credito', 'select', 'int', true, null, array('8' => 8, '15' => 15, '30' => 30, '60' => 60, '90' => 90), false)
        );
        $this->gridCols = array(
            'ID' => 'id_personal',
            'Nombre' => 'nombres',
            'Apellidos' => 'apellidos',
            'Creado' => 'fecha_creacion',
            'Creado por' => 'usuario_creacion',
        );
    }
    
    protected function specialProcessBeforeShow($resultSet){
        usort($resultSet, array("MantClientes", "cmp"));
        return $resultSet;
    }
    
    protected function specialValidation($fields, $r, $mess, $pkFields) {
//        $cliente = new Entity($fields);
//        if($cliente->get('tiene_credito') == 1 && (!$cliente->existsAndNotEmpty('dias_credito') || $cliente->get('dias_credito') == 0)){
//            $r = 0;
//            $mess = 'Debe indicar los días de crédito aprobados';
//        }
//        $wn ='factura_nit = '.$cliente->get('nit') . ' and identificacion!=' . $cliente->get('id_personal');
//        $nit= $this->db->query_select('clientes',$wn);
//
//        if(count($nit) > 0 && $cliente->get('factura_nit') != "'CF'"){
//            $r = 0;
//            $mess = "El nit ingresado ya existe en el sistema";
//        }
//
//        $wid ='id_personal = '.$cliente->get('id_personal') . ' and id_cliente!=' . $pkFields['id_cliente'];
//        $cui = $this->db->query_select('clientes',$wid);
//
//        if(count($cui) > 0){
//            $r = 0;
//            $mess = "El número de identificación ingresado ya existe en el sistema";
//        }
        return array('r' => $r, 'mess' => $mess);
    }
    
    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(self_escape_string($user['FIRST_NAME']), 'text');
        return $updateData;
    }
    
    static function cmp($a, $b)
    {
        if ($a["apellidos"] == $b["apellidos"]) 
        {
           return 0;
        }
        return ($a["apellidos"] < $b["apellidos"]) ? -1 : 1;
    }    
}
