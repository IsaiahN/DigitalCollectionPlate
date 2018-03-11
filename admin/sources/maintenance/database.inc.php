<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }

permission("maintenance","read",$halt=TRUE);
$db_maintenance='Database `%1$s` Maintenance';
$db_success="Action `%1\$s TABLE` has been performed successfully.";
$db_info='<strong>MySQL %1$s</strong> running on <strong>%2$s</strong> as <strong>%3$s@%4$s</strong>';
if(isset($_POST['action']) && is_array($_POST['tableName'])){ 

$sqlQuery = $_POST['action']." TABLE ";


foreach($_POST['tableName'] as $value){
	
	$sqlQuery.= "`".$value."` ,";

}
$sqlQuery = substr($sqlQuery,0,strlen($sqlQuery) -2);
$results = $db->getRows($sqlQuery); 

	$msg = "<p class='infoText'>".sprintf($db_success,$_POST['action'])."</p>";

} elseif(isset($_POST['action'])) {
	$msg = "<p class='warnText'>Please check the tables you wish to perform maintenance on.</p>";
}

require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");

?>
<p class="pageTitle"><?php echo sprintf($db_maintenance,$glob['dbdatabase']);?> </p>
<?php 
if(isset($msg)){ 
	echo msg($msg); 
}
?>
<p class="copyText"><?php echo sprintf($db_info,mysql_get_server_info(),$glob['dbhost'],$glob['dbusername'],$glob['dbhost']); ?> <a href="<?php echo $glob['adminFile'];?>?_g=maintenance/sql" class="txtLink">&raquo;</a></p>

<?php if(isset($_POST['action']) && is_array($_POST['tableName'])){  ?>
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
	<tr>
		<td align="center" class="tdTitle">Table</td>
		<td align="center" class="tdTitle">Operation</td>
		<td align="center" class="tdTitle">Message Type</td>
		<td align="center" class="tdTitle">Message Text</td>
	</tr>
	<?php 
	if(is_array($results)){
	
	foreach($results as $result){
		$i++;
		$cellColor = cellColor($i);
	?>
	<tr>
		<td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $result[0]; ?></span></td>
		<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $result[1]; ?></span></td>
		<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $result[2]; ?></span></td>
		<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $result[3]; ?></span></td>
	</tr>
	<?php 
		}
	} ?>
</table>
	<?php } else { ?>
<form name="maintainDB" action="<?php echo $glob['adminFile']; ?>?_g=maintenance/database" enctype="multipart/form-data" method="post">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
	<tr>
		<td width="10" class="tdTitle">&nbsp;</td>
		<td align="center" class="tdTitle">Table</td>
		<td align="center" class="tdTitle">Records</td>
		<td align="center" class="tdTitle">Type</td>
		<td align="center" class="tdTitle">Size</td>
		<td align="center" class="tdTitle">Overhead</td>
	</tr>
	<?php 
	$tables = $db->getRows("SHOW TABLE STATUS LIKE '".$glob['dbprefix']."%';"); 
	if(is_array($tables)){
		
		$totalRecords = 0;
		$totalSize = 0;
		$totalOverhead = 0;
		
		foreach($tables as $table){
		
		$i++;
		
		$cellColor = cellColor($i); 
		$totalRecords = $totalRecords + $table[4];
		$totalSize = $totalSize + $table[8];
		$totalOverhead = $totalOverhead + $table[9];
			?>
			<tr class="<?php echo $cellColor; ?>">
				<td width="10" align="center" class="<?php echo $cellColor; ?>"><input type="checkbox" id="tableName" value="<?php echo $table[0]; ?>" name="tableName[]" /></td>
				<td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $table[0]; ?></span></td>
				<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $table[4]; ?></span></td>
				<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $table[1]; ?></span></td>
				<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo format_size($table[8]); ?></span></td>
				<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo format_size($table[9]); ?></span></td>
			</tr>
		<?php }   } ?>
		<tr>
				<td colspan="2" class="tdText">
				<img src="<?php echo $glob['adminFolder']; ?>/images/selectAll.gif" alt="" width="16" height="11" /> <a href="javascript:checkAll('tableName','true');" class="txtLink">Check All</a> / <a href="javascript:checkAll('tableName','false');" class="txtLink">Uncheck All</a>
				<select name="action" size="1" class="textbox" onchange="submitDoc('maintainDB');">
                  <option value="">With Selected:</option>
				  <option value="OPTIMIZE">Optimise</option>
                  <option value="REPAIR">Repair</option>
				  <option value="CHECK" >Check</option>
            	  <option value="ANALYZE" >Analyze</option>
                </select>
				</td>
				<td align="center" class="tdText"><strong><?php echo $totalRecords; ?></strong></td>
				<td align="center" class="tdText">&nbsp;</td>
				<td align="center" class="tdText"><strong><?php echo format_size($totalSize); ?></strong></td>
				<td align="center" class="tdText"><strong><?php echo format_size($totalOverhead); ?></strong></td>
			</tr>

	</table>
</form>
<?php 
} 
?>