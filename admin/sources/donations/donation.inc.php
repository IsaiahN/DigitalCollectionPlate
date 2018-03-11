<?php
if (!defined('CC_INI_SET')) die("Access Denied");
require $glob['adminFolder'].CC_DS.'includes'.CC_DS.'currencyVars.inc.php';
permission('donations', 'write', true);
$users = $db->mySQLSafe((string)$_POST['users']);
$sql = 'SELECT DISTINCT * FROM `'.$glob['dbprefix'].'users` WHERE (`email_address` RLIKE ".$users." OR `last_name` RLIKE ".$users." OR `first_name` RLIKE ".$users." OR `user_id` RLIKE ".$users.") AND `type` > 0 ORDER BY `last_name`, `first_name` ASC';
$users = $db->select($sql);
if (isset($_POST['donation_id']) &&  $_POST['user_id']>0) {

	// donation SUMMARY
	$donation['donation_id'] 	= $db->mySQLSafe($_POST['donation_id']);
	$donation['user_id'] 	= $db->mySQLSafe($_POST['user_id']);
	$donation['donation_amount'] 		= $db->mySQLSafe($_POST['donation_amount']);
	$donation['status'] 		= $db->mySQLSafe($_POST['status']);
	
	if (isset($_GET['edit'])) {

		$where = '`donation_id` = '.$db->mySQLSafe($_GET['edit']);
		$update = $db->update($glob['dbprefix'].'donations', $donation, $where);

		if ($update) {
			$msg .= "<p class='infoText'>".sprintf("'%1\$s' updated successfully.",$_GET['edit'])."</p>";
		}

	} else {
		$donation['ip'] = $db->mySQLSafe(get_ip_address());
		$donation['time'] = $db->mySQLSafe(time());
		$insert = $db->insert($glob['dbprefix']."donations", $donation);
		if ($insert) {
			$msg .= "<p class='infoText'>".sprintf("'%1\$s' added successfully.",$_POST['donation_id'])."</p>";
			// send email confirmation
			$donation->newOrderEmail($_POST['donation_id']);
		} else {
			$msg .= "<p class='warnText'>".sprintf("'%1\$s' was not updated.",$_POST['donation_id'])."</p>";
		}
	}
	


	if ($_POST['donation_id']!=$_GET['edit']) {
		httpredir($glob['adminFile'].'?_g=donations/donation&edit='.$_POST['donation_id']);
	}
}

if (isset($_GET['edit'])) {
	$donationSum = $db->select('SELECT * FROM '.$glob['dbprefix'].'donations WHERE `donation_id` = '.$db->mySQLSafe($_GET['edit']));
	
}

$sql = 'SELECT * FROM '.$glob['dbprefix'].'users WHERE `activated` > 0';
$noCustomers = $db->numrows($sql);

if($noCustomers<500) {
	$users = $db->select($sql.' ORDER BY `last_name`, `first_name` ASC');
}
require_once($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');
$status= array(
'donationState_1' => "Pending",
'donationState_2' => "Processing",
'donationState_3' => "Complete",
'donationState_4' => "Declined",
'donationState_5' => "Failed Fraud Review",
'donationState_6' => "Cancelled",
);
?>

<p class="pageTitle">
  <?php if(isset($_GET['edit'])) { echo 'Edit'; } else { echo 'Add'; } ?>
 Donation</p>
<?php if (isset($msg)) echo $msg; ?>
<form action="<?php echo $glob['adminFile']; ?>?_g=donations/donation<?php if(isset($_GET['edit'])) { echo "&amp;edit=".$_GET['edit']; } ?>" method="post" enctype="multipart/form-data" name="donation" target="_self">
  <table cellspacing="1" cellpadding="3" class="mainTable" width="100%">
    <tr>
      <td colspan="2" class="tdTitle">Donation Summary</td>
    </tr>
    <tr>
      <td class="tdText"><strong>User:</strong></td>
      <td class="tdText">
        <?php
      if ($users) {
      ?>
      <select name="user_id" id="user_select" onchange="populate();">
		<?php if($donationSum) { ?>
		<option value="0" <?php if(!$_POST['user_id'] && !$donationSum) { echo 'selected="selected"'; } ?>>-- N/A --</option>
		<?php
		}
foreach ($users as $users) {
			
		?>
		<option value="<?php echo $users['user_id'];?>"
		<?php if($users['user_id']==$_POST['user_id'] || $users['user_id']==$donationSum[0]['user_id']){ echo 'selected="selected"'; } ?>
		onmouseover="findObj('name').value='<?php echo addslashes($users['title'].' '.html_entity_decode($users['first_name'].' '.$users['last_name'], ENT_QUOTES));?>';findObj('organization').value='<?php echo addslashes(html_entity_decode($users['organization'], ENT_QUOTES));?>';findObj('address').value='<?php echo addslashes(html_entity_decode($users['address'], ENT_QUOTES));?>';findObj('add_2').value='<?php echo addslashes(html_entity_decode($users['add_2'], ENT_QUOTES));?>';findObj('town').value='<?php echo addslashes(html_entity_decode($users['town'], ENT_QUOTES));?>';findObj('country').value='<?php echo $countriesArray[$users['country']];?>';findObj('postcode').value='<?php echo $users['postcode'];?>';findObj('county').value='<?php echo $users['county'];?>';findObj('phone').value='<?php echo $users['phone'];?>';findObj('mobile').value='<?php echo $users['mobile'];?>';findObj('email').value='<?php echo $users['email'];?>';"
		> <?php echo $users['last_name'];?>, <?php echo $users['first_name'];?> (<?php echo $users['user_id'];?>)</option>
		<?php
			}
		?>
        </select>
      </td>
    </tr>
    <tr>
      <td width="14%" class="tdText"><strong>Donation Number:</strong></td>
      <td width="50%" class="tdText"><input name="donation_id" type="text" class="textbox" value="<?php if(isset($donationSum[0]['donation_id'])) { echo $donationSum[0]['donation_id'].'" readonly="readonly'; } else { echo $order->mkOrderNo(); } ?>" size="21" /></td>
    </tr>
    <tr>
      <td class="tdText"><strong>Donation Amount:</strong></td>
      <td class="tdText"><input name="donation_amount" id="donation_amount" type="text" class="textbox"  value="<?php echo $donationSum[0]['donation_amount']; ?>" size="21" /></td>
    </tr>
    <tr>
      <td class="tdText"><strong>Status</strong></td>
      <td class="tdText"><select name="status" class="dropDown">
        <?php
		for ($i=1; $i<=6; ++$i) {
		?>
        <option value="<?php echo $i; ?>" <?php if($donationSum[0]['status']==$i) { echo 'selected="selected"'; } ?>><?php echo $status['donationState_'.$i]; ?></option>
        <?php
		}
		?>
      </select></td>
    </tr>
    <tr>
      <td class="tdText">&nbsp;</td>
      <td class="tdText"><input type="submit" name="submit" value="<?php if(isset($_GET['edit'])) { echo 'Edit'; } else { echo 'Add'; } ?> <?php echo 'Donation';?>"  class="submit" />
      <input name="Button" type="button" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=donations/index');return document.returnValue" value="Back" class="submit" /></td>
    </tr>
  </table>
  <br />
 </table>
 </form>
 <?
	  }
 ?>