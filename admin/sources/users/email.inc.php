<?php
if (!defined('CC_INI_SET')) die("Access Denied");
permission("users", "write", true);

if (isset($_GET['action']) && $_GET['action']=="download") {
	$query = "SELECT email_address, first_name, last_name, type FROM ".$glob['dbprefix']."users";
	$results = $db->select($query);
	if ($results) {
		$emailList = "";
		for ($i=0; $i<count($results); $i++){
			if ($_POST['incName']==1 && $results[$i]['activated']==1) {
				$emailList .=$results[$i]['first_name']." ".$results[$i]['last_name']." <".$results[$i]['email_address'].">";
			} else {
				$emailList .=  $results[$i]['email_address'];
			}
		
			$emailList .=  "\r\n";
		}
		$filename="userEmails_".date("dMy").".txt";
		header('Pragma: private');
		header('Cache-control: private, must-revalidate');
		header("Content-Disposition: attachment; filename=".$filename);
		header("Content-type: text/plain");
		header("Content-type: application/octet-stream");
		header("Content-length: ".strlen($emailList));
		header("Content-Transfer-Encoding: binary");
		echo $emailList;
		exit;
	} else {
		$msg = "<p class='warnText'>There were no emails to download.</p>";
	}
	exit;
}

require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");
?>
<p class="pageTitle">Email Users</p>

<?php 
if(isset($_GET['action']) && $_GET['action']=="send")
{ 
?>
<form name="form1" method="post" action="<?php echo $glob['adminFile']; ?>?_g=users/send" target="_self" enctype="multipart/form-data">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle">Please create your email below:</td>
  </tr>
  <tr>
    <td class="tdRichText" valign='top'>
    	<strong>HTML:</strong>
    </td>
    <td class="tdRichText">
	<?php
	require($glob['adminFolder']."/includes".CC_DS."rte".CC_DS."fckeditor.php");
	
	$oFCKeditor = new FCKeditor('FCKeditor');
	$oFCKeditor->BasePath = $GLOBALS['rootRel'].$glob['adminFolder'].'/includes/rte/';
		
	if (isset($_POST['FCKeditor'])) {
		$oFCKeditor->Value = stripslashes($_POST['FCKeditor']);
	} else {
		$oFCKeditor->Value = "";
	}
	
	if (!$config['richTextEditor']) $oFCKeditor->off = true;
	$oFCKeditor->Create();
?></td>
  </tr>
  <tr>
    <td valign="top" class="tdText"><strong>Plain Text:</strong></td>
    <td><textarea name='plain_text' style='width: 100%;' rows='14'><?php echo $_POST['plain_text']; ?></textarea></td>
  </tr>
  <tr>
    <td class="tdRichText"><span class="tdText"><em><strong>Hint:</strong> </em></span></td>
    <td class="tdRichText"><span class="tdText"><em>You can click the source button above an paste in a html document you have already made.</em></span></td>
  </tr>
  <tr>
    <td valign="top" class="tdRichText"><span class="tdText"><em><strong>Important:</strong></em></span></td>
    <td class="tdRichText"><span class="tdText"><em>In most countries it is a legal obligation to provide an unsubscribe link:</em> </span>
        <input name="unsubscribe" type="text" class="textbox" value="<?php echo $GLOBALS['siteURL']."/unsubscribe.php"; ?>" size="30" />   </td>
  </tr>
  <tr>
    <td width="110" class="tdText"><strong>Email Subject:</strong>      </td>
    <td class="tdText"><input name="subject" type="text" id="subject" class="textbox" value="<?php if(isset($_POST['subject'])) echo stripslashes($_POST['subject']); ?>" /></td>
  </tr>
  <tr>
    <td class="tdText"><strong>Senders Name:</strong></td>
    <td class="tdText"><input name="fromName" type="text" class="textbox" id="fromName" value="<?php if(isset($_POST['fromName'])) echo 
	$_POST['fromName']; ?>" /></td>
  </tr>
  <tr>
    <td class="tdText"><strong>Senders Email:</strong></td>
    <td class="tdText"><input name="fromEmail" type="text" class="textbox" id="fromEmail" value="<?php if(isset($_POST['fromEmail'])) echo $_POST['fromEmail']; ?>" /></td>
  </tr>
  <!--
  <tr>
    <td class="tdText"><strong>Return Path:</strong></td>
    <td class="tdText"><input name="returnPath" type="text" class="textbox" id="returnPath" value="<?php if(isset($_POST['returnPath'])) echo $_POST['returnPath']; ?>" /> 
      (The return path bounced emails go to.)</td>
  </tr>
  -->
  <tr>
    <td class="tdText"><strong>Email Format:</strong></td>
    <td class="tdText">Use Customers Preference:  
      <input name="format" type="radio" value="user" <?php if(!isset($_POST['format']) || $_POST['format']=="user") { echo 'checked="checked"'; } ?>  /> 
      HTML:
      <input name="format" type="radio" value="html" <?php if($_POST['format']=="html") { echo 'checked="checked"'; } ?> />
     Plain Text:
      <input name="format" type="radio" value="text" <?php if($_POST['format']=="text") { echo 'checked="checked"'; } ?> /></td>
  </tr>
  <tr>
    <td width="110" class="tdText"><strong>Send Test Email?</strong></td>
    <td class="tdText">
	Yes     
      <input name="test" type="radio" value="1" <?php if(isset($_POST['test']) && $_POST['test']=="1") echo "checked='checked'"; elseif(!isset($_POST['test'])) echo "checked='checked'"; ?> /> 
	No 
      <input name="test" type="radio" value="0" <?php if(isset($_POST['test']) && $_POST['test'] =="0") echo "checked='checked'"; ?> /> 
      <strong>Test Email Recipient:</strong>
	  <input name="testEmail" type="text" id="testEmail" value="<?php if(isset($_POST['testEmail'])) echo $_POST['testEmail']; else echo $ccAdminData['email']; ?>" /></td>
  </tr>
  <tr>
    <td class="tdText">&nbsp;</td>
    <td class="tdText"><input type="submit" class="submit" value="Send Email" /></td>
  </tr>
</table>
</form>
<?php 
} else {  
	if (isset($msg)) echo msg($msg);
?>
<p class="copyText">Please choose whether to download users email addresses or to send a bulk email through this website.</p>
<table width="450" border="0" align="center" cellpadding="3" cellspacing="1" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle">Please choose:</td>
  </tr>
  <tr>
    <td width="50%" valign="top" class="copyText">This is used to download email address to be used in bulk email software.</td>
    <td width="50%" valign="top" class="copyText">This allows you to send a bulk email ONLY to those who have subscribed to the mailing list through this website.</td>
  </tr>
  <tr align="center">
    <td valign="bottom" class="copyText">
    <form name="download" method="post" action="<?php echo $glob['adminFile']; ?>?_g=users/email&amp;action=download">
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td>Include Name?</td>
          <td>
		  Yes
            <input name="incName" type="radio" value="1" checked='checked' />
		  No
<input name="incName" type="radio" value="0" /></td>
        </tr>
        <tr align="center">
          <td height="30" colspan="2"><input name="download" type="submit" class="submit" id="download" value="Download Email" /></td>
          </tr>
      </table>
    </form></td>
    <td valign="bottom" class="copyText">
	<form name="download" method="post" action="<?php echo $glob['adminFile']; ?>?_g=users/email&amp;action=send" enctype="multipart/form-data">
	  <input name="send" type="submit" class="submit" id="send" value="Send Email" />
	</form>
	</td>
  </tr>
</table>
<?php 
} 
?>