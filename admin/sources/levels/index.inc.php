<?php
if (!defined('CC_INI_SET')) die("Access Denied");
permission("levels","read", true);
$rowsPerPage=$config['displayitemRows'];
if (isset($_GET['move']) && $_GET['move']=="up"){
$query = sprintf("UPDATE levels SET priority=priority-1 WHERE priority=".$db->mySQLSafe($_GET['moveto']+1));
$db->misc($query);
$query=sprintf("UPDATE levels SET priority=priority+1 WHERE levelId=". $_GET['id']);
$db->misc($query);
 httpredir($GLOBALS['rootRel'].$glob['adminFile']."?_g=levels/index");
}
if (isset($_GET['move']) && $_GET['move']=="down"){
$query = sprintf("UPDATE levels SET priority=priority+1 WHERE priority=" . $db->mySQLSafe($_GET['moveto']-1));
$db->misc($query);
$query = sprintf("UPDATE levels SET priority=priority-1 WHERE levelId= " . $_GET['id']);
$db->misc($query);
httpredir($GLOBALS['rootRel'].$glob['adminFile']."?_g=levels/index");
}
if (isset($_GET['status'])) {
	
	
	$record['status'] = $_GET['status'];
	$where = "levelId=".$db->mySQLSafe($_GET['levelId']);
	$update = $db->update($glob['dbprefix']."levels", $record, $where);
		
	$msg = ($update == true) ? "<p class='infoText'>'".$_POST['title']."' Updated successfully.</p>" : "<p class='warnText'>Update failed.</p>";

} else if (isset($_GET['delete']) && $_GET['delete']>0) {

	
	
	$where = "levelId = ".$db->mySQLSafe($_GET['delete']);
	
	$delete = $db->delete($glob['dbprefix']."levels", $where, ""); 
	
	if ($delete == TRUE) {
		$msg = "<p class='infoText'>levels deleted successfully.</p>";
	} else {
		$msg = "<p class='warnText'>Delete failed.</p>";
	}
	
	
	
}else if(isset($_POST['levelId'])) {

	
	
	$allowedFields = $db->getFields($glob['dbprefix'].'levels');
	
	foreach ($_POST as $name => $value) {
		if (in_array($name, $allowedFields)) { 
			$record[$name] = $db->mySQLSafe($value);
		}
	}
	if (isset($_POST['levelId']) && $_POST['levelId']>0) 
	{
 		
		$where = "levelId=".$db->mySQLSafe($_POST['levelId']);
		$update = $db->update($glob['dbprefix']."levels", $record, $where);
		unset($record, $where);
		

		if($update == TRUE)
		{
			$msg = "<p class='infoText'>'".$_POST['title']."' updated successfully.</p>";
		} 
		else 
		{
			$msg = "<p class='warnText'>Failed to update  levels.</p>";
		}
 		
	} 
	else 
	{
	 	
		$insert = $db->insert($glob['dbprefix']."levels", $record);
		unset($record);
		
		
	if($insert == TRUE)
		{
			
			$msg = "<p class='infoText'>'".$_POST['title']."' added successfully.</p>";
			
			} 
		else 
		{
			$msg = "<p class='warnText'>Failed to add levels.</p>";
		}
	}
}

if (!isset($_GET['mode'])) {
	## Build the SQL Query
	if (isset($_GET['edit']) && $_GET['edit']>0) {
		
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."levels WHERE levelId = %s", $db->mySQLSafe($_GET['edit'])); 
	} else {
		
		$query = "SELECT * FROM ".$glob['dbprefix']."levels  ORDER BY  priority DESC";
	}
	
	// query database
	$page = (isset($_GET['page'])) ? $_GET['page'] : 0;
	
	$results = $db->select($query, $rowsPerPage, $page);
	$numrows = $db->numrows($query);
	$pagination = paginate($numrows, $rowsPerPage, $page, "page", "txtLink", 7, array('delete'));
}

require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap' class="pageTitle">Manage Levels</td>
     <?php if(!isset($_GET["mode"])){ ?><td align="right" valign="middle"><a <?php if(permission("levels","write")==TRUE){ ?>href="<?php echo $glob['adminFile']; ?>?_g=levels/index&amp;mode=new" class="txtLink" <?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/buttons/new.gif" alt="" hspace="4" border="0" title="" />Add New</a></td><?php } ?>
  </tr>
</table>

<?php 
if (isset($msg)) echo msg($msg); 

if(!isset($_GET['mode']) && !isset($_GET['edit'])){
	if ($results == true) {
?>
<p class="copyText">Below is a list of all the current levels in the database.</p>

<?php } ?>
<p class="copyText" style="text-align: right;"><?php echo $pagination; ?></p>
<form method="post" id="movelevels" enctype="multipart/form-data">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td class="tdTitle" width="26">No.</td>
    <td width="234" class="tdTitle">Level</td>
    <td width="256" class="tdTitle">Points</td>
    <td width="82" align="center" valign="middle" class="tdTitle">Status</td>
    <td align="center" width="90" class="tdTitle">Sort Order</td>
    <td class="tdTitle" colspan="2" align="center">Action</td>
  </tr>
<?php 
	if ($results) {
		$cellColor = "";
		$pos = 1;
		for ($i=0; $i<count($results); $i++) { 
			$cellColor = cellColor($i);
			
?>
  <tr class="<?php echo $cellColor; ?>">
    <td class="<?php echo $cellColor; ?>"><?php echo ($i+1); ?></td>
    <td class="<?php echo $cellColor; ?>">
      <?php echo $results[$i]['title'];?>
    </td>
    <td class="<?php echo $cellColor; ?>"><?php echo $results[$i]['points'];?></td>
    <td align="center" valign="middle" class="<?php echo $cellColor; ?>">
      <?php
	 if($results[$i]['status']==0){
		 ?>
      <a <?php if(permission('levels','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=levels/index&amp;status=1&amp;levelId=<?php echo $results[$i]['levelId']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $results[$i]['status']; ?>1.gif" alt="Show" title="Show" /></a>
      
      <?
		 }else
		 {
		?>
      <a <?php if(permission('levels','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=levels/index&amp;status=0&amp;levelId=<?php echo $results[$i]['levelId']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $results[$i]['status']; ?>1.gif" alt="Hide" title="Hide" /></a>
      <?	 
		}
		
	?>
      
    </td>
     <td width="90" align="center" class="<?php echo $cellColor; ?>">
     <?php
		if ($i>0) {
		?>
     <a href="<?php echo $glob['adminFile']; ?>?_g=levels/index&amp;move=up&amp;id=<?php echo $results[$i]['levelId']; ?>&amp;moveto=<?php echo $pos-1; ?>">
       <img src="<?php echo $glob['adminFolder']; ?>/images/up.gif" border="0" /></a>
     <?php
		}
		if ($i!==count($results)-1) {
		?>
     <a href="<?php echo $glob['adminFile']; ?>?_g=levels/index&amp;move=down&amp;id=<?php echo $results[$i]['levelId']; ?>&amp;moveto=<?php echo $pos+1; ?>">
       <img src="<?php echo $glob['adminFolder']; ?>/images/down.gif" border="0" /></a>
     <?php
		}
	?>	</td>
    <td align="center" width="20" class="<?php echo $cellColor; ?>">
      <?php if (permission("levels","edit") == true) { ?>
      <a href="<?php echo $glob['adminFile']; ?>?_g=levels/index&amp;edit=<?php echo $results[$i]['levelId']; ?>" class="txtLink">
        <?php } else { echo '<a '.$link401.'>'; } ?>
      <?php echo 'Edit'; ?></a>	</td>
	<td align="center" width="50" class="<?php echo $cellColor; ?>">
	<?php 
	if (permission("levels","delete") == true) { ?>
	<a href="javascript:decision('Are you sure you want to delete this?','<?php echo $glob['adminFile']; ?>?_g=levels/index&amp;delete=<?php echo $results[$i]['levelId']; ?>');" class="txtLink">
	<?php } else { echo '<a '.$link401.'>'; } ?>
	<?php echo 'Delete'; ?></a>	</td>
    </tr>
  <?php
  		$pos++;
  	} // end loop
?>
  

<?php
  } else { ?>
   <tr>
    <td colspan="6" class="tdText">There are no levels in the database.</td>
  </tr>
  <?php } ?>
</table>

</form>

<p class="copyText" style="text-align: right;"><?php echo $pagination; ?></p>

<?php
if(isset($msg2))
{ 
	echo msg($msg2); 
}
?>


<?php 
} else if ($_GET["mode"] == "new" || $_GET["edit"]>0) {

if(isset($_GET["edit"]) && $_GET["edit"]>0){ $modeTxt = 'Edit'; } else { $modeTxt = 'Add'; } 
?>
<p class="copyText">You can use the form below to <?=strtolower($modeTxt)?> a  levels.</p>
<form action="<?php echo $glob['adminFile']; ?>?_g=levels/index" target="_self" method="post" enctype="multipart/form-data">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle"><?php if(isset($_GET["edit"]) && $_GET["edit"]>0){ echo $modeTxt; } else { echo $modeTxt; } echo "  Level";?></td>
  </tr>
  <tr>
    <td width="12%" class="tdRichText"><span class="tdText"><strong>Name:</strong></span>	  </td>
    <td width="88%" class="tdRichText"><input name="title" value="<?php if(isset($results[0]['title'])) echo $results[0]['title']; ?>" type="text" class="textbox" size="35" /></td>
  </tr>
  <tr>
    <td class="tdRichText"><strong>Points:</strong></td>
    <td class="tdRichText"><input name="points" value="<?php if(isset($results[0]['points'])) echo $results[0]['points']; ?>" type="text" class="textbox" size="35" /></td>
  </tr>
 
  <tr>
    <td align="left" valign="top" class="tdRichText"><strong>Status:</strong></td>
    <td class="tdRichText">
      Enabled
      <input name="status" type="radio" value="1" <?php if(isset($results[0]['status']) && $results[0]['status']==1) { echo "checked='checked'"; } if(isset($_GET['mode']) && $_GET['mode']=="new") { echo "checked='checked'"; } ?> />
      Disabled
      <input name="status" type="radio" value="0" <?php if(isset($results[0]['status']) && $results[0]['status']==0) echo "checked='checked'";?> />    </td>
  </tr>
  
   <tr>
     <td>&nbsp;</td>
	<td>
    <input type="hidden" value="<?php if(isset($_GET['edit'])) echo $_GET['edit']; ?>" name="levelId" />
	<input name="priority" type="hidden" value="<?php echo ($results == true) ? $results[0]['priority'] : 0; ?>" />
	<input name="submit" type="submit" class="submit" id="submit" value="<?php echo (isset($results) && $results == true) ? "Update" : "Save" ?> Level" />
    <input name="Button" type="button" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=levels/index');return document.returnValue" value="Cancel" class="submit" />    </td>
    </tr>
 </table>

</form>
<?php
}
?>