<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of fastreportcolumn
 *
 * @author baci5
 */
class FastReportColumn {
    public $name;
    public $field;
    public $function;
    
    function __construct($name, $field, $function = null) {
        $this->name = $name;
        $this->field = $field;
        $this->function = $function;
    }
    
    function serveValue($row){
        $return = null;
        switch($this->function){
            case 'sanitize':
                $return = self_escape_string($row[$this->field]);
                break;
            case 'number_format':
                $return = number_format($row[$this->field], 2);
                break;
            case 'number_format_inverse':
                $return = number_format($row[$this->field], 0);
                break;
            case 'format_datetime':
                $return = DateTime::createFromFormat(SQL_DT_FORMAT, $row[$this->field])->format('d/m/Y H:i:s');
                break;
            case 'format_date':
                $return = DateTime::createFromFormat('Y-m-d', $row[$this->field])->format('d/m/Y');
                break;
            default:
                $return = $row[$this->field];
        }
        return $return;
    }
}
