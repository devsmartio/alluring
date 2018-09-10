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
        
        $categoria = $this->db->query_select('tipo');
        $provs = [];
        foreach($categoria as $p){
            $provs[self_escape_string($p['nombre'])] = $p['id_tipo'];
        }
        
        $params = [
            new FastField('Bodega', 'id_sucursal', 'select', 'int', true, null, $sucs, false),
            new FastField('Categoria', 'id_tipo', 'select', 'int', true, null, $provs, false)
        ];
        
        $this->setParams($params);
        $this->columns = [
            new FastReportColumn("Codigo", "codigo_origen"),
            new FastReportColumn("Producto", "nombre_producto", "sanitize"),
            new FastReportColumn("Categoria", "nombre_categoria", "sanitize"),
            new FastReportColumn("Bodega", "nombre_sucursal", "sanitize"),
            new FastReportColumn("Existencia", "total_existencias", "number_format_inverse")
        ];
        $this->useDefaultView = true;
    }
    
    protected function fieldsAreValid() {
        return true;
    }
    
    protected function getResultSet() {
        $sucursal = getParam('id_sucursal');
        $id_tipo = getParam('id_tipo');
        
        
        $where = '';
        if(!isEmpty($sucursal)){
            $where = sprintf('id_sucursal=%s', $sucursal);
        }
        
        if(!isEmpty($id_tipo)){
            $where = !isEmpty($where) ? "$where and " : $where;
            $where .= sprintf('id_tipo=%s', $id_tipo);
        }
        $query = 'select * from reporte_inventario';
        $query = isEmpty($where) ? $query : "$query where $where";
        
        return $this->db->queryToArray($query);
    }
}
