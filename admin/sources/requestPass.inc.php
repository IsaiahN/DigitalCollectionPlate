<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }

if (isset($_POST['email']))
{
	
	$query = sprintf("SELECT adminId, username, name FROM ".$glob['dbprefix']."admin_users WHERE email = %s", $db->mySQLSafe($_POST['email']));
 
	$result = $db->select($query);
	
	
	if($result == TRUE) 
	{
	
		$salt = randomPass(6);
		$newPass = randomPass();
		$data["salt"] = $db->mySQLSafe($salt);
		$data["password"]= $db->mySQLSafe(md5(md5($salt).md5($newPass)));
		$update = $db->update($glob['dbprefix']."admin_users",$data,"adminId=".$result[0]['adminId']);
		
		// make email
		require("classes".CC_DS."htmlMimeMail".CC_DS."htmlMimeMail.php");
		
		$mail = new htmlMimeMail();
        
		require("includes/email.inc.php");
		
			$macroArray = array(
		
				"RECIP_NAME" => $result[0]['name'],
				"USERNAME" => $result[0]['username'],
				"PASSWORD" => $newPass,
				"SITE_URL" => $GLOBALS['storeURL'],
				"SENDER_IP" => get_ip_address()
		
			);
		
		$text = macroSub($lang['email']['admin_reset_pass_body'],$macroArray);
		unset($macroArray);
		
		$mail->setText($text);
		$mail->setReturnPath($_POST['email']);
		$mail->setFrom('fcm-groups.com <'.$config['masterEmail'].'>');
		$mail->setSubject($lang['email']['admin_reset_pass_subject']);
		$mail->setHeader('X-Mailer', 'fcm-groups.com');
		$result = $mail->send(array($_POST['email']), $config['mailMethod']);
		
		httpredir($glob['adminFile']."?_g=login&email=".urlencode($_POST['email']));
		
	} 
	else 
	{
		$msg = "<p class='warnText'>Password reset failed.</p>";
	}

}
 require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php"); 
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
        <td height="20" colspan="5" bgcolor="#ffffff">
       <?
        if(isset($msg))
		{ 
			echo msg($msg); 
		}


?>
        </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="20">&nbsp;</td>
  </tr>
  <tr>
    <td><table width="765" height="475" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="168" align="center" valign="top">&nbsp;</td>
        <td width="18">&nbsp;</td>
        <td width="357" height="300" align="left" valign="top"><form action="<?php echo $GLOBALS['rootRel']; ?><?php echo $glob['adminFile']; ?>?_g=requestPass" method="post" enctype="multipart/form-data" name="login" target="_self">
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <table border="0" align="center" width="284" cellpadding="3" cellspacing="1" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle">Please enter your email address below:</td>
    </tr>
  <tr>
    <td class="tdText">Email Address:</td>
    <td><input name="email" type="text" id="email" class="textbox" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input name="login" type="submit" class="submit" id="login" value="Send Password" /></td>
  </tr>
</table>
</form></td>
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

