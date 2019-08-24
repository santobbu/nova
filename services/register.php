<?php
	error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
	
	if (!defined('_VALID_SETTING')) {
	    define( '_VALID_SETTING', 0x0001 );
	}
	
	require_once( 'module/applicationSettings.php' );
	include_once( ABSPATH. CONNECTION. 'register.php' );

	$conn = new Register();

	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

		$data = json_decode(file_get_contents('php://input'), true);

		if( $data['action'] == 'register' ) {

			$response = array( 'status' => 'failed', 'reason' => '', 'identifier' => 0 );

			$resp = $conn->register_member( $data['data'] );

			// incase the customer id return correctly
			if( ctype_digit($resp) ) {

				$response['status'] = 'success';
				$response['identifier'] = $data['data']['identifier'];

				// email sending to inform customer
				if( $config['enablemail']) {
					
					//inform customer 
					$mail = new Mailer();
					$mail->register_completed( $data['data'] );

				} else {

					echo 'Mail not enable';

				}

			} else {
				$response['reason'] = $resp;
			}

			echo json_encode( $response );

		} else if( $data['action'] == 'report' ) {

			$response = array( 'status' => 'success', 'data' => '' );
			$response['data'] = $conn->get_report( $data['startdate'], $data['enddate'] );

			echo json_encode( $response );
		}

	} else {

		echo 'Invalid Method';

	}
?>