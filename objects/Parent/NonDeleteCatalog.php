<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NonDeleteCatalog
 *
 * @author baci5
 */
abstract class NonDeleteCatalog extends FastMaintenance{
    protected $deleteFlagName = 'eliminado';
    
    public function getRows(){
        try {
            $resultSet = $this->db->query_select($this->table, sprintf("%s=0", $this->deleteFlagName));
            foreach($this->fields as $f){
                $i = 0;
                while(count($resultSet) > $i){
                    if($f instanceof FastField){
                        if($f->valueType == 'text'){
                            $resultSet[$i][$f->name] = self_escape_string($resultSet[$i][$f->name]);
                        }
                    }
                    $i++;
                }
            }
            $resultSet = $this->specialProcessBeforeShow($resultSet);
        } catch(Exception $e){
            error_log($e->getTraceAsString());
        }
        echo json_encode(array('data' => $resultSet));
    }
    
    public function doDelete(){
        $r = 1;
        $msg = 'Eliminado';
        $pkFields = array();
        $where = '';
        try {
            $pkFields = array();
            foreach($this->fields as $f){
                if($f instanceof FastField){
                    if($f->isPk){
                        $pkFields[$f->name] = $f->getValue();
                    }
                }
            }
            $pkFields = $this->processPkFields($pkFields);
            foreach($pkFields as $k => $v){
                $where = isEmpty($where) ? $where : sprintf('%s AND ', $where);
                $where.= sprintf('%s="%s"', $k, $v);
            }
            $user = AppSecurity::$UserData["data"];
            $date = new DateTime();
            $toUpdate = [
                $this->deleteFlagName => "1",
                'usuario_eliminacion' => sqlValue(decode_email_address($user["ID"]), 'text'),
                'fecha_eliminacion' => sqlValue($date->format('Y-m-d H:i:s'), 'date')
            ];
            $this->db->query_update($this->table, $toUpdate, $where);
        } catch (Exception $e){
            $r = 0;
            $msg = 'Error desconocido, contacte a soporte';
            error_log($e->getTraceAsString());
        }
        echo json_encode(array('result' => $r, 'msg' => $msg));
    }
}
