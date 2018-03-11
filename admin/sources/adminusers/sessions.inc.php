<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }
require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");
$rowsPerPage = 50;
?>
<p class="pageTitle">Admin Sessions</p>
 <p class="copyText">Below shows the last login attempts to admin. Keep an eye on this to ensure that there are no Hijacking attempts. It is strongly recommend that you change you admin password regularly.</p>
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
  	<td class="tdTitle">Login ID</td>
    <td class="tdTitle">Username</td>
    <td align="center" class="tdTitle">Time</td>
	<td align="center" class="tdTitle">IP Address</td>
    <td align="center" class="tdTitle">Success</td>
  </tr>
<?php

if(isset($_GET['page'])){
	$page = $_GET['page'];
} else {
	$page = 0;
}

$query = "SELECT * FROM ".$glob['dbprefix']."admin_sessions ORDER BY `time` DESC";
$results = $db->select($query, $rowsPerPage, $page);
$numrows = $db->numrows($query);

if($results == TRUE){

	for($i=0; $i<count($results); $i++) {
	
		$cellColor = "";
		$cellColor = cellColor($i);
?>
  <tr class="<?php echo $cellColor; ?>">
  	<td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['loginId']; ?>.</span></td>
    <td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['username']; ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo formatTime($results[$i]['time']); ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>"><a href="javascript:;" class="txtLink" onclick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=misc/lookupip&amp;ip=<?php echo $results[$i]['ipAddress']; ?>','misc',300,130,'yes,resizable=yes')"><?php echo $results[$i]['ipAddress']; ?></a></td>
	    <td align="center" class="<?php echo $cellColor; ?>"><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $results[$i]['success']; ?>.gif" alt="" title="" /></td>
  </tr>
<?php } 
}
?>

</table>
<p class="copyText"><?php echo paginate($numrows, $rowsPerPage, $page, "page"); ?></p>