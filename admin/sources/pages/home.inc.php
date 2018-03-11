<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }
require("classes".CC_DS."gd".CC_DS."gd.inc.php");
permission("homepage", "read", true);
require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");
if (isset($_POST['FCKeditor'])) 
{
	
	$record['title'] = $db->mySQLSafe($_POST['title']);
	$fckEditor = (detectSSL()==true && $config['force_ssl']==false) ?  str_replace($config['rootRel_SSL'], $glob['rootRel'], $_POST['FCKeditor']) : $_POST['FCKeditor'];
	$record["content"] = $db->mySQLSafe($fckEditor);
	$sql = sprintf("SELECT * FROM home  LIMIT 1;");
	
	if ($db->numrows($sql) == 1) {	
		$update = $db->update($glob['dbprefix']."home", $record);
		$msg = "<p class='infoText'>'".$_POST['title']."' Homepage updated successfully.</p>"; 
	} else {
		$insert = $db->insert($glob['dbprefix']."home", $record);
		$msg = "<p class='infoText'>'".$_POST['title']."' added successfully.</p>";
	}
	
} 
$query= sprintf("SELECT * FROM home  LIMIT 1;");
$results = $db->select($query);
?>
<p class="pageTitle">Homepage</p>
<?php 
if (isset($msg)) { 
	echo msg($msg);
} else { 
?>
<p class="copyText">You can use the form below to add or edit homepage.</p>
<?php
}
?>
<form action="<?php echo $glob['adminFile']; ?>?_g=pages/home" target="_self" method="post" language="javascript">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle">Homepage</td>
  </tr>
  
	
	</td>
  </tr>
  <tr>
    <td width="30%" class="tdRichText"><span class="copyText"><strong>Title:</strong></span></td>
    <td class="tdRichText"><input name="title" class="textbox" type="text" size="30" value="<?php echo stripslashes($results[0]['title']); ?>" /></td>
  </tr>
  <tr>
    <td colspan="2" class="tdRichText">
<?php
	require($glob['adminFolder']."/includes".CC_DS."rte".CC_DS."fckeditor.php");
	$oFCKeditor = new FCKeditor('FCKeditor');
	$oFCKeditor->BasePath = $GLOBALS['rootRel'].$glob['adminFolder'].'/includes/rte/';
	$oFCKeditor->Value = stripslashes($results[0]['content']);
	if($config['richTextEditor']==0) {
		$oFCKeditor->off = TRUE;
	}
	$oFCKeditor->Create();
 ?>
</td>
  </tr>
  <tr>
    <td colspan="2" class="tdRichText"><input name="submit" type="submit" class="submit" id="submit" value="Update Homepage" />
    
    </td>
  </tr>
</table>
</form>
