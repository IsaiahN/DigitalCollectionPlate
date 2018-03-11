<?php require_once 'includes/common.php';?>
<?php
if($_POST['email']){
	$query = "SELECT first_name, last_name FROM ".$glob['dbprefix']."users WHERE `email_address` = ".$db->mySQLSafe($_POST['email'])." AND `type`>0";
$result = $db->select($query);
// start validation
	if ($result == false || empty($_POST['email'])) {
		$errorMsg ="<p class=\"warnText\">Sorry but that email address was not found.</p>";
		
	}else {
		// update to new password
		$salt = randomPass(6);
		$newPass = randomPass();
		$data['salt'] = "'".$salt."'";
		$data['password'] = $db->mySQLSafe(md5(md5($salt).md5($newPass)));
		$where = '`email_address` = '.$db->mySQLSafe($_POST['email']);
		$update = $db->update($glob['dbprefix'].'customer', $data, $where);
		
		// send email
		include("includes/email.inc.php");
		require("classes".CC_DS."htmlMimeMail".CC_DS."htmlMimeMail.php");
		
		
		
		$mail = new htmlMimeMail();
		$macroArray = array(
			"RECIP_NAME"	=> $result[0]['first_name']." ".$result[0]['last_name'],
			"EMAIL"			=> $_POST['email'],
			"PASSWORD"		=> $newPass,
			"SITE_URL"		=> $glob['storeURL']."/login.php",
			"SENDER_IP" => get_ip_address()
			
		);
		
		$text = macroSub($lang['email']['reset_password_body'],$macroArray);
		unset($macroArray);
		
		$mail->setText($text);
		$mail->setReturnPath($_POST['email']);
		$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
		$mail->setSubject($lang['email']['reset_password_subject']);
		$mail->setHeader('X-Mailer', 'digitalcollectionplate.com');
		$send = $mail->send(array($_POST['email']), $config['mailMethod']);
		$passSent = TRUE;
	
	}

}if($passSent == TRUE)
{
	$errorMsg= sprintf("<p class=\"infoText\">A new password has been sent to %s.</p>",$_POST['email']);
}
?>
<body>
<div class="topbg2 maindiv">
<div class="maincenter">
<div class="header">
<?php require_once 'includes/header.php';?>
</div>
<a href="index.<?=$ext;?>" class="left"><img alt="" src="images/logos/logo2.jpg"  class="left logo2" /> </a>
<div class="menu2">
<?php require_once 'includes/nav.php';?>
</div>
</div>
</div>
</div>
<div class="maincenter">
<div class="content bgNone">
<h3>Forgotten Password</h3>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>
<?php
if (isset($errorMsg)){
	echo $errorMsg;
} else{
	echo ("Please enter your login email address below to have a temporary password sent to you:");
}
?>
</p>
<p>&nbsp;</p>
<form name="forgotpasswordForm" action="forgot-password.<?php echo $ext;?>" method="post">
<div class="register_row">
<div class="left_col">Your Email:</div>
<div class="right_col"><input type="text" value="<?php echo $_POST['email']; ?>" name="email" size="30" class="textbox" required placeholder="Enter your Email." /></div>
</div>
<div class="register_row">
<div class="left_col">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
<div class="right_col"><input value="Send Password" name="submit" type="submit" class="submit"></div>
</div>
</form>

</div>
</div>

</body>
</html>
