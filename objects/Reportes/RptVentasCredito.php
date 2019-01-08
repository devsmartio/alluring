<?php

/**
 * Description of RptVentasCredito
 *
 * @author baci5
 */
class RptVentasCredito extends FastReport {
    function __construct() {
        parent::__construct();
        $this->instanceName = "RptVentasCredito";
        $this->excelFileName = "Reporte ventas crédito";
        $this->setPrefix('rpt_ventas_credito');
        $this->setTitle('Ventas crédito (últimos 60 días)');

        $clientes = (new Collection($this->db->queryToArray("select concat(nombres, ' ',apellidos) nombre_completo, id_cliente from clientes")))->toSelectList('id_cliente', 'nombre_completo');
        
        $params = [
            new FastField('Cliente', 'id_cliente', 'select', 'int', true, null, $clientes, false)
        ];
        
        $this->setParams($params);
        $this->columns = [
            new FastReportColumn("Venta", "id_venta"),
            new FastReportColumn("Cliente", "nombre_completo", "sanitize"),
            new FastReportColumn("Fecha", "fecha", "format_datetime"),
            new FastReportColumn("Productos", "cantidad_productos"),
            new FastReportColumn("Total", "total", "number_format")
        ];
        $this->useDefaultView = true;
    }
    
    protected function fieldsAreValid() {
        return true;
    }
    
    protected function getResultSet() {
        $cliente = getParam('id_cliente');
        
        
        $where = '';
        if(!isEmpty($cliente)){
            $where = sprintf('id_cliente=%s', $cliente);
        }
        
        $query = 'select * from reporte_ventas_credito';
        $query = isEmpty($where) ? $query : "$query where $where";
        return $this->db->queryToArray($query);
    }
}
