<?php
if ($_GET['act'] == "invalidfundee") { ?>
<div id="notice" class="error"><span>&#x2716;</span> Error: The Fundraiser Page Selected Is Invalid!</div>
<?php } if ((!empty($_POST)) && (!isset($_POST["keyword"]))) { ?>
<div id="notice" class="update"><span>&#x2714;</span> Success: Your General Settings Have Been Saved!</div>
<?php } ?>
<div class="tabbedheader">Recent Activity</div><br/> 
<div class="seprator2"></div>
<div class="box3">
<?php
	// get all recent activity, : dcp point level increases, new donations, new fundraisers, fundraisers that met thier goals, raffle winners.
	$rowsPerPage=10;
	$query= "SELECT `user_id`, `donation_amount`, `donation_date`, `organization` FROM `donations` WHERE cast(donation_date as DATE) BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE() ORDER BY `donation_date` DESC";
	$page = (isset($_GET['page'])) ? $_GET['page'] : 0;
	$results = $db->select($query, $rowsPerPage, $page);
	$numrows = $db->numrows($query);
	$pagination = paginate($numrows, $rowsPerPage, $page, "page", "txtLink", 7, array('delete'));
	if($results==TRUE){
	for ($i=0; $i<count($results); $i++) { 
	// get username, and organization + link - add avatar soon
	$query= "SELECT `username`,`avatar` FROM `users` WHERE `user_id` = '".$results[$i]["user_id"]."' LIMIT 1";
	$donator= $db->select($query);
	$query= "SELECT `Short_Link` FROM `users` WHERE `organization` = '".$results[$i]["organization"]."' LIMIT 1";
	$organization= $db->select($query);
	$time = strtotime($results[$i]["donation_date"]);
	
?>
<div class="box3Inner">
<div class="imgbox">
<img alt="DigitalCollectionPlate | User: <?php if(!empty( $donator[0]['username'])) {echo $donator[0]['username'];} else {echo "DCP Supporter";}?>" width="45" height="41" src="images/avatar/<?php if($donator[0]['avatar']!=""){echo $donator[0]['avatar']; }else{ echo "default_avatar.jpg";};?>" />
</div>
<span class="username"><?php if(!empty($donator[0]['username'])) {echo $donator[0]['username'];} else {echo "DCP Supporter";}?></span> (<abbr class="timeago" title="<?php echo date(DATE_ISO8601, $time);?>"><?php echo date('F j, Y, g:i a', $time); ?></abbr>)<br/>
<?php $donator[0]['username'];?> just donated $<?php echo $results[$i]["donation_amount"];?> to <?php if(!empty($organization[0]['Short_Link'])) {?><a href="http://www.digitalcollectionplate.com/u/<?php echo $organization[0]['Short_Link']; ?>"><?php echo $results[$i]["organization"];?></a> <?php } else { ?><span class="old_fundraiser"><?php echo $results[$i]["organization"];?></span><?php } ?>
</div>							
<?
}
}
?>
<div class="maindiv">
<div class="pagination ">
<div class="pageNuber">
<?php echo $pagination; ?>
</div>
</div>
</div>
</div>