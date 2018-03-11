<?php require_once 'includes/common.php';?>
<?php
if ($_POST['submit']=="Update Account" && $cc_session->ccUserData['user_id']>0) {

	if ($_POST['email_address']!==$cc_session->ccUserData['email_address']) {
		$emailArray = $db->select("SELECT user_id, type FROM ".$glob['dbprefix']."users WHERE email_address=".$db->mySQLSafe($_POST['email_address']));
	}

	if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email_address']) || empty($_POST['phone']) || empty($_POST['address'])) {
		$msg ="Please make sure all required fields are completed.";
	} else if (!validateEmail($_POST['email_address'])) {
		$msg ="Please enter a valid email address.";
		
	} else if(!preg_match('#^([0-9-\s]+)$#',$_POST['phone'])) {
		$msg ='Telephone numbers must be numeric only.';
	} else if(!empty($_POST['mobile']) && !preg_match('#^([0-9-\s]+)$#', $_POST['mobile'])) {
		$msg ="Telephone numbers must be numeric only.";
	} else if(isset($emailArray) && $emailArray == true ) {
		$msg ="Sorry that email address is already in use.";
	} else {
		## update database
if($_FILES['file']['name']!=""){
$target = "images/avatar/";
$target = $target . basename($_FILES['file']['name']) ;
if(move_uploaded_file($_FILES['file']['tmp_name'], $target)){
$data['avatar'] = $db->mySQLSafe($_FILES['file']['name']); 
}
}


		$data['username'] = $db->mySQLSafe($_POST['username']);
		$data['first_name'] = $db->mySQLSafe($_POST['first_name']);
		$data['last_name'] = $db->mySQLSafe($_POST['last_name']); 
		$data['email_address'] = $db->mySQLSafe($_POST['email_address']); 
		$data['organization'] = $db->mySQLSafe($_POST['organization']); 
		$data['address'] = $db->mySQLSafe($_POST['address']);
		$data['website'] = $db->mySQLSafe($_POST['website']);
		$data['phone'] = $db->mySQLSafe($_POST['phone']); 
		$data['Short_Link'] = $db->mySQLSafe($_POST['Short_Link']); 
		$data['about'] = $db->mySQLSafe($_POST['about']); 
		
		$where = "user_id = ".$cc_session->ccUserData['user_id'];
		$updateAcc = $db->update($glob['dbprefix']."users",$data,$where);
		
		## make email
		require("classes".CC_DS."htmlMimeMail".CC_DS."htmlMimeMail.php");
		require("includes".CC_DS."email.inc.php");
				
		$mail = new htmlMimeMail();
		
		$macroArray = array(
			"CUSTOMER_NAME" => sanitizeVar($_POST['firstName']." ".$_POST['lastName']),
			"Site_URL" => $GLOBALS['storeURL'],
			
		);
		
		$text = macroSub($lang['email']['profile_mofified_body'], $macroArray);
		unset($macroArray);
		
		$mail->setText($text);
		$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
		$mail->setReturnPath($config['masterEmail']);
		$mail->setSubject($lang['email']['profile_mofified_subject']);
		$mail->setHeader('X-Mailer', 'digitalcollectionplate.com');
		$send = $mail->send(array(sanitizeVar($_POST['email_address'])), $config['mailMethod']);
		
		## rebuild user array
		$query	= "SELECT * FROM ".$glob['dbprefix']."sessions INNER JOIN ".$glob['dbprefix']."users ON ".$glob['dbprefix']."sessions.user_id = ".$glob['dbprefix']."users.user_id WHERE sessId = '".$GLOBALS[CC_SESSION_NAME]."'";
		$results	= $db->select($query);
		$cc_session->ccUserData = $results[0];
	}
}

if (isset($_POST["keyword"])) {
$keyword = mysql_real_escape_string($_POST["keyword"]);
if (strlen($keyword) >= 8 ) {
		$qry= "
		SELECT `organization`, `Short_Link`, `about`, `avatar`, `signup_date`
		FROM  `users`
		WHERE MATCH (organization, Short_Link) AGAINST ('".$keyword."' IN BOOLEAN MODE) 
		AND `user_level` = '1' ORDER BY `signup_date` DESC
		";
}else {
		$qry= "
		SELECT `organization`, `Short_Link`, `about`, `avatar`, `signup_date`
		FROM  `users` 
		WHERE `organization` LIKE '%".$keyword."%' 
		AND `user_level` = '1' ORDER BY `signup_date` DESC
		";
}
}
if ($_POST['submit']=="Edit My Fundee Page" && $cc_session->ccUserData['user_id']>0) {
$data['text_page'] = $db->mySQLSafe($_POST['FCKeditor']);
$where = "user_id = ".$cc_session->ccUserData['user_id'];
$updateAcc = $db->update($glob['dbprefix']."users",$data,$where);
$query	= "SELECT * FROM ".$glob['dbprefix']."sessions INNER JOIN ".$glob['dbprefix']."users ON ".$glob['dbprefix']."sessions.user_id = ".$glob['dbprefix']."users.user_id WHERE sessId = '".$GLOBALS[CC_SESSION_NAME]."'";
$results	= $db->select($query);
$cc_session->ccUserData = $results[0];
}

?>
<body>
<div class="topbg maindiv">
	<div class="maincenter">
      <div class="header">
    	<?php require_once 'includes/header-cp.php';?>          
        </div>
      <div class="content">
      <div  id="tabContainer2">
          	<div class="menu">
            	<?php require_once 'includes/nav-cp.php';?>
                </div>
 <div class="left" style="width:964px;">    

<?
if($cc_session->ccUserData['user_level']==2){
?>
<div class="donation">
<div class="leftSide">
<div class="tabpage" id="tabpage_5">
<?php if (!empty($cc_session->ccUserData['Short_Link'])) { ?>
<div class="tabbedheader">View My Fundraiser</div>
<div class="seprator2 long"></div> <br /> 
<span>Displayed below is a preview of your fundraiser page. <br />  To view your full page, <a href="http://www.digitalcollectionplate.com/u/<?php echo $cc_session->ccUserData['Short_Link'];?>">click here</a>.</span><br /><br /> 
<iframe class="iframe_preview" src="http://www.digitalcollectionplate.com/preview/<?php echo $cc_session->ccUserData['Short_Link'];?>" width="1020" height="800"></iframe>
<?php } ?>
 </div>
	<div class="tabpage" id="tabpage_6">
    <? require_once 'includes/donations.inc.php';?>
    </div>
    <div class="tabpage" id="tabpage_7">
   <? require_once 'includes/fundee.inc.php';?>
    </div>
    <div class="tabpage" id="tabpage_8">
    <? require_once 'includes/general-setting.inc.php';?>
    </div>
    </div>
   </div>
<?
}
?>
</div>
</div>
</div>
</div>
</div>
<script src="js/acidTabs.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
$("#tabContainer").acidTabs({
     style: "three"											 
});
$("#tabContainer2").acidTabs({
   											 
});
});
</script>	
</body>
</html>