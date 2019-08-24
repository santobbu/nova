<?php
defined( '_VALID_SETTING' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * @param string $method
 * @return  associative array
 */
function get_param( $method = 'POST' )
{
	$param = array();
    $method	= strtoupper( $method );
	if( $method == "GET" )
	{
		foreach ( $_GET as $key => $val ) {
			$param[$key] = $val;
 		}
	}
	else if( $method == "POST" )
	{
		foreach ( $_POST as $key => $val ) {
			$param[$key] = $val;
		}
	}
	else if( $method == "AUTO")
	{
		foreach ( $_POST as $key => $val ) {
			$param[$key] = $val;
 		}

		if( !$param )
		{
			foreach ( $_GET as $key => $val ) {
				$param[$key] = $val;
			}
		}
	}
	return	$param;
}

function getDisplayMessage()
{
	$result = "Greatermans";
	if(isset($user['username']))
	{
		$result = $user['username'];
	}
	else
	{
		$result = "ยินดีต้อนรับผู้เยี่ยมชม";
	}
	return $result;
}

/**
 * @param string $message
 * @param string $title
 * @param  string $relate_link
 * @param string $tech_support
 */
function error( $message, $title = 'Error', $relate_link = '', $tech_support = '' )
{
	global $error;

    if( $error['handle_error'] ){
        $error['error_message']			= $message;
        $error['error_title']			= $title;
        $error['error_tech_support']	= $tech_support;
        $error['error_relate_link']		= $relate_link;
        session_start() or void();
        $_SESSION['error'] = $error;
        jumptopage( 'index.php?section=error', 'script');
        exit();
    }
    $error['handle_error'] = true;
}


/**
 *
 * @return  string IP
 */
function client_ip( ){

	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		$ip = getenv("HTTP_CLIENT_IP");
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
		$ip = getenv("REMOTE_ADDR");
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		$ip = $_SERVER['REMOTE_ADDR'];
	else
		$ip = "unknown";
	return $ip;
}

function is_valid_email($email_address) {

	return (preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email_address) > 0);
}

function is_valid_username($username) {
	$regex = '/(^[A-z_][\w-]{3,17})$/';
	return (preg_match($regex, $username));
}

/**
 * กระโดดไปที่หน้าที่กำหนด
 * $page = ชื่อหรือ URL ของหน้าที่จะกระโดดไป
 * $method = รูปแบบของคำสั่ง ได้แก่ HEADER และ SCRIPT
 * @param string $page
 */
function jumptopage( $page, $method = 'SCRIPT', $_param = array() )
{
	if( !$page )
		$page = 'index.php';
	$page = addslashes( $page );
    $pstr = ( strpos( $page, '?' ) === FALSE )? '?': '&';

    foreach( $_param as $key => $val )
    {
        $pstr .= $key.'='.$val.'&';
    }

	if( $method == 'SCRIPT' )
	{
		echo '<script type="text/javascript" language="javascript">';
		echo 'window.location.href = \'' . $page .$pstr.'\';';
		echo '</script>';
	}else{
		header( 'location: ' . $page )
		or
		jumptopage( $page.$pstr, 'SCRIPT' );
	}

	exit();
}

/**
 * เปลี่ยนอักขระพิเศษที่อาจเป็นคำสั่ง
 * ใช้คำสั่งนี้กรองข้อมูลเสมอก่อนการส่งคำสั่งไปยังฐานข้อมูล
 * @param mixed $str
 * @return mixed
 */
function escape( $str )
{
	if( is_numeric( $str ) )
		return $str;

    if( is_array($str) )
    {
        $ret = array();
        foreach( $str as $key => $val )
            $ret[$key] = htmlspecialchars( $val );
    }else{
	    $ret = htmlspecialchars( $str );
    }
	return $ret;
}

/**
 * แปลความหมายของรหัสผิดพลาดจากการอัพโหลดไฟล์
 *
 * @param int $error_code
 * @return string
 */
function translate_upload_error( $error_code )
{
	if( is_numeric($error_code) )
	{
		$err =	array(
		 0=>"There is no error, the file uploaded with success",
		 1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini",
		 2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
		 3=>"The uploaded file was only partially uploaded",
		 4=>"No file was uploaded",
		 6=>"Missing a temporary folder");

		return $err[$error_code];
	}else{
		return "";
	}
}

function has_permission( $per, $target )
{
	return $per & $target;
}

function chk_perm( $perm, $msg = 'Acess denied.' )
{
    global $user;
    global $param;
    if( !($user['permission'] & $perm) )
    {
        savelog( $param['section'].'.'.$param['action'], 'restrict '.$user['username']  );
        error(  $msg, 'Error' );
    }
}

function savelog( $section, $msg )
{
	if(defined( '_SAVELOG' )) {
		//echo 'writing 1 : ';
		//$folder = date('d-m-Y');
		$filename = date('d-m-Y'). '-log.txt';
		$filepath = 'log/'. $filename;
		//mkdir_r($folder);

		//echo 'writing 2 : ';
		$file = fopen( ABSPATH. $filepath, 'a+t' );

		//echo 'writing 3 : ';
		$str = date('d-m-Y H:i:s') . "	@$section\r: $msg\r\n";
		fwrite( $file , $str );

		//echo 'writing 4 : ';
		fclose( $file );
	} else {
		echo 'checked';
	}
	return;
}

function mkdir_r($dirName, $rights=0777){
    $dir = ABSPATH . 'slip/'. $dirName ;
    if (!is_dir($dir) && strlen($dir) > 0) {
        mkdir($dir, $rights);
    }
}

function mkdir_r2($dirName, $rights=0777){
    $dir = ABSPATH . 'passport/'. $dirName ;
    if (!is_dir($dir) && strlen($dir) > 0) {
        mkdir($dir, $rights);
    }
}

function datetime_from_mysql( $strtime )
{
	if( !$strtime ){
		return;
	}

	list($datetime['date'], $datetime['hours']) = explode(' ', $strtime);
	list($datetime['year'], $datetime['month'], $datetime['day']) = explode('-', $datetime['date']);
	list($datetime['hour'], $datetime['min'], $datetime['sec']) = explode(':', $datetime['hours']);
    
    $datetime['day'] = intval( $datetime['day']  );
    $datetime['month'] = intval( $datetime['month']  );
    $datetime['year'] = intval( $datetime['year']  );
    $datetime['hour'] = intval( $datetime['hour']  );
    $datetime['min'] = intval( $datetime['min']  );
    $datetime['sec'] = intval( $datetime['sec']  );
	return $datetime;
}

function datetime_from_mysql_string( $strtime )
{
	if( !$strtime ) {
		return;
	}

	list($datetime['date'], $datetime['hours']) = explode(' ', $strtime);
	list($datetime['year'], $datetime['month'], $datetime['day']) = explode('-', $datetime['date']);
	list($datetime['hour'], $datetime['min'], $datetime['sec']) = explode(':', $datetime['hours']);

	return $datetime;
}

function date_db_to_display( $strtime )
{
	if( !$strtime ) {
		return;
	}

	$datetime = datetime_from_mysql_string( $strtime );

	return $datetime['day']. '/'. $datetime['month']. '/'. $datetime['year'];
}

function date_picker_to_db( $strdate )
{//echo 'check : '.$strdate.'<br />';
	if( strpos($strdate, '-') !== false) { return $strdate; }
	if( !isset($strdate) || $strdate == '' ){ return NULL; }

	list($datetime['day'], $datetime['month'], $datetime['year']) = explode('/', $strdate);

	if( count( $datetime ) != 3 ) { return NULL; }
	return ($datetime['year'] . '-' . $datetime['month'] . '-' . $datetime['day']) ;
}

function message( $msg, $referer = '', $title = 'ข้อความ' )
{
     echo $msg;
     
     if( $referer )
         jumptopage( $referer );
         
}

// คืนหมายเลขฟิลด์ที่ซ้ำจากการ insert ครั้งสุดท้าย ถ้าไม่มีการเกิดข้อผิดพลาดเกี่ยวกับการซ้ำค่า จะคืนค่า false
function check_duplicate_error()
{
    $errmsg = trim( mysql_error() );

    if( mysql_errno() == 1062 )
    {
        $p = strstr( $errmsg, 'key ' );
        $p = substr( $p, 4 );
        return $p;
    }
    return false;
}

/**
* get system datetime
**/
function get_system_date()
{
	global $config;
	return date($config['dateformatdb']);
}

/**
* concate date time
**/
function concate_date_time( $dt, $time )
{
	if( !isset($dt) || $dt == '' ) { return ''; }
	if( !isset($time) || $time == '' ) { return $dt; }

	list($datetime['day'], $datetime['month'], $datetime['year']) = explode( '-', $dt );

	return $datetime['year'] . '-' . $datetime['month'] . '-' . $datetime['day'] . ' ' . $time;	
}

function get_header( ) {
	$headers = getallheaders();
	return $headers;
}

function set_user_session( $user ) {
	$account = new Account();
	$account->setEmail( $user['email'] );
	$account->setFirstName( $user['firstname'] );
	$account->setLastName( $user['lastname'] );
	$account->setUserId( $user['userid'] );
	$account->setRole( $user['role'] );
	$_SESSION['CurrentAccount'] = serialize($account);
	$_SESSION['RawCurrentAccount'] = serialize($user);
}

?>