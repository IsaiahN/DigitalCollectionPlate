<?php
$filename = basename($_SERVER['REQUEST_URI']);
if (($filename  == "fmoderate.html" ) && ($cc_session->ccUserData['user_level']==2)) {
?>
	<ul class="menu_fundraiser_moderate">
	<?php if (!empty($cc_session->ccUserData['Short_Link'])) { ?>
	<li id="tabHeader_5"><a href="http://www.digitalcollectionplate.com/u/<?php echo $cc_session->ccUserData['Short_Link'];?>" target="_blank">View My Fundraiser</a></li>
	<?php } ?>
	<li id="tabHeader_7">Edit My Fundraiser</li>
	<li id="tabHeader_6">Donations To Me</li>
	<li id="tabHeader_8">Account Settings</li>
	<li><a href="http://www.digitalcollectionplate.com/dashboard.html">Back To Home</a></li>
	<li id="tab_end"><a href="logout.php">Logout</a></li>
		<?
} else {
	if($cc_session->ccUserData['user_level']==2){
	?>
	<ul class="menu_fundraiser">
	<?php } else { ?>
	<ul class="menu_donator">
	<?php } ?>
	<li id="tabHeader_5">Home</li>
	<?php
	if($cc_session->ccUserData['user_level']==2){
	?>
	<li><a href="http://www.digitalcollectionplate.com/f/panel">My Fundraiser</a></li>
	<?php 
	} 
	?>
	<?php
	if($cc_session->ccUserData['user_level']==1){
	?>
	<li><a href="http://www.digitalcollectionplate.com">Browse Fundraisers</a></li>
	<?php 
	} 
	?>
	<li id="tabHeader_8" <?php if(!empty($_POST["keyword"])){echo("class=\"tabActiveHeader\""); }?>>Search Fundraisers</li>
	<li id="tabHeader_6">Donations From Me</li>
	<li id="tabHeader_7">Payouts To Me</li>
	<li id="tabHeader_9">Account Settings</li>
	<li id="tab_end"><a href="logout.php">Logout</a></li>
<?php
}
?>

</ul>