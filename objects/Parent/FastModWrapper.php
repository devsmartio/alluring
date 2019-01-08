<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FastMaintenance
 *
 * @author Bryan Cruz y Ricardo Roesch
 */
class FastModWrapper implements BaseMod{
    
    protected $db;
    public $childs;
    private $title;
    public $instanceName;
    protected $user;
    protected $icon;
    protected $iconDesc;
    protected $showSideBar;
    
   function __construct() {
        $this->db = DbManager::getMe();
        $user = AppSecurity::$UserData;
        $this->user = $user['data'];
        $this->showSideBar = true;
   }
   
    public function init() {
        $this->showMe();
    }

    public function showSideBar() {
        return $this->showSideBar;
    }
    
    protected function showMiddle(){
        ?>
        <div class="alert alert-info">Estará listo pronto</div>
        <?php
    }
    
    protected function showMe(){
        ?>
        <div class="col-md-12 col-lg-12 p-0 m-0">
            <div class="row">
                <?php 
                $this->alertMe();
                ?>
            </div>
            <?php 
            $this->showMiddle();
            ?>
        </div>
        <?php
    }
    
    public function alertMe(){
        ?>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" id="alertsCont" style="position: fixed; z-index:99999999; ">
            <div role="alert" class='alert alert-dismissible myAlert {{alert.type}}' ng-repeat='alert in alerts'>
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">x</span>
                    <span class="sr-only">Cerrar</span>
                </button>
                <p style="display: inline-block" ng-bind-html="alert.msg"></p>
            </div>
        </div>
        <?php
    }

    public function myStyle() {
        ?>
<!--        <link rel="stylesheet" href="media/css/FastModWrapper.min.css"/>-->
        <?php 
    }

    public function myTitle() {
        return $this->title;
    }    //put your code here
    
    protected function setTitle($title){
        $this->title = $title;
    }
    
    protected function validChilds(){
        foreach($this->childs as $ch){
            if($ch instanceof BaseWrappedMod){
                //Go on
            } else {
                return false;
            }
        }
    }
    
    public function getMail(){
        $mg = InboxManager::getMe();
        $mail = $mg->getMail();
        $fav_mail = $mg->getFavorites();
        $newMsgs = $mg->newMessages();
        echo json_encode(
                array(
                    'mail' => array(
                        'inbox' => $mail,
                        'newMessages' => $newMsgs,
                        'fav_mail' => $fav_mail
                    ), 
                )
            );
    }
    
    public function readMe(){
        $id = getParam('id');
        InboxManager::getMe()->readMe($id);
    }
    
    public function addToFav(){
        $id = getParam('id');
        InboxManager::getMe()->addToFav($id);
    }
    
    public function removeFavMail(){
        $id = getParam('id');
        InboxManager::getMe()->removeFav($id);
    }
    
    public function removeMail(){
        $id = getParam('id');
        InboxManager::getMe()->remove($id);
    }
    
    public function levelUpdate(){
        echo json_encode(AppSecurity::getMe()->levelUpdate());
    }

    public function myJavascript() {}
}

?>
