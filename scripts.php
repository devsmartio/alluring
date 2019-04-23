<?php

    include_once "libraries/db.inc.php";
    include_once "config/dbconnection.config.php";

    $db = DbManager::getMe();
    $date = new DateTime();

    $resultA = $db->query_select('descuentos', "cast(now() as date) between cast(fecha_inicio as date) and cast(fecha_fin as date) and activo = 0");
    $resultD = $db->query_select('descuentos', "cast(now() as date) not between cast(fecha_inicio as date) and cast(fecha_fin as date) and activo = 1");

    $i = 0;
    while (count($resultA) > $i) {
        $update = array(
            'activo' => 1             
        );
        $where ="id_descuento = '".$resultA[$i]['id_descuento']."'";  
        $db->query_update('descuentos', $update, $where);           
        $i++;
    }

    $i = 0;
    while (count($resultD) > $i) {
        $update = array(
            'activo' => 0             
        );
        $where ="id_descuento = '".$resultD[$i]['id_descuento']."'";  
        $db->query_update('descuentos', $update, $where);           
                $i++;
    }
     
