<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantObservacionesProyecto
 *
 * @author baci5
 */
class MantObservacionesProyecto extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantObservacionesProyecto';
        $this->table = 'observaciones_proyecto';
        $this->setTitle('Observaciones proyectos');
        $this->gridCols = array(
            'ID' => 'id_observacion_proyecto',
            'Proyecto' => 'nombre_proyecto',
            'Área' => 'nombre_area',
            'Creado por' => 'usuario_creacion',
            'Fecha creación' => 'fecha_creacion',
            'Modificado por' => 'usuario_modificacion',
            'Última modificación' => 'fecha_modificacion'
        );
        
        //Se obtiene solo el área a la cuál esta asociada el perfil. Sino tiene ninguna, no puede agregar comentarios
        $areaPerfil = $this->db->query_select("perfiles_areas", sprintf("id_perfil=%s", $this->user['FK_PROFILE']));
        $areaPerfil = count($areaPerfil) > 0 ? $areaPerfil[0]['id_area'] : 0;
        $areas = Collection::get($this->db, 'areas', sprintf("id_area=%s", $areaPerfil))->toSelectList('id_area', 'nombre');
        $proyectos = Collection::get($this->db, 'proyectos', 'activo=1')->toSelectList('id_proyecto', 'nombre');
        
        $this->fields = array(
            new FastField('ID', 'id_observacion_proyecto', 'text', 'int', true, null, array(), FALSE, null, true),
            new FastField('Proyecto', 'id_proyecto', 'select', 'int', true, null, $proyectos),
            new FastField('Área', 'id_area', 'select', 'int', true, null, $areas),
            new FastField('Observación', 'contenido', 'textarea', 'text', true)
        );
    }
    
    protected function specialValidation($fields, $r, $mess, $pkFields) {
        $w = "";
        if(isset($pkFields['id_observacion_proyecto']) && !isEmpty($pkFields['id_observacion_proyecto'])){
            $w = sprintf("id_proyecto!=%s and id_observacion_proyecto=%s", $fields['id_proyecto'],$pkFields['id_observacion_proyecto']);
            $count = $this->db->query_select("observaciones_proyecto", $w);
            if(count($count) > 0){
                $r = 0;
                $mess = self_escape_string(sprintf("Ha modificado el proyecto. Seleccione el proyecto asociado originalmente ('%s') o refresque e intente de nuevo", $count[0]['nombre']));
            }
        }
        return ['r' => $r, 'mess' => $mess];
    }
    
    public function getRows(){
        $resultSet = [];
        try {
            $user = AppSecurity::$UserData['data'];
            $sql = "select o.*, p.nombre nombre_proyecto, a.nombre nombre_area from "
                    . "observaciones_proyecto o "
                    . "join proyectos p on p.id_proyecto=o.id_proyecto and p.activo=1 "
                    . "join areas a on a.id_area=o.id_area "
                    . "join perfiles_areas pa on pa.id_area=a.id_area and pa.id_perfil=%s";
            $resultSet = $this->db->queryToArray(sprintf($sql, $user["FK_PROFILE"]));
            $resultSet = sanitize_array_by_keys($resultSet, ['nombre_proyecto', 'nombre_area', 'contenido', 'usuario_creacion', 'usuario_modificacion']);
        } catch(Exception $e){
            error_log($e->getTraceAsString());
        }
        echo json_encode(array('data' => $resultSet));
    }
    
    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(decode_email_address($user['ID']), 'text');
        return $updateData;
    }
    
    protected function specialProcessBeforeUpdate($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_modificacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_modificacion'] = sqlValue(decode_email_address($user['ID']), 'text');
        return $updateData;
    }
}
