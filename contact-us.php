<?php require_once 'includes/common.php';?>
<?php
if($_POST['submit']=='Send'){
		
	$name=$_POST['name'];
	$phone=$_POST['phone'];
	$email=$_POST['email'];
	$comments=$_POST['comments'];
	$to = "".$config['storeName']." <".$config['masterEmail'].">"; 
	
	// make email
		require("classes".CC_DS."htmlMimeMail".CC_DS."htmlMimeMail.php");
		
		$mail = new htmlMimeMail();
		require_once("includes/email.inc.php");
       
		$macroArray = array(
		
				"RECIP_NAME" => $name,
				"TELEPHONE" => $phone,
				"EMAIL" => $email,
				"COMMENTS" => $comments,
				"SITE_URL" => "www.digitalcollectionplate.com",
				"SENDER_IP" => get_ip_address()
		
			);
		
		$text = macroSub($lang['email']['admin_contact_us_body'],$macroArray);
		unset($macroArray);
		$mail->setText($text);
		$mail->setReturnPath($_POST['email']);
		$mail->setFrom($name." <".$config['masterEmail'].">");
		$mail->setSubject($lang['email']['admin_contact_us_subject']);
		$mail->setHeader('X-Mailer', 'digitalcollectionplate.com');
		$result = $mail->send(array($config['masterEmail']), $config['mailMethod']);
			
		
		
	if($result== false)
	{ 
	$msgs= "<p class='warnText'>Error sending message</p>";
		
	} 
	else
	{
			require("classes".CC_DS."htmlMimeMail".CC_DS."htmlMimeMail.php");
			$mail = new htmlMimeMail();
        	require_once("includes/email.inc.php");
			$macroArray = array(
		
				"RECIP_NAME" => $name,
				"ADDRESS" => $config['storeAddress'],
				"TELEPHONE" => $config['phone'],
				"EMAIL" =>$config['masterEmail'],
				"COMMENTS" => $comments,
				"SITE_URL" => "www.digitalcollectionplate.com",
				"SENDER_IP" => get_ip_address()
		
			);
					
		$text = macroSub($lang['email']['users_contact_us_body'],$macroArray);
		unset($macroArray);
		$mail->setText($text);
		$mail->setReturnPath($_POST['email']);
		$mail->setFrom($config['storeName']." <".$config['masterEmail'].">");
		$mail->setSubject($lang['email']['users_contact_us_subject']);
		$mail->setHeader('X-Mailer', 'digitalcollectionplate.com');
		$result = $mail->send(array($email), $config['mailMethod']);
		
	} 
print "<meta http-equiv=\"refresh\" content=\"0;URL=thanks.php\">";
exit;
}
?>
<body>
<div class="topbg2 maindiv">
<div class="maincenter">
<div class="header">
<?php require_once 'includes/header.php';?>
</div>
<a href="index.<?php echo $ext;?>" class="left"><img alt="DigitalCollectionPlate Logo" src="images/logos/logo2.jpg"  class="left logo2" /> </a>
<div class="menu2">
<?php require_once 'includes/nav.php';?>
</div>
</div>
</div>
</div>
<div class="maincenter">
<div class="content bgNone">
<div class="contentBox">
<h1><?php echo validHTML(stripslashes($result[0]['title']));?></h1>
<?php
if (isset($msgs)){
	echo $msgs;
} else{
echo stripslashes($result[0]['content']);
}
?>
<div class="box_contact">
<br />
<form id="contactUsForm" name="contactUsForm" action="contact-us.<?php echo $ext;?>" method="post">

<div class="contact_row">
<div class="contact_col_left">Your Name:</div>
<div class="contact_col_right"><input type="text" value="<?php echo $_POST['name']; ?>" name="name" class="textbox" required="required"  /></div>
</div>

<div class="contact_row">
<div class="contact_col_left">Email Address:</div>
<div class="contact_col_right"><input id="email" name="email" value="<?php echo $_POST['email']; ?>" class="textbox" required="required" type="email" /></div>
</div>
<div class="contact_row">
<div class="contact_col_left">Phone:</div>
<div class="contact_col_right"><input type="text" value="<?php echo $_POST['phone']; ?>" name="phone" class="textbox" required="required" /></div>
</div>
<div class="contact_row">
<div class="contact_col_left">Your comment:&nbsp;</div>
<div class="contact_col_right"><textarea   name="comments" cols="25" rows="5" required="required" class="textbox"><?php echo $_POST['comments']; ?></textarea></div>
</div>
<div class="contact_row">
<div class="contact_col_left">&nbsp;</div>
<div class="contact_col_right">
<input type="submit" class="submit" name="submit" id="submit" value="Send"></div>
</div>
</form>
</div>

</div>
</div>
</div> 
<div class="maindiv footer">
<?php require_once 'includes/footer.php';?>
</div>
</body>
</html>
