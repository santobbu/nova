<?php 
defined( '_VALID_SETTING' ) or die( 'Direct Access to this location is not allowed.' );

class mailSender{
	
    private $to = null;
    private $from = null;
    private $subject = null;
    private $body = null;
    private $headers = null;

     function __construct($to, $subject, $body){
     	global $config;
     	
        $this->from    = $config['mailform'];
        $this->to      = $to;
        $this->subject = $subject;
        $this->body    = $body;
    }

    function send(){
        $this->addHeader('From: '.$this->from."\r\n");
        $this->addHeader('Reply-To: '.$this->from."\r\n");
        $this->addHeader('X-Mailer: PHP/' . phpversion()."\r\n");
        $this->addHeader("MIME-Version: 1.0\r\n");
        $this->addHeader('Return-Path: '. $this->from ."\r\n");
        $this->addHeader('Content-Type: text/html; charset=utf-8'. "\r\n");

        mail($this->to, $this->subject, $this->body, $this->headers);
    }

    function addHeader($header){
        $this->headers .= $header;
    }
    
    //Properties
    function setReceiver( $value ){
    	$this->to = $value;
    }
    function getSender( ){
    	return $this->from;
    }
    function setSender( $value ){
    	$this->from = $value;
    }
    function setSubject( $value ){
    	$this->subject = $value;
    }
    function setBody( $value ){
    	$this->body = $value;
    }     
    
}
?>