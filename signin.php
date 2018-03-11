<?php require_once 'includes/common.php';?>
<?php
if(isset($_POST['submit']) && $_POST['submit']=='Sign me up!') {
$emailArray = $db->select("SELECT user_id, user_level FROM ".$glob['dbprefix']."users WHERE email_address=".$db->mySQLSafe($_POST['email']));
if(empty($_POST['name']) || empty($_POST['email']) || empty($_POST['email_confirmation'])|| empty($_POST['password']) || empty($_POST['password_confirmation'])) {
$errorMsg = "Please make sure all required fields are completed.";
}elseif($_POST['email'] !== $_POST['email_confirmation']) {
$errorMsg ="Please make sure your emails match.";
}elseif($_POST['password'] !== $_POST['password_confirmation']) {
	
$errorMsg ="Please make sure your passwords match.";
} elseif(validateEmail($_POST['email'])==FALSE) {
	
$errorMsg ="Please enter a valid email address.";
	
}elseif($emailArray == true) {
	$errorMsg ="Sorry but that email is assigned to an account holder.";
}else {
		
		
		
		$record["email_address"]	= $db->mySQLSafe($_POST['email']);
		$record["user_level"]		= $db->mySQLSafe($_POST['user_level']);
		$record["username"]		= $db->mySQLSafe($_POST['name']);
		$record["activated"]		= $db->mySQLSafe(1);
		if($_POST['user_level']==2){
			$record["text_page"]= $db->mySQLSafe('<p><span style="font-size: large; "><strong>My Fundee Page</strong></span></p>
	<p>&nbsp;</p>
	<p><input type="image" src="http://www.digitalcollectionplate.com/images/general/donation_demo_image3.jpg" alt="Digital Collection Plate | Fundraiser Page" /></p>
	<p>&nbsp;</p>
	<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur</p>');	
		if (!empty($_POST['Short_Link'])){
		$record["Short_Link"] = $db->mySQLSafe($_POST['Short_Link']);
		} else {
			$first_inital = substr($_POST['first_name'], 0,1); 
			$generatedlink = $first_inital.$_POST['last_name'].rand(1, 99);
			$record["Short_Link"] = $db->mySQLSafe($generatedlink);
		}
			}
		$record["signup_date"]		= $db->mySQLSafe(gmdate("Y-m-d H:i:s"));
		$record["ip_address"]		= $db->mySQLSafe(get_ip_address());
		$salt 				= randomPass(6);
		$record["salt"] 		= "'".$salt."'"; 
		$record["password"] 		= $db->mySQLSafe(md5(md5($salt).md5($_POST['password'])));
	
	
		if ($emailArray == true) {
		// update
		
			$where = "user_id = ".$db->mySQLSafe($emailArray[0]['user_id']);
	
			$update = $db->update($glob['dbprefix']."users", $record, $where);
			
			$sessData['user_id'] = $emailArray[0]['user_id'];
			$update = $db->update($glob['dbprefix']."sessions", $sessData,"sessId=".$db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));
			$redir = sanitizeVar(urldecode($_COOKIE["redir"]));
			} else {

			$insert = $db->insert($glob['dbprefix']."users", $record);
			
			## send welcome email
			
				require_once "classes".CC_DS."htmlMimeMail".CC_DS."htmlMimeMail.php";
				require_once "includes".CC_DS."email.inc.php";
				
				$mail = new htmlMimeMail();
				
				$macroArray = array(
					"CUSTOMER_NAME" => sanitizeVar($_POST['name']),
					"EMAIL"			=> sanitizeVar($_POST['email']),
					"PASSWORD"		=> sanitizeVar($_POST['password']),
					"STORE_URL"		=> 'www.digitalcollectionplate.com',
					"SENDER_IP"		=> get_ip_address()
					
				);
				
				$text = macroSub($lang['email']['new_reg_body'],$macroArray);
				unset($macroArray);
				
				$mail->setText($text);
				$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
				$mail->setReturnPath($config['masterEmail']);
				$mail->setSubject($lang['email']['new_reg_subject']);
				$mail->setHeader('X-Mailer', 'digitalcollectionplate.com');
				$mail->send(array(sanitizeVar($_POST['email'])), $config['mailMethod']);
			
			
			$sessData['user_id'] = $db->insertid();
			$update = $db->update($glob['dbprefix']."sessions", $sessData,"sessId=".$db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));
			httpredir($GLOBALS['rootRel']."dashboard.php");
		}
		
}
}
?>
<?php
if (isset($_POST['username']) && isset($_POST['password'])) {
	$remember = (!empty($_POST['remember'])) ? true : false;
	
	$emailArray = $db->select("SELECT user_id, email_address FROM ".$glob['dbprefix']."users WHERE email_address=".$db->mySQLSafe($_POST['username']));
if($emailArray == true){	
	$cc_session->authenticate($_POST['username'],$_POST['password'], $remember);
}else
{
$cc_session->authenticate2($_POST['username'],$_POST['password'], $remember);
}
	
}
if($cc_session->ccUserData['user_id'] > 0   &&  isset($_POST['submit'])){
	//$msg="You have logged in successfully.";
	httpredir("dashboard.php");
} elseif($cc_session->ccUserData['user_id']>0 &&  !isset($_POST['submit'])) {
	//$msg="You have already logged in.";
	httpredir("dashboard.php");
} elseif($cc_session->ccUserData['user_id'] == 0 && $_POST['submit']!="") {
	if($cc_session->ccUserBlocked == TRUE){
		$msg=sprintf("<p class=\"warnText\">Authentication blocked for %s ",sprintf("%.0f",$ini['bftime']/60)." minutes for security reasons.</p>");
	} else{
		$msg="<p class=\"warnText\">Login failed!</p>";
	}
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
<div class="login">
<h3>Log in</h3>
<p><?php
if (isset($msg)){
	echo $msg;
} else{
	echo('Please log in to continue.') ;
}
?></p>
<form id="loginForm" name="loginForm" action="signin.<?=$ext?>" method="post">
<ul>
<li>
<label for="email">Email</label>
<input class="input-text text" id="username" name="username" tabindex="1" type="text"  required="required" placeholder="Enter your username or email."/>
</li>
<li>
<span class="input-tip"><a href="forgot-password.<?=$ext?>" class="link_to_forgot-password-dialog">I forgot my password</a></span>
<label>Password</label>
<input  name="password" tabindex="2" type="password" required="required" placeholder="Enter your account password." />
</li>
<li>
<input checked="checked"  name="remember_me" tabindex="3"  type="checkbox" />
<label style="display:inline-block">Remember me</label>
</li>
<li>
<input class="submit" name="submit" tabindex="4" value="Sign in!" type="submit" />
</li>
</ul>
</form>

</div>
<div class="signUp login">

<h3>New to Digital Collection Plate?</h3>
<p>
<?php
if (isset($errorMsg)){
	echo "<p class=\"warnText\">".$errorMsg."</p>";
} else{
	echo('An account is required to continue.') ;
}
?>
</p>

<div class="fieldset-errors">

</div>
<form id="signUpForm" name="signUpForm" action="signin.<?=$ext?>" method="post">
<ul>
<li>
<label for="user_name">Username</label>
<input class="input-text text" id="user_name" name="name" size="30" tabindex="5" type="text" required="required" />
</li>
<li id="form-signup-email">
<label for="user_email">Email</label>
<input class="input-text text" id="user_email" name="email" size="30" tabindex="6" type="text" required="required" />

</li>
<li>
<label for="user_email_confirmation">Re-Enter Email</label>
<input class="input-text text" id="user_email_confirmation" name="email_confirmation" size="30" tabindex="7" type="text" required="required" />
</li>
<li>
<label for="user_password">Password</label>
<input class="input-text password" id="user_password" name="password" tabindex="8" type="password" required="required" />
</li>
<li>
<label for="user_password_confirmation">Re-Enter Password</label>
<input class="input-text password" id="user_password_confirmation" name="password_confirmation" tabindex="9" type="password" required="required" />
</li>
<li>

<input checked="checked"  class="left"  name="checkbox" tabindex="10" type="checkbox" style="margin:8px 10px 20px" />
<label  style="display:inline-block" ><strong>Discover new projects</strong><br /> with our weekly newsletter</label>
</li>
<li>
<span class="left">By signing up, you agree to our <a href="terms-conditions.<?php echo $ext;?>" >Terms & Conditions</a>.</span>
</li>
<li>
<input type="hidden" name="user_level" value="<?=$_POST['user_level']?>" />
<input class="left submit" name="submit" tabindex="11" value="Sign me up!" type="submit" />
</li>
</ul>
</form>
</div>
</div>
</div>

</body>
</html>