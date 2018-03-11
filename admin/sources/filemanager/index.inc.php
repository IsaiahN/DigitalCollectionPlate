<?php
if (!defined('CC_INI_SET')) die("Access Denied");
permission("filemanager", "read", true);

require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");

## Include new Filemanager class
include_once CC_ROOT_DIR.CC_DS.'classes'.CC_DS.'filemanager.class.php';
$filemanager	= new Filemanager();

if (isset($_GET['unlink']) && is_numeric($_GET['unlink'])) {
	
	## Get file data
	$sql		= sprintf("SELECT * FROM %sfilemanager WHERE file_id = %d LIMIT 1;", $glob['dbprefix'], $_GET['unlink']);
	$files		= $db->select($sql);
		
	## Check for dependencies	
	$fileName	= $files[0]['filename'];
	$file_id	= $files[0]['file_id'];
	
	$query		= sprintf("SELECT image FROM %1\$scauses  WHERE image LIKE '%%%2\$s'", $glob['dbprefix'], $files[0]['filename']);
	$results	= $db->select($query);
	
	$homepage	= false;
	$idx_path 	= str_replace("images/uploads/","",$files[0]['filepath']);
	$query		= "SELECT img FROM ".$glob['dbprefix']."img_idx WHERE img = '".$idx_path."'";
	$extraImg	= $db->select($query);
	
	
	if ($results && !isset($_GET['confirmed'])){
		$msg	= "<p class='warnText'>".sprintf("There are product or categories using the image '%1\$s'.",$fileName)." <a href=\"".$glob['adminFile']."?_g=filemanager/index&amp;unlink=".$file_id."&confirmed=1\" onclick=\"return confirm('".str_replace("\n", '\n', addslashes("Are you sure you want to delete this?"))."')\" class='txtRed'>CONTINUE TO DELETE?</a></p>";
		$fmhalt = true;
		
	}else if ($homepage && !isset($_GET['confirmed'])) {
		$msg	= "<p class='warnText'>".sprintf("The image '%1\$s' is used on the site homepage.",$fileName)." <a href=\"".$glob['adminFile']."?_g=filemanager/index&amp;unlink=".$file_id."&amp;confirmed=1\" onclick=\"return confirm('".str_replace("\n", '\n', addslashes("Are you sure you want to delete this?"))."')\" class='txtRed'>CONTINUE TO DELETE?</a></p>";
		$fmhalt = true;
		
	} else if ($extraImg && !isset($_GET['confirmed'])) {
		$msg	= "<p class='warnText'>".sprintf("The image '%1\$s' is used in a product image gallery.",$fileName)." <a href=\"".$glob['adminFile']."?_g=filemanager/index&amp;unlink=".$file_id."&amp;confirmed=1&amp;idx=1\" onclick=\"return confirm('".str_replace("\n", '\n', addslashes("Are you sure you want to delete this?"))."')\" class='txtRed'>CONTINUE TO DELETE?</a></p>";
		$fmhalt = true;
		
	} else {
		$fmhalt = false;
	}
	
	
	## New Filemanager based delete method
	if (is_numeric($_GET['unlink']) && (!$fmhalt || isset($_GET['confirmed']))) {
		if ($filemanager->deleteFile($_GET['unlink'])) {
			$msg = "<p class='infoText'>Image deleted.</p>";
		} else {
			$msg = "<p class='warnText'>Delete failed.</p>";
		}
	}
}
?>
<p class="pageTitle">Image Manager</p>
<?php 
if(isset($msg)){ 
	echo msg($msg); 
} else { ?>
<p class="copyText">Below you can delete images from the server.</p>
<?php } 

if(isset($_GET['page']) && $_GET['page']>0){
	$page = $_GET['page'];
} else {
	$page = "";
}

$thumbsPerPage = 30;


/*
$dirArray = walkDir(CC_ROOT_DIR.CC_DS.'images'.CC_DS.'uploads', true, $thumbsPerPage, $page, false, $int = 0);
$pagination = paginate($dirArray['max'], $thumbsPerPage, $page, 'page', 'txtLink', 10);
*/

$dirArray		= $filemanager->showFileList(FM_FILETYPE_IMG, $page, $thumbsPerPage);

$totalRows		= $db->select("SELECT COUNT(`file_id`) as count FROM ".$glob['dbprefix']."filemanager WHERE type = ".FM_FILETYPE_IMG." AND disabled = 0");
$exclude		= array('add' => 1, 'remove' => 1);
$pagination		= paginate($totalRows[0]['count'], $thumbsPerPage, $page, 'page', 'txtLink', 10, $exclude);

?>
<p class="copyText"><?php echo $pagination; ?></p>
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td class="tdTitle">Image (Click to Preview)</td>
	<td class="tdTitle">&nbsp;</td>
    <td align="center" class="tdTitle">Size</td>
    <td align="center" class="tdTitle">Action</td>
  </tr>
<?php
$i = 0;
if (is_array($dirArray)) {
	foreach($dirArray as $fileData) {
		
		$file = $fileData['filepath'];
		## get root rel link
		$fileRoot	= imgPath($file, false, 'rel');
		$thumbRoot	= imgPath($file, true, 'root');
		$thumbRel	= imgPath($file, true, 'rel');
		
		if (file_exists($file)) {
			$size = getimagesize($file);
		}
		if (checkImgExt(strtolower($file)) && !stristr($file, 'thumb_')) {
			$i++;
			$cellColor = cellColor($i);
			 
?>
  <tr>
    <td class="<?php echo $cellColor; ?>"><a href="javascript:;" onclick="openPopUp('<?php echo $glob['adminFile'];?>?_g=filemanager/preview&amp;file_id=<?php echo $fileData['file_id']; ?>','filemanager',<?php echo $size[0]+14; ?>,<?php echo $size[1]+12; ?>)" class="txtDir"><?php echo $fileData['filepath']; ?></a></td>
	<td align="center" class="<?php echo $cellColor; ?>">
	<?php
	if(file_exists($thumbRoot)) { 
	?>
	<a href="javascript:;" onclick="openPopUp('<?php echo $glob['adminFile'];?>?_g=filemanager/preview&amp;file_id=<?php echo $fileData['file_id']; ?>','filemanager',<?php echo $size[0]+14; ?>,<?php echo $size[1]+12; ?>)" class="txtDir">
	<img src="<?php echo $thumbRel; ?>" border="0" />
	</a>
	<?php 
	} 
	?>
	</td>
    <td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo format_size($fileData['filesize']); ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>">
	<a <?php if(permission("filemanager","delete")){ ?>href="<?php echo $glob['adminFile']; ?>?_g=filemanager/index&amp;unlink=<?php echo $fileData['file_id']; ?>" onclick="return confirm('<?php echo str_replace("\n", '\n', addslashes("Are you sure you want to delete this?")); ?>')" class="txtLink" <?php } else { echo $link401; } ?>>Delete</a> 
	
	<?php if($config['gdversion']>0){ ?>
	/ 
	
	<a <?php if(permission("filemanager","edit")){ ?>href="javascript:;" class="txtLink" onclick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=filemanager/resize&amp;file=<?php echo $fileData['filepath']; ?>','filemanager',<?php echo $size[0]+14; ?>,<?php echo $size[1]+120; ?>)" <?php } else { echo $link401; } ?>>Resize</a>
	
	<?php } ?>
	</td>
  </tr>
<?php 
			}
		
		}
		
	} 
	if($i==0) {
	?>
	<tr>
    <td colspan="3" class="tdText">No images have been added.</td>
	</tr>
<?php } ?>
</table>
<p class="copyText"><?php echo $pagination; ?></p>