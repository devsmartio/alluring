<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantProductos
 *
 * @author baci5
 */
class MantProductos extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantProductos';
        $this->table = 'producto';
        $this->setTitle('Mantenimiento de productos');
        $this->gridCols = array(
            'Código' => 'codigo_producto',
            'Nombre' => 'nombre',
            'Tipo' => 'tipo_label',
            'Subtipo' => 'subtipo_label',
            'Marca' => 'marca_label',
            'Creado por' => 'usuario_creacion',
            'Fecha creación' => 'fecha_creacion'
        );
        
        $subtipos = $this->db->query_select('subtipo');
        $subs = array();
        foreach($subtipos as $c){
                $subs[self_escape_string($c['nombre'])] = $c['id_subtipo'];
        }
        
        $proveedores = $this->db->query_select('proveedor');
        $provs = array();
        foreach($proveedores as $c){
                $provs[self_escape_string($c['nombre'])] = $c['id_proveedor'];
        }
        $marcas = $this->db->query_select('marca');
        $marcs = array();
        foreach($marcas as $m){
                $marcs[self_escape_string($m['nombre'])] = $m['id_marca'];
        }
        
        $tipos = $this->db->query_select('tipo');
        $ts = array();
        foreach($tipos as $c){
                $ts[self_escape_string($c['nombre'])] = $c['id_tipo'];
        }
        
        $this->fields = array(
            new FastField(null, 'id_producto', 'hidden', 'int', true, null, array(), FALSE, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true),
            new FastField('Precio por defecto', 'precio_por_defecto', 'text', 'text'),
            new FastField('Precio por docena', 'precio_docena', 'text', 'text'),
            new FastField('Precio mayorista', 'precio_mayorista', 'text', 'text'),
            new FastField('Costo', 'costo', 'text', 'text'),
            new FastField('Proveedor', 'id_proveedor', 'select', 'int', true, null, $provs),
            new FastField('Marca', 'id_marca', 'select', 'int', true, null, $marcs),
            new FastField('Tipo', 'id_tipo', 'select', 'int', true, null, $ts),
            new FastField('Subtipo', 'id_subtipo', 'select', 'text', true, null, $subs),
            new FastField('SKU', 'sku', 'text', 'text', true, null, array(), false),
            new FastField('Descripción', 'descripcion', 'textarea', 'text', true, null, array(), false),
            new FastField('Mínimo inventario', 'minimo_inventario', 'text', 'int', true, null, array(), false)
        );
    }
    
    protected function specialProcessBeforeShow($rows){
        for($i = 0; count($rows) > $i; $i++){
            if(!isEmpty($rows[$i]['id_subtipo'])){
                $sub = $this->db->query_select('subtipo', sprintf('id_subtipo=%s', $rows[$i]['id_subtipo']));
                $rows[$i]['subtipo_label'] = self_escape_string($sub[0]['nombre']);
                $pref_subtipo = $sub[0]['prefijo'];
                $es_subtipo = true;
            }
            $proveedor = $this->db->query_select("proveedor", sprintf("id_proveedor=%s", $rows[$i]['id_proveedor']));
            $pref_prov = $proveedor[0]['es_internacional'] == 1 ? "I" : "L";
            
            $tipo = $this->db->query_select('tipo', sprintf('id_tipo=%s', $rows[$i]['id_tipo']));
            $rows[$i]['tipo_label'] = $tipo[0]['nombre'];

            $marca = $this->db->query_select('marca', sprintf('id_marca=%s', $rows[$i]['id_marca']));
            $pref_marca = $marca[0]['prefijo'];
            $rows[$i]['marca_label'] = $marca[0]['nombre'];

            $codigo = !isEmpty($rows[$i]['sku']) && strlen($rows[$i]['sku']) > 4 ? substr($rows[$i]['sku'], -4) : $rows[$i]['id_producto'];

            $codigo_producto = $pref_prov . $pref_subtipo . $pref_marca . $codigo;

            $rows[$i]['codigo_producto'] = $codigo_producto;
        }
        return sanitize_array_by_keys($rows, array('codigo_producto', 'tipo_label', 'marca_label'));
    }
    
    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(self_escape_string($user['FIRST_NAME']), 'text');
        return $updateData;
    }
    
    protected function specialProcessBeforeUpdate($updateData, $pkFields = array()) {
        if(!isset($updateData['id_tipo'])){
            $updateData['id_tipo'] = 'null';
        } else if(!isset($updateData['id_subtipo'])){
            $updateData['id_subtipo'] = 'null';
        }
        return $updateData;
    }
}
