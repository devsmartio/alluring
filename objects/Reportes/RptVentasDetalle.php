<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 9/9/2018
 * Time: 7:36 PM
 */

class RptVentasDetalle  extends FastReport {
    function __construct() {
        parent::__construct();
        $this->instanceName = "RptVentasDetalle";
        $this->excelFileName = "Ventas por Item";
        $this->setPrefix('rpt_ventas_detalle');
        $this->setTitle('Ventas por Item');

        $sucursales = $this->db->query_select('sucursales');
        $sucs = [];
        foreach($sucursales as $s){
            $sucs[self_escape_string($s['nombre'])] = $s['id_sucursal'];
        }

        $dsClientes = $this->db->query_select('clientes');
        $clientes = [];
        foreach($dsClientes as $p){
            $clientes[self_escape_string($p['nombres']) . ' ' . self_escape_string($p['apellidos'])] = $p['id_cliente'];
        }

        $params = [
            new FastField('Bodega', 'id_sucursal', 'select', 'int', true, null, $sucs, false),
            new FastField('Fecha desde', 'fecha_desde', 'text', 'text', true, null, array(), false),
            new FastField('Fecha hasta', 'fecha_hasta', 'text', 'text', true, null, array(), false)
        ];

        $this->setParams($params);
        $this->columns = [
            new FastReportColumn("Codigo Producto", "codigo_origen", "sanitize"),
            new FastReportColumn("Nombre Producto", "nombre_producto", "sanitize"),
            new FastReportColumn("Cantidad", "cantidad", "number_format_inverse")
        ];
        $this->useDefaultView = true;
    }

    protected function fieldsAreValid() {
        return true;
    }

    protected function getResultSet() {
        $sucursal = getParam('id_sucursal');
        $fecha_desde = getParam('fecha_desde');
        $fecha_hasta = getParam('fecha_hasta');


        $where = '';
        if(!isEmpty($sucursal)){
            $where = sprintf('id_sucursal=%s', $sucursal);
        }

        if(!isEmpty($fecha_desde)){
            $where = !isEmpty($where) ? "$where and " : $where;
            $where .= sprintf("fecha_creacion >= '%s'", $fecha_desde);
        }

        if(!isEmpty($fecha_hasta)){
            $where = !isEmpty($where) ? "$where and " : $where;
            $where .= sprintf("fecha_creacion <= '%s'", $fecha_hasta);
        }

        $query = 'select * from reporte_venta_detalle';
        $query = isEmpty($where) ? $query : "$query where $where";

        return $this->db->queryToArray($query);
    }
}