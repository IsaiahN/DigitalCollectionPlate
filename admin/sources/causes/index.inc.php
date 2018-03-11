<?php
if (!defined('CC_INI_SET')) die("Access Denied");
permission("causes","read", true);
$rowsPerPage=50;
## delete amenitiesument
if (isset($_GET['dir'])) {
	switch ($_GET['dir']) {
		case 'up':
			$query = sprintf("UPDATE %scauses SET priority = '%d' WHERE priority = '%d'", $glob['dbprefix'], $_GET['moveto']+1, $_GET['moveto']);
			$db->misc($query);
				
			$query = sprintf("UPDATE %scauses SET priority = '%d' WHERE causeId = '%d'", $glob['dbprefix'], $_GET['moveto'], $_GET['causeId']);
			$db->misc($query);
			break;
			
		case 'down':
		case 'dn':
			$query = sprintf("UPDATE %scauses SET priority = '%d' WHERE priority = '%d'", $glob['dbprefix'], $_GET['moveto']-1, $_GET['moveto']);
			$db->misc($query);
				
			$query = sprintf("UPDATE %scauses SET priority = '%d' WHERE causeId = '%d'", $glob['dbprefix'], $_GET['moveto'], $_GET['causeId']);
			$db->misc($query);
			break;
			
		case 'reset':
			$query = sprintf("UPDATE %scauses SET priority =causeId WHERE 1", $glob['dbprefix']);
			$db->misc($query);
			break;
	}
	
	
	httpredir($GLOBALS['rootRel'].$glob['adminFile']."?_g=causes/index");

}elseif (isset($_GET['status'])) {
	
	
	$record['status'] = $_GET['status'];
	$where = "causeId=".$db->mySQLSafe($_GET['causeId']);
	$update = $db->update($glob['dbprefix']."causes", $record, $where);
		
	$msg = ($update == true) ? "<p class='infoText'>'".$_POST['name']."' Updated successfully.</p>" : "<p class='warnText'>Update failed.</p>";

} else if (isset($_GET['delete']) && $_GET['delete']>0) {

	
	
	$where = "causeId = ".$db->mySQLSafe($_GET['delete']);
	
	$delete = $db->delete($glob['dbprefix']."causes", $where, ""); 
	
	if ($delete == TRUE) {
		$msg = "<p class='infoText'>Slide deleted successfully.</p>";
	} else {
		$msg = "<p class='warnText'>Delete failed.</p>";
	}
	
	
	
}else if(isset($_POST['causeId'])) {

	

	
	$allowedFields = $db->getFields($glob['dbprefix'].'causes');
	
	foreach ($_POST as $name => $value) {
		if (in_array($name, $allowedFields)) { 
			$record[$name] = $db->mySQLSafe($value);
		}
	}
		
$record['description']=$db->mySQLSafe($_POST['FCKeditor']);
$record['image']= $db->mySQLSafe(imgPath($_POST['imageName'], false, ''));
	
	
	if (isset($_POST['causeId']) && $_POST['causeId']>0) 
	{
 		
		$where = "causeId=".$db->mySQLSafe($_POST['causeId']);
		$update = $db->update($glob['dbprefix']."causes", $record, $where);
		unset($record, $where);
		

		if($update == TRUE)
		{
			$msg = "<p class='infoText'>'".$_POST['name']."' updated successfully.</p>";
		} 
		else 
		{
			$msg = "<p class='warnText'>Failed to update causes.</p>";
		}
 		
	} 
	else 
	{
	 	
		$insert = $db->insert($glob['dbprefix']."causes", $record);
		unset($record);
		$db->misc("UPDATE ".$glob['dbprefix']."causes SET priority= causeId WHERE causeId= ".$db->insertid());
		
	if($insert == TRUE)
		{
			
			$msg = "<p class='infoText'>'".$_POST['name']."' added successfully.</p>";
			
			} 
		else 
		{
			$msg = "<p class='warnText'>Failed to add causes.</p>";
		}
	}
}

if (!isset($_GET['mode'])) {
	## Build the SQL Query
	if (isset($_GET['edit']) && $_GET['edit']>0) {
		
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."causes WHERE causeId = %s", $db->mySQLSafe($_GET['edit'])); 
	} else {
		
		$query = "SELECT * FROM ".$glob['dbprefix']."causes  ORDER BY  causes.priority  ASC";
	}
	
	// query database
	$page = (isset($_GET['page'])) ? $_GET['page'] : 0;
	
	$results = $db->select($query, $rowsPerPage, $page);
	$numrows = $db->numrows($query);
	$pagination = paginate($numrows, $rowsPerPage, $page, "page", "txtLink", 7, array('delete'));
}
 $users= $db->select("SELECT * FROM ".$glob['dbprefix']."users WHERE activated='1' AND user_level='2' AND organization <> '' ORDER BY user_id ASC");
require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap' class="pageTitle">Manage Causes</td>
     <?php if(!isset($_GET["mode"])){ ?><td align="right" valign="middle"><a <?php if(permission("causes","write")==TRUE){ ?>href="<?php echo $glob['adminFile']; ?>?_g=causes/index&amp;mode=new" class="txtLink" <?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/buttons/new.gif" alt="" hspace="4" border="0" title="" />Add New</a></td><?php } ?>
  </tr>
</table>

<?php 
if (isset($msg)) echo msg($msg); 

if(!isset($_GET['mode']) && !isset($_GET['edit'])){
	if ($results == true) {
?>
<p class="copyText">Below is a list of all the current causes in the database.</p>

<?php } ?>
<p class="copyText" style="text-align: right;"><?php echo $pagination; ?></p>
<form method="post" id="moveamenities" enctype="multipart/form-data">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td class="tdTitle" width="25">No.</td>
    <td class="tdTitle" width="25">Fundraiser</td>
    <td  class="tdTitle">Name</td>
    <td class="tdTitle">Caption</td>
    <td  class="tdTitle">Image</td>
    <td  class="tdTitle">Status</td>
    <td align="center"  class="tdTitle" nowrap="nowrap">Sort Order</td>
    <td class="tdTitle" colspan="2" width="15" align="center">Action</td>
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
     <?php
 $name= $db->select("SELECT * FROM ".$glob['dbprefix']."users WHERE user_id='".$results[$i]['user_id']."'"); 
	
echo $name[0]['organization'];
	?>
    </td>
    <td class="<?php echo $cellColor; ?>">
      <?php echo $results[$i]['name'];?>    </td>
    <td class="<?php echo $cellColor; ?>"><?php echo $results[$i]['caption'];?></td>
    <td class="<?php echo $cellColor; ?>">
     <?php
	$thumbPathRoot = imgPath($results[$i]['image'],$thumb=1,$path="root");
	$thumbPathRel = imgPath($results[$i]['image'],$thumb=1,$path="rel");
	
	$masterPathRoot = imgPath($results[$i]['image'],$thumb=0,$path="root");
	$masterPathRel = imgPath($results[$i]['image'],$thumb=0,$path="rel");
	
	if (file_exists($thumbPath) && !empty($results[$i]['image'])) {
		$imgSize = getimagesize($thumbPath);
		$imgFile = $thumbPathRel; 
	} else if (file_exists($masterPathRoot) && !empty($results[$i]['image'])) {
		$imgSize = getimagesize($masterPathRoot); 
		$imgFile = $masterPathRel;
	}
		
	if (isset($imgFile) && !empty($imgFile)) { 
	?>
	<a href="causes.php?causeId=<?php echo $results[$i]['causeId']?>" target="_blank"><img src="<?php echo $imgFile; ?>" alt="<?php echo $results[$i]['name']; ?>" title="" height="150px" width="290px" /></a>
	<div>
	<?php
	unset($imgFile);
	} else {
		echo "&nbsp;";
	}
	?>
    </div>    </td>
    <td align="center" valign="middle" class="<?php echo $cellColor; ?>">
    <?php
	 if($results[$i]['status']==0){
		 ?>
      <a <?php if(permission('causes','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=causes/index&amp;status=1&amp;causeId=<?php echo $results[$i]['causeId']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $results[$i]['status']; ?>1.gif" alt="Show" title="Show" /></a>
      
      <?
		 }else
		 {
		?>
      <a <?php if(permission('causes','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=causes/index&amp;status=0&amp;causeId=<?php echo $results[$i]['causeId']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $results[$i]['status']; ?>1.gif" alt="Hide" title="Hide" /></a>
      <?	 
		}
		
	?>         </td>
    <td width="67" align="center" class="<?php echo $cellColor; ?>">
  <?php
		if ($i>0) {
		?>
      <a href="<?php echo $glob['adminFile']; ?>?_g=causes/index&amp;dir=up&amp;causeId=<?php echo $results[$i]['causeId']; ?>&amp;moveto=<?php echo $pos-1; ?>">
        <img src="<?php echo $glob['adminFolder']; ?>/images/up.gif" border="0" /></a>
  <?php
		}
		if ($i!==count($results)-1) {
		?>
      <a href="<?php echo $glob['adminFile']; ?>?_g=causes/index&amp;dir=dn&amp;causeId=<?php echo $results[$i]['causeId']; ?>&amp;moveto=<?php echo $pos+1; ?>">
        <img src="<?php echo $glob['adminFolder']; ?>/images/down.gif" border="0" /></a>
      <?php
		}
	?>	</td>
    <td align="center"  class="<?php echo $cellColor; ?>">
      <?php if (permission("causes","edit") == true) { ?>
      <a href="<?php echo $glob['adminFile']; ?>?_g=causes/index&amp;edit=<?php echo $results[$i]['causeId']; ?>" class="txtLink">
        <?php } else { echo '<a '.$link401.'>'; } ?>
      <?php echo 'Edit'; ?></a>	</td>
	<td align="center"  class="<?php echo $cellColor; ?>">
	<?php 
	if (permission("causes","delete") == true) { ?>
	<a href="javascript:decision('Are you sure you want to delete this?','<?php echo $glob['adminFile']; ?>?_g=causes/index&amp;delete=<?php echo $results[$i]['causeId']; ?>');" class="txtLink">
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
    <td colspan="8" class="tdText">There are no causes in the database.</td>
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
<p class="copyText">You can use the form below to <?=strtolower($modeTxt)?> a slide.</p>
<form action="<?php echo $glob['adminFile']; ?>?_g=causes/index" target="_self" method="post" enctype="multipart/form-data">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle"><?php if(isset($_GET['edit']) && $_GET['edit']>0){ echo $modeTxt; } else { echo $modeTxt; } echo " Cause";?></td>
  </tr>
  <tr>
    <td width="222" class="tdText"><strong>Fundraiser:</strong></td>
    <td width="560">
     <select name="user_id" id="user_id">
     
	<?php
	for($i=0; $i<count($users); $i++){
	?>
	<option value="<?php echo $users[$i]['user_id']; ?>" <?php if($users[$i]['user_id']==$results[0]['user_id']) {echo "selected='selected'";} ?>><?php echo $users[$i]['organization']; ?></option>
	<?php } ?>
	</select>
      </td>
  </tr>
  <tr>
    <td width="30%" align="left" valign="top" class="tdRichText"><strong>Name:</strong></td>
    <td width="84%" class="tdRichText"><input name="name" class="textbox" value="<?php if(isset($results[0]['name'])) echo $results[0]['name']; ?>" type="text" maxlength="255" size="40" /></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="tdRichText">
      <strong>Image:</strong> <br />
      (Optional and thumbnails will automatically be made IF the format chosen is compatible.)
      </td>
    <td class="tdRichText">
      <?php
	$imgSrc = (!empty($results[0]['image'])) ? imgPath($results[0]['image'], 0, 'rel') : $glob['rootRel'].'images/general/px.gif';
	?>
      <img src="<?php echo $imgSrc; ?>" alt="" id="previewImage" title="" />
      <div>
        <input name="upload" class="submit" type="button" id="upload" onclick="openPopUp('<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/includes/rte/editor/filemanager/browser/default/browser.html?Type=uploads&amp;Connector=<?php echo urlencode($GLOBALS['rootRel'].$glob['adminFolder']); ?>%2Fincludes%2Frte%2Feditor%2Ffilemanager%2Fconnectors%2Fphp%2Fconnector.php','filemanager',700,600)" value="Browse / Upload Image" />
        <input type="button" class="submit" value="Remove Image" onclick="findObj('previewImage').src='<?php echo $glob['rootRel']; ?>/images/general/px.gif';findObj('imageName').value = '';" />
        <input type="hidden" name="imageName" id="imageName" value="<?php if(isset($results[0]['image'])) echo $results[0]['image']; ?>" />
        </div>
      </td>
  </tr>
  <tr>
    <td align="left" valign="top" class="tdRichText"><strong>Cause Description:</strong></td>
    <td class="tdRichText">
    <?php

	require($glob['adminFolder']."/includes".CC_DS."rte".CC_DS."fckeditor.php");
	$oFCKeditor = new FCKeditor('FCKeditor');
	$oFCKeditor->BasePath = $GLOBALS['rootRel'].$glob['adminFolder'].'/includes/rte/';
	$oFCKeditor->Value = (isset($results[0]['description'])) ? (!get_magic_quotes_gpc()) ? stripslashes($results[0]['description']) : $results[0]['description'] : '';
	if ($config['richTextEditor'] == 0) {
		$oFCKeditor->off = true;
	}
	$oFCKeditor->Create();
?>
    </td>
  </tr>
  <tr>
    <td align="left" valign="top" class="tdRichText"><strong>Caption:</strong></td>
    <td class="tdRichText">
    <textarea name="caption" cols="35" rows="3" class="textbox"><?php echo $results[0]['caption']; ?></textarea>
    </td>
  </tr>
  <tr>
    <td align="left" valign="top" class="tdRichText"><strong>Display on Home in:</strong></td>
    <td class="tdRichText"><table width="200" border="0" align="left" cellpadding="0" cellspacing="0">
      <tr>
        <td width="31"><input name="boxes" type="checkbox" value="1" <?php if($results[0]['boxes']==1){echo "checked";} if(isset($_GET['mode']) && $_GET['mode']=="new") { echo "checked='checked'"; }?> tabindex="29" /></td>
        <td width="51">&nbsp;Boxes</td>
        <td width="28"><input name="slide" type="checkbox" value="1" <?php if($results[0]['slide']==1){echo "checked";}?> tabindex="29" /></td>
        <td width="345">SlideShow</td>
        </tr>
      </table></td>
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
    <input type="hidden" value="<?php if(isset($_GET['edit'])) echo $_GET['edit']; ?>" name="causeId" />
	<input name="priority" type="hidden" value="<?php echo ($results == true) ? $results[0]['priority'] : 0; ?>" />
	<input name="submit" type="submit" class="submit" id="submit" value="<?php echo (isset($results) && $results == true) ? "Update" : "Save" ?> Cause" />
    <input name="Button" type="button" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=causes/index');return document.returnValue" value="Cancel" class="submit" />    </td>
    </tr>
 </table>

</form>
<?php
}
?>