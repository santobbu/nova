<?php
defined( '_VALID_SETTING' ) or die( 'Direct Access to this location is not allowed.' );

/** Database Configuration*/
define('DB_NAME', 'huawei_db');
define('DB_USERNAME', 'huawei');
define('DB_PASSWORD', 'Z!675ejl');
define('DB_HOSTNAME', 'localhost');

/** directory absolute path */
if ( !defined('ABSPATH') ){
	$currentPath = str_replace( '\\', '/', dirname( __FILE__ )) . '/';
	define('ABSPATH', str_replace( '/module', '', $currentPath));
}

define('COMMON', 'common/' );
define('MODULE', 'module/');
define('CONNECTION', 'connection/');
define('EMAIL', 'email/');
define('_SAVELOG', true);

global $config;
$config['dateformatdb'] = 'Y-m-d H:i:s';
$config['dateformat'] = 'd/m/Y';
$config['enablemail'] = true;
$config['mailreceiver'] = array("iammonkey2929@gmail.com");
$config['mailform'] = "huawei4.life.th@gmail.com";
$config['mailformpass'] = "huaweiP@ssw0rd";
$config['mailsender'] = "ขอบคุณสำหรับการเข้าร่วมกิจกรรม";
$config['daystoexpired'] = 30;
?>
