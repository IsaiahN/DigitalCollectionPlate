<?php 
if(($_GET['act'] != "preview") || ($cpage != "fundeedonation" )) {
if(!isset($cc_session->ccUserData['user_id'])){
	httpredir('signin.php');
}
}
?>


