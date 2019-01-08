<?php
/**
 * Description of MantCentrosConsumo
 *
 * @author Bryan Cruz
 */
class MantPerfiles extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantPerfiles';
        $this->table = 'app_profile';
        $this->setTitle('Mantenimiento de perfiles');
        $status = array(
            "Activo" => 1,
            "Deshabilitado" => 0
        );
        $this->fields = array(
            new FastField('Id', 'ID', 'text', 'int', TRUE, null, array(), false, null, true),
            new FastField('Nombre', 'NAME', 'text', 'text'),
            new FastField('Estado', 'STATUS', 'select', 'int', true, null, $status)
        );
        $this->gridCols = array(
            'ID' => 'ID',
            'Nombre' => 'NAME',
            'Estado' => 'STATUS',
        );
    }
}