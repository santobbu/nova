<?php
defined( '_VALID_SETTING' ) or die( 'Direct Access to this location is not allowed.' );
ini_set("memory_limit","128M");
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600); //1 hour
session_start();

require( 'configurations.php' );
require( ABSPATH . COMMON . 'utility.php' );
require( ABSPATH . COMMON . 'database.php' );
require( ABSPATH . COMMON . 'common.php' );
require( ABSPATH . EMAIL . 'PHPMailer/PHPMailerAutoload.php' );
require( ABSPATH . EMAIL . 'mailsender.php' );
require( ABSPATH . EMAIL . 'mailsender2.php' );
require( ABSPATH . EMAIL . 'mailer.php' );
?>
