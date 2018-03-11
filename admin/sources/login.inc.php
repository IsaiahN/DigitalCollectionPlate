<?php 

if(!defined('CC_INI_SET')){ die("Access Denied"); }
if (isset($_POST['username']) && isset($_POST['password']))
{

	$result = $admin_session->login($_POST['username'], $_POST['password']);
	// data for admin session log
	$data["username"] = $db->mySQLSafe($_POST['username']);
	$data["time"] = time();
	$data["ipAddress"] = $db->mySQLSafe(get_ip_address());		
		
	if($result == TRUE) 
	{
		// First level of brute force attack prevention
		if($db->blocker($_POST['username'],$ini['bfattempts'],$ini['bftime'],TRUE,"b")==TRUE)
		{
			$blocked = TRUE; 
		}
		else
		{
		
			$data["success"] = 1;
			// Reset fail level
			$newdata['failLevel'] = 0;
			$newdata['blockTime'] = 0;
			$newdata['noLogins'] = "noLogins+1";
			
			$db->update($glob['dbprefix']."admin_users", $newdata, "adminId=".$result[0]['adminId'],$stripQuotes="");
		
		}
	
	} 
	else
	{
		// First level of brute force attack prevention
		$blocked = $db->blocker($_POST['username'],$ini['bfattempts'],$ini['bftime'],FALSE,"b");

		if($blocked==FALSE)
		{
		
			$data["success"] = 0;
			
			// check user exists
			$query = sprintf("SELECT adminId, failLevel, blockTime, username, lastTime FROM ".$glob['dbprefix']."admin_users WHERE username = %s", 
			$db->mySQLSafe($_POST['username']));
	 
			$user = $db->select($query);
			
			// Second level of brute force attack prevention
			if($user==TRUE)
			{
				
				if($user[0]['blockTime']>0 && $user[0]['blockTime']<time())
				{
					// reset fail level and time
					$newdata['failLevel'] = 1;
					$newdata['blockTime'] = 0;
				}
				elseif($user[0]['failLevel']==($ini['bfattempts']-1))
				{
					
					$timeAgo = time() - $ini['bftime'];
					
					if($user[0]['lastTime']<$timeAgo)
					{
						$newdata['failLevel'] = 1;
						$newdata['blockTime'] = 0;
					}
					else
					{
					
						// block the account
						$newdata['failLevel'] = $ini['bfattempts'];
						$newdata['blockTime'] = time()+$ini['bftime'];
					
					}
				
				}
				elseif($user[0]['blockTime']<time())
				{
					
					$timeAgo = time() - $ini['bftime'];
					if($user[0]['lastTime']<$timeAgo)
					{
						$newdata['failLevel'] = 1;
					}
					else
					{
						// set fail level + 1
						$newdata['failLevel'] = $user[0]['failLevel']+1;
					}
					
					$newdata['blockTime'] = 0;
				}
				else
				{
					$msg = "<p class='warnText'>".sprintf("Authentication blocked for %s minutes for security reasons.",($ini['bftime']/60))."</p>";
					$blocked = TRUE;
				}
				
				if(is_array($newdata))
				{
					$newdata['lastTime'] = time();
					$db->update($glob['dbprefix']."admin_users", $newdata, "adminId=".$user[0]['adminId'],$stripQuotes="");
				}
			
			} 
		
		}
		else
		{
			// login failed message
			$msg = "<p class='warnText'>Last login by %s, failed on %s</p>";

		}
		
	}	
	
	if($blocked==TRUE)
	{
		$msg = "<p class='warnText'>".sprintf("Authentication blocked for %s minutes for security reasons.",sprintf("%.0f",($ini['bftime']/60)))."</p>";
	}
	else
	{
		
		$insert = $db->insert($glob['dbprefix']."admin_sessions", $data);
			
		// if there is over max amount of login records delete last one
		// this prevents database attacks of bloating
		if($db->numrows("SELECT loginId FROM ".$glob['dbprefix']."admin_sessions")>250)
		{
			$loginId = $db->select("SELECT min(loginId) as id FROM ".$glob['dbprefix']."admin_sessions");
			$db->delete($glob['dbprefix']."admin_sessions","loginId='".$loginId[0]['id']."'");
		}
	
	}
	
	
	if($result == TRUE && $blocked==FALSE)
	{
		

		$admin_session->createSession($result[0]['adminId']);
		
		if(isset($_GET['goto']) && !empty($_GET['goto']))
		{
			httpredir(sanitizeVar(urldecode($_GET['goto'])));
		} 
		else 
		{
			httpredir($GLOBALS['rootRel'].$glob['adminFile']);
		}
		
	}

}
if(isset($_GET['email']))
{
	$msg = "<p class='infoText'>A new password has been emailed to ".sanitizeVar(urldecode($_GET['email']))."</p>";
}

require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");



$goTo = sanitizeVar($_GET['goto']);


	// make sure goto URL is HTTP rather than HTTPS
	$goTo = str_replace($config['storeURL_SSL'], $glob['storeURL'],$goTo);
	
	$onclickurl = $config['storeURL_SSL']."/".$glob['adminFile']."?_g=login&amp;ccSSL=1";
	$postUrl = $glob['storeURL']."/".$glob['adminFile']."?_g=login";


if(!empty($goTo)){
	$onclickurl .= "&amp;goto=".urlencode($goTo);
	$postUrl .= "&amp;goto=".urlencode($goTo);
}
?>
<style type="text/css">
body{
background-color:#DADEDF;
margin-top:0px;

}
</style>
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
  <tr>
    <td width="800"><table width="799" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="1" colspan="5"></td>
        </tr>
      <tr>
        <td colspan="5" align="center" valign="middle"><img src="admin/images/ccAdminLogoLrg.gif" width="800" height="150" alt="FCM Investment Group" /></td>
      </tr>
      <tr>
        <td height="1" colspan="5"></td>
        </tr>
      
      <tr>
        <td height="10" colspan="5" align="center" bgcolor="#ffffff">
        <?
        if(isset($msg))
{ 
	echo msg($msg,FALSE); 
} 
elseif(!isset($GLOBALS[CC_ADMIN_SESSION_NAME]) && !isset($_POST['username']) && !isset($_POST['password']))
{ 
?>
<p class="infoText">No administration session was found.</p>
<?php } elseif (isset($_POST['username']) && isset($_POST['password'])){ ?>
<p class="warnText">Login failed. Please try again.</p>
<?php } ?>        </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="80">&nbsp;</td>
  </tr>
  <tr>
    <td><table width="765" height="400" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="168" align="center" valign="top">&nbsp;</td>
        <td width="18"></td>
        <td width="357" height="300" align="left" valign="top">
        
        <form action="<?php echo  $postUrl; ?>" method="post" enctype="multipart/form-data" name="ccAdminLogin" target="_self"  onsubmit="disableSubmit(document.getElementById('login'),'Please wait ...');" >
  
  <table border="0" align="center" width="224" cellpadding="3" cellspacing="1" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle">Please login below:</td>
    </tr>
  <tr>
    <td class="tdText">Username:</td>
    <td><input name="username" type="text" id="username" class="textbox"  value="<?php if(isset($_POST['username'])) echo sanitizeVar($_POST['username']); ?>" /></td>
  </tr>
  <tr>
    <td class="tdText">Password:</td>
    <td><input name="password" type="password" id="password" class="textbox"  /></td>
  </tr>
  <?php
  if($config['ssl']==1)
  {
	  
?>
	  <tr>
		<td>&nbsp;</td>
		<td class="tdText">Use secure login: <input type="checkbox" name="ccSSL" value="1" <?php if($_GET['ccSSL']==1) { echo "checked='checked'"; }?> 
		onclick="parent.location='<?php echo  $onclickurl; ?>'" /></td>
	  </tr>
	  <?php
  }
  ?>
  <tr>
    <td>&nbsp;</td>
    <td><a href="<?php echo  $glob['adminFile']; ?>?_g=requestPass" class="txtLink">Request Password</a> </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
	<input name="login" type="submit" id="login" value="Login" class="submit" />	</td>
  </tr>
</table>
        </form>
     
        </td>
        <td width="17">&nbsp;</td>
        <td width="205" valign="top">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center">
    <div class="footer">
    <div style="padding-top:10px; font-weight:bold;">Copyright &copy; <?php echo date("Y");?> Digital Collection Plate</div>
    </div>
    </td>
  </tr>
</table>

