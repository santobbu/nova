<?php 
defined( '_VALID_SETTING' ) or die( 'Direct Access to this location is not allowed.' );
date_default_timezone_set('Asia/Bangkok');

class mailSenderMG{
	
    private $mail = null;

     function __construct(){
     	global $config;
     	
        $this->mail = new PHPMailer;
        $this->mail->CharSet = "UTF-8";
        $this->mail->isSMTP();
        $this->mail->SMTPDebug = 0;
        $this->mail->Host = "smtp.mailgun.org";
        $this->mail->Port = 587;
        $this->mail->SMTPSecure = 'tls';
        $this->mail->SMTPAuth = true;
    }

    function send(){
        $receiver = $this->mail->send();
    }
    function addReceiver( $value ){
        $this->mail->addAddress($value, $value);
    }
    function setSender( $value, $password ){
        $this->mail->Username = $value;
        $this->mail->Password = $password;
        $this->mail->setFrom($value, $value);
    }
    function setSubject( $value ){
        $this->mail->Subject = $value;
    }
    function setBody( $value ){
       $this->mail->msgHTML($value);
    }     
    
}
?>