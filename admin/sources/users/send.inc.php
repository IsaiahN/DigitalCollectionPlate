<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }

permission("users","write", true);

require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");

// number of email_address recipients per page
$perPage = 20;

if($_POST['test']==0){
	$query = "SELECT email_address, first_name, last_name FROM ".$glob['dbprefix']."users";
	$email_addressList = $db->select($query, $perPage, $_GET['page']);
}
?>

<div id="sending"  class="pageTitle">
Sending Email <img src="<?php echo $glob['adminFolder']; ?>/images/progress.gif" alt="" width="32" height="32" title="" /></div>
<div id="sent" class="pageTitle" style="visibility:hidden;">Sending Complete</div>
<?php
// start email_address

require "classes".CC_DS."htmlMimeMail".CC_DS."htmlMimeMail.php";
		
$html		= stripslashes($_POST['FCKeditor']);
$subject	= stripslashes($_POST['subject']);
$fromName	= $_POST['fromName'];
$fromEmail	= $_POST['fromEmail'];
//$returnPath = $_POST['returnPath'];

$text		= $_POST['plain_text'];
$find		= array("'".$GLOBALS['rootRel'],"\"".$GLOBALS['rootRel']);
$replace	= array("'".$glob['siteURL']."/","\"".$glob['siteURL']."/");
$html 		= str_replace($find,$replace,$html);

if ($_POST['test']==1) {

	if ($_POST['format']=="user" || $_POST['format']=="html") {
	
		$mail = new htmlMimeMail();
		$mail->setSubject($subject." "."(Sample HTML Email)");	## ???
		$mail->setHeader('X-Mailer', 'croatianholidays Bulk Mailer');
		$mail->setFrom($fromName." <".$fromEmail.">");
		$mail->setReturnPath($fromName." <".$fromEmail.">");
	#	$mail->setText($text);
		$mail->setHtml($html);
		
		$result = $mail->send(array($_POST['testEmail']), $config['mailMethod']);
	}
	
	if ($_POST['format']=="user" || $_POST['format']=="text") {
	
		$mail = new htmlMimeMail();
		$mail->setSubject($subject." "."(Sample Text Email)");
		$mail->setHeader('X-Mailer', 'digitalcollectionplate.com');
		$mail->setFrom($fromName." <".$fromEmail.">");
		$mail->setReturnPath($_POST['testEmail']);
		$mail->setText($text);
		$result = $mail->send(array($_POST['testEmail']), $config['mailMethod']);
	}
			
	echo "<p class='copyText'><strong>Recipient:</strong> ".$_POST['testEmail']."</p>";
	
	?>
	<img src="<?php echo $glob['adminFolder']; ?>/images/progress.gif" alt="" width="1" height="1" title="" onload="showHideLayers('sending','','hide','sent','','show');" />
	<form method="post" action="<?php echo $glob['adminFile']; ?>?_g=users/email_address&amp;action=send" enctype="multipart/form-data">
	<?php
	// recover post vars
	echo recoverPostVars($_POST,"FCKeditor");
	?>
	<input name="submit" type="submit" class="submit" id="submit" value="Previous Page" />
	</form>
	<?php
} else {

	$i = 0;
	if (isset($_GET['startTime'])) $startTime = $_GET['startTime']; else $startTime = $_GET['startTime'] = time();
	if ($email_addressList == TRUE) {
		echo "<table border='0' cellspacing='0' cellpadding='3' class='mainTable'>";
		print "<tr><td class='tdTitle' colspan='3'>Page: ".($_GET['page']+1)."</td></tr>";
		
		for ($i=0; $i<count($email_addressList); $i++) {
					
			$cellColor = "";
			$cellColor = cellColor($i);
			
			$mail = new htmlMimeMail();
			$mail->setSubject($subject);
			$mail->setHeader('X-Mailer', 'digitalcollectionplate.com');
			$mail->setFrom($fromName." <".$fromEmail.">");
			$mail->setReturnPath($fromEmail);
			
			if(($email_addressList[$i]['htmlEmail']==1 && $_POST['format']=="user") || $_POST['format']=="html")  {
				$mail->setHtml($html);
			} else {
				$mail->setText($text);
			}
			$result = $mail->send(array($email_addressList[$i]['email_address']), $config['mailMethod']);
			
			$recipNo = $_GET['email_address']+($i+1);
			
			echo "<tr><td class='".$cellColor."'><span class='copyText'>".($recipNo).".</span></td><td class='".$cellColor."'><span class='copyText'>".$email_addressList[$i]['first_name']." ".$email_addressList[$i]['last_name']."</span></td><td class='".$cellColor."'><span class='copyText'> &lt;".$email_addressList[$i]['email_address']."&gt;</span></td></tr>\r\n";
			flush();
		}
		
		echo "</table>"; 
		?>
		<form method="post" name="autoSubmitForm" action="<?php echo $glob['adminFile']; ?>?_g=/users/send&amp;page=<?php echo $_GET['page']+1; ?>&amp;startTime=<?php echo $startTime; ?>&amp;email_address=<?php echo $recipNo;?>" enctype="multipart/form-data">
		<?php
		echo recoverPostVars($_POST,"FCKeditor");
		?>
		<img src="<?php echo $glob['adminFolder']; ?>/images/px.gif" alt="" width="1" height="1" title="" onload="submitDoc('autoSubmitForm');" />
		</form>
		<?php
		} else {
		?>
		<p class="infoText">The bulk email_addresser has completed its task successfully.</p>
	
		<img src="<?php echo $glob['adminFolder']; ?>/images/px.gif" alt="" width="1" height="1" title="" onload="showHideLayers('sending','','hide','sent','','show');" />
<?php
	} // else

?>
<p class="copyText">
	<strong>Time taken:</strong> <?php echo readableSeconds(time() - $startTime); ?><br/>
	<strong>Recipients:</strong> <?php echo $_GET['email_address'] + $i; ?>
</p>
<?php
}
?>