<?php
if (!defined('CC_INI_SET')) die('Access Denied');
permission('maintenance', 'edit', true);
if (isset($_POST['execute'])) {
	if (!empty($_POST['sql'])) {
		$query = stripslashes($_POST['sql']);
		$db->misc($query, false);
		
		if ($db->error()) {
			$msg = '<p class="warnText">'.$db->errorstring().'</p>';
		} else {
			httpredir($glob['adminFile'].'?_g=maintenance/sql&affected='.$db->affected()."&query=".urlencode($query));
		}
	} else {
		$msg = '<p class="warnText">No query entered</p>';
	}
}

if (isset($_GET['affected']) && is_numeric($_GET['affected'])) {
	$msg = '<p class="infoText">'.urldecode($_GET['query']).'<br />'.$_GET['affected'].' row(s) affected</p>';
}

require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");
$db_maintenance='Database `%1$s` Maintenance';
$db_info='<strong>MySQL %1$s</strong> running on <strong>%2$s</strong> as <strong>%3$s@%4$s</strong>';
?>
<p class="pageTitle"><?php echo sprintf($db_maintenance, $glob['dbdatabase']);?> </p>
<p class="copyText"><?php echo sprintf($db_info,mysql_get_server_info(),$glob['dbhost'],$glob['dbusername'],$glob['dbhost']); ?> prefix <?php echo ($glob['dbprefix']==true) ? $glob['dbprefix'] : "none";?>.</p>
<?php 
if (isset($msg)) echo msg($msg);
?>
<form action="<?php echo $glob['adminFile']; ?>?_g=maintenance/sql" method="post" enctype="multipart/form-data">
<div id="sql">
  <div><textarea name="sql" id="sql" rows="25" cols="100" style="border: 1px solid #000000;"><?php echo $query; ?></textarea></div>
  <div><input type="submit" name="execute" value="Execute SQL Queries" class="submit" /></div>
</div>
</form>