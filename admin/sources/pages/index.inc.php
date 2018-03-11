<?php
if (!defined('CC_INI_SET')) die("Access Denied");
require("classes".CC_DS."gd".CC_DS."gd.inc.php");
permission("pages","read", true);
mysql_query("SET NAMES 'utf8'");
mysql_query('SET CHARACTER SET utf8');
$rowsPerPage=$config['displayitemRows'];
if (isset($_GET['dir'])) {
	switch ($_GET['dir']) {
		case 'up':
			$query = sprintf("UPDATE %spages SET priority = '%d' WHERE priority = '%d'", $glob['dbprefix'], $_GET['moveto']+1, $_GET['moveto']);
			$db->misc($query);
				
			$query = sprintf("UPDATE %spages SET priority = '%d' WHERE pageId = '%d'", $glob['dbprefix'], $_GET['moveto'], $_GET['id']);
			$db->misc($query);
			break;
			
		case 'down':
		case 'dn':
			$query = sprintf("UPDATE %spages SET priority = '%d' WHERE priority = '%d'", $glob['dbprefix'], $_GET['moveto']-1, $_GET['moveto']);
			$db->misc($query);
				
			$query = sprintf("UPDATE %spages SET priority = '%d' WHERE pageId = '%d'", $glob['dbprefix'], $_GET['moveto'], $_GET['id']);
			$db->misc($query);
			break;
			
		case 'reset':
			$query = sprintf("UPDATE %spages SET priority = pageId WHERE 1", $glob['dbprefix']);
			$db->misc($query);
			break;
	}
	
	
	httpredir($GLOBALS['rootRel'].$glob['adminFile']."?_g=pages/index");

} 
elseif (isset($_GET['status'])) {
	
	
	$record['status'] = $_GET['status'];
	$where = "pageId=".$db->mySQLSafe($_GET['pageId']);
	$update = $db->update($glob['dbprefix']."pages", $record, $where);
		
	$msg = ($update == true) ? "<p class='infoText'>'".$_POST['title']."' Updated successfully.</p>" : "<p class='warnText'>Update failed.</p>";

}elseif (isset($_GET['access'])) {
	
	
	$record['access'] = $_GET['access'];
	$where = "pageId=".$db->mySQLSafe($_GET['pageId']);
	$update = $db->update($glob['dbprefix']."pages", $record, $where);
		
	$msg = ($update == true) ? "<p class='infoText'>'".$_POST['title']."' Updated successfully.</p>" : "<p class='warnText'>Update failed.</p>";

}else if (isset($_GET['delete']) && $_GET['delete']>0) {

	
	$query = sprintf("SELECT * FROM ".$glob['dbprefix']."pages WHERE pageId = %s", $db->mySQLSafe($_GET['delete']));
		$result= $db->select($query);
		$result[0]['name'];
		$string= strtolower($result[0]['name']);
		$output = str_replace(" ","-",$string);
		$file ="$output.php" ;
		if(file_exists($file) )
		{
		unlink($file)	;
		}
	$where = "pageId = ".$db->mySQLSafe($_GET['delete']);
	
	$delete = $db->delete($glob['dbprefix']."pages", $where, ""); 
	if ($delete == TRUE) {
		$msg = "<p class='infoText'>Page deleted successfully.</p>";
	} else {
		$msg = "<p class='warnText'>Delete failed.</p>";
	}
	
	
	
} else if (isset($_POST['docId']) && $_POST['docId']>0) {
	$record["father_id"] = $db->mySQLSafe($_POST['father_id']);
	$record['title']		= $db->mySQLSafe($_POST['title']);
	$record['name']		= $db->mySQLSafe($_POST['name']);
	$record['url']		= $db->mySQLSafe($_POST['url']);
	$record['url_openin']	= $db->mySQLSafe($_POST['url_openin']);
	
	$fckEditor = (detectSSL()==true && $config['force_ssl']==false) ?  str_replace($config['rootRel_SSL'],$glob['rootRel'],$_POST['FCKeditor']) : $_POST['FCKeditor'];
	$record["content"] = $db->mySQLSafe($fckEditor);
	$record["priority"] = $db->mySQLSafe($_POST['priority']);
	$record["access"] = $db->mySQLSafe($_POST['access']);
	$record["status"] = $db->mySQLSafe($_POST['status']);
	$record["top_menu"] = $db->mySQLSafe($_POST['top_menu']);
	$record["footer_menu"] = $db->mySQLSafe($_POST['footer_menu']);
	$record["home_menu"] = $db->mySQLSafe($_POST['home_menu']);
	$record['metatitle']	= $db->mySQLSafe($_POST['metatitle']);
	$record['metadesc']		= $db->mySQLSafe($_POST['metadesc']);
	$record['metakeywords']	= $db->mySQLSafe($_POST['metakeywords']);
	
if($_POST['old_name']!=$_POST['name'])
		{
			$string= strtolower($_POST['name']);
			$newname= str_replace(" ","-",$string);
			$string2= strtolower($_POST['old_name']);
			$oldname= str_replace(" ","-",$string2);
			copy($oldname.".php",$newname.".php");
			$file=$oldname.".php";
			if(file_exists($file) )
  			{
			 	unlink($file)	;
			}
		}
	$update = $db->update($glob['dbprefix']."pages", $record, array('pageId' => $_POST['docId']));
			
	if ($update == TRUE){
		$msg = "<p class='infoText'>'".$_POST['name']."' Homepage updated successfully.</p>"; 
	} else {
		$msg = "<p class='warnText'>'".$_POST['name']."' was not updated.</p>"; 
	}

} else if (isset($_POST['docId']) && empty($_POST['docId'])) {
	
	$record["father_id"] = $db->mySQLSafe($_POST['father_id']);
	$record['title']		= $db->mySQLSafe($_POST['title']);
	$record['name']		= $db->mySQLSafe($_POST['name']);
	$record['url']		= $db->mySQLSafe($_POST['url']);
	$record['url_openin']	= $db->mySQLSafe($_POST['url_openin']);
	## Fix for bug 315
	$fckEditor = (detectSSL()==true && $config['force_ssl']==false) ?  str_replace($config['rootRel_SSL'],$glob['rootRel'],$_POST['FCKeditor']) : $_POST['FCKeditor'];
	$record["content"] = $db->mySQLSafe($fckEditor);
	$record["priority"] = $db->mySQLSafe($_POST['priority']);
	$record["status"] = $db->mySQLSafe($_POST['status']);
	$record["top_menu"] = $db->mySQLSafe($_POST['top_menu']);
	$record["footer_menu"] = $db->mySQLSafe($_POST['footer_menu']);
	$record["home_menu"] = $db->mySQLSafe($_POST['home_menu']);
	
	
	
		$record["metatitle"] = $db->mySQLSafe($_POST['metatitle']);
		$record["metadesc"] = $db->mySQLSafe($_POST['metadesc']);
		$record["metakeywords"] = $db->mySQLSafe($_POST['metakeywords']);	
	
$string= strtolower($_POST['name']);
		$output = str_replace(" ","-",$string);
		$file =$output.'.php';
		
		if(file_exists($file) )
  		{
		 
		}else
		{
		$file ='sample.php';
		copy($file,"$output.php");
		}
	$insert = $db->insert($glob['dbprefix']."pages", $record);

	if($insert == TRUE) {
		$msg = "<p class='infoText'>'".$_POST['name']."' added successfully.</p>";
		// add order
		$db->misc("UPDATE ".$glob['dbprefix']."pages SET `priority` = `pageId` WHERE `pageId` = ".$db->insertid() );
	} else {
		$msg = "<p class='infoText'>Failed to add page.</p>";
	}
	
	
}

// retrieve current pages
if(!isset($_GET['mode'])) {
	
	// make sql query
	if (isset($_GET['edit']) && $_GET['edit']>0) {
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."pages WHERE pageId = %s", $db->mySQLSafe($_GET['edit'])); 
	} else {
		//$query = "SELECT * FROM ".$glob['dbprefix']."pages ORDER BY priority ASC";
		$whereClause = (is_numeric($_GET['parent'])) ? sprintf("father_id = '%d'", $_GET['parent']) : 'father_id = 0';
		$orderBy = $glob['dbprefix']."pages.priority ASC";
$query = "SELECT * FROM ".$glob['dbprefix']."pages WHERE (name != 'Imported Products' OR content  != '##HIDDEN##') AND ".$whereClause." ORDER BY ".$orderBy;
	} 
	if(isset($_GET['page'])){
	
		$page = $_GET['page'];
	
	} else {
		
		$page = 0;
	
	}
	
	// query database
	$results = $db->select($query, $rowsPerPage, $page);
	$numrows = $db->numrows($query);
	$pagination = paginate($numrows, $rowsPerPage, $page, "page", "next");
	
} // end if mode is not new


require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");
echo msg($msg);
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle">Site Pages</p></td>
    <?php if (!isset($_GET["mode"])){ ?><td align="right" valign="middle">
	<?php if (permission("pages", "write") == true) { ?>
	<a href="<?php echo $glob['adminFile']; ?>?_g=pages/index&amp;mode=new" class="txtLink">
	<?php } else { echo '<a '.$link401.'>'; } ?>
	<img src="<?php echo $glob['adminFolder']; ?>/images/buttons/new.gif" alt="" hspace="4" border="0" title="" />Add New</a>
	</td><?php } ?>
  </tr>
</table>
<?php if((isset($_GET['edit']) && $_GET['edit']>0) || (isset($_GET['mode']) && $_GET['mode']=="new")){ ?>
<form action="<?php echo $glob['adminFile']; ?>?_g=pages/index" target="_self" method="post">
<p class="copyText">Please use the area below to make changes to the homepage of the website. On saving changes take place immediately so please be sure to preview the changes first.</p>
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle">Site Page</td>
  </tr>
    <tr>
      <td align="left" valign="top" class="tdRichText"><strong>Parent:</strong></td>
      <td class="tdRichText"><?php
	$db = new db();
	$query = "SELECT name, father_id, pageId  FROM ".$glob['dbprefix']."pages ORDER BY pageId ASC";
	$pages = $db->select($query);
	?>
        <select name="father_id" class="textbox">
          <option value="0">None</option>
          <?php
  	for ($i=0; $i<count($pages); $i++){
		if ($pages[$i]['pageId']!==$results[0]['pageId']){ 
			$pageId = $pages[$i]['pageId'];
			$selected = ($_GET['mode'] == 'new' && $cat_id == $_GET['parent'] || isset($results[0]['father_id']) && $pages[$i]['pageId']==$results[0]['father_id']) ? ' selected="selected"' : '';
			$name = getDocDir($pages[$i]['name'], $pages[$i]['father_id'], $pages[$i]['pageId']);
			echo sprintf('<option value="%d"%s>%s</option>', $pageId, $selected, $name);
		} // end if cat_id is not the same
	}
	?>
      </select></td>
    </tr>
   <tr>
    <td align="left" valign="top" class="tdRichText"><strong>Page Title:</strong></td>
    <td class="tdRichText"><input name="title" type="text" class="textbox" value="<?php if(isset($results[0]['title'])) echo $results[0]['title']; ?>" maxlength="255" size="52" /></td>
  </tr>
   <tr>
     <td align="left" valign="top" class="tdRichText"><strong>Tab Name:</strong></td>
     <td class="tdRichText"> 
     <input name="name" type="text" class="textbox" value="<?php if(isset($results[0]['name'])) echo $results[0]['name']; ?>" maxlength="255" size="52" />
     </td>
   </tr>
  <tr>
    <td width="21%" align="left" valign="top" class="tdRichText"><strong>Friendly URL:</strong></td>
    <td width="79%" class="tdRichText"><input name="url" class="textbox" value="<?php if(isset($results[0]['url'])) echo $results[0]['url']; ?>" type="text" maxlength="255" size="52" />&nbsp;<select name="url_openin" class="textbox">
		<?php
		$options = array(
			"Same Window",
			"New Window",
		
		);
		foreach ($options as $key => $value) {
			$selected = ($key == $results[0]['url_openin']) ? 'selected="selected"' : '';
			echo sprintf('<option value="%d"%s>%s</option>', $key, $selected, $value);
		}
		?>
	  </select></td>
  </tr>
 
  <tr>
    <td align="left" valign="top" class="tdRichText"><strong>Page Content:</strong></td>
    <td class="tdRichText">
<?php

	require($glob['adminFolder']."/includes".CC_DS."rte".CC_DS."fckeditor.php");
	$oFCKeditor = new FCKeditor('FCKeditor');
	$oFCKeditor->BasePath = $GLOBALS['rootRel'].$glob['adminFolder'].'/includes/rte/';
	$oFCKeditor->Value = (isset($results[0]['content'])) ? (!get_magic_quotes_gpc ()) ? stripslashes($results[0]['content']) : $results[0]['content'] : '';
	if ($config['richTextEditor'] == 0) {
		$oFCKeditor->off = true;
	}
	$oFCKeditor->Create();
?></td>
  </tr>

<tr> 
<td width="21%" class="tdText"><strong>Browser Title:</strong></td>
<td width="79%" align="left"><input name="metatitle" type="text" size="35" class="textbox" value="<?php if(isset($results[0]['metatitle'])) echo $results[0]['metatitle']; ?>" /></td>
</tr>
<tr> 
<td width="21%" align="left" valign="top" class="tdText"><strong>Meta Description:</strong></td>
<td align="left"><textarea name="metadesc" cols="35" rows="3" class="textbox"><?php if(isset($results[0]['metadesc'])) echo $results[0]['metadesc']; ?></textarea></td>
</tr>
<tr> 
<td width="21%" align="left" valign="top" class="tdText"><strong>Meta Keywords:</strong> (Comma Separated)</td>
<td align="left"><textarea name="metakeywords" cols="35" rows="3" class="textbox"><?php if(isset($results[0]['metakeywords'])) echo $results[0]['metakeywords']; ?></textarea></td>
</tr>
 <tr>
    <td class="tdText"><strong>Display In Menu:</strong></td>
    <td><table width="455" border="0" align="left" cellpadding="0" cellspacing="0">
      <tr>
        <td width="21">
         
          <input name="top_menu" type="checkbox" value="1" <?php if($results[0]['top_menu']==1){echo "checked";} if(isset($_GET['mode']) && $_GET['mode']=="new") { echo "checked='checked'"; }?> tabindex="29" />
          </td>
        <td width="57">&nbsp;Top</td>
        <td width="23"><input name="home_menu" type="checkbox" value="1" <?php if($results[0]['home_menu']==1){echo "checked";}?> tabindex="29" /></td>
        <td width="73">Home</td>
        <td width="23"><input name="footer_menu" type="checkbox" value="1" <?php if($results[0]['footer_menu']==1){echo "checked";}?> tabindex="29" /></td>
        <td width="73">Footer</td>
        
        </tr>
      </table></td>
  </tr>
<tr>
	    <td align="left" valign="top" class="tdText"><strong>Access:</strong></td>
	    <td align="left">
          Public
      <input name="access" type="radio" value="0" <?php if(isset($results[0]['access']) && $results[0]['access']==0) { echo "checked='checked'"; } if(isset($_GET['mode']) && $_GET['mode']=="new") { echo "checked='checked'"; } ?> />
      Private
      <input name="access" type="radio" value="1" <?php if(isset($results[0]['access']) && $results[0]['access']==1) echo "checked='checked'";?> /> 
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
    <input type="hidden" value="<?=$results[0]['name']?>" name="old_name" />
    <input type="hidden" value="<?php if(isset($_GET['edit'])) echo $_GET['edit']; ?>" name="docId" />
	<input name="priority" type="hidden" value="<?php echo ($results == true) ? $results[0]['priority'] : 0; ?>" />
	<input name="submit" type="submit" class="submit" id="submit" value="<?php echo (isset($results) && $results == true) ? "Update" : "Save" ?> Page" />
    <input name="Button" type="button" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=pages/index');return document.returnValue" value="Cancel" class="submit" />    </td>
    </tr>
 </table>
</form>
<?php
} else { 
?>
<p class="copyText">Below is a list of all the current site pages. You may have an unlimited amount of these and they can be edited and/or deleted at any time.</p>
<p align="right"><?php echo $pagination; ?></p>
<p class="copyText"><strong>Location:</strong> <a href="?_g=pages/index" class="txtLink">Home</a><?php if (is_numeric($_GET['parent'])) echo getDocDir('', $_GET['parent'], 0, true, false, true, true); ?></p>
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td class="tdTitle" width="17">&nbsp;#</td>
     <td width="100" class="tdTitle">Tab Name</td>
    <td width="100" class="tdTitle">Page Title</td>
    <td width="150" class="tdTitle">Directory</td>
    <td width="200" class="tdTitle">Page Content</td>
    <td width="30" align="center" class="tdTitle">Access</td>
    <td width="30" align="center" class="tdTitle">Status</td>
    <td align="center" width="30" class="tdTitle">Sort Order</td>
    <td class="tdTitle" colspan="2" align="center">Action</td>
  </tr>
<?php 
if($config['sef']==1)
{
	$ext="html";
} 
elseif($config['sef']==0)
{
	$ext="php";
}
	if ($results) {
		$cellColor = "";
		$pos = 1;
		for ($i=0; $i<count($results); $i++) { 
			$cellColor = cellColor($i);
		$string= strtolower($results[$i]['url']);
		$link= str_replace(" ","-",$string);
	
	if($doc['url_openin']==1)
	{
	$target= 'target="_blank"';
	}else
	{
	$target= '';
	}
	
$sql= sprintf("SELECT pageId FROM %spages WHERE father_id = '%d'", $glob['dbprefix'], $results[$i]['pageId']);
$subdoc	= $db->numrows($sql);
?>
  <tr class="<?php echo $cellColor; ?>">
    <td class="<?php echo $cellColor; ?>"><?=$i+1?></td>
    <td class="<?php echo $cellColor; ?>">
	<span class="editlinktip hasTip" title="Edit Menu Title::<?php echo $results[$i]['url'];?>">
    <?php
				if($subdoc >= 1)
				{
				?>
    <a href="?_g=pages/index&amp;parent=<?php echo($results[$i]['pageId']);?>"  title="View Subpages" class="txtLink"><?php echo ucwords($results[$i]['name']);?></a>
                <?
				}
				else
				{
// get host name from URL
preg_match('@^(?:http://)?([^/]+)@i', $results[$i]['url'], $matches);
$host = $matches[1];
// get last two segments of host name
preg_match('/[^.]+\.[^.]+$/', $host, $matches);

if($matches[0])
{
?>
 <a href="<?php echo $glob['rootRel']."".$link; ?>" target="_blank" class="txtLink" title="<?php echo ucwords($results[$i]['title']); ?>"><?php echo ucwords($results[$i]['name']); ?></a>
<?

}
else
{
?>
 <a href="<?php echo $glob['rootRel']."".$link; ?>.<?php echo $ext;?>" target="_blank" class="txtLink" title="<?php echo ucwords($results[$i]['title']); ?>"><?php echo ucwords($results[$i]['name']); ?></a>
 <?
}

}
 ?>
					</span>
	</td>
    <td class="<?php echo $cellColor; ?>">
    
   <?php echo ucwords($results[$i]['title']); ?>
       </td>
    
    <td class="<?php echo $cellColor; ?>">
      <?php echo getDocDir($results[$i]['name'],$results[$i]['father_id'], $results[$i]['pageId']);?> - (<?php echo $subdoc; ?> SubPage)</td>
      
    <td class="<?php echo $cellColor; ?>">
    <?php
	$description=limit_text($results[$i]['content'],'80');
	echo strip_tags($description); 
	?>
    </td>
    <td align="center" valign="middle" class="<?php echo $cellColor; ?>">
     <?php
	 if($results[$i]['access']==0){?>
        <a <?php if(permission('pages','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=pages/index&amp;access=1&amp;pageId=<?php echo $results[$i]['pageId']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $results[$i]['access']; ?>.gif" alt="Show" title="Show" /></a>
        <? }else{
		?>
        <a <?php if(permission('pages','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=pages/index&amp;access=0&amp;pageId=<?php echo $results[$i]['pageId']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $results[$i]['access']; ?>.gif" alt="Hide" title="Hide" /></a>
        <? }?>
    </td>
   <td align="center" valign="middle" class="<?php echo $cellColor; ?>">
     <?php
	 if($results[$i]['status']==0){
		 ?>
     <a <?php if(permission('pages','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=pages/index&amp;status=1&amp;pageId=<?php echo $results[$i]['pageId']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $results[$i]['status']; ?>1.gif" alt="Show" title="Show" /></a>
     
     <?
		 }else
		 {
		?>
     <a <?php if(permission('pages','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=pages/index&amp;status=0&amp;pageId=<?php echo $results[$i]['pageId']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $results[$i]['status']; ?>1.gif" alt="Hide" title="Hide" /></a>
     <?	 
		}
		
	?>
     
   </td>
   <td width="64" align="center" class="<?php echo $cellColor; ?>">
     <?php
		if ($i>0) {
		?>
     <a href="<?php echo $glob['adminFile']; ?>?_g=pages/index&amp;dir=up&amp;id=<?php echo $results[$i]['pageId']; ?>&amp;moveto=<?php echo $pos-1; ?>">
       <img src="<?php echo $glob['adminFolder']; ?>/images/up.gif" border="0" /></a>
     <?php
		}
		if ($i!==count($results)-1) {
		?>
     <a href="<?php echo $glob['adminFile']; ?>?_g=pages/index&amp;dir=dn&amp;id=<?php echo $results[$i]['pageId']; ?>&amp;moveto=<?php echo $pos+1; ?>">
       <img src="<?php echo $glob['adminFolder']; ?>/images/down.gif" border="0" /></a>
     <?php
		}
	?>	</td>
    <td align="center" width="18" class="<?php echo $cellColor; ?>">
      <?php if (permission("pages","edit") == true) { ?>
      <a href="<?php echo $glob['adminFile']; ?>?_g=pages/index&amp;edit=<?php echo $results[$i]['pageId']; ?>" class="txtLink">
        <?php } else { echo '<a '.$link401.'>'; } ?>
      <?php echo 'Edit'; ?></a>	</td>
	<td align="center" width="35" class="<?php echo $cellColor; ?>">
	  <?php 
	if (permission("pages","delete") == true) { ?>
	  <a href="javascript:decision('Are you sure you want to delete this?','<?php echo $glob['adminFile']; ?>?_g=pages/index&amp;delete=<?php echo $results[$i]['pageId']; ?>');" class="txtLink">
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
    <td colspan="10" class="tdText">There are no site pages in the database.</td>
  </tr>
  <?php } ?>
</table>
<p align="right"><?php echo $pagination; ?></p>
<?php 
} 
?>
