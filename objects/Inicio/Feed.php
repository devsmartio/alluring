<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Feed
 *
 * @author GeoDeveloper
 */
class Feed extends FastModWrapper{
    function __construct() {
        parent::__construct();
        $this->setTitle('Mensajes de Nutrivesa');
        $this->gallery = GalleryManager::getMe();
        $this->gallery->responseFather = 'Feed';
    }
    
    protected function showMiddle() {
        include VIEWS . '/feed.phtml';
    }
    
    public function myJavascript() {
        parent::myJavascript();
        ?>
    <script>
        app.controller('WrapperCtrl', function($scope, $http){
            $scope.ajaxUrl = "./?mod=Feed&<?php echo AJAX ?>=true";
            $http.get($scope.ajaxUrl + '&classMethod=getNotices').success(function(response){
                $scope.notices = response.data;
            });
        });
    </script>
        <?php
    }
    
    public function getNotices(){
        $date = new DateTime();
        $date->modify('-1 month');
        $this->db->query_delete('app_feeder', sprintf('CREATED < "%s"', $date->format('Y-m-d H:i:s')));
        $notices = $this->db->query_select('app_feeder', null, 'CREATED');
        $notices = sanitize_array_by_keys($notices, array('CONTENT'));
        for ($i = 0;count($notices) > $i; $i++){
            $image = $this->db->query_select('dts_files', sprintf('FK_FEED=%s', $notices[$i]['ID']));
            if(count($image) == 1){
                $image = $image[0];
                $notices[$i]['HAS_IMAGE'] = 1;
                $notices[$i]['IMAGE'] = $image['NAME'];
            } else {
                $notices[$i]['HAS_IMAGE'] = 0;
            }
            $user = $this->db->query_select('app_user', sprintf('ID="%s"', $notices[$i]['FK_USER']));
            if(count($user) > 0){
                $user = $user[0];
                $name = self_escape_string($user['FIRST_NAME'] . " " . $user['LAST_NAME']);
            } else {
                $name = 'Desconocido';
            }
            $notices[$i]['POSTED_BY'] = $name;
            $notices[$i]['POSTED_PIC'] = $this->getPic($notices[$i]['FK_USER']);
        }
        echo json_encode(array('data' => $notices));
    }
    
    public function getPic($id){
       $result = $this->db->query_select('app_user', sprintf('ID="%s" AND NOT ISNULL(PROFILE_PIC)', $id));
       if(count($result) == 0){
           $profilePic = 'no-img.png';
       } else {
           $profilePic = $this->gallery->getFiles(array('ID' => $result[0]['PROFILE_PIC']));
           $profilePic = $profilePic[0]['NAME'];
       }
       return $profilePic;
   }
}

?>
