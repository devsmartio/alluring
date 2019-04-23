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


        $bods = [];
        if($this->user['FK_PROFILE'] == 1 /* SUPER ADMIN */){
            $bods = Collection::get($this->db, "sucursales")->toSelectList("id_sucursal", "nombre");
        } else {
            $accessBods = 
            join(
                array_map(function($bod) {
                    return $bod['id_bodega'];
                }, $this->db->query_select("usuarios_bodegas", sprintf("id_usuario='%s'", $this->user['ID']))
                ), 
            ",");
            if(empty($accessBods)){
                die("<b>NO TIENE CONFIGURADA NINGUNA BODEGA</b>");
            } else {
                $access = sprintf("id_sucursal in (%s)", $accessBods);
                $bods = (new Collection($this->db->query_select("sucursales", $access)))->toSelectList("id_sucursal", "nombre");
            }
        }
        
        $categoria = $this->db->query_select('tipo');
        $provs = [];
        foreach($categoria as $p){
            $provs[self_escape_string($p['nombre'])] = $p['id_tipo'];
        }
        
        $params = [
            new FastField('Bodega', 'id_sucursal', 'select', 'int', true, null, $bods, false),
            new FastField('Categoria', 'id_tipo', 'select', 'int', true, null, $provs, false)
        ];
        
        $this->setParams($params);
        $this->columns = [
            new FastReportColumn("Codigo", "codigo"),
            new FastReportColumn("Codigo Origen", "codigo_origen"),
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
