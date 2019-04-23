<?php
/**
 * Objects that handles all outside and inside logging requests
 *
 * @author Bryan Cruz
 * @property mixed $UserData Holds logged user data
 * @property AppSecurity $selfInstance Holds self instance
 * @property DbManager $db Handles all db requests
 */

class AppSecurity {
    
    public static $UserData;
    private static $selfInstance = null;
    private $db;
    
    /**
     * Constructs new AppSecurity
     */
    function __construct() {
        $this->db = DbManager::getMe();
    }
    
    /**
     * Instanciates new AppSecurity if object does not exist
     * @return AppSecurity 
     */
    public static function getMe(){
        if(self::$selfInstance == null){
            self::$selfInstance = new self();
        }
        return self::$selfInstance;
    }
    
    /**
     * Generates random password based on current UNIX timestamp in microseconds and md5 encoded with specific length
     * @param int $length
     * @return string Random password
     */
    public static function getRandomPass($length){
        return substr(str_shuffle(md5(microtime())), 0, $length);
    }
    
    /**
     * Verifies if data is valid and creates a new user 
     */
    public function createUser(){
        if(getParam("pass") !== getParam('confPass')){
            header("Location:?mess=p");
        } elseif(!filter_var(getParam('correo'), FILTER_VALIDATE_EMAIL)) {
            header("Location:?mess=d");
        }else {
            $user = encode_email_address(getParam("correo"));
            $exists = $this->db->query_select('app_user', sprintf('ID="%s"', $user));
            if(count($exists) > 0){
                header("Location: ./?mess=a_u");
                return;
            }
            $pass = getParam("pass");
            $name = getParam("nombre");
            $last = getParam("apellido");
            $date = new DateTime();
            $insert = array(
                "ID" => sqlValue($user, "text"),
                "FIRST_NAME" => sqlValue($name, "text"),
                "LAST_NAME" => sqlValue($last, "text"),
                "PASSWORD" => sqlValue(md5($pass), "text"),
                "FK_PROFILE" => sqlValue(1, "text"),
                'CREATED' => sqlValue($date->format('Y-m-d H:i:s'), 'date'),
                'LAST_LOGIN' => sqlValue($date->format('Y-m-d H:i:s'), 'date')
            );
            try {
                $this->db->query_insert("app_user", $insert);
                $newDir = PATH_UPLOAD_GENERAL . "/" . $user;
                mkdir($newDir, 0755);
                $this->firstLogIn($user, $pass);
            } catch (Exception $e){
                echo $e->getTraceAsString();
            }
        }
    }
    
    /**
     * Verifica el usuario y loguea la primera vez que se inscriben
     * @param string $user
     * @param string $pass
     */
    private function firstLogIn($user, $pass){
        $encoded = $user;
        $r = $this->db->queryToArray(sprintf("select * from app_user where ID='%s' and PASSWORD='%s' ", $encoded, md5($pass)));
        if (count($r) > 0) {
            $r = $r[0];
            self::$UserData = array("data" => $r);
            $_SESSION[USER] = serialize(self::$UserData);
            $_SESSION['firstView'] = true;
            $_SESSION[USER_TYPE] = self::$UserData['data']['FK_PROFILE'];
            header('Location:./');
        } else {
            header('Location:./?mess=nv');
        }
    }
    
    /**
     * Checks if user is logged in
     * @return boolean TRUE is session is set and user is set, returns FALSE if not
     */
    public function isLogged() {
        if (isset($_SESSION[USER])) {
            self::$UserData = unserialize($_SESSION[USER]);
            return true;
        }
        return false;
    }
    
    /**
     * Checks for valid individual user as individual or temporal user
     * @return boolean TRUE is user is valid as temp user or real user. FALSE if not valid
     */
    public function validUser() {
        if (!isEmpty(getParam("USER_LOG")) && !isEmpty(getParam("PASS_LOG"))) {
            $encoded = encode_email_address(getParam('USER_LOG'));
            $r = $this->db->queryToArray(sprintf("select * from app_user where ID='%s' and PASSWORD='%s' ", $encoded, md5(getParam("PASS_LOG"))));
            if (count($r) > 0) {
                $r = $r[0];
                self::$UserData = array("data" => $r);
                $_SESSION[USER] = serialize(self::$UserData);
                $_SESSION['firstView'] = true;
                $_SESSION[USER_TYPE] = self::$UserData['data']['FK_PROFILE'];
                return true;
            } 
        } 
        return false;
    }
    
    public function isValidUser($user){
        $encoded = encode_email_address($user);
        $result = $this->db->query_select('app_user', sprintf('ID="%s"', $encoded));
        if(count($result) > 0){
            return true;
        }
        return false;
    }
    
    /**
     * Initializes Module
     */
    public function init(){
        include SEC . "/main.php";
    }
    
    /**
     * Checks for a valid user and redirects to correct area
     */
    public function logIn(){
        if($this->validUser()){
            #$this->log("Ha ingresado al sistema", "IN");
            header("Location:./");
        } else {
            header("Location:?mess=nv");
        }
    }
    
    /**
     * Destroys session and logs out
     */
    public function logOut(){
        session_destroy();
        header("Location:?mess=lo");
    }
    
    /**
     * Logs in database user changes and entries
     * @param string $entry Entry text
     * @param string $type Type of transaction
     */
    public function log($entry, $type){
        $userData = self::$UserData;
        $user = $userData['data']['ID'];
        $insert = array(
            "DESCRIPTION" => sqlValue($entry, "text"),
            "DATETIME" => sqlValue(now(), "date"),
            "FK_USER" => sqlValue(encode_email_address($user), 'text'),
            "TYPE" => sqlValue($type, "text")
        );
        $this->db->query_insert("app_user_log", $insert);
    }
    
    /**
     * If user not loggued in, handles limited outside requests
     */
    public function handleOutsideReq(){
        if(!isEmpty(getParam("action"))){
            switch(getParam("action")){
                case "li" : {
                    $this->logIn();
                    break;
                }
                case "nu" : {
                    $this->createUser();
                    break;
                }
                case 'reset':{
                    if(!isEmpty('auth')){
                        $auth = getParam('auth');
                        $sql = sprintf('SELECT * FROM app_pass_req WHERE AUTH="%s" AND EXPIRES > NOW()', $auth);
                        $result = $this->db->queryToArray($sql);
                        if(count($result) > 0){
                            include VIEWS . "/reset.php";
                        } else {
                            $this->db->query_delete('app_pass_req', sprintf('AUTH="%s"', $auth));
                            header('Location: ./?mess=pass_not_valid');
                        }
                    } else {
                        header('Location: ./?mess=pass_not_valid');
                    }
                    break;
                }
                case 'reset_sent': {
                    $auth = getParam('auth');
                    $pass = getParam('PASS');
                    try {
                        $request = $this->db->query_select('app_pass_req', sprintf('AUTH="%s"', $auth));
                        $request = $request[0];
                        $update = array(
                            'PASSWORD' => sqlValue(md5($pass), 'text')
                        );
                        $this->db->query_update('app_user', $update, sprintf('ID="%s"', $request['FK_USER']));
                        $this->db->query_delete('app_pass_req', sprintf('AUTH="%s"', $auth));
                        header('Location: ./?mess=pass_done');
                    } catch (Exception $e){
                        error_log($e->getTraceAsString());
                    }
                    break;
                }
                case 'reset_req':{
                    $user = getParam('usuario');
                    if($this->isValidUser($user)){
                        if(!$this->hasPasswordRequest($user)){
                            try {
                                $encoded = encode_email_address($user);
                                $insert = array(
                                    'FK_USER' => sqlValue($encoded, 'text')
                                );
                                $this->db->query_insert('app_pass_req', $insert);
                                header('Location: ./?mess=req_sent');
                            } catch(Exception $e){
                                error_log($e->getTraceAsString());
                            }
                        } else {
                            header('Location: ./?mess=has_req');
                        }
                    } else {
                        header('Location: ./?mess=nv');
                    }
                    break;
                }
                default : {
                    $this->init();
                    break;
                }
            }
        } else {
            $this->init();
        }
    }
    
    private function hasPasswordRequest($user){
        $w = sprintf('FK_USER="%s"', $user);
        $result = $this->db->query_select('app_pass_req', $w);
        if(count($result) > 0){
            return true;
        }
        return false;
    }
}

?>
