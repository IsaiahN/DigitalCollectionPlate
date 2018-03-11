<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }
require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'currencyVars.inc.php');
permission('donations', 'read', true);

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

// delete document
if(isset($_GET['delete']) && $_GET['delete']>0) {

	
	$record['noDonations'] = 'noDonations - 1';
	$where = '`user_id` = '.$_GET['users'];
	$update = $db->update($glob['dbprefix'].'users', $record, $where);

	$where = '`donation_id` = '.$db->mySQLSafe($_GET['delete']);

	$delete = $db->delete($glob['dbprefix'].'donations', $where);

	if ($delete) {
		$msg = "<p class='infoText'>Deleted successfully.</p>";
	} else {
		$msg = "<p class='infoText'>Delete failed.</p>";
	}

	$delete = $db->delete($glob['dbprefix'].'donations', $where);
	//$delete = $db->delete($glob['dbprefix'].'transactions', '`order_id` = '.$db->mySQLSafe($_GET['delete']));

}


$sqlQuery = '';

if(isset($_GET['status'])){
	$sqlQuery = 'WHERE '.$glob['dbprefix'].'donations.status = '.$db->mySQLsafe($_GET['status']);
} elseif(isset($_GET['oid'])) {
	if(empty($_GET['oid'])) {
	 	# Show all
		$sqlQuery = '';
	} else {
		$sqlQuery = 'WHERE donation_id= '.$db->mySQLsafe($_GET['oid']);
	}
} elseif(isset($_GET['user_id']) && $_GET['user_id']>0 && !isset($_GET['delete'])) {
	$sqlQuery = 'WHERE '.$glob['dbprefix'].'users.user_id = '.$db->mySQLsafe($_GET['user_id']);
}


// query database
if(isset($_GET['page'])){
	$page = $_GET['page'];
} else {
	$page = 0;
}

$donationPerPage = 25;

$query = "SELECT ".$glob['dbprefix']."users.user_id, `donation_id`, `first_name`, `last_name`, ".$glob['dbprefix']."donations.ip_address,".$glob['dbprefix']."donations.status,".$glob['dbprefix']."donations.`donation_date`, `donation_amount`, ".$glob['dbprefix']."users.email_address FROM ".$glob['dbprefix']."donations INNER JOIN ".$glob['dbprefix']."users ON ".$glob['dbprefix']."donations.user_id = ".$glob['dbprefix']."users.user_id ".$sqlQuery." ORDER BY `donation_id` DESC";

$results = $db->select($query, $donationPerPage, $page);
$numrows = $db->numrows($query);
$exclude		= array('delete' => 1);
$pagination = paginate($numrows, $donationPerPage, $page, "page", 'txtLink', 10, $exclude);
$status= array(
'orderState_1' => "Pending",
'orderState_2' => "Processing",
'orderState_3' => "Complete",
'orderState_4' => "Declined",
'orderState_5' => "Failed Fraud Review",
'orderState_6' => "Cancelled"
);
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap' class="pageTitle">Donations</td>
     <?php if(!isset($_GET['mode'])){ ?><td align="right" valign="middle"><a <?php if(permission('donations','write')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=donations/donation" class="txtLink" <?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/buttons/new.gif" alt="" hspace="4" border="0" title="" />Add New</a></td><?php } ?>
  </tr>
</table>
<?php
if(isset($msg))
{
	echo msg($msg);
}
?>
<p class="copyText">Below are all donations stored in the database.</p>
<p style="text-align:right" class="copyText">Filter:
<select name="status" class="dropDown" onchange="jumpMenu('parent',this,0)">

		<option value="<?php echo $glob['adminFile']; ?>?_g=donations/index">-- All --</option>
		<?php
		for($i=1; $i<=6; ++$i)
		{
		?>
		<option value="<?php echo $glob['adminFile']; ?>?_g=donations/index&amp;status=<?php echo $i; ?>" <?php if($_GET['status']==$i) { echo 'selected="selected"'; } ?>><?php echo $status['orderState_'.$i]; ?></option>
		<?php
		}
		?>

</select>
</p>
<p class="copyText"><?php echo $pagination; ?></p>
<table border="0" width="100%" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td width="9%" class="tdTitle">Donation No</td>
    <td width="9%" class="tdTitle">Status</td>
    <td width="16%" class="tdTitle">Date/Time</td>
    <td width="22%" class="tdTitle">User</td>
    <td width="15%" class="tdTitle">IP Address</td>
    <td width="12%" class="tdTitle">Donation Amount</td>
    <td colspan="2" align="center" class="tdTitle">Action</td>
  </tr>
  <?php
  if($results){

  	for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {

	$cellColor = '';
	$cellColor = cellColor($i);

  ?>
  <tr class="<?php echo $cellColor; ?>">
    <td class="<?php echo $cellColor; ?>"><a href="<?php echo $glob['adminFile']; ?>?_g=donations/donation&amp;edit=<?php echo $results[$i]['donation_id']; ?>" class="txtLink"><?php echo $results[$i]['donation_id']; ?></a></td>
    <td class="<?php echo $cellColor; ?>"><span class="copyText"><?php
	echo $status['orderState_'.$results[$i]['status']];
	?></span></td>
    <td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['donation_date']; ?></span></td>
    <td class="<?php echo $cellColor; ?>"><a href="<?php echo $glob['adminFile']; ?>?_g=users/index&amp;searchStr=<?php echo urlencode($results[$i]['email_address']); ?>" class="txtLink"><?php echo $results[$i]['title']." ".$results[$i]['first_name']." ".$results[$i]['last_name']; ?></a></td>
    <td class="<?php echo $cellColor; ?>"><a href="javascript:;" class="txtLink" onclick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=misc/lookupip&amp;ip=<?php echo $results[$i]['ip_address']; ?>','misc',300,130,'yes,resizable=yes')"><?php echo $results[$i]['ip_address']; ?></a></td>
    <td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo priceFormat($results[$i]['donation_amount'],true); ?></span></td>

    <td width="7%" align="center" class="<?php echo $cellColor; ?>">
	
<a <?php if(permission('donations','edit')){ ?>href="<?php $glob['adminFile']; ?>?_g=donations/donation&amp;edit=<?php echo $results[$i]['donation_id']; ?>" class="txtLink" <?php } else { echo $link401; } ?>>Edit</a>


	</td>
    <td width="10%" align="center" class="<?php echo $cellColor; ?>">
    <a <?php if(permission('donations','delete')){ ?>href="javascript:decision('Are you sure you want to delete this?','<?php echo $glob['adminFile']; ?>?_g=donations/index&amp;delete=<?php echo $results[$i]['donation_id']; ?>&users=<?php echo $results[$i]['user_id']; ?>');" class="txtLink" <?php } else { echo $link401; } ?>>Delete</a>
    </td>
  </tr>
  <?php } // end loop
  } else { ?>
   <tr>
    <td colspan="8" class="tdText">There are no donations in the database.</td>
  </tr>
  <?php } ?>
</table>
<p class="copyText"><?php echo $pagination; ?></p>