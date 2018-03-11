<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }
permission('winners', 'read', true);
require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');
$rowsPerPage = 25;
if(isset($_GET["delete"]) && $_GET["delete"]>0)
{
	
	// instantiate db class
	$where = "winner_id=".$db->mySQLSafe($_GET["delete"]);
	$delete = $db->delete($glob['dbprefix']."winners", $where);
		
	if($delete == TRUE)
	{
		$msg = "<p class='infoText'>Deleted successfully.</p>";
	} 
	else 
	{
		$msg = "<p class='warnText'>Deleted successfully.</p>";
	}

} 
elseif(isset($_POST['winner_id'])) 
{

	
	$record["user_id"] = $db->mySQLSafe($_POST['user_id']);
	$record["organization"] = $db->mySQLSafe($_POST['organization']);		
	$record["date_won"] = $db->mySQLSafe($_POST['date_won']);	
	
	if (isset($_POST['winner_id']) && $_POST['winner_id']>0){
		
		$where = "winner_id=".$db->mySQLSafe($_POST['winner_id']);
		$update = $db->update($glob['dbprefix']."winners", $record, $where);
		
		if($update == TRUE){
			$msg = "<p class='infoText'>winner updated successfully.</p>";
		} else {
			$msg = "<p class='warnText'>Failed to update winners.</p>";
		}
	
	} else {
		$insert = $db->insert($glob['dbprefix']."winners", $record);
		
		if($insert == TRUE) {
			$msg = "<p class='infoText'>winner added successfully.</p>";
		} else {
			$msg = "<p class='warnText'>Failed to add winner.</p>";
		}
	
	}

}
if (!isset($_GET['mode'])) {
	## Build the SQL Query
	if (isset($_GET['edit']) && $_GET['edit']>0) {
		
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."winners WHERE winner_id = %s", $db->mySQLSafe($_GET['edit'])); 
	} else {
		
		$query = "SELECT ".$glob['dbprefix']."`first_name`, `last_name`,`username`,`phone`,`ip_address`,`signup_date`, ".$glob['dbprefix']."winners.winner_id,".$glob['dbprefix']."winners.`organization`, winners.`date_won`, ".$glob['dbprefix']."users.email_address FROM ".$glob['dbprefix']."winners INNER JOIN ".$glob['dbprefix']."users ON ".$glob['dbprefix']."winners.user_id = ".$glob['dbprefix']."users.user_id  ORDER BY `date_won` DESC";
	}
	
	// query database
	$page = (isset($_GET['page'])) ? $_GET['page'] : 0;
	
	$results = $db->select($query, $rowsPerPage, $page);
	$numrows = $db->numrows($query);
	$pagination = paginate($numrows, $rowsPerPage, $page, "page", "txtLink", 7, array('delete'));
}
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap' class="pageTitle">Manage Winners</td>
     <?php if(!isset($_GET['mode'])){ ?><td align="right" valign="middle"><a <?php if(permission('winners','write')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=winners/index" class="txtLink" <?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/buttons/new.gif" alt="" hspace="4" border="0" title="" />Add New</a></td><?php } ?>
  </tr>
</table>
<?php 
if(isset($msg))
{ 
	echo msg($msg); 
}
if(!isset($_GET['mode']) && !isset($_GET['edit']))
{
?>
<p class="copyText">Below is a list of all the current winners in the database.</p>
<p class="copyText"><?php echo $pagination; ?></p>
<table width="100%" border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td width="9%" class="tdTitle">Name</td>
    <td width="6%" class="tdTitle">Email</td>
   <td width="9%" class="tdTitle">username</td>
    <td width="11%" align="left" nowrap="nowrap" class="tdTitle">Address</td>
    <td width="7%" align="left" nowrap="nowrap" class="tdTitle">Phone </td>
	 <td width="9%" align="left" nowrap="nowrap" class="tdTitle">Organization</td>
	 <td width="12%" class="tdTitle">Date Won</td>
    <td width="15%" class="tdTitle">Reg Date / IP Address</td>
    <td colspan="2" align="center" class="tdTitle">Action</td>
  </tr>
  <?php
  if($results){

  	for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {

	$cellColor = '';
	$cellColor = cellColor($i);

  ?>
  <tr class="<?php echo $cellColor; ?>">
    <td class="<?php echo $cellColor; ?>">
	<a href="<?php echo $glob['adminFile']; ?>?_g=users/index&amp;searchStr=<?php echo urlencode($results[$i]['email_address']); ?>" class="txtLink"><?php echo $results[$i]['first_name']." ".$results[$i]['last_name']; ?></a>	</td>
    <td class="<?php echo $cellColor; ?>">
	<a href="mailto:<?php echo $results[$i]['email_address']; ?>" class="txtLink"><?php echo $results[$i]['email_address']; ?></a>	</td>
    <td class="<?php echo $cellColor; ?>"><?php echo $results[$i]['username']; ?></td>
	 <td class="<?php echo $cellColor; ?> tdText"><?php 
	if(!empty($results[$i]['organization'])) echo $results[$i]['organization'].", ";
	if(!empty($results[$i]['address'])) echo $results[$i]['address'].", "; 
	
	?>	</td>
    <td class="<?php echo $cellColor; ?> tdText"><?php echo $results[$i]['phone']; ?></td>
    <td class="<?php echo $cellColor; ?> tdText">
	<?php echo $results[$i]['organization']; ?>	</td>
    <td class="<?php echo $cellColor; ?>">
	<?php echo $results[$i]['date_won']; ?></td>
    <td class="<?php echo $cellColor; ?>">
	<?php echo $results[$i]['signup_date']; ?><br />
	  <a href="javascript:;" class="txtLink" onclick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=misc/lookupip&amp;ip=<?php echo $results[$i]['ip_address']; ?>','misc',300,130,'yes,resizable=yes')"><?php echo $results[$i]['ip_address']; ?></a>	</td>
    <td width="4%"  align="center" class="<?php echo $cellColor; ?>">
	
<a <?php if(permission('winners','edit')){ ?>href="<?php $glob['adminFile']; ?>?_g=winners/index&amp;edit=<?php echo $results[$i]['winner_id']; ?>" class="txtLink" <?php } else { echo $link401; } ?>>Edit</a>	</td>
    <td width="4%"  align="center" class="<?php echo $cellColor; ?>">
    <a <?php if(permission('winners','delete')){ ?>href="javascript:decision('Are you sure you want to delete this?','<?php echo $glob['adminFile']; ?>?_g=winners/index&amp;delete=<?php echo $results[$i]['winner_id']; ?>&winner_id=<?php echo $results[$i]['winner_id']; ?>');" class="txtLink" <?php } else { echo $link401; } ?>>Delete</a>    </td>
  </tr>
  <?php } // end loop
  } else { ?>
   <tr>
    <td colspan="11" class="tdText">There are no winners in the database.</td>
  </tr>
  <?php } ?>
</table>
<p class="copyText"><?php echo $pagination; ?></p>
<?php 
} else if ($_GET["mode"]=="new" || $_GET["edit"]>0) {

?>
<form name="editUser" method="post" action="<?php echo $glob['adminFile']; ?>?_g=winners/index">
<table  border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">
  <tr>
    <td colspan="2" class="tdTitle"><?php if($_GET["mode"]=="new") { echo "Please add a new winners below:"; } else { echo "Please edit this winners below:"; } ?></td>
    </tr>
  <tr>
    <td width="222" class="tdText"><strong>User:</strong></td>
    <td width="560">
      <select name="user_id" id="user_id">
	  <?php
	  $query = "SELECT * FROM ".$glob['dbprefix']."users WHERE user_level='2' ORDER BY signup_date DESC";
	  $usersData = $db->select($query);
	  if ($usersData) { 
	for ($i=0; $i<count($usersData); $i++) {
	  ?>
        <option value="<?php echo $usersData[$i]['user_id'];?>" <?php if($usersData[$i]['user_id']==$results[0]['user_id']) echo "selected='selected'"; ?>>
		<?php echo $usersData[$i]['first_name']." ".$usersData[$i]['last_name'];?>
		</option>
		<?
		}
		}
		?>
        </select>      
		</td>
  </tr>
  <tr>
    <td class="tdText"><strong>Organization:</strong></td>
    <td><input name="organization" class="textbox" id="organization" size="30" value="<?php echo $results[0]['organization'];?>" tabindex="1" type="text" /></td>
  </tr>
  <tr>
    <td class="tdText"><strong>Date Won:</strong></td>
    <td><input name="date_won" size="30" type="text" id="date_won" value="<?php echo $results[0]['date_won']; ?>" class="textbox" required="required" /></td>
  </tr>
  <tr>
    <td width="222">&nbsp;</td>
    <td width="560">
      <input type="hidden" name="winner_id" value="<?php echo $results[0]['winner_id']; ?>" />
      <input name="Submit" type="submit" class="submit" value="<?php if($_GET['mode']=='new') { echo "Add Winner"; } else { echo "Edit Winner"; } ?>" />	
      <input name="Button" type="button" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=winners/index');return document.returnValue" value="Cancel" class="submit" />      </td>
  </tr>
</table>
</form>
<?php 
} 
?>