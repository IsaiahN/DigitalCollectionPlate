<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }
permission("filemanager","read", true);

$dirArray = walkDir("..".CC_DS."images".CC_DS."uploads", false, 0, 0, false, $int = 0);

if(is_array($dirArray)){

foreach ($dirArray as $file) {
	$i++;
	$file = preg_replace("#//+#", CC_DS, $file);
	$IMAGES_BASE_DIR = preg_replace("#//+#", '/', "..".CC_DS."images".CC_DS."uploads".CC_DS);
	
	$file = preg_replace("#$IMAGES_BASE_DIR#", '', $file);
	
	if (checkImgExt(strtolower($file))) {
	
		$files[$i]['name'] = str_replace("..".CC_DS."images".CC_DS."uploads".CC_DS,"",$file);	//adding filenames to array
		$files[$i]['size'] = filesize($IMAGES_BASE_DIR.$file);	//adding filenames to array
	
	}
}

if(is_array($files)){
sort($files);	//sorting array
}

// generating $html_img_lst
$html_img_lst = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
for ($i = 0; $i < count($files); $i++) {
	$html_img_lst .= "<tr><td><a href=\"javascript:getImage('".$files[$i]['name']."');\">".$files[$i]['name']."</a></td><td align='right'>".format_size($files[$i]['size'])."</td></tr>\n";
}
$html_img_lst .= "</table>"; 
} else { // end if is array
	$empty = 1;
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<html>
	<head>
		<title>Image Browser</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $glob['adminFolder']; ?>/includes/rte/editor/css/fck_dialog.css">
		<script language="javascript">
var sImagesPath  = "<?php echo $GLOBALS['rootRel']."images/uploads/"; ?>";
var sActiveImage = "" ;
var fileName = "" ;

function getImage(imageName)
{
	if(imageName){
	sActiveImage = sImagesPath + imageName ;
	fileName = imageName;
	} else {
	sActiveImage = sImagesPath + 'noPreview.gif' ;
	}
	imgPreview.src = sActiveImage ;
}
<?php
if($_GET['custom']==1){
?>
function ok()
{	
	window.opener.addImage(fileName, sActiveImage) ;
	window.close() ;
}
<?php
} else {
// start standard code
?>
function ok()
{	
	window.setImage(sActiveImage);
	window.close() ;
}
<?php } ?>
		</script>
	</head>
	<body bottommargin="5" leftmargin="5" topmargin="5" rightmargin="5">
<TABLE cellspacing="1" cellpadding="1" border="0" class="dlg" height="100%">
	<tr height="100%">
		<td>
			<TABLE cellspacing="0" cellpadding="0" border="0" height="100%">
				<tr>
					<td valign="top">
						<table cellpadding="0" cellspacing="0" height="100%" width="220">
							<tr>
								<td>File:</td>
							</tr>
							<tr height="100%">
								<td>
									<div class="ImagePreviewArea">
									<?php 
									if($empty==1){ 
										echo "No images are available. Please close this window and upload an image instead."; 
									} else { 
										echo $html_img_lst; 
									} 
									?>
									</div>
								</td>
							</tr>
						</table>
				  </td>
					<td width="5">&nbsp;</td>
					<td>
						<table cellpadding="0" cellspacing="0" height="100%" width="220">
							<tr>
								<td>Preview:</td>
							</tr>
							<tr>
								<td height="100%" align="center" valign="middle">
									<?php if($empty==1){ ?>&nbsp;<?php } else { ?><div class="ImagePreviewArea"><IMG src="<?php echo $glob['rootRel'];?>images/general/px.gif" border="0" id="imgPreview" title="" alt="" /></div><?php } ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center">
			<input style="width: 80px" type="button" value="OK" onClick="ok();" />  
			
			<input style="width: 80px" type="button" value="Cancel" onClick="window.close();" />
		</td>
	</tr>
</table>
	</body>
</html>
