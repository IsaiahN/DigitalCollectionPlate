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
require_once("includes".CC_DS."functions.inc.php" );
require_once("classes".CC_DS."session".CC_DS."cc_session.php" );
require_once ("includes" . CC_DS . "currencyVars.inc.php");
$cc_session = new session( );
$config = fetchdbconfig("config");
require("classes".CC_DS."gd".CC_DS."gd.inc.php");
if (!$config['offLine'] && !(!$config['offLineAllowAdmin']))
{
		$offlineContent = false;
		$offlineFiles = glob("offline.{php,htm,html}", GLOB_BRACE);
		if (!empty($offlineFiles) || is_array($offlineFiles))
		{
				foreach ($offlineFiles as $file)
				{
						include ($file);
						exit();
				}
		}
		echo stripslashes(base64_decode($config['offLineContent']));
		exit();
}
mysql_query("SET NAMES 'utf8'");
mysql_query('SET CHARACTER SET utf8');
$query= sprintf("SELECT * FROM home  LIMIT 1;");
$home= $db->select($query);
$curPageName=substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
$getExt = explode ('.', $curPageName);
$cpage=$getExt[0];
$string= strtolower($cpage);
$link= str_replace("-"," ",$string);
if(isset($link)){
$result= $db->select("SELECT * FROM ".$glob['dbprefix']."pages where url='".$link."'");
if ($result[0]['access']==1) { 
require_once 'includes/session.php';
}
}

if(isset($result) && $result == TRUE){
	
		if($result[0]['name']!=""){
			$prevDirSymbol = "-";
		}
		if($result[0]['metatitle']!=""){
		$meta['siteTitle'] = $result[0]['metatitle'];
		}
		else{
		$meta['siteTitle'] = $result[0]['name'].$prevDirSymbol.$config['siteTitle'];
		}		
	if($result[0]['metadesc']!=""){
		$meta['metaDescription'] =$result[0]['metadesc'];
	}else{
		$meta['metaDescription']=$config['metaDescription'];
	}
	if($result[0]['metakeywords']!=""){
		$meta['metaKeyWords'] =$result[0]['metakeywords'];
	}
	else{
	$meta['metaKeyWords'] =$config['metaKeyWords'];
	}
}else{
	$meta['siteTitle'] = $config['siteTitle'];
	$meta['metaDescription'] =$config['metaDescription'];
	$meta['metaKeyWords'] =$config['metaKeyWords'];
}
if($config['sef']==1){
	$ext="html";
}elseif($config['sef']==0){
	$ext="php";
}
$query="SELECT DISTINCT(ip) FROM sessions ORDER BY ip";  
$totalVisitor = $db->numrows($query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="google-site-verification" content="dYCqYX2u1d9TGMvRBGq8p3vw8DdAmoa-e3xUMJjp1r0" />
<title><?=$meta['siteTitle']?></title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $config['charsetIso'];?>"/>  
<meta name="description" content="<? echo $meta['metaDescription'];?>"/>
<meta name="keywords" content="<?php echo $meta['metaKeyWords'];?>"/>
<meta name="Author" content="<? echo $config['masterName'];?>(<? echo $config['masterEmail'];?>)"/>
<meta name="rating" content="general"/>
<meta name="Search_Engines" content="Google, MSN, Overture, AltaVista, Yahoo, AOL, Infoseek, LookSmart, Excite, Hotbot, Lycos, Magellan, CNET, DogPile, Ask Jeeves, Teoma, Snap, Webcrawler"/>
<meta name="revisit-after" content="5 days"/>
<meta name="robots" content="index,follow"/>
<meta name="title" content="<?=$meta['siteTitle']?> - digitalcollectionplate.com"/>
<meta name="identifier" content="http://www.digitalcollectionplate.com/"/>
<meta name="googlebot" content="index, follow"/>
<meta name="country" content="uk"/>
<meta name="organization-Email" content="<?php echo $config['masterEmail']; ?>"/>
<meta name="copyright" content="copyright <?php echo date("Y"); ?>- <? echo $config['masterName'];?>"/>
<meta name="generator" content="<? echo $config['masterName'];?> (www.fcm-groups.com)" />
<meta content="en" name="language"/>
<meta content="global" name="distribution"/>
<meta name="coverage" content="Worldwide"/>
<meta name="classification" content="<? echo $config['metaKeyWords'];?>"/>
<meta content="business" name="<? echo $meta['metaDescription'];?>"/>
<meta property="og:title" content="DigitalCollectionPlate.com"/>
<meta property="og:type" content="website"/>
<meta property="og:url" content="http://www.digitalcollectionplate.com"/>
<meta property="og:image" content="http://www.digitalcollectionplate.com/images/logos/logo2.jpg"/>
<meta property="og:site_name" content="digitalcollectionplate.com"/>
<meta property="og:email" content="<?php echo $config['masterEmail']; ?>"/>
<meta property="og:description" content="<? echo $meta['metaDescription'];?>"/>
<link rel="icon" href="<?php echo $glob['storeURL'];?>/images/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="<?php echo $glob['storeURL'];?>/images/favicon.ico"/>
<link href="<?php echo $glob['storeURL'];?>/css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $glob['storeURL'];?>/css/slider.css" rel="stylesheet" type="text/css" />
<?php if (($cpage == "signup" ) || ($cpage == "signin")) {  ?>
<link href="<?php echo $glob['storeURL'];?>/css/style_inc_forms.css" rel="stylesheet" type="text/css" />
<?php } ?>
<script type="text/javascript" src="<?php echo $glob['storeURL'];?>/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo $glob['storeURL'];?>/js/jquery.slider.js"></script>
<?php if (($cpage == "dashboard" ) || ($cpage == "fundeedonation")) {  ?>
<script src="<?php echo $glob['storeURL'];?>/js/jquery.timeago.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery("abbr.timeago").timeago();
});
</script>
<?php } if (($cpage == "dashboard" ) || ($cpage == "fundeemoderation")) { ?>
<script src="<?php echo $glob['storeURL'];?>/js/jquery.iphone-switch.js" type="text/javascript"></script>
<?php } ?>
    <script type="text/javascript">
		
    $(window).load(function() {
        $('#slider').nivoSlider();
    });
    </script>
<script language='JavaScript' type='text/javascript'>
function refreshCaptcha()
{
	var img = document.images['captchaimg'];
	img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
}
</script>
</head>