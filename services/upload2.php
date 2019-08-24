<?php
	error_reporting(0);
	
	if (!defined('_VALID_SETTING')) {
	    define( '_VALID_SETTING', 0x0001 );
	}
	
	require_once( 'module/applicationSettings.php' );
	
	$conn = new Common();
	$table = 'tb_uploaded_file';
	$keyField = 'fileid';	
	global $config;

	$return_arr = array(
		result => true,
		path => '',
		error => null
	);

	if ( $_FILES['file'] ) {
		try
		{
			//echo 'xxx';
			//return;

		    $tempPath 	= $_FILES[ 'file' ][ 'tmp_name' ];
		    $filename 	= $_FILES[ 'file' ][ 'name' ];
		    $emei 		= $_POST['imei'];//$_FILES[ 'imei' ];

		    $ext = pathinfo($filename , PATHINFO_EXTENSION);

		    $newName 	= $emei . '.' . $ext ;

			$fileDir = date('d-m-Y');
		    mkdir_r2($fileDir);

		    $newPath 	= $fileDir.'/'.$newName;

		    //echo 'temp path: '. $tempPath;

		    //get unique id from database
		    //$details['filename'] 	= $filename;
		    //$conn->insert_item( $table, $keyField, $details );
		    //$generatedFileId = $conn->get_last_insert_id( $table );

		    //format unique file name
		    //$filename = $generatedFileId. $filename;

		    //Move file to physical path
		    //$uploadPath = dirname( __FILE__ ) . '\slip\\' . $filename;
			$uploadPath = './passport/' . $newPath;
		    //echo 'upload path: ' . $uploadPath;
		    move_uploaded_file( $tempPath, $uploadPath );

			$return_arr['path'] = $newPath;
		
		}catch(Exception $ex){
			$return_arr['result'] = false;
			$return_arr['error'] = $ex->getMessage();
		}

	} else {

		$return_arr['result'] = false;
		$return_arr['error'] = 'File is not found';

	}

    echo json_encode( $return_arr );
?>