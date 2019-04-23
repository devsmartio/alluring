<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FastMailer
 *
 * @author Bryan Cruz
 */
class FastMailer {
    public static $selfInstance = null;
    private $mail;
    
    public static function getMe(){
        if(self::$selfInstance == null){
            self::$selfInstance = new self();
        }
        return self::$selfInstance;
    }
    
    public function sendMail($from, $fromName, $addresses, $subject, $body){
        $this->mail = null;
        $this->mail = new phpmailer();
        $this->mail->IsHTML(true);
        $this->mail->WordWrap = 50;
        $this->mail->Host = "postmaster@localhost";
        $this->mail->From = $from;
        $this->mail->FromName = $fromName;
        foreach($addresses as $a){
            $this->mail->addAddress($a['EMAIL'], $a['NAME']);
        }
        $this->mail->Subject = $subject;
        $this->mail->Body = $body;
        if(!$this->mail->Send()){
            return $this->mail->ErrorInfo;
        } else {
            return true;
        }
    }
    
    
    //No usada por el momento
    public function sendTemplate($from, $fromName, $addresses, $subject, $template, $templateParams = array()){
        ob_start();
        $this->getTemplate($template, $params);
        $this->mail = null;
        $this->mail = new phpmailer();
        $this->mail->IsHTML(true);
        $this->mail->WordWrap = 50;
        $this->mail->Host = "postmaster@localhost";
        $this->mail->From = $from;
        $this->mail->FromName = $fromName;
        foreach($addresses as $a){
            $this->mail->addAddress($a['EMAIL'], $a['NAME']);
        }
        $this->mail->Subject = $subject;
        $html = file_get_contents(MAIL_TEMPLATE . DS . $template);
        $this->mail->Body = $html;
        if(!$this->mail->Send()){
            return $this->mail->ErrorInfo;
        } else {
            return true;
        }
    }
    
    private function getTemplate($template, $params){
        
    }
}

?>
