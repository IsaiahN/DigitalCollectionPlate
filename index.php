<?php require_once 'includes/common.php';?>
<?php
if($_POST['action']=='send'){
	$name=$_POST['name'];
	$email=$_POST['email'];
	$message=$_POST['message'];
	$to = "".$config['storeName']." <".$config['masterEmail'].">"; 
	
	// make email
		require("classes".CC_DS."htmlMimeMail".CC_DS."htmlMimeMail.php");
		
		$mail = new htmlMimeMail();
		require_once("includes/email.inc.php");
       
		$macroArray = array(
		
				"RECIP_NAME" => $name,
				"EMAIL" => $email,
				"MESSAGE" => $message,
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
<a href="index.<?=$ext;?>" class="left"><img alt="" src="images/logos/logo2.jpg"  class="left logo2" /> </a>
<div class="menu2">
<?php require_once 'includes/nav.php';?>
</div>
</div>
</div>
</div>
<div class="maindiv bannerBg">
<div class="maincenter">
<div class="baner">
<div id="wrapper">
<div class="slider-wrapper theme-default">
<div class="ribbon"></div>
<div id="slider" class="nivoSlider">
<?php require_once 'includes/slider.php';?>
</div>
<div id="htmlcaption" class="nivo-html-caption">
<strong>Save this House</strong>A beautiful, modern home built at the beginning of the Great Recession
is at risk of destruction after years of vacancy.
</div>
</div>
</div>
</div>
</div>
</div>
<div class="maincenter">
<div class="content bgNone">
<?php echo ($home[0]['content']);?>
<div class="maindiv">
<a href="signup.<?php echo $ext;?>" class="addCause"><img alt="" src="images/buttons/addyourcause.jpg"  /></a>
</div>
<div class="box2">
<a href="causes.php?causeId=1" class="donteNow"><img alt="" src="images/buttons/donate.jpg"  /></a>
<h1>Donate Your Time Today!</h1>
<p><?php echo $config['donate']; ?></p>
</div>
<div class="maindiv">
<?php require_once 'includes/causes.inc.php';?>
</div>
</div>
</div> 
<div class="maindiv footer">
<?php require_once 'includes/footer.php';?>
</div>
</body>
</html>