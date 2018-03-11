<?php require_once 'includes/common.php';?>
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

<div class="signUp login fundraisers" >

<h3>Fundraisers </h3>
<p> Do you want to quickly raise money for your particular cause? Sign up here and start generating revenue without taking a penny from your supporters! </p>

<form id="signinForm" name="signinForm" action="signin.<?=$ext?>" method="post">
<input type="hidden" name="user_level" value="2" />
<input class="left submit" name="submit" tabindex="11" value="Sign me up!" type="submit" />
</form>
</div>
<div class="signUp login fundraisers ">

<h3>Supporters</h3>
<p>
Do you want to donate your time to charitable & innovative causes and earn money at the same time? Join our community by signing up here!
</p>
<form id="signinForm" name="signinForm" action="signin.<?=$ext?>" method="post">
<input type="hidden" name="user_level" value="1" />
<input class="left submit" name="submit" tabindex="11" value="Sign me up!" type="submit" />
</form>
</div>
<div class="login">
<h3>Log in</h3>
<p>
<?php
if (isset($msg)){
	echo $msg;
} else{
	echo('Please log in to continue.') ;
}
?>
</p>
<form id="loginForm" name="loginForm" action="signup.<?=$ext?>" method="post">
<ul>
<li>
<label for="email">Email</label>
<input class="input-text text" id="username" name="username" tabindex="1" type="text" required="required" placeholder="Enter your username or email." />
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
</div>
</div>
</body>
</html>
