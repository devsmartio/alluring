<?php

/**
 * Description of RptTotalesCategorias
 *
 * @author baci5
 */
class RptTotalesCategorias extends FastReport {
    function __construct() {
        parent::__construct();
        $this->instanceName = "RptTotalesCategorias";
        $this->excelFileName = "totales_por_categoria";
        $this->setPrefix('rpt_totales_cat');
        $this->setTitle('Totales por categoría');

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
                $bods = (new Collection($this->db->query_select("sucursales", sprintf("id_sucursal in ('%s')", $accessBods))))->toSelectList("id_sucursal", "nombre");
            }
        }
        
        $provs = Collection::get($this->db, "tipo")->toSelectList("id_tipo", "nombre");
        
        $params = [
            new FastField('Bodega', 'id_sucursal', 'select', 'int', true, null, $bods, false),
            new FastField('Categoria', 'id_tipo', 'select', 'int', true, null, $provs, false)
        ];
        
        $this->setParams($params);
        $this->columns = [
            new FastReportColumn("Categoría", "nombre_categoria", "sanitize"),
            new FastReportColumn("Sucursal", "nombre_sucursal", "sanitize"),
            new FastReportColumn("Existencia", "total_existencias", "number_format_inverse")
        ];
        if($this->user['FK_PROFILE'] == 1){
            $this->columns[] = new FastReportColumn("Costo Total", "costo_total", "number_format");
            $this->columns[] = new FastReportColumn("Precio Total Mayorista", "precio_total_mayorista", "number_format");
        }
        $this->columns[] = new FastReportColumn("Precio Total", "precio_total", "number_format");
        $this->useDefaultView = true;
    }
    
    protected function fieldsAreValid() {
        $sucursal = getParam("id_sucursal");
        if(isEmpty($sucursal) && $this->user['FK_PROFILE'] != 1 /* NO ES SUPER ADMIN */){
            $this->r = 0;
            $this->msg = "Debe elegir la bodega";
            return false;
        }
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
        $query = 'select 
            max(nombre_categoria) nombre_categoria, 
            max(nombre_sucursal) nombre_sucursal, 
            sum(total_existencias) total_existencias, 
            sum(costo_total) costo_total, 
            sum(precio_total) precio_total, 
            sum(precio_total)/2 precio_total_mayorista
        from reporte_inventario';
        $query = isEmpty($where) ? $query : "$query where $where";
        $query .= " GROUP BY ";
        if(!isEmpty($sucursal)){
            $query.= "id_sucursal, ";
        }
        $query .= "id_tipo";
        $query .= " ORDER BY id_sucursal"; 
        return $this->db->queryToArray($query);
    }
}
