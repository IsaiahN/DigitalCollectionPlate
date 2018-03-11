<div class="tabbedheader">Donations From Me</div><br/> 
<div class="seprator2"></div>
<div class="box3">
<?php
// get all recent activity, : dcp point level increases, new donations, new fundraisers, fundraisers that met thier goals, raffle winners.
$rowsPerPage=10;
$query = "SELECT ".$glob['dbprefix']."users.user_id, ".$glob['dbprefix']."donations.status,donation_id,donation_date, username, users.organization,avatar, Short_Link,donations.ip_address,donation_amount FROM ".$glob['dbprefix']."donations INNER JOIN ".$glob['dbprefix']."users ON ".$glob['dbprefix']."donations.user_id = ".$glob['dbprefix']."users.user_id WHERE ".$glob['dbprefix']."users.user_id ='".$cc_session->ccUserData['user_id']."' ORDER BY donation_date DESC";
$page = (isset($_GET['page'])) ? $_GET['page'] : 0;
$results = $db->select($query, $rowsPerPage, $page);
$numrows = $db->numrows($query);
$pagination = paginate($numrows, $rowsPerPage, $page, "page", "txtLink", 7, array('delete'));
if($results==TRUE){
for ($i=0; $i<count($results); $i++) { 
$time = strtotime($results[$i]["donation_date"]);
?>
<div class="box3Inner">
<div class="imgbox">
<img alt="Digital Collection Plate | Avatar" width="45" height="41" src="images/avatar/<?php if($results[$i]['avatar']!=""){echo ($results[$i]['avatar']); }else{ echo("default_avatar.jpg");};?>" />
</div>
<span class="username"><?php echo $results[$i]['username'];?></span> (<abbr class="timeago" title="<?php echo date(DATE_ISO8601, $time);?>"><?php echo date('F j, Y, g:i a', $time); ?></abbr>)<br/>
<?php $results[$i]['username'];?> just contributed $<?php echo $results[$i]["donation_amount"];?> to  <?php if(!empty($organization[0]['Short_Link'])) {?><a href="http://www.digitalcollectionplate.com/u/<?php echo $organization[0]['Short_Link']; ?>"><?php echo $results[$i]["organization"];?></a> <?php } else { ?><span class="old_fundraiser"><?php echo $results[$i]["organization"];?></span><?php } ?>
</div>
							
<?
}
} else {
echo '
<div class="box3Inner center">You Have Not Yet Given Any Contributions.
<img class="tableft" src="images/general/no_donations'.rand(1, 4).'.png" alt="DigitalCollectionPlate | Banner"/></div>
';
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