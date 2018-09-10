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

        $departamentos = Collection::get($this->db, 'departamentos')->select(['id_departamento','nombre'],true,['nombre'])->toSelectList('id_departamento','nombre');
        $vendedores = Collection::get($this->db, 'empleados')->where(['es_vendedor' => '1'])->toSelectList('id_empleado','nombres','apellidos');

        $params = [
            new FastField('Departamento', 'id_departamento', 'select', 'int', true, null, $departamentos, false),
            new FastField('Vendedor', 'id_empleado', 'select', 'int', true, null, $vendedores, false),
            new FastField('Nombres', 'nombres', 'text', 'text', true, null, array(), false),
            new FastField('Apellidos', 'apellidos', 'text', 'text', true, null, array(), false)
        ];

        $this->setParams($params);
                
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
        $id_departamento = getParam('id_departamento');
        $id_empleado = getParam('id_empleado');
        $nombres = getParam('nombres');
        $apellidos = getParam('apellidos');


        $where = '';
        if(!isEmpty($id_departamento)){
            $where = sprintf('id_departamento=%s', $id_departamento);
        }

        if(!isEmpty($id_empleado)){
            $where = !isEmpty($where) ? "$where and " : $where;
            $where .= sprintf('id_empleado=%s', $id_empleado);
        }

        if(!isEmpty($nombres)){
            $where = !isEmpty($where) ? "$where and " : $where;
            $where .= "nombres LIKE '%" . $nombres . "%'";
        }

        if(!isEmpty($apellidos)){
            $where = !isEmpty($where) ? "$where and " : $where;
            $where .= "apellidos LIKE '%" . $apellidos . "%'";
        }

        $query = 'select * from reporte_clientes';
        $query = isEmpty($where) ? $query : "$query where $where";

        return $this->db->queryToArray($query);
    }
}

