<?php
session_start();
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );
require_once 'ini.inc.php';
require_once "includes".CC_DS."global.inc.php";
require_once("classes".CC_DS."db".CC_DS."db.php" );
$db = new db( );
require_once( "includes".CC_DS."functions.inc.php" );
require_once( "classes".CC_DS."session".CC_DS."cc_session.php" );
$cc_session = new session( );
if($cc_session->ccUserData['user_id']>0){
$cc_session->destroySession($GLOBALS[CC_SESSION_NAME]);
session_unset();
}
httpredir("signin.php");
?>