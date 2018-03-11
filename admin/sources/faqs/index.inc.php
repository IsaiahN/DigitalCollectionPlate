<?php
if (!defined('CC_INI_SET')) die("Access Denied");
permission("faqs","read", true);
$rowsPerPage=50;
## delete amenitiesument
if (isset($_GET['dir'])) {
	switch ($_GET['dir']) {
		case 'up':
			$query = sprintf("UPDATE %sfaqs SET priority = '%d' WHERE priority = '%d'", $glob['dbprefix'], $_GET['moveto']+1, $_GET['moveto']);
			$db->misc($query);
				
			$query = sprintf("UPDATE %sfaqs SET priority = '%d' WHERE faqId = '%d'", $glob['dbprefix'], $_GET['moveto'], $_GET['faqId']);
			$db->misc($query);
			break;
			
		case 'down':
		case 'dn':
			$query = sprintf("UPDATE %sfaqs SET priority = '%d' WHERE priority = '%d'", $glob['dbprefix'], $_GET['moveto']-1, $_GET['moveto']);
			$db->misc($query);
				
			$query = sprintf("UPDATE %sfaqs SET priority = '%d' WHERE faqId = '%d'", $glob['dbprefix'], $_GET['moveto'], $_GET['faqId']);
			$db->misc($query);
			break;
			
		case 'reset':
			$query = sprintf("UPDATE %sfaqs SET priority =faqId WHERE 1", $glob['dbprefix']);
			$db->misc($query);
			break;
	}
	
	
	httpredir($GLOBALS['rootRel'].$glob['adminFile']."?_g=faqs/index");

}elseif (isset($_GET['status'])) {
	
	
	$record['status'] = $_GET['status'];
	$where = "faqId=".$db->mySQLSafe($_GET['faqId']);
	$update = $db->update($glob['dbprefix']."faqs", $record, $where);
		
	$msg = ($update == true) ? "<p class='infoText'>'".$_POST['name']."' Updated successfully.</p>" : "<p class='warnText'>Update failed.</p>";

} else if (isset($_GET['delete']) && $_GET['delete']>0) {

	
	
	$where = "faqId = ".$db->mySQLSafe($_GET['delete']);
	
	$delete = $db->delete($glob['dbprefix']."faqs", $where, ""); 
	
	if ($delete == TRUE) {
		$msg = "<p class='infoText'>Platform deleted successfully.</p>";
	} else {
		$msg = "<p class='warnText'>Delete failed.</p>";
	}
	
	
	
}else if(isset($_POST['faqId'])) {
	$allowedFields = $db->getFields($glob['dbprefix'].'faqs');
	
	foreach ($_POST as $name => $value) {
		if (in_array($name, $allowedFields)) { 
			$record[$name] = $db->mySQLSafe($value);
		}
	}
	
$record['answer']=$db->mySQLSafe($_POST['FCKeditor']);
	
	if (isset($_POST['faqId']) && $_POST['faqId']>0) 
	{
 		
		$where = "faqId=".$db->mySQLSafe($_POST['faqId']);
		$update = $db->update($glob['dbprefix']."faqs", $record, $where);
		unset($record, $where);
		

		if($update == TRUE)
		{
			$msg = "<p class='infoText'>'".$_POST['question']."' updated successfully.</p>";
		} 
		else 
		{
			$msg = "<p class='warnText'>Failed to update Platform.</p>";
		}
 		
	} 
	else 
	{
	 	
		$insert = $db->insert($glob['dbprefix']."faqs", $record);
		unset($record);
		$db->misc("UPDATE ".$glob['dbprefix']."faqs SET priority= faqId WHERE faqId= ".$db->insertid());
		
	if($insert == TRUE)
		{
			
			$msg = "<p class='infoText'>'".$_POST['question']."' added successfully.</p>";
			
			} 
		else 
		{
			$msg = "<p class='warnText'>Failed to add Platform.</p>";
		}
	}
}

if (!isset($_GET['mode'])) {
	## Build the SQL Query
	if (isset($_GET['edit']) && $_GET['edit']>0) {
		
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."faqs WHERE faqId = %s", $db->mySQLSafe($_GET['edit'])); 
	} else {
		
		$query = "SELECT * FROM ".$glob['dbprefix']."faqs  ORDER BY  faqs.priority  ASC";
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
    <td nowrap='nowrap' class="pageTitle">Frequently Asked Questions</td>
     <?php if(!isset($_GET["mode"])){ ?><td align="right" valign="middle"><a <?php if(permission("faqs","write")==TRUE){ ?>href="<?php echo $glob['adminFile']; ?>?_g=faqs/index&amp;mode=new" class="txtLink" <?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/buttons/new.gif" alt="" hspace="4" border="0" title="" />Add New</a></td><?php } ?>
  </tr>
</table>

<?php 
if (isset($msg)) echo msg($msg); 

if(!isset($_GET['mode']) && !isset($_GET['edit'])){
	if ($results == true) {
?>
<p class="copyText">Below is a list of all the current faqs in the database.</p>

<?php } ?>
<p class="copyText" style="text-align: right;"><?php echo $pagination; ?></p>
<form method="post" id="moveamenities" enctype="multipart/form-data">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td class="tdTitle" width="25">No.</td>
    <td width="144" class="tdTitle">Question</td>
    <td width="498" class="tdTitle">Answer</td>
    <td width="52" class="tdTitle">Status</td>
    <td align="center" width="67" class="tdTitle" nowrap="nowrap">Sort Order</td>
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
      <?php echo $results[$i]['question'];?>
    </td>
    <td class="<?php echo $cellColor; ?>"><?php echo $results[$i]['answer'];?></td>
    <td align="center" valign="middle" class="<?php echo $cellColor; ?>">
       <?php
	 if($results[$i]['status']==0){
		 ?>
      <a <?php if(permission('faqs','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=faqs/index&amp;status=1&amp;faqId=<?php echo $results[$i]['faqId']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $results[$i]['status']; ?>1.gif" alt="Show" title="Show" /></a>
      
      <?
		 }else
		 {
		?>
      <a <?php if(permission('faqs','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=faqs/index&amp;status=0&amp;faqId=<?php echo $results[$i]['faqId']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $results[$i]['status']; ?>1.gif" alt="Hide" title="Hide" /></a>
      <?	 
		}
		
	?>
      
         
      
      </td>
    <td width="67" align="center" class="<?php echo $cellColor; ?>">
  <?php
		if ($i>0) {
		?>
      <a href="<?php echo $glob['adminFile']; ?>?_g=faqs/index&amp;dir=up&amp;faqId=<?php echo $results[$i]['faqId']; ?>&amp;moveto=<?php echo $pos-1; ?>">
        <img src="<?php echo $glob['adminFolder']; ?>/images/up.gif" border="0" /></a>
  <?php
		}
		if ($i!==count($results)-1) {
		?>
      <a href="<?php echo $glob['adminFile']; ?>?_g=faqs/index&amp;dir=dn&amp;faqId=<?php echo $results[$i]['faqId']; ?>&amp;moveto=<?php echo $pos+1; ?>">
        <img src="<?php echo $glob['adminFolder']; ?>/images/down.gif" border="0" /></a>
      <?php
		}
	?>	</td>
    <td align="center" width="21" class="<?php echo $cellColor; ?>">
      <?php if (permission("faqs","edit") == true) { ?>
      <a href="<?php echo $glob['adminFile']; ?>?_g=faqs/index&amp;edit=<?php echo $results[$i]['faqId']; ?>" class="txtLink">
        <?php } else { echo '<a '.$link401.'>'; } ?>
      <?php echo 'Edit'; ?></a>	</td>
	<td align="center" width="27" class="<?php echo $cellColor; ?>">
	<?php 
	if (permission("faqs","delete") == true) { ?>
	<a href="javascript:decision('Are you sure you want to delete this?','<?php echo $glob['adminFile']; ?>?_g=faqs/index&amp;delete=<?php echo $results[$i]['faqId']; ?>');" class="txtLink">
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
    <td colspan="7" class="tdText">There are no faqs in the database.</td>
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
<p class="copyText">You can use the form below to <?=strtolower($modeTxt)?> a faq.</p>
<form action="<?php echo $glob['adminFile']; ?>?_g=faqs/index" target="_self" method="post" enctype="multipart/form-data">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle"><?php if(isset($_GET['edit']) && $_GET['edit']>0){ echo $modeTxt; } else { echo $modeTxt; } echo " Faq";?></td>
  </tr>
  <tr>
    <td width="16%" align="left" valign="top" class="tdRichText"><strong>Question:</strong></td>
    <td width="84%" class="tdRichText"><input name="question" class="textbox" value="<?php if(isset($results[0]['question'])) echo $results[0]['question']; ?>" type="text" maxlength="255" size="40" /></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="tdRichText"><strong>Answer:</strong></td>
    <td class="tdRichText">
      <?php
		require($glob['adminFolder'].'/includes'.CC_DS.'rte'.CC_DS.'fckeditor.php');

		$oFCKeditor				= new FCKeditor('FCKeditor');
		$oFCKeditor->BasePath	= $GLOBALS['rootRel'].$glob['adminFolder'].'/includes/rte/' ;
		$oFCKeditor->Value		= (isset($results[0]['answer'])) ? $results[0]['answer'] : '';

		if (!$config['richTextEditor']) $oFCKeditor->off = true;
		$oFCKeditor->Create();
?>
      </td>
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
    <input type="hidden" value="<?php if(isset($_GET['edit'])) echo $_GET['edit']; ?>" name="faqId" />
	<input name="priority" type="hidden" value="<?php echo ($results == true) ? $results[0]['priority'] : 0; ?>" />
	<input name="submit" type="submit" class="submit" id="submit" value="<?php echo (isset($results) && $results == true) ? "Update" : "Save" ?> Faq" />
    <input name="Button" type="button" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=faqs/index');return document.returnValue" value="Cancel" class="submit" />    </td>
    </tr>
 </table>

</form>
<?php
}
?>