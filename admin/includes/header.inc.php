<?php
if (!defined('CC_INI_SET')) die("Access Denied");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charsetIso; ?>" />
<link href="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/styles/style.css" rel="stylesheet" type="text/css" />
<link rel="icon" href="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/favicon.ico"/>
<script type="text/javascript" src="<?php echo $GLOBALS['rootRel']; ?>js/prototype.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['rootRel']; ?>js/scriptaculous.js"></script>
<script type="text/javascript">
var fileLoadingImage		= '<?php echo $GLOBALS['rootRel']; ?>images/lightbox/loading.gif';
var fileBottomNavCloseImage	= '<?php echo $GLOBALS['rootRel']; ?>images/lightbox/close.gif';
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['rootRel']; ?>js/jslibrary.js"></script>
<?php 
if (isset($jsScript)) { ?>
<script type="text/javascript">
<?php echo $jsScript; ?>
</script>
<?php
}
?>
<title>Digital Collection Plate - Admin Control Panel</title>
</head>
<body id="pageTop">
<?php 
if (isset($ccAdminData['adminId']) && $ccAdminData['adminId']>0) {
?>
<!-- start wrapping table -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" width="180" rowspan="3" class="tdNav">
<?php require(CC_ROOT_DIR . CC_DS . $glob['adminFolder'] . CC_DS . "includes" .CC_DS. "navigation.inc.php"); ?> 
	</td>
  </tr>
  <tr>
  <td valign="top" class="tdContent">
<div id="border-top" class="h_green">
<div>
<div>
<span class="date"><?php echo formatTime(time(),$strftime); ?></span>
<span class="title">Logged in as:<?php echo $ccAdminData['username']; ?></span>
</div>
</div>
</div>
<div id="header-box">
<div id="module-status">
<span class="preview"><a href="<?php echo $GLOBALS['rootRel']; ?>index.php" target="_blank" class="txtLink">Preview</a></span>
<span class="loggedin-users"><a href="<?php echo $GLOBALS['rootRel']; ?><?php echo $glob['adminFile']; ?>?_g=adminusers/changePass" class="txtLink">Change Password</a></span>
<span class="support"><a href="http://whimsicalwebsolutions.com/blog/contact/" target="_blank" class="txtLink">Support</a></span>
<span class="logout"><a href="<?php echo $GLOBALS['rootRel']; ?><?php echo $glob['adminFile']; ?>?_g=logout" class="txtLink">Logout</a></span>
</div>
<!-- end wrapping table -->
<div id="topBar"></div>
<!-- start of admin content -->
<div id="contentPad">
<?php } ?>