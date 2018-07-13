<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of pdfmanager
 *
 * @author Bryan Cruz
 */
class PDFManager {
    private static $selfInstance = null; 
    private $user;
    private $db;
    private $gallery;
    
    public static function getMe(){
        if(self::$selfInstance == null){
            self::$selfInstance = new self();
        }
        return self::$selfInstance;
    }
    
    public function removeTmp(){
        if(file_exists("tmp/" . $this->user['ID'] . ".pdf")){
            unlink("tmp/" . $this->user['ID'] . ".pdf");
        }
    }
     
    function __construct(){
        $this->db = DbManager::getMe();
        $userData = AppSecurity::$UserData;
        $this->user = $userData['data'];
    }
    
    public function generatePdf(){
        $template = getParam('tmp');
        switch($template){
            case "VT": {
                $ventaId = getParam("id_venta");
                $template = "print_venta.phtml";
                break;
            }
            case "TS": {
                $movimientoId = getParam("id_movimiento_sucursales");
                $template = "print_traslado_sucursales.phtml";
                break;
            }
            case "CP": {
                $id_cambio = getParam('id_cambio_producto');
                $template = "print_cambio_producto.phtml";
                break;
            }
        }
        
        ob_start();
        require_once(LIB . DS . "dompdf-master/dompdf_config.inc.php");
        require PDF_TEMPLATE . "/$template";
        $dompdf = new DOMPDF();
        $dompdf->set_paper('letter');
        $dompdf->load_html(ob_get_clean());
        $dompdf->render();
        $dompdf->stream("imprimible.pdf", array("Attachment" => false));
        exit(0);
    }
    
    public function generateCustomPdf(){
        $template = getParam('tmp');
        if((isEmpty(getParam('ind')) || !$this->isValidUser(getParam('ind'))) && 
                isEmpty(getParam('tmpUsr')) || !$this->isValidUser(getParam('tmpUsr'))){
            die('Error al generar PDF');
        }
        ob_start();
        require_once(LIB . DS . "dompdf-master/dompdf_config.inc.php");
        require PDF_TEMPLATE . "/$template.php";
        $dompdf = new DOMPDF();
        $dompdf->set_paper('letter');
        $dompdf->load_html(ob_get_clean());
        $dompdf->render();
        $dompdf->stream('hoja_de_vida.pdf');
    }
    
    public function getTemplate(){
        $template = getParam('tmp');
        include PDF_TEMPLATE . "/$template";
    }
    
    public function isValidUser($id){
        $user = $this->db->query_select('app_user', sprintf('ID="%s" AND FK_PROFILE=1', $id));
        if(count($user) > 0){
            return true;
        }
        return false;
    }
}

?>
