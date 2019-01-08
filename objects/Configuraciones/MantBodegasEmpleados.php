
<?php

class MantBodegasEmpleados extends FastModWrapper{
    
    function __construct() {
        parent::__construct();
        $this->setTitle('Manejo bodegas empleados');
    }

    public function myStyle() {
        parent::myStyle();
        ?>
        <link rel="stylesheet" href="media/css/PermissionsManager.css" />
        <?php
    }

    protected function showMiddle() {
        include VIEWS . '/bodegas_empleados.phtml';
    }
    public function myJavascript() {
        ?>
        <script src="media/js/bodegas_empleados.js" type='text/javascript'></script>
        <?php
    } 
    
    public function getData(){
        $result = $this->db->query_select("app_user", "is_seller=1");
        $i = 0;
        while(count($result) > $i){
            $result[$i]['ALLOWED'] = array();
            $result[$i]['DENIED'] = array();
            foreach($this->getPermissions($result[$i]['ID']) as $p){
                if($p['TAG'] == 1){
                    $result[$i]['ALLOWED'][] = $p;
                } elseif($p['TAG'] == 0){
                    $result[$i]['DENIED'][] = $p;
                }
            }
            $i++;
        }
        echo json_encode(array('data' => sanitize_array_by_keys($result, ["FIRST_NAME", "LAST_NAME"])));
    }
    
    private function getPermissions($id){
        $permissionsSql = sprintf('select a.*, 1 as TAG 
                from sucursales as a'
                . ' where id_sucursal in ('
                . ' select id_bodega from usuarios_bodegas where id_usuario="%s"'
                . ' )'
                . ' union '
                . 'select a.*, 0 as TAG from sucursales as a'
                . ' where id_sucursal not in ('
                . ' select id_bodega from usuarios_bodegas where id_usuario="%s"'
                . ')', $id, $id);
        $result = $this->db->queryToArray($permissionsSql);
        return sanitize_array_by_keys($result, array('nombre')); 
    } 
    
    public function savePermissions(){
        $r = 1;
        $msg = "";
        $data = inputStreamToArray();
        $data = $data['data'];
        foreach($data as $user){            
            $this->db->query_delete('usuarios_bodegas',  sprintf('id_usuario="%s"',$user['ID']));
            foreach($user['ALLOWED'] as $bodega){
                try {
                $insert = array(
                    'id_usuario' => sqlValue($user['ID'], 'text'),
                    'id_bodega' => sqlValue($bodega['id_sucursal'], 'int'),
                );
                $this->db->query_insert('usuarios_bodegas', $insert);
                $r = 1;                
                } catch (Exception $e) {
                    $r = 0;
                    $msg = '¡Error al guardar datos!';
                    var_dump($e->getTraceAsString());
                }
            }
        }  
        echo json_encode(array('result' => $r, 'msg' => $msg));        
    }
}



