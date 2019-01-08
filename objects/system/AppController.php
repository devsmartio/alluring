<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreModule
 *
 * @author Bryan Cruz
 */
class AppController{
    
    private $modules;
    private $mod;
    private $db;
    private $title;
    private $instanceName;
    private static $selfInstance = null;    
    private $mensaje;
    private $browser;
    
    public static final function getApp(){
        include_once CONFIG . '/includes.config.php';
        if(self::$selfInstance == null){
            self::$selfInstance = new self();
        }
        return self::$selfInstance;
    }
    
    function __construct() {
        $this->db = DbManager::getMe();
        $this->instanceName = "CoreModule";
        $this->mod = null;
        $this->title = "Perfil";
        $this->modules = array();
    }
    
    public function init(){
        include APP . "/main.php";
    }
    
    public function setMod($mod){
        if(class_exists($mod)){
            if (!array_key_exists($mod, $this->modules)) {
                $module = new $mod;
                $this->modules[$mod] = $module;
            } else {
                $module = $this->modules[$mod];
            }
        } else {
            $module = GeoModError::getMe();
        }
        $this->mod = $module;
    }
    
    public function run(){
        $defaultModule = "";
        //Check browser only if not ajax request
        /*
        if(isEmpty(getParam(AJAX))){
            if(!$this->isValidBrowser()){
                include_once VIEWS  . '/sorry.html';
                return;
            }
        }
        */
        $sec = AppSecurity::getMe();
        if($sec->isLogged()){ 
            $this->loadModules();
            if(!isEmpty(getParam("action"))){
                $action = getParam("action");
                switch($action){
                    case 'lo':{
                        $sec->logOut();
                        break;
                    }
                    case 'pdf':{
                        $pdf = PDFManager::getMe();
                        $pdf->generatePdf();
                    }
                }
            }
            $isActive = $this->db->queryToArray('select valor  from variables_sistema where nombre="FECHA_EXP" && cast(now() as date) >= cast(valor as date)');
            if(count($isActive) > 0){
                $this->setMod('GeoAdmin');
                $this->init();
            } else if(isset($_GET[AJAX])){
                if(isset($_GET['act']) && isset($_GET['mod'])){
                    $module = $_GET['mod'];
                    $method = $_GET['act'];
                    $this->setMod($module);
                    $this->mod->$method();
                }
            } else {
                if(!isEmpty(getParam("mod"))){
                    $this->setMod(getParam("mod"));
                } else {
                    try {
                        $modulo = $this->db->queryToArray('select
                            am.PATH, ad.id_profile
                            from app_defaultscreen ad join app_modules am
                            on ad.id_module = am.ID 
                            where ad.id_profile = '.$_SESSION[USER_TYPE]
                        );
                        if (count($modulo) > 0){
                            foreach($modulo as $m){
                                $defaultModule = $m['PATH'];
                            }
                            $this->setMod($defaultModule);
                        } else {                       
                            $this->setMod('GeoModError');
                        }    
                    } catch(Exception $e){
                        $this->setMod('GeoModError');
                    } 
                }
                $this->init();
            }
        } else {
            $sec->handleOutsideReq();
        }
    }
    
    private function isValidBrowser(){
        $bm = new Browser();
        $bm->Browser();
        #Browser::getMe()->Browser();
        $allowedBot = array(Browser::BROWSER_BINGBOT, Browser::BROWSER_GOOGLEBOT, Browser::BROWSER_MSNBOT);
        $allowedIos = array(Browser::BROWSER_CHROME, Browser::BROWSER_SAFARI);
        $allowedAndroid = array(Browser::BROWSER_CHROME, Browser::BROWSER_FIREFOX);
        $allowedDesktop = array(Browser::BROWSER_CHROME, Browser::BROWSER_FIREFOX);
        if($bm->isFacebook()){
            return true;
        }
        if($bm->isRobot()){
            if(in_array($bm->getBrowser(), $allowedBot)){
                return true;
            }
            return false;
        }
        if($bm->isMobile()){
            if($bm->getPlatform() == Browser::PLATFORM_ANDROID){
                if(in_array($bm->getBrowser(), $allowedAndroid)){
                    return true;
                }
                return false;
            } elseif($bm->getPlatform() == Browser::PLATFORM_IPAD ||
                    $bm->getPlatform() == Browser::PLATFORM_IPHONE ||
                    $bm->getPlatform() == Browser::PLATFORM_IPOD){
                if(in_array($bm->getBrowser(), $allowedIos)){
                    return true;
                }
                return false;
            }
            return false;
        }
        if($bm->isTablet()){
            if($bm->getPlatform() == Browser::PLATFORM_ANDROID){
                if(in_array($bm->getBrowser(), $allowedAndroid)){
                    return true;
                }
                return false;
            } elseif($bm->getPlatform() == Browser::PLATFORM_IPAD ||
                    $bm->getPlatform() == Browser::PLATFORM_IPHONE ||
                    $bm->getPlatform() == Browser::PLATFORM_IPOD){
                if(in_array($bm->getBrowser(), $allowedIos)){
                    return true;
                }
                return false;
            }
            return false;
        }
        if($bm->getPlatform() == Browser::PLATFORM_WINDOWS ||
            $bm->getPlatform() == Browser::PLATFORM_WINDOWS_CE ||
            $bm->getPlatform() == Browser::PLATFORM_APPLE ||
            $bm->getPlatform() == Browser::PLATFORM_LINUX){
            if(in_array($bm->getBrowser(), $allowedDesktop)){
                    return true;
            }
            return false;
        }
        return false;
    }
    
    private function loadModules(){
        $modules = ModulosTable::getMe()->loadAdmitted();
        foreach($modules as $m){
            include_once OBJ . DS . $m['CATEGORY'] . DS . $m['MODULE'] . '.php';
        }
    }
    
    private function getModulesByCategory(){
        return ModulosTable::getMe()->getModulesByCategory();
    }
    
    private function getModules(){
        $modules = ModulosTable::getMe()->getModules();
        return $modules;
    }
}

?>
