<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }

permission("users","read", true);

require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");

$rowsPerPage = 25;
if (isset($_GET['activated'])) {
	

	$record['activated']	= sprintf("'%d'", $_GET['activated']);
	$where			= '`user_id` = '.$db->mySQLSafe($_GET['user_id']);
	$update			= $db->update($glob['dbprefix'].' users', $record, $where);

	$msg	= ($update) ? "<p class='infoText'>Updated successfully.</p>" : "<p class='warnText'>Update failed.</p>";

	## Rebuild the cached list
	

}elseif(isset($_GET["delete"]) && $_GET["delete"]>0)
{
	
	// instantiate db class
	$where = "user_id=".$db->mySQLSafe($_GET["delete"]);
	$delete = $db->delete($glob['dbprefix']."users", $where);
		
	if($delete == TRUE)
	{
		$msg = "<p class='infoText'>Deleted successfully.</p>";
	} 
	else 
	{
		$msg = "<p class='warnText'>Deleted successfully.</p>";
	}

} 
elseif(isset($_POST['user_id'])) 
{

	
	$record["user_level"] = $db->mySQLSafe($_POST['user_level']);
	$record["username"] = $db->mySQLSafe($_POST['username']);		
	$record["first_name"] = $db->mySQLSafe($_POST['first_name']);	
	$record["last_name"] = $db->mySQLSafe($_POST['last_name']);
	$record["email_address"] = $db->mySQLSafe($_POST['email_address']);  
	$record["organization"] = $db->mySQLSafe($_POST['organization']);  
	$record["address"] = $db->mySQLSafe($_POST['address']); 
	$record["about"] = $db->mySQLSafe($_POST['about']);
	$record["website"] = $db->mySQLSafe($_POST['website']);
	$record["phone"] = $db->mySQLSafe($_POST['phone']);
	$record["points"] = $db->mySQLSafe($_POST['points']);
	$record["fundee_amount"] = $db->mySQLSafe($_POST['fundee_amount']);
	$record["fundee_total"] = $db->mySQLSafe($_POST['fundee_total']);
	$record["fundee_payout"] = $db->mySQLSafe($_POST['fundee_payout']);
	$record["activated"] = $db->mySQLSafe($_POST['activated']);
	$record["signup_date"] = $db->mySQLSafe(gmdate("Y-m-d H:i:s"));
	$record["text_page"] = $db->mySQLSafe($_POST['FCKeditor']);
	if (!empty($_POST['Short_Link'])){
	$record["Short_Link"] = $db->mySQLSafe($_POST['Short_Link']);
	} else {
		$first_inital = substr($_POST['first_name'], 0,1); 
		$generatedlink = $first_inital.$_POST['last_name'].rand(1, 99);
		$record["Short_Link"] = $db->mySQLSafe($generatedlink);
	}
	


	if( (!empty($_POST['password']) && !empty($_POST['password_conf']) && $_POST['password']==$_POST['password_conf']) ){
		$salt = randomPass(6);
		$record["salt"] = "'".$salt."'";
		$record["password"] = $db->mySQLSafe(md5(md5($salt).md5($_POST['password']))); 
	}
	
	
	if($_POST['user_id']>0){
		
		$where = "user_id=".$db->mySQLSafe($_POST['user_id']);
		$update = $db->update($glob['dbprefix']."users", $record, $where);
		
		if($update == TRUE){
			$msg = "<p class='infoText'>User updated successfully.</p>";
		} else {
			$msg = "<p class='warnText'>Failed to update users.</p>";
		}
	
	} else {
		$record["signup_date"] = $db->mySQLSafe(time());
		
		$insert = $db->insert($glob['dbprefix']."users", $record);
		
		if($insert == TRUE) {
			$msg = "<p class='infoText'>User added successfully.</p>";
		} else {
			$msg = "<p class='warnText'>Failed to add users.</p>";
		}
	
	}

}

	if (isset($_GET['edit']) && $_GET['edit']>0) {
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."users WHERE user_id = %s", $db->mySQLSafe($_GET['edit'])); 
		
	} else if (isset($_GET['searchStr'])) {
		
		if (is_numeric($_GET['searchStr'])) {
			
			$query = "SELECT * FROM ".$glob['dbprefix']."users WHERE user_id  = ".$db->mySQLSafe($_GET['searchStr']);
		} else {
			
		
			$searchwords = preg_split ( "/[ ,]/", trim($_GET['searchStr'])); /* bug fix 1448 thanks Brivtech */
			foreach($searchwords as $word) {
				$searchArray[]=$word;
			}
		
			$noKeys = count($searchArray);
			
			$like = "";
			
			for ($i=0; $i<$noKeys;$i++) {
				
				$ucSearchTerm = strtoupper($searchArray[$i]);
				if(($ucSearchTerm!=="AND")AND($ucSearchTerm!=="OR")) {
					
					$like .= "(email_address LIKE '%".$searchArray[$i]."%'  OR  first_name LIKE '%".$searchArray[$i]."%' OR last_name LIKE '%".$searchArray[$i]."%' OR address LIKE '%".$searchArray[$i]."%' OR  phone LIKE '%".$searchArray[$i]."%' OR website LIKE '%".$searchArray[$i]."%' OR organization LIKE '%".$searchArray[$i]."%' OR  about LIKE '%".$searchArray[$i]."%' OR Short_Link LIKE '%".$searchArray[$i]."%' OR phone LIKE '%".$searchArray[$i]."%' OR  ip_address LIKE '%".$searchArray[$i]."%') OR ";
					
				} else {
					$like = substr($like,0,strlen($like)-3);
					$like .= $ucSearchTerm;
				}  
			$like = substr($like,0,strlen($like)-3);
			$whereClause .= "WHERE ".$like;
			}
			
		if (isset($_GET['user_level']) && $_GET['user_level']>0) {
			$whereClause .= (isset($like)) ? ' AND ' : ' WHERE ';
			$whereClause .= "user_level = ".$_GET['user_level'];
			}
			$query = "SELECT * FROM ".$glob['dbprefix']."users  ".$whereClause." ORDER BY user_id";
			
			
	
	}
	
	} else if ($_GET['mode']!=="new") {
		
		$query = "SELECT * FROM ".$glob['dbprefix']."users ORDER BY signup_date DESC";
	
	}
	
	// query database
	if (isset($query)) {
		$page = (is_numeric($_GET['page'])) ? $_GET['page'] : 0;
		$usersData = $db->select($query, $rowsPerPage, $page);
		$numrows = $db->numrows($query);
		$pagination = paginate($numrows, $rowsPerPage, $page, "page");
	}
	 $levels= $db->select("SELECT * FROM ".$glob['dbprefix']."levels WHERE status='1' ORDER BY `priority` ASC"); 
?>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap' class="pageTitle">Manage Users</td>
     <?php if(!isset($_GET["mode"])){ ?><td align="right" valign="middle"><a <?php if(permission("users","write")==TRUE){ ?>href="<?php echo $glob['adminFile']; ?>?_g=users/index&amp;mode=new" class="txtLink" <?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/buttons/new.gif" alt="" hspace="4" border="0" title="" />Add New</a></td><?php } ?>
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

<form name="filter" method="get" action="<?php echo $glob['adminFile']; ?>">
<input type="hidden" name="_g" value="users/index" />
 	<p align="right" class="copyText">
     
 user Level:<select name="user_level" id="user_level">
 <option value="All" <?php if(isset($_GET['user_level']) && $_GET['user_level']=="All") echo "selected='selected'"; ?>>All Users</option>
 <option value="1" <?php if($_GET['user_level']==1) echo "selected='selected'"; ?>>Donator</option>
 <option value="2" <?php if($_GET['user_level']==2) echo "selected='selected'"; ?>>Fundraiser</option>
</select>
   Search Term:
    <input type="text" name="searchStr" class="textbox" value="<?php if(isset($_GET['searchStr'])) echo $_GET['searchStr']; ?>" />    
    <input name="Submit" type="submit" class="submit" value="Filter" />
    <input name="Button" type="button" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=users/index');return document.returnValue" value="Reset" class="submit" />
	</p>
</form>

<p class="copyText">Below is a list of all the current users in the database.</p>
<p class="copyText"><?php echo $pagination; ?></p>
<table width="100%" border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td width="5%" align="left" nowrap="nowrap" class="tdTitle">User Level</td>
    <td width="5%" align="left" nowrap="nowrap" class="tdTitle">Level</td>
    <td width="5%" align="left" nowrap="nowrap" class="tdTitle">Points</td>
    <td width="18%" align="left" nowrap="nowrap" class="tdTitle">Organization</td>
    <td width="13%" align="left" nowrap="nowrap" class="tdTitle">Email</td>
    <td width="16%" align="left" nowrap="nowrap" class="tdTitle">Address</td>
    <td width="12%" align="left" nowrap="nowrap" class="tdTitle">Phone </td>
    <td width="15%" align="left" nowrap="nowrap" class="tdTitle">Reg Date / IP Address</td>
    <td width="7%" nowrap="nowrap" class="tdTitle">No Donations</td>
    <td width="5%" nowrap="nowrap" class="tdTitle">Active</td>
    <td colspan="2" align="center" valign="middle" nowrap="nowrap" class="tdTitle">Action</td>
  </tr>
<?php 
if ($usersData) { 
	for ($i=0; $i<count($usersData); $i++) {
		
	
		
		$cellColor = cellColor($i);
?>
  <tr class="<?php echo $cellColor; ?>">
    <td class="<?php echo $cellColor; ?> tdText">
     
	<?php
	if($usersData[$i]['user_level']==1){
		echo("Donator");
		}elseif($usersData[$i]['user_level']==2){
			echo("Fundraiser");
			}
 ?>
    </td>
    <td class="<?php echo $cellColor; ?> tdText">
    <?php echo getStatus($usersData[$i]["points"]); ?>
    </td>
    <td class="<?php echo $cellColor; ?> tdText"><?php echo $usersData[$i]['points']; ?></td>
    <td class="<?php echo $cellColor; ?> tdText"><?php if(!empty($usersData[$i]['organization'])) {echo $usersData[$i]['organization'];} else {echo "Organization: N/A <br /><br /> Name:<br />".$usersData[$i]['first_name']."<br />".$usersData[$i]['last_name'];} ?></td>
    <td class="<?php echo $cellColor; ?>"><a href="mailto:<?php echo $usersData[$i]['email_address']; ?>" class="txtLink"><?php echo $usersData[$i]['email_address']; ?></a></td>
    <td class="<?php echo $cellColor; ?> tdText"><?php 
	
	if(!empty($usersData[$i]['address'])) echo $usersData[$i]['address'].", "; 
	
	?>	</td>
    <td class="<?php echo $cellColor; ?> tdText"><?php echo $usersData[$i]['phone']; ?></td>
    <td nowrap='nowrap' class="<?php echo $cellColor; ?> tdText">
		<?php echo $usersData[$i]['signup_date']; ?><br />
		<a href="javascript:;" class="txtLink" onclick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=misc/lookupip&amp;ip=<?php echo $usersData[$i]['ipAddress']; ?>','misc',300,130,'yes,resizable=yes')"><?php echo $usersData[$i]['ip_address']; ?></a>	</td>
    <td align="center" class="<?php echo $cellColor; ?>">
	<?php 
$query ="SELECT count(donation_id) as noDonations FROM donations WHERE user_id='".$usersData[$i]['user_id']."'";
$noDonations= $db->select($query);

	if($noDonations[0]['noDonations']>0) { ?>
	<a href="<?php echo $glob['adminFile']; ?>?_g=donations/index&amp;user_id=<?php echo $usersData[$i]['user_id']; ?>" class="txtLink"><?php echo number_format($noDonations[0]['noDonations']); ?></a>
	<?php } else { ?>
	<span class="tdText"><?php 
	
	echo  number_format($noDonations[0]['noDonations']); ?></span>
	<?php } ?>	</td>
    <td align="center" class="<?php echo $cellColor; ?>">
    <?php
	 if($usersData[$i]['activated']==0){
		 ?>
     <a <?php if(permission('users','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=users/index&amp;activated=1&amp;user_id=<?php echo $usersData[$i]['user_id']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $usersData[$i]['activated']; ?>1.gif" alt="Show" title="Show" /></a>
     
     <?
		 }else
		 {
		?>
     <a <?php if(permission('users','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=users/index&amp;activated=0&amp;user_id=<?php echo $usersData[$i]['user_id']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $usersData[$i]['active']; ?>1.gif" alt="Hide" title="Hide" /></a>
     <?	 
		}
		
	?>
    </td>
    <td align="center" width="4%" class="<?php echo $cellColor; ?>">
	<a <?php if(permission("users","edit")==TRUE){?>href="<?php echo $glob['adminFile']; ?>?_g=users/index&amp;edit=<?php echo $usersData[$i]['user_id']; ?>" class="txtLink"<?php } else { echo $link401; } ?>>Edit</a>	</td>
    <td align="center" width="5%" class="<?php echo $cellColor; ?>">
	<a <?php if(permission("users","delete")==TRUE){?>href="<?php echo $glob['adminFile']; ?>?_g=users/index&amp;delete=<?php echo $usersData[$i]['user_id']; ?>" onclick="return confirm('<?php echo str_replace("\n", '\n', addslashes("Are you sure you want to delete this?")); ?>')" class="txtLink"<?php } else { echo $link401; } ?>>Delete</a></td>
  </tr>
<?php 
  		} // end loop  
	} 
	else 
	{ ?>
   <tr>
    <td colspan="12" class="tdText">No users exist in the database.</td>
  </tr>
<?php
  } 
?>
</table>
<p class="copyText"><?php echo $pagination; ?></p>
<?php 
} else if ($_GET["mode"]=="new" || $_GET["edit"]>0) {

?>
<form name="editUser" method="post" action="<?php echo $glob['adminFile']; ?>?_g=users/index">
<table  border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">
  <tr>
    <td colspan="2" class="tdTitle"><?php if($_GET["mode"]=="new") { echo "Please add a new users below:"; } else { echo "Please edit this users below:"; } ?></td>
    </tr>
  <tr>
    <td width="222" class="tdText"><strong>User Level:</strong></td>
    <td width="560">
      <select name="user_level" id="user_level">
        <option value="1" <?php if($usersData[0]['user_level']==1) echo "selected='selected'"; ?>>Donator</option>
        <option value="2" <?php if($usersData[0]['user_level']==2) echo "selected='selected'"; ?>>Fundraiser</option>
        </select>
      </td>
  </tr>
  <tr>
    <td class="tdText"><strong>Points:</strong></td>
    <td><input name="points" class="textbox" id="points" size="10" value="<?php echo $usersData[0]['points'];?>" tabindex="1" type="text" /></td>
  </tr>
  <tr>
    <td class="tdText"><strong>Username:</strong></td>
    <td><input name="username" type="text" id="username" value="<?php echo $usersData[0]['username']; ?>" class="textbox" required="required" /></td>
  </tr>
  <tr>
    <td width="222" class="tdText"><strong>First Name:</strong></td>
    <td width="560"><input name="first_name" type="text" id="first_name" value="<?php echo $usersData[0]['first_name']; ?>" class="textbox" required="required" /></td>
  </tr>
  <tr>
    <td width="222" class="tdText"><strong>Last Name:</strong></td>
    <td width="560"><input name="last_name" type="text" id="last_name" value="<?php echo $usersData[0]['last_name']; ?>" class="textbox" required="required" /></td>
  </tr>
  <tr>
    <td width="222" class="tdText"><strong>Email Address:</strong></td>
    <td width="560"><input name="email_address" type="text" id="email_address" value="<?php echo $usersData[0]['email_address']; ?>" class="textbox" required="required" /></td>
  </tr>
  <tr>
    <td class="tdText"><strong>Phone:</strong></td>
    <td><input name="phone" type="text" id="phone" value="<?php echo $usersData[0]['phone']; ?>" class="textbox" /></td>
  </tr>
  <tr>
    <td width="222" class="tdText"><strong>Organization:</strong></td>
    <td width="560"><input name="organization" type="text" id="organization" value="<?php echo $usersData[0]['organization']; ?>" class="textbox" /></td>
  </tr>
  <tr>
    <td width="222" class="tdText"><strong>Website:</strong></td>
    <td width="560"><input name="website" type="text" id="website" value="<?php echo $usersData[0]['website']; ?>" class="textbox"  /></td>
  </tr>
  <tr>
    <td class="tdText"><strong>Short Link: <span style="color: red;">(Important For Fundraisers Page!)</span></strong></td>
    <td>
      <input name="Short_Link" type="text" id="Short_Link" value="<?php echo $usersData[0]['Short_Link']; ?>" class="textbox"  /></td>
  </tr>
  <tr>
    <td class="tdText"><strong>Amount Raised:</strong></td>
    <td><input name="fundee_amount" type="text" id="fundee_amount" value="<?php echo $usersData[0]['fundee_amount']; ?>" class="textbox"  /></td>
  </tr>
  <tr>
    <td class="tdText"><strong>Total Amount Needed:</strong></td>
    <td><input name="fundee_total" type="text" id="fundee_total" value="<?php echo $usersData[0]['fundee_total']; ?>" class="textbox"  /></td>
  </tr>
    <tr>
    <td class="tdText"><strong>Incentive Payout:</strong></td>
    <td><input name="fundee_payout" type="text" id="fundee_payout" value="<?php echo $usersData[0]['fundee_payout']; ?>" class="textbox"  /></td>
  </tr>
  <tr>
    <td class="tdText"><strong>Address</strong></td>
    <td><textarea name="address" cols="40" rows="6" class="textbox"><?php echo $usersData[0]['address']; ?></textarea></td>
  </tr>
  <tr>
    <td width="222" align="left" valign="top" class="tdText"><strong>About:</strong></td>
    <td width="560">
      <textarea name="about" cols="40" rows="6" class="textbox"><?php echo $usersData[0]['about']; ?></textarea>
      </td>
  </tr>
  <tr>
    <td width="222" class="tdText"><strong>Text Page:</strong></td>
    <td width="560">
    <?php

	require($glob['adminFolder']."/includes".CC_DS."rte".CC_DS."fckeditor.php");
	$oFCKeditor = new FCKeditor('FCKeditor');
	$oFCKeditor->BasePath = $GLOBALS['rootRel'].$glob['adminFolder'].'/includes/rte/';
	$oFCKeditor->Value = (isset($usersData[0]['text_page'])) ? (!get_magic_quotes_gpc ()) ? stripslashes($usersData[0]['text_page']) : $usersData[0]['text_page'] : '';
	if ($config['richTextEditor'] == 0) {
		$oFCKeditor->off = true;
	}
	$oFCKeditor->Create();
?>
    </td>
  </tr>
  <tr>
    <td class="tdText"><strong>New Password:</strong><br />
      (Leave empty to keep current password)</td>
    <td><input name="password" type="password" id="password" value="" class="textbox" /> </td>
  </tr>
  <tr>
    <td class="tdText"><strong>Confirm Password:</strong></td>
    <td><input name="password_conf" type="password" id="password_conf" value="" class="textbox" /></td>
  </tr>
   <tr>
    <td align="left" valign="top" class="tdRichText"><strong>Status:</strong></td>
    <td class="tdRichText">
     Active
<input name="activated" type="radio" value="1" <?php if(isset($usersData[0]['activated']) && $usersData[0]['activated']==1) { echo "checked='checked'"; } if(isset($_GET['mode']) && $_GET['mode']=="new") { echo "checked='checked'"; } ?> />
 DeActive
<input name="activated" type="radio" value="0" <?php if(isset($usersData[0]['activated']) && $usersData[0]['activated']==0) echo "checked='checked'";?> />    </td>
  </tr>
  <tr>
    <td width="222">&nbsp;</td>
    <td width="560">
      <input type="hidden" name="user_id" value="<?php echo $usersData[0]['user_id']; ?>" />
      <input name="Submit" type="submit" class="submit" value="<?php if($_GET['mode']=='new') { echo "Add User"; } else { echo "Edit User"; } ?>" />	
      <input name="Button" type="button" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=users/index');return document.returnValue" value="Cancel" class="submit" /> 
      </td>
  </tr>
</table>
</form>
<?php 
} 
?>