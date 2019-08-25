<?php 
defined( '_VALID_SETTING' ) or die( 'Direct Access to this location is not allowed.' );

class Mailer extends mailSenderNew {
	
	function __construct() {
		include_once( ABSPATH. CONNECTION. 'register.php' );
		global $config;
		parent::__construct();
	}
	
	/**
	*	On Registration completed
	*/
	function register_completed( $data ) {
		global $config;

	   	if( is_valid_email($config['mailform']) ) {
			$this->setSender( $config['mailform'], $config['mailformpass'] );	

		  	$this->addReceiver( $data['email'] );
	  	
			$content = file_get_contents(  ABSPATH. 'email/template/register_completed.htm' );
			
			//receiver detail
			$content = str_replace('{0:RECEIVER_NAME}', $data['firstname'], $content);
			$content = str_replace('{0:QR_CODE}', $data['qrurl'], $content);    

			$content = html_entity_decode($content);
		   	$this->setSubject('ขอขอบคุณที่ร่วมสนุกกับกิจกรรม');
	    	$this->setBody( $content );

		   	$this->send();
	    }
	}

	/**
	*	Acknowlege admin when registration completed
	*/
	function acknowleged_registration( $customerid ) {
		global $config;
		$conn = new Register();

	   	$data = $conn->get_customer_detail( $customerid );

		if( !isset($data) ) { return; }

	   	if( is_valid_email($config['mailform']) ) {
			$this->setSender( $config['mailform'], $config['mailformpass'] );

			$receivers = $config['mailreceiver'];
			foreach ($receivers as $receiver) {
  				$this->addReceiver( $receiver );
			}
	
			$content = file_get_contents(  ABSPATH. 'email/template/admin_acknowledge.htm' );
	   		
	   		//set email detail
	   		$content = str_replace('{0:NAME}', $data['firstname'], $content);

			$content = html_entity_decode($content);
		   	$this->setSubject( 'ลูกค้าลงทะเบียน' );
	    	$this->setBody( $content );    	
		   	/*echo 'mail detail ' . $content;
	    	echo '----------------------------------';*/
			$this->send();
	    }
	}

}


?>