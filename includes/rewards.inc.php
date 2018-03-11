<div class="tabbedheader">Payouts To Me</div><br/> 
<div class="seprator2"></div>
<div class="box3">
<?php
// Get Any User Rewards
$rowsPerPage=10;

$query 		= "SELECT ".$glob['dbprefix']."users.user_id, ".$glob['dbprefix']."winners.winner_id, ".$glob['dbprefix']."users.username, ".$glob['dbprefix']."winners.organization,  ".$glob['dbprefix']."winners.date_won, ".$glob['dbprefix']."users.avatar FROM winners INNER JOIN users ON ".$glob['dbprefix']."winners.user_id = ".$glob['dbprefix']."users.user_id WHERE winners.user_id = '".$cc_session->ccUserData['user_id']."' ORDER BY `date_won` DESC";
$page 		= (isset($_GET['page'])) ? $_GET['page'] : 0;
$rewards	= $db->select($query, $rowsPerPage, $page);
$numrows 	= $db->numrows($query);
$pagination 	= paginate($numrows, $rowsPerPage, $page, "page", "txtLink", 7, array('delete'));

if ($rewards==true){
for ($i=0; $i<count($rewards); $i++) { 
$query_link 	= "SELECT `Short_Link`,`fundee_payout` FROM `users` WHERE `organization` = '".$winners[$i]['organization']."'";
$link		= $db->select($query_link);
?>	
<div class="box3Inner">
<div class="imgbox">
<img alt="<?php echo $winners[$i]['username'];?> Has won a Payout!" src="images/avatar/<?php if($winners[$i]['avatar']!=""){echo($winners[$i]['avatar']);}else{echo("default_avatar.jpg");}?>" width="45" height="41" />
</div>
<span><?php echo $winners[$i]['username'];?></span>
recently won a <?php if ((!ctype_alpha($link[0]['fundee_payout'])) && (is_numeric($link[0]['fundee_payout']))) {echo "$".number_format($link[0]['fundee_payout'],2,".",",");} else {echo $link[0]['fundee_payout'];}?> payout for supporting <a href="u/<?php echo $link[0]['Short_Link'];?>"><?php echo $winners[$i]['organization'];?></a> 
</div>
<?
}
} 
else 
{
echo '<div class="box3Inner center">You Have Not Yet Won A Payout.</div>';
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