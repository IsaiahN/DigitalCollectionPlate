<?php 
if(!defined('CC_INI_SET')){ die("Access Denied"); }

permission("maintenance","read",$halt=TRUE);

if(isset($_POST['dbbackup'])){
	
	if(!isset($_POST['drop']) && !isset($_POST['structure']) && !isset($_POST['data'])){
	
		$msg = "<p class='warnText'>Please check at least one of the checkboxes to take a backup.</p>";
	
	} elseif($_POST['drop']==1 && !isset($_POST['structure'])){
		
		$msg = "<p class='warnText'>If you select \"Include Drop Table\" then you must also check \"Include Structure\"</p>";
	
	} else {
	 
		$tables = $db->getRows("SHOW TABLE STATUS LIKE '".$glob['dbprefix']."%';"); 
		
		$data = "-- --------------------------------------------------------\n-- ccshop SQL Dump\n-- version ".$ini['ver']."\n-- http://www.ccshop.com\n-- \n-- Host: ".$glob['dbhost']."\n-- Generation Time: ".strftime($config['timeFormat'],time())."\n-- Server version: ".mysql_get_server_info()."\n-- PHP Version: ".phpversion()."\n-- \n-- Database: `".$glob['dbdatabase']."`\n";
		
		foreach($tables as $table){ 
			$data .= $db->sqldumptable($table,$_POST['drop'],$_POST['structure'],$_POST['data']); 
		} 
	
		$filename = $glob['dbdatabase']."_".date("dMy").".sql";
	
		header('Pragma: private');
		header('Cache-control: private, must-revalidate');
		header("Content-Disposition: attachment; filename=".$filename);
		header("Content-type: text/plain");
		header("Content-type: application/octet-stream");
		header("Content-length: ".strlen($data));
		header("Content-Transfer-Encoding: binary");
		echo $data;
		exit;
	}

}

require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");

?>
<p class="pageTitle">Backup Tool</p>
<?php 
if(isset($msg))
{ 
	echo msg($msg); 
}
?>
<form method="post" action="<?php echo $glob['adminFile']; ?>?_g=maintenance/backup" enctype="multipart/form-data" name="dbbackup">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
<tr>
<td class="tdTitle" colspan="2">Backup Tool</td>
</tr>
<tr>
<td width="33%" class="tdText"><strong>Include Drop Table?</strong><br />Check this if you want your backup to overwrite existing data if used to reSite.</td>
<td class="tdText"><input name="drop" type="checkbox" value="1" /></td>
</tr>
<tr>
  <td width="33%" class="tdText"><strong>Include Structure?</strong><br />This is critical for creating the database structure for the data to be imported into. </td>
  <td class="tdText"><input type="checkbox" name="structure" value="1" checked="checked" /></td>
</tr>
<tr>
  <td width="33%" class="tdText"><strong>Include Data?</strong><br />This includes all your Site inventory including products, customers etc which is imported into the database core structure.</td>
  <td class="tdText"><input type="checkbox" name="data" value="1" checked="checked" /></td>
</tr>
<tr>
<td width="33%" class="tdText"> </td>
<td class="tdText">
<input type="hidden" name="dbbackup" value="1" />
<input name="submit" type="submit" class="submit" value="Download Now" /></td>
</tr>
</table>
</form>