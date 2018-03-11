<?php
if (!defined('CC_INI_SET')) die("Access Denied");
permission('administrators', 'read', true);

$rowsPerPage = 25;

if (isset($_GET["delete"]) && $_GET["delete"]>0){

	$where	= 'adminId='.$db->mySQLSafe($_GET['delete']);
	$delete	= $db->delete($glob['dbprefix'].'admin_users', $where);
	$deletePerms = $db->delete($glob['dbprefix'].'admin_permissions', $where);
		
	if ($delete) {
		$msg = '<p class="infoText">Deleted successfully.</p>';
	} else {
		$msg = '<p class="warnText">Delete failed.</p>';
	}	

} elseif (isset($_POST['adminId'])) {

	$record["name"] = $db->mySQLSafe($_POST['name']);		
	$record["username"] = $db->mySQLSafe($_POST['adminUsername']);	
	
	if(!empty($_POST['adminPassword']) && ($_POST['adminPassword'] == $_POST['adminPassword_verify'])){
		$salt = randomPass(6);
		$record["salt"] = $db->mySQLSafe($salt);
		$record["password"] = $db->mySQLSafe(md5(md5($salt).md5($_POST['adminPassword'])));
	}
	
	$record["notes"] = $db->mySQLSafe($_POST['notes']);
	$record["email"] = $db->mySQLSafe($_POST['email']);
	$record["isSuper"] = $db->mySQLSafe($_POST['isSuper']);  
	
	if(!empty($_POST['adminPassword']) && ($_POST['adminPassword'] !== $_POST['adminPassword_verify'])){
		$msg = "<p class='warnText'>The passwords entered do not match. User has not been updated.</p>";
	} else {
		
		if($_POST['adminId']>0) {
			$where = "adminId=".$db->mySQLSafe($_POST['adminId']);
			$update = $db->update($glob['dbprefix']."admin_users", $record, $where);
			unset($record, $where);
	
			if($update == true){
				 $msg = "<p class='infoText'>'".$_POST['name']."' updated successfully.</p>";
			} else {
				$msg = "<p class='warnText'>Failed to update.</p>";
			}
			
		} else {
			$insert = $db->insert($glob['dbprefix']."admin_users", $record);
			unset($record);
	
			if($insert == true) {
				$msg = "<p class='infoText'>'".$_POST['name']."' added successfully.</p>";
			} else {
				$msg = "<p class='warnText'>Failed to add user.</p>";
			}
		}
		
	}
}

if(!isset($_GET['mode'])){

	// make sql query
	if(isset($_GET['edit']) && $_GET['edit']>0){
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."admin_users WHERE adminId = %s", $db->mySQLSafe($_GET['edit'])); 
	} else {
	
		$query = "SELECT * FROM ".$glob['dbprefix']."admin_users ORDER BY isSuper DESC";
	} 
	
	if(isset($_GET['page'])){
	
		$page = $_GET['page'];
	
	} else {
		
		$page = 0;
	
	}
	
	// query database
	$results = $db->select($query, $rowsPerPage, $page);
	$numrows = $db->numrows($query);
	$pagination = paginate($numrows, $rowsPerPage, $page, "page");
}

require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");
?>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle">Administrators</p></td>
     <?php if(!isset($_GET["mode"]) && permission("administrators","write")==TRUE){ ?><td align="right" valign="middle"><a href="<?php echo $glob['adminFile']; ?>?_g=adminusers/administrators&amp;mode=new" class="txtLink"><img src="<?php echo $glob['adminFolder']; ?>/images/buttons/new.gif" alt="" hspace="4" border="0" title="" />Add New</a></td><?php } ?>
  </tr>
</table>
<?php 
if(isset($msg))
{ 
	echo msg($msg); 
}

if(!isset($_GET["mode"]) && !isset($_GET['edit'])){
?> 
<p class="copyText">Below is a list of all the current admin administrators in the database.</p>
<p class="copyText"><?php echo $pagination; ?></p>
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
  	<td class="tdTitle">Id</td>
    <td class="tdTitle">Username / Notes</td>
	<td align="center" class="tdTitle">No Logins</td>
    <td align="center" class="tdTitle">Super User?</td>
	<td align="center" class="tdTitle">Email</td>
	<td align="center" class="tdTitle">Action</td>
  </tr>
<?php
for($i=0; $i<count($results); $i++) {

	$cellColor = "";
	$cellColor = cellColor($i);
?>
  <tr class="<?php echo $cellColor; ?>">
  	<td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['adminId']; ?>.</span></td>
    <td class="<?php echo $cellColor; ?>"><span class="copyText"><strong><?php echo $results[$i]['username']; ?></strong><?php if(!empty($results[$i]['notes'])) { echo " - ".$results[$i]['notes']; } ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['noLogins']; ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>"><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $results[$i]['isSuper']; ?>.gif" alt="" title="" /></td>
	    <td align="center" class="<?php echo $cellColor; ?>"><a href="mailto:<?php echo $results[$i]['ipAddress']; ?>" class="txtLink"><?php echo $results[$i]['email']; ?></a></td>
	    <td align="center" class="<?php echo $cellColor; ?>">
		<?php if(permission("administrators","edit")==TRUE){ ?>	
		<a href="<?php echo $glob['adminFile']; ?>?_g=adminusers/administrators&amp;edit=<?php echo $results[$i]['adminId']; ?>" class="txtLink">Edit</a> /  
		<?php }  if(permission("administrators","delete")==TRUE) { ?>	      
		<a href="<?php echo $glob['adminFile']; ?>?_g=adminusers/administrators&amp;delete=<?php echo $results[$i]['adminId']; ?>" onclick="return confirm('<?php echo str_replace("\n", '\n', addslashes("Are you sure you want to delete this?")); ?>')" class="txtLink">Delete</a> 	            
		<?php } if(permission("administrators","edit")==TRUE && $results[$i]['isSuper']==0) { ?>	      
		 / <a href="<?php echo $glob['adminFile']; ?>?_g=adminusers/permissions&amp;adminId=<?php echo $results[$i]['adminId']; ?>" class="txtLink">Permissions</a> <?php } ?></td>
  </tr>
<?php } ?>

</table>
<p class="copyText"><?php echo $pagination; ?></p>


<?php 
} elseif($_GET["mode"]=="new" || $_GET["edit"]>0){  

if(isset($_GET["edit"]) && $_GET["edit"]>0){ $modeTxt = 'Edit'; } else { $modeTxt = 'Add'; } 
?>
<p class="copyText">You can use the form below to add an administrator.</p>
<form action="<?php echo $glob['adminFile']; ?>?_g=adminusers/administrators" method="post" enctype="multipart/form-data" name="form1">
<table border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle"><?php if(isset($_GET["edit"]) && $_GET["edit"]>0){ echo $modeTxt; } else { echo $modeTxt;  }  echo '  Administrator'; ?></td>
  </tr>
  <tr>
    <td width="25%" class="tdText"><strong>Full Name:</strong></td>
    <td>
      <input name="name" type="text" class="textbox" value="<?php if(isset($results[0]['name'])) echo $results[0]['name']; ?>" maxlength="255" />
    </td>
  </tr>
  <tr>
    <td width="25%" class="tdText"><strong>Username:</strong><br />
</td>
    <td><input name="adminUsername" type="text" class="textbox" value="<?php if(isset($results[0]['username'])) echo $results[0]['username']; ?>" maxlength="255" /></td>
  </tr>
  <tr>
    <td width="25%" class="tdText"><strong>Email:</strong></td>
    <td><input name="email" value="<?php if(isset($results[0]['email'])) echo $results[0]['email']; ?>" type="text" class="textbox" /></td>
  </tr>
  <tr>
    <td class="tdText"><strong>Password:</strong><br />
      (Only enter a password if you want to change the current one.)</td>
    <td class="tdText"><input type="password" name="adminPassword" class="textbox" /></td>
  </tr>
  <tr>
    <td class="tdText"><strong>Confirm Password:</strong></td>
    <td class="tdText"><input type="password" name="adminPassword_verify" class="textbox" /></td>
  </tr>
  <tr>
    <td class="tdText">Make Super User?</td>
    <td class="tdText">
Yes
<input name="isSuper" type="radio" value="1" <?php if(isset($results[0]['isSuper']) && $results[0]['isSuper']==1) { echo "checked='checked'"; } ?> />
No
<input name="isSuper" type="radio" value="0" <?php if(isset($results[0]['isSuper']) && $results[0]['isSuper']==0) echo "checked='checked'";  if(isset($_GET['mode']) && $_GET['mode']=="new") { echo "checked='checked'"; } ?> /></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="tdText"><strong>Notes:</strong></td>
    <td><textarea name="notes" class="textbox" cols="60" rows="3" id="notes"><?php if(isset($results[0]['notes'])) echo $results[0]['notes']; ?></textarea></td>
  </tr>
  <tr>
    <td width="25%">&nbsp;</td>
    <td>
	<input type="hidden" name="adminId" value="<?php  if(isset($results[0]['adminId'])) echo $results[0]['adminId']; ?>" />
	<input name="Submit" type="submit" class="submit" value="<?php if(isset($_GET["edit"]) && $_GET["edit"]>0){ echo $modeTxt; } else { echo $modeTxt;  } ?> User" />
        <input name="Button" type="button" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=adminusers/administrators');return document.returnValue" value="Cancel" class="submit" /> 
    </td>
  </tr>
</table>
</form>
<?php 
} 
?>