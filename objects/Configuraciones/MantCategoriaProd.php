<?php
/**
 * Created by PhpStorm.
 * User: eder.herrera
 * Date: 7/26/2018
 * Time: 12:34 PM
 */

class MantCategoriaProd extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantCategoriaProd';
        $this->table = 'tipo';
        $this->setTitle('Mantenimiento Categoría de Productos');

        $agrupaciones = Collection::get($this->db, 'tipos_agrupaciones')->toSelectList('id_tipo_agrupacion', 'nombre');
        $this->fields = array(
            new FastField('Id', 'id_tipo', 'text', 'int', true, null, array(), FALSE, null, true),
            new FastField('Nombre', 'nombre', 'text', 'text', true),
            new FastField('Agrupación', 'id_tipo_agrupacion', 'select', 'int', true, null, $agrupaciones, false)
        );

        $this->gridCols = array(
            'Id' => 'id_tipo',
            'Nombre' => 'nombre',
            'Agrupación' => 'nombre_agrupacion'
        );
    }

    protected function specialProcessBeforeShow($rows){
        $agrupaciones = Collection::get($this->db, 'tipos_agrupaciones')->select(['id_tipo_agrupacion', 'nombre'], true);
        foreach($rows as &$row){
            $ag = $agrupaciones->where(['id_tipo_agrupacion' => $row['id_tipo_agrupacion']]);
            if($ag->any()){
                $ag = $ag->single();
                $row['nombre_agrupacion'] = self_escape_string($ag['nombre']);
            } else {
                $row['nombre_agrupacion'] = 'N/A';
            }
        }
        return sanitize_array_by_keys($rows, ['nombre','usuario_creacion']);
    }

    protected function specialProcessBeforeInsert($updateData, $pkFields = array()) {
        $date = new DateTime();
        $user = AppSecurity::$UserData['data'];
        $updateData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $updateData['usuario_creacion'] = sqlValue(self_escape_string($user['FIRST_NAME']), 'text');
        return $updateData;
    }
}