<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }
require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'currencyVars.inc.php');
require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');
//todayVisitor
$timeLimit = time() - 57857;
$query = "SELECT * FROM ".$glob['dbprefix']."sessions LEFT JOIN ".$glob['dbprefix']."users ON ".$glob['dbprefix']."sessions.user_id = ".$glob['dbprefix']."users.user_id WHERE timeLast>".$timeLimit." ORDER BY timeLast DESC";
$todayVisitor= $db->numrows($query);
///Visitor
$query = "SELECT * FROM ".$glob['dbprefix']."sessions LEFT JOIN ".$glob['dbprefix']."users ON ".$glob['dbprefix']."sessions.user_id = ".$glob['dbprefix']."users.user_id  ORDER BY timeLast DESC";
$totalVisitor = $db->numrows($query);
//online
$timeLimit = time() - 900;
$query = "SELECT * FROM ".$glob['dbprefix']."sessions LEFT JOIN ".$glob['dbprefix']."users ON ".$glob['dbprefix']."sessions.user_id = ".$glob['dbprefix']."users.user_id WHERE timeLast>".$timeLimit." ORDER BY timeLast DESC";
$onlineVisitor = $db->numrows($query);

// no Slides
$query = 'SELECT count(`causeId`) as noCauses FROM '.$glob['dbprefix'].'causes WHERE `status`=1';
$noCauses= $db->select($query);
// no Slides
$query = 'SELECT count(`levelId`) as noLevels FROM '.$glob['dbprefix'].'levels WHERE `status`=1';
$noLevels= $db->select($query);
// no donations
$query = 'SELECT count(`donation_id`) as noDonations FROM '.$glob['dbprefix'].'donations';
$noDonations= $db->select($query);

//no Users
$query = 'SELECT count(`user_id`) as noUsers FROM '.$glob['dbprefix'].'users';
$noUsers= $db->select($query);

// no Pages
$query = 'SELECT count(`pageId`) as noPages FROM '.$glob['dbprefix'].'pages WHERE `status`=1';
$noPages= $db->select($query);

// no Products
$query = 'SELECT count(`faqId`) as noFaqs FROM '.$glob['dbprefix'].'faqs WHERE `status`=1';
$noFaqs= $db->select($query);


// no News
$query = 'SELECT count(`newsId`) as noNews FROM '.$glob['dbprefix'].'news WHERE `status`=1';
$noNews= $db->select($query);
// last admin session
$query = 'SELECT * FROM '.$glob['dbprefix'].'admin_sessions ORDER BY `time` DESC LIMIT 1, 1';
$lastSession = $db->select($query);

$query = 'SELECT count(`adminId`) as noAdministrators FROM '.$glob['dbprefix'].'admin_users';
$noAdministrators= $db->select($query);

$_GET['po'] = (!isset($_GET['po'])) ? '' : $_GET['po'];
$_GET['rev'] = (!isset($_GET['rev'])) ? '' : $_GET['rev'];

## check if setup folder remains after install/upgrade
if ($glob['installed'] && !$config['debug'] && file_exists(CC_ROOT_DIR.'/setup')) {
	echo sprintf('<p class="warnText">%s</p>', "WARNING: The Lexus setup folder 'setup/' exists on your server. It must be deleted immediately as your store is at risk.");
}
@chmod('includes'.CC_DS.'global.inc.php',0444);
if (substr(PHP_OS, 0, 3) != 'WIN' && cc_is_writable('includes'.CC_DS.'global.inc.php')) {
	echo sprintf('<p class="warnText">%s</p>', "WARNING: The main configuration file 'includes/global.inc.php' is writable and your store is at risk. Please change the file permissions so that it is read only.");
}

## check if setup folder remains after install/upgrade
if ($glob['dbusername'] == 'root') {
	//echo sprintf('<p class="warnText">%s</p>', 'WARNING: You are currently connected to the MySQL database using the root account. This is very insecure, and should be changed if possible.');
}
?>
<p class="pageTitle">Welcome to the Digital Collection Plate Administration Control Panel</p>
<?php
if ($lastSession) {
	$loginTime = formatTime($lastSession[0]['time']);
	if ($lastSession[0]['success']) {
		echo "<p class='infoText'>".sprintf("Last login by %1\$s on %2\$s", strip_tags($lastSession[0]['username']), $loginTime)."</p>";
	} else {
		echo "<p class='warnText'>".sprintf("Last login by %1\$s, failed on %2\$s", strip_tags($lastSession[0]['username']), $loginTime)."</p>";
	}
}
?>

    <!-- Product Field Begin -->
<div class="shortcuts">
<ul>
<li> <a <?php if(permission('administrators','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=adminusers/administrators" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/administrator.png">
<h6>Admin Users(<?php echo number_format($noAdministrators[0]['noAdministrators']); ?>)</h6>
</a> </li>
<li> <a <?php if(permission('levels','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=levels/index" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/user_levels.png">
<h6>Levels(<?php echo number_format($noLevels[0]['noLevels']); ?>)</h6>
</a> </li>
<li> <a <?php if(permission('users','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=users/index" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/customers.png">
<h6>Users(<?php echo number_format($noUsers[0]['noUsers']); ?>)</h6>
</a> </li>
<li><a <?php if(permission('customers','write')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=customers/email" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/mail.png">
<h6>Newsletter</h6>
</a> </li>
<li> <a <?php if(permission('donations','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=donations/index" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/donations.png">
<h6>Donations(<?php echo number_format($noDonations[0]['noDonations']); ?>)</h6>
</a> </li>
<li> <a <?php if(permission('homepage','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=pages/home" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/manufacturer.png">
<h6>Homepage</h6>
</a> </li>
<li> <a <?php if(permission('pages','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=pages/index" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/information.png">
<h6>Pages(<?php echo number_format($noPages[0]['noPages']); ?>)</h6>
</a> </li>
<li> <a <?php if(permission("causes","write")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=causes/index&amp;mode=new" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/add-product.png">
<h6>Add Cause</h6>
</a> </li>
<li> <a <?php if(permission('causes','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=causes/index" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/slides.png">
<h6>Causes(<?php echo number_format($noCauses[0]['noCauses']); ?>)</h6>
</a> </li>
<li> <a <?php if(permission("faqs","write")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=faqs/index&amp;mode=new" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/add-product.png">
<h6>Add Faq</h6>
</a> </li>
<li> <a <?php if(permission("faqs","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=faqs/index" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/products.png">
<h6>Faqs(<?php echo number_format($noFaqs[0]['noFaqs']); ?>)</h6>
</a> </li>
<li> <a <?php if(permission('news','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=news/index" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/news.png">
<h6>News(<?php echo number_format($noNews[0]['noNews']); ?>)</h6>
</a> </li>
<li> <a <?php if(permission('settings','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=settings/index" <?php } else { echo $link401; } ?>><img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/settings.png">
<h6>Settings</h6>
</a> </li>
<li> <a <?php if(permission('statistics','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=stats/index" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/purchased.png">
<h6>Statistics</h6>
</a> </li>
<li> <a <?php if(permission('maintenance','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=maintenance/database" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/backup_restore.png">
<h6>Backup / Restore</h6>
</a> </li>

<li> <a <?php if(permission('maintenance','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=filemanager/index" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/image_manager.png">
<h6>Media</h6>
</a> </li>
<li> <a <?php if(permission('maintenance','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=misc/serverInfo" <?php } else { echo $link401; } ?>> <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/info.png">
<h6>Server Info</h6>
</a> </li>
<li> <a href="<?php echo $GLOBALS['rootRel']; ?><?php echo $glob['adminFile']; ?>?_g=adminusers/changePass"><img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/key.png">
<h6>Change Password</h6>
</a> </li>
<li><a href="<?php echo $GLOBALS['rootRel']; ?><?php echo $glob['adminFile']; ?>?_g=logout" ><img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/icons/close.png">
<h6>Logout</h6>
</a> </li>
</ul>
</div>
<div style="clear:both;"></div>
<!-- Product Field End -->
<div style="height:500px;"></div>

