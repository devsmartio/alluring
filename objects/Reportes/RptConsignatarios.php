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
class RptConsignatarios extends FastReport {
    function __construct() {
        parent::__construct();
        $this->instanceName = "RptConsignatarios";
        $this->excelFileName = "reporte_consignatarios";
        $this->setPrefix('rpt_consignatarios');
        $this->setTitle('Consignatarios');

        $this->setParams([]);
                
        $this->columns = [
            new FastReportColumn('Nombre', 'nombre_cliente', 'sanitize'),
            new FastReportColumn('Fecha Recepción', 'fecha_recepcion'),
            new FastReportColumn('Origen', 'bodega_origen', 'sanitize'),
            new FastReportColumn('Destino', 'bodega_destino', 'sanitize'),
            new FastReportColumn('% Mínimo', 'porcentaje', "number_format"),
            new FastReportColumn('Días Consignación', 'dias_consignacion', "number_format_inverse"),
            new FastReportColumn('Piezas' , 'piezas', "number_format_inverse"),
            new FastReportColumn('Total' , 'total', "number_format"),
            new FastReportColumn('Fecha máxima' , 'fecha_maxima')
        ];
        $this->useDefaultView = true;
    }
    
    protected function fieldsAreValid() {
        return true;
    }

    protected function getResultSet() {
        $query = 'select * from reporte_consignatarios_pendientes';

        return $this->db->queryToArray($query);
    }
}

