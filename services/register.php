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

			$response = array( 'status' => 'failed', 'reason' => '', 'identifier' => 0, 'customerid' => 0 );

			$resp = $conn->register_member( $data['data'] );

			// incase the customer id return correctly
			if( ctype_digit($resp) ) {

				$response['status'] = 'success';
				$response['identifier'] = $data['data']['identifier'];
				$response['customerid'] = $resp ;

			} else {
				$response['reason'] = $resp;
			}

			echo json_encode( $response );

		} else if ( $data['action'] == 'test' ) {

			$response = array( 'imageUrl' => "//api.qrcode.studio/tmp/7138cdb43bd3c8809bde22caffe2a173.png" );
			echo json_encode( $response );

		} else if ( $data['action'] == 'thankyou' ) {

			// Update user with qr url 
			$datainfo = array ();
			$datainfo['customerid'] = $data['data']['customerid'];
			$datainfo['qrurl'] = $data['data']['qrurl'];
			$conn->update_item( 'tb_customer', 'customerid', $datainfo );

			// save file to server
			$qrcodePath = "https:" . $datainfo['qrurl'];
			$fileExt = substr(strrchr($qrcodePath, '.'), 1);
			$target = ABSPATH. 'qrcode/' . $datainfo['customerid'] . '.' . $fileExt;

			$file = fopen ($qrcodePath , 'rb');
			if ($file) {
				$newf = fopen ($target, 'wb');
				if ($newf) {
					while(!feof($file)) {
						fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
					}
				}
			}
			if ($file) {
				fclose($file);
			}
			if ($newf) {
				fclose($newf);
			}

			// email sending to inform customer
			if( $config['enablemail']) {

				// get user data for sending email
				$cusDetail = $conn->get_by_key( 'tb_customer', 'customerid', $datainfo['customerid'] );

				if (isset($cusDetail)) {
					$cusDetail['qrpath'] = $datainfo['customerid'] . '.' . $fileExt;; 

					//inform customer 
					$mail = new Mailer();
					$mail->register_completed( $cusDetail );

				}else {
					echo 'Cannot sent email user not found';
				}

			} else {
				echo 'Mail not enable';
			}

			echo 'completed';

		} else if( $data['action'] == 'report' ) {

			$response = array( 'status' => 'success', 'data' => '' );
			$response['data'] = $conn->get_report( $data['startdate'], $data['enddate'] );

			echo json_encode( $response );
		} else if( $data['action'] == 'identifier' ) {

			$resp = $conn->get_by_key( 'tb_customer', 'identifier', $data['identifier'] );
			
			if( !empty($resp) ) {
				echo 'used';
			} else {
				echo 'ok';
			}

		} else {

			echo 'Invalid Action';
	
		}

	} else {

		echo 'Invalid Method';

	}
?>