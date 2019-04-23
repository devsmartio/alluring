<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MyProfile
 *
 * @author Edgar
 */
class MyProfile extends FastTransaction{
    function __construct() {
        parent::__construct();
        $this->setTitle('Mi perfil');
        $this->hasCustomSave = true;
        $this->instanceName = "MyProfile";
        $this->gallery = GalleryManager::getMe();
        $this->gallery->responseFather = 'MyProfile';
    }
    
    protected function showModule() {
        include VIEWS . DS . 'my_profile.phtml';
    }
    
   public function myJavascript() {
       parent::myJavascript();
       ?>
    <script>
        app.controller('ModuleCtrl', function($scope, $http){
            $http.get($scope.ajaxUrl + "&classMethod=getPic").success(function(r){
                $scope.profilePic = r.data;
            });
        });
    </script>
       <?php
   }
   
   public function getPic(){
       $userId = $this->user['ID'];
       $result = $this->db->query_select('app_user', sprintf('ID="%s" AND NOT ISNULL(PROFILE_PIC)', $userId));
       if(count($result) == 0){
           $profilePic = 'no-img.png';
       } else {
           $profilePic = $this->gallery->getFiles(array('ID' => $result[0]['PROFILE_PIC']));
           $profilePic = $profilePic[0]['NAME'];
       }
       echo json_encode(array('data' => $profilePic));
   }
   
   public function getErr(){
        $err = getParam('err');
        $this->gallery->getErr($err);
    }
    
    public function manageUploads(){
        if(file_exists($_FILES['file']['tmp_name']) || is_uploaded_file($_FILES['file']['tmp_name'])){
            $specialData = array(
                'CAPTION' => 'PROFILE_PIC'
            );
            $this->gallery->setSpecialDataToSave($specialData);
            if($this->gallery->sortUpload($_FILES['file'], false, true)){
                $userInfo = $this->db->query_select('app_user', sprintf('ID="%s" AND NOT ISNULL(PROFILE_PIC)', $this->user['ID']));
                if(count($userInfo) != 0){
                    $userInfo = $userInfo[0];
                    $path = PATH_UPLOAD_GENERAL . DS;
                    $this->db->query_delete('dts_files', sprintf('ID=%s', $userInfo['PROFILE_PIC']));
                    unlink($path . $userInfo['PROFILE_PIC']);
                }
                $resultPic = $this->db->queryToArray('select MAX(ID) as last from dts_files where ISNULL(FK_CATEGORY) AND CAPTION="PROFILE_PIC"');
                $idPic = $resultPic[0]['last'];
                $update = array(
                    'PROFILE_PIC' => sqlValue($idPic, 'text')
                );
                $this->db->query_update('app_user', $update, sprintf('ID="%s"', $this->user['ID']));
                header("Location:./?mod=MyProfile&status=ok");
            } else {
                $this->gallery->specialResponse();
            }
        } else {
            header("Location:./?mod=MyProfile&status=ok");
        }
    }
}
