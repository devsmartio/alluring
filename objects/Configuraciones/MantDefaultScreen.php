<?php

class MantDefaultScreen extends FastMaintenance {
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantDefaultScreen';
        $this->table = 'app_defaultscreen';
        $this->setTitle('Mantenimiento Pantalla de inicio');
        $this->gridCols = array(
            'Perfil' => 'nombre_pefil', 
            'Modulo' => 'nombre_modulo'
        );
        
        $profiles = $this->db->query_select('app_profile');
        $prf = [];
        foreach($profiles as $p){
            $prf[self_escape_string($p['NAME'])] = $p['ID'];
        }

        $modules = $this->db->query_select('app_modules');
        $mdl = [];
        foreach($modules as $m){
            $mdl[self_escape_string($m['NAME'])] = $m['ID'];
        }                        
        
        $this->fields = array(
            new FastField('Perfil', 'id_profile', 'select', 'int', true, null, $prf, true),
            new FastField('Modulo', 'id_module', 'select', 'int', true, null, $mdl, true)
        );
    }
    
    protected function specialValidation($fields, $r, $mess, $pkFields){
        $regs = new Entity($fields);
          
        if(!$regs->existsAndNotEmpty('id_module')){
            $r = 0;
            $mess = "Debe seleccionar un modulo";
        }
        
        if(!$regs->existsAndNotEmpty('id_profile')){
            $r = 0;
            $mess = "Debe seleccionar un perfil";
        }

        $wp ='id_profile = '.$regs->get('id_profile');
        $pr = $this->db->query_select('app_defaultscreen',$wp);
        
        if(count($pr) > 0){
            $r = 0;
            $mess = "Este perfil ya tiene una pantalla de inicio";
        }
        
        $w ='id_profile = '.$regs->get('id_profile').' and id_module = '.$regs->get('id_module');
        $config = $this->db->query_select('app_defaultscreen',$w);
        
        if(count($config) > 0){
            $r = 0;
            $mess = "Ya existe esta configuracion de pantalla de inicio";
        }
        
        $wa ='FK_PROFILE = '.$regs->get('id_profile').' and FK_MODULE = '.$regs->get('id_module');
        $access = $this->db->query_select('app_profile_access',$wa);

        if(count($access) == 0){
            $r = 0;
            $mess = "El perfil seleccionado no tiene permisos de acceso al modulo";
        }         
        
        return array('r' => $r, 'mess' => $mess);
    }
    
   protected function specialProcessBeforeShow($rows){
                //no reeemplaces los valobres de la columnas porque va a tratar de guardar esos valores
       //en cambio usa indexes nuevo
        $perfiles = Collection::get($this->db, 'app_profile');
        $modulos = Collection::get($this->db, 'app_modules');

        for($i = 0; count($rows) > $i; $i++){
            $p = $perfiles->where(['ID' => $rows[$i]['id_profile']])->single();            
            $rows[$i]['nombre_pefil'] = $p['NAME'];
            $m = $modulos->where(['ID' => $rows[$i]['id_module']])->single();
            $rows[$i]['nombre_modulo'] = $m['NAME'];            
        }            
        return sanitize_array_by_keys($rows, array('nombre_pefil', 'nombre_modulo'));
    }
    
}
