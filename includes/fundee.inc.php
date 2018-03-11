<div class="tabbedheader">Edit My Fundraiser</div>
<div class="seprator2 long"></div>
<div class="box3">
<form id="fundeeForm" class="tableft" name="fundeeForm" action="dashboard.php" method="post">
<?php

	require($glob['adminFolder']."/includes".CC_DS."rte".CC_DS."fckeditor.php");
	$oFCKeditor = new FCKeditor('FCKeditor');
	$oFCKeditor->BasePath = $GLOBALS['rootRel'].$glob['adminFolder'].'/includes/rte/';
	$oFCKeditor->Value = (isset($cc_session->ccUserData['text_page'])) ? (!get_magic_quotes_gpc ()) ? stripslashes($cc_session->ccUserData['text_page']) : $cc_session->ccUserData['text_page'] : '';
	if ($config['richTextEditor'] == 0) {
		$oFCKeditor->off = true;
	}
	$oFCKeditor->Create();
?>
<div style="margin-top: 10px;"><input type="submit" class="submit" name="submit" id="submit" value="Edit My Fundee Page"></div>
</form>
</div>