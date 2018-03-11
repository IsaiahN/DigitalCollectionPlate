<div class="rightPanel">
<div class=" headingbg">
<h1>ACCOUNT STATS</h1>
</div>
<ul class="ul">
<li>Donations: <?php 
$query ="SELECT count(`donation_id`) as noDonations FROM donations WHERE user_id='".$cc_session->ccUserData['user_id']."'";
$noDonations= $db->select($query);
echo number_format($noDonations[0]['noDonations']); ?></li>
<li>Level:<?php echo getStatus($cc_session->ccUserData["points"]); ?></li>
<li> DCP Points:<?php echo $cc_session->ccUserData['points']; ?></li>
</ul>
<div class="rightImgbox">
<img class="avatar" src="<?php echo $glob['storeURL'];?>/images/avatar/<?php if($cc_session->ccUserData['avatar']!=""){echo($cc_session->ccUserData['avatar']);}else{echo("default_avatar.jpg");}?>" />
<?php if ($cc_session->ccUserData['user_level']==2) { ?>
<span class="userName level2">Fundraiser</span>
<?php } else { ?>
<span class="userName">Supporter</span>
<?php } ?>

</div>
<div class="recent2">
<p>RECENT WINNERS</p>
</div>
<?php
$query = "SELECT ".$glob['dbprefix']."users.user_id, ".$glob['dbprefix']."winners.winner_id, ".$glob['dbprefix']."users.username, ".$glob['dbprefix']."winners.organization,  ".$glob['dbprefix']."winners.date_won, ".$glob['dbprefix']."users.avatar FROM winners INNER JOIN users ON ".$glob['dbprefix']."winners.user_id = ".$glob['dbprefix']."users.user_id  ORDER BY `date_won` DESC LIMIT 3";
$winners= $db->select($query);
if ($winners) {
for ($i=0; $i<count($winners); $i++) { 

$query_link = "SELECT `Short_Link`,`fundee_payout` FROM `users` WHERE `organization` = '".$winners[$i]['organization']."'";
$link= $db->select($query_link);
?>
<div class="box3Inner">
<div class="imgbox">
<img alt="<?php echo $winners[$i]['username'];?>" src="images/avatar/<?php if($winners[$i]['avatar']!=""){echo($winners[$i]['avatar']);}else{echo("default_avatar.jpg");}?>" width="45" height="41" />
</div>
<span><?php echo $winners[$i]['username'];?></span>
recently won a <?php if ((!ctype_alpha($link[0]['fundee_payout'])) && (is_numeric($link[0]['fundee_payout']))) {echo "$".number_format($link[0]['fundee_payout'],2,".",",");} else {echo $link[0]['fundee_payout'];}?> payout for supporting <a href="u/<?php echo $link[0]['Short_Link'];?>"><?php echo $winners[$i]['organization'];?></a> 
</div>
 
<?
}
} else {
echo "<div id=\"donator_about\">No Supporters Have Won A Payout Yet.</div>";
}
?>
<div class=" recent2">
<p>SUCCESS STORY</p>
</div>
<?php
$newsArray= $db->select("SELECT * FROM ".$glob['dbprefix']."news where status='1' ORDER BY RAND() LIMIT 1");
?>
<img alt="News Story" src="<?php echo $glob['storeURL'];?>/images/uploads/<?php echo $newsArray[0]['image'];?>" class="home" />
<div class="david">
<?php

if($newsArray== TRUE){
?>
<h1><?php echo $newsArray[0]['title'];?></h1>
<p><?php echo $newsArray[0]['description'];?></p>
<?
}
?>
</div>
<a href="news.php?newsId=<?php echo $newsArray[0]['newsId'];?>"  class="readmore"><img alt="" src="images/buttons/readmore.jpg"  /></a>
</div>