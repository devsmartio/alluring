<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of thumbnailgen
 *
 * @author Bryan Cruz
 */
class ThumbnailGen {
    public static function generate(){
        if(!isEmpty(getParam('img'))){
            $img = getParam('img');
        } else {
            die('Error');
        }
        require_once LIB . '/thumbgen/ThumbLib.inc.php';
        if(isset($_GET['w'])){
            $w = $_GET['w'];
        } else {
            $w = 100;
        }
        if(isset($_GET['h'])){
            $h = $_GET['h'];
        } else {
            $h = 100;
        }
        $thumb = PhpThumbFactory::create($img, array('resizeUp' => true));
        $thumb->resize(50, 50);
        $thumb->show();
    }
}

?>
