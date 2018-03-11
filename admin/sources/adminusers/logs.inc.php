<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }
require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");
$rowsPerPage = 50;
?>
<p class="pageTitle">Administrator Logs</p>
 
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
  	<td class="tdTitle">Id</td>
    <td class="tdTitle">Username</td>
    <td align="center" class="tdTitle">Log</td>
	<td align="center" class="tdTitle">Time</td>
    <td align="center" class="tdTitle">IP Address</td>
  </tr>
<?php

if(isset($_GET['page'])){
	$page = $_GET['page'];
} else {
	$page = 0;
}

$query = "SELECT * FROM ".$glob['dbprefix']."admin_log ORDER BY `time` DESC";
$results = $db->select($query, $rowsPerPage, $page);
$numrows = $db->numrows($query);

if($results == TRUE){

	for($i=0; $i<count($results); $i++) {
	
		$cellColor = "";
		$cellColor = cellColor($i);
?>
  <tr class="<?php echo $cellColor; ?>">
  	<td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['id']; ?>.</span></td>
    <td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['user']; ?></span></td>
	<td align="left" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['desc']; ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo formatTime($results[$i]['time']); ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>"><a href="javascript:;" class="txtLink" onclick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=misc/lookupip&amp;ip=<?php echo $results[$i]['ipAddress']; ?>','misc',300,130,'yes,resizable=yes')"><?php echo $results[$i]['ipAddress']; ?></a></td>
  </tr>
<?php } 
}
?>

</table>
<p class="copyText"><?php echo paginate($numrows, $rowsPerPage, $page, "page"); ?></p>