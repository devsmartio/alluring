<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RptInventario
 *
 * @author baci5
 */
class RptInventario extends FastReport {
    function __construct() {
        parent::__construct();
        $this->instanceName = "RptInventario";
        $this->excelFileName = "Reporte Inventario";
        $this->setPrefix('rpt_inventario');
        $this->setTitle('Inventario');
        $sucursales = $this->db->query_select('sucursales');
        $sucs = [];
        foreach($sucursales as $s){
            $sucs[self_escape_string($s['nombre'])] = $s['id_sucursal'];
        }
        
        $proveedores = $this->db->query_select('proveedor');
        $provs = [];
        foreach($proveedores as $p){
            $provs[self_escape_string($p['nombre'])] = $p['id_proveedor'];
        }
        
        $params = [
            new FastField('Sucursal', 'id_sucursal', 'select', 'int', true, null, $sucs, false),
            new FastField('Proveedor', 'id_proveedor', 'select', 'int', true, null, $provs, false)
        ];
        
        $this->setParams($params);
        $this->columns = [
            new FastReportColumn("Producto", "nombre_producto", "sanitize"),
            new FastReportColumn("Cantidad en inventario", "cantidad", "number_format_inverse"),
            new FastReportColumn("Minimo en inventario", "minimo_inventario"),
            new FastReportColumn("Sucursal", "nombre_sucursal", "sanitize"),
            new FastReportColumn("Proveedor", "nombre_proveedor", "sanitize"),
            new FastReportColumn("Descripción", "descripcion_producto", "sanitize"),
            new FastReportColumn("Marca", "nombre_marca", "sanitize"),
            new FastReportColumn("Tipo", "nombre_tipo", "sanitize"),
            new FastReportColumn("Subtipo", "nombre_subtipo", "sanitize"),
            new FastReportColumn("Costo", "costo", "sanitize")
        ];
        $this->useDefaultView = true;
    }
    
    protected function fieldsAreValid() {
        return true;
    }
    
    protected function getResultSet() {
        $sucursal = getParam('id_sucursal');
        $proveedor = getParam('id_proveedor');
        
        
        $where = '';
        if(!isEmpty($sucursal)){
            $where = sprintf('id_sucursal=%s', $sucursal);
        }
        
        if(!isEmpty($proveedor)){
            $where = !isEmpty($where) ? "$where and " : $where;
            $where = sprintf('id_proveedor=%s', $proveedor);
        }
        $query = 'select * from reporte_inventario';
        $query = isEmpty($where) ? $query : "$query where $where";
        
        return $this->db->queryToArray($query);
    }
}
