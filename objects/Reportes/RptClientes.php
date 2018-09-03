<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RptClientes
 *
 * @author Usuario
 */
class RptClientes extends FastReport {
    function __construct() {
        parent::__construct();
        $this->instanceName = "RptClientes";
        $this->excelFileName = "Reporte Clientes";
        $this->setPrefix('rpt_clientes');
        $this->setTitle('Clientes');

        $this->setParams(array());
                
        $this->columns = [
            new FastReportColumn('Identificación', 'identificacion'),
            new FastReportColumn('Nombres', 'nombres', 'sanitize'),
            new FastReportColumn('Apellidos', 'apellidos', 'sanitize'),
            new FastReportColumn('NIT', 'factura_nit'),
            new FastReportColumn('Teléfono', 'numero'),
            new FastReportColumn('Dirección', 'direccion', 'sanitize'),
            new FastReportColumn('Crédito' , 'tiene_credito'),
            new FastReportColumn('Días Crédito' , 'dias_credito')
        ];
        $this->useDefaultView = true;
    }
    
    protected function fieldsAreValid() {
        return true;
    }
    
    protected function getResultSet() {
        $query = 'select * from reporte_clientes';
        return $this->db->queryToArray($query);
    }
}

