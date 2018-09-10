<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 9/9/2018
 * Time: 6:39 PM
 */

class RptVentas extends FastReport {
    function __construct() {
        parent::__construct();
        $this->instanceName = "RptVentas";
        $this->excelFileName = "Reporte de Ventas";
        $this->setPrefix('rpt_ventas');
        $this->setTitle('Ventas');

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
            new FastField('Fecha hasta', 'fecha_hasta', 'text', 'text', true, null, array(), false),
            new FastField('Cliente', 'id_cliente', 'select', 'int', true, null, $clientes, false)
        ];

        $this->setParams($params);
        $this->columns = [
            new FastReportColumn("Bodega", "nombre_sucursal", "sanitize"),
            new FastReportColumn("Cliente", "nombre_cliente", "sanitize"),
            new FastReportColumn("Venta Total", "venta", "number_format_inverse")
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
        $id_cliente = getParam('id_cliente');


        $where = '';
        if(!isEmpty($sucursal)){
            $where = sprintf('id_sucursal=%s', $sucursal);
        }

        if(!isEmpty($fecha_desde)){
            $where = !isEmpty($where) ? "$where and " : $where;
            $where .= sprintf('fecha_desde=%s', $fecha_desde);
        }

        if(!isEmpty($fecha_hasta)){
            $where = !isEmpty($where) ? "$where and " : $where;
            $where .= sprintf('fecha_hasta=%s', $fecha_hasta);
        }

        if(!isEmpty($id_cliente)){
            $where = !isEmpty($where) ? "$where and " : $where;
            $where .= sprintf('id_cliente=%s', $id_cliente);
        }

        $query = 'select * from reporte_ventas';
        $query = isEmpty($where) ? $query : "$query where $where";

        return $this->db->queryToArray($query);
    }
}