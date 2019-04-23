<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of empleadoutil
 *
 * @author baci5
 */
class EmpleadoUtil {
    
    public static function empleadoExiste(){
        $user = AppSecurity::$UserData['data'];
        $empleados = Collection::get(DbManager::getMe(), 'empleados');
        $existe = (count($empleados->where(array('id_usuario' => $user['ID']))->toArray()) > 0);
        return $existe;
    } 
    
    public static function getEmpleado(){
        $user = AppSecurity::$UserData['data'];
        if(static::empleadoExiste()){
            $db = DbManager::getMe();
            $empleado = $db->query_select('empleados', sprintf("id_usuario='%s'", $user['ID']));
            return $empleado[0];
        }
        return false;
    }
    
    public static function esSuperUsuario(){
        $perfilSuperUsuario = Collection::get(DbManager::getMe(), 'variables_sistema')->where(['nombre' => 'Perfil_SuperUsuario'])->toArray();
        $perfilSuperUsuario = $perfilSuperUsuario[0]['valor'];
        return $perfilSuperUsuario == AppSecurity::$UserData['data']['FK_PROFILE'];
    }
}
