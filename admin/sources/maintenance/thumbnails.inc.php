<?php 
if(!defined('CC_INI_SET')){ die("Access Denied"); }

permission("maintenance","read", true);
if (isset($_POST['rebuild'])) {

	if (is_array($_POST['thumbName'])) {
	
		$newThumb = array();
		require("classes".CC_DS."gd".CC_DS."gd.inc.php");
		
		// recalculate upload folder size
		$newConfig['uploadSize'] = $config['uploadSize'];
		
		foreach ($_POST['thumbName'] as $thumbName){
			
			// unlink original ready for rebuild
			$thumbPath	= imgPath($thumbName, true, "root");
			$masterPath	= imgPath($thumbName, false, "root");
			
			if (file_exists($thumbPath)) {
				$newConfig['uploadSize'] -= @filesize($thumbPath);
				@unlink($thumbPath);
				$newThumb[$thumbName] = false;
			}
			
			if ($_POST['action'] == 'rebuild') {
				$img = new gd($masterPath);
				$img->size_auto($config['gdthumbSize']);
				$img->save($thumbPath);
				$newConfig['uploadSize'] += @filesize($thumbPath);
				$newThumb[$thumbName] = true;
			}
			
			## Save uploadSize in DB
			writeDbConf($newConfig, 'config', $config, false);
			$msg = "<p class='infoText'>Selected thumbnails have been created or rebuilt.</p>";
		}
	} else {
		$msg = "<p class='warnText'>Please check at least one thumbnail to build or rebuild.</p>";
	}
	
}

require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");

?>
<p class="pageTitle">Rebuild Thumbnails</p>
<?php 

if (isset($msg)) {
	echo msg($msg);
}
if (!$config['gdversion']) {
	## error message - please set GD Version
	echo '<p class="warnText">Please set your <a href="?_g=settings/index#gd_settings">GD version</a> before using this function</p>';
} else {
	$page = (isset($_GET['page']) && $_GET['page']>0) ? $_GET['page'] : null;
	$thumbsPerPage = 100;
	
	$dirArray = walkDir(CC_ROOT_DIR.CC_DS.'images'.CC_DS.'uploads', true, $thumbsPerPage, $page, false, $int = 0);
	$pagination = paginate($dirArray['max'], $thumbsPerPage, $page, 'page', 'txtLink', 10);
		
?>
<p class="copyText"><?php echo $pagination; ?></p>

<form method="post" action="<?php echo $glob['adminFile']; ?>?_g=maintenance/thumbnails&amp;page=<?php echo $_GET['page']; ?>" enctype="multipart/form-data" name="thumbs">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
  	<td width="10" class="tdTitle">&nbsp;</td>
    <td class="tdTitle">Master Image Image (Click to Preview)</td>
    <td align="center" class="tdTitle">Thumbnail</td>
    <td align="center" class="tdTitle">Size</td>
  </tr>

<?php	
	$i = 0;
	if (is_array($dirArray)) {
		foreach($dirArray as $file) {
		
			$masterfile_rootPath	= imgPath($file, false, 'root');
			$masterfile_rootRel		= imgPath($file, false, 'rel');
			$thumb_rootPath			= imgPath($file, true, 'root');
			$thumb_rootRel			= imgPath($file, true, 'rel');
			
			if (file_exists($masterfile_rootPath)){
				$size = @getimagesize($masterfile_rootPath);
			}
	
			if (checkImgExt(strtolower($file)) && !strstr($file, "thumb_")){
				$i++;
				$cellColor = "";
				$cellColor = cellColor($i);
				$thumbName = "thumb_".$file;
?>
	  <tr>
		<td width="10" align="center" class="<?php echo $cellColor; ?>"><input type="checkbox" id="thumbName" value="<?php echo $masterfile_rootRel; ?>" name="thumbName[]" /></td>
		<td class="<?php echo $cellColor; ?>">
		<?php
		if(isset($newThumb[$masterfile_rootRel]) && $newThumb[$masterfile_rootRel]==1){
		
			echo "<span class=\"txtGreen\" style=\"float: right;\">Created &gt;</span>";	
		
		} elseif(isset($newThumb[$masterfile_rootRel]) && $newThumb[$masterfile_rootRel]==0) {
		
			echo "<span class=\"txtRed\" style=\"float: right;\">Deleted &gt;</span>";
		
		}
		?>
		
		<a href="javascript:;" onclick="openPopUp('<?php echo $glob['adminFile'];?>?_g=filemanager/preview&amp;file=<?php echo $masterfile_rootRel."&amp;x=".$size[0]."&amp;y=".$size[1]; ?>','filemanager',<?php echo $size[0]+12; ?>,<?php echo $size[1]+12; ?>)" class="txtDir"><?php echo $masterfile_rootRel; ?></a><br />
	</td>
		<td align="center" class="<?php echo $cellColor; ?>">
		
		<?php
		if(file_exists($thumb_rootPath)) { ?>
		<a href="javascript:;" onclick="openPopUp('<?php echo $glob['adminFile'];?>?_g=filemanager/preview&amp;file=<?php echo $masterfile_rootRel."&amp;x=".$size[0]."&amp;y=".$size[1]; ?>','filemanager',<?php echo $size[0]+12; ?>,<?php echo $size[1]+12; ?>)" class="txtDir"><img src="<?php echo $thumb_rootRel; ?>" border="0" /></a>
		<?php 
		$size = format_size(@filesize($thumb_rootPath));
		unset($thumb);
		}
		else
		{ 
		$size = "-";
		?>
		<span class="copyText">No Thumb</span>
		<?php } ?>
		</td>
		<td align="center" class="<?php echo $cellColor; ?>">
		<span class="copyText"><?php echo $size; ?></span></td>
	  </tr>
	<?php 
				}
			
			}
		
	} 
	if($i==0){
	?>
	<tr>
    <td colspan="3" class="tdText">No images have been added.</td>
	</tr>
<?php } else { ?>
<tr>
	  <td colspan="4" class="tdText">
				<img src="<?php echo $glob['adminFolder']; ?>/images/selectAll.gif" alt="" width="16" height="11" /> <a href="javascript:checkAll('thumbName','true');" class="txtLink">Check All</a> / <a href="javascript:checkAll('thumbName','false');" class="txtLink">Uncheck All</a>
		<input name="rebuild" type="hidden" value="1" /> 
		<select name="action" size="1" class="textbox" onchange="submitDoc('thumbs');">
                  <option value="">With selected:</option>
				  <option value="rebuild">Build / Rebuild Thumbnail</option>
                  <option value="delete">Delete Thumbnail</option>
        </select></td>
			</tr>
<?php } ?>
</table>
<p class="copyText"><?php echo $pagination; ?></p>
</form>
<?php } ?>