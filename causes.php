<?php require_once 'includes/common.php';?>
<?php
if($_GET['causeId']){
$query= "SELECT * FROM causes WHERE causeId='".$_GET['causeId']."'";
$cause= $db->select($query);
}else
{
print "<meta http-equiv=\"refresh\" content=\"0;URL=index.php\">";
exit;
}
?>
<body>
<div class="topbg2 maindiv">
<div class="maincenter">
<div class="header">
<?php require_once 'includes/header.php';?>
</div>
<a href="index.<?php echo $ext;?>" class="left"><img alt="DigitalCollectionPlate.com | Logo" src="images/logos/logo2.jpg"  class="left logo2" /> </a>
<div class="menu2">
<?php require_once 'includes/nav.php';?>
</div>
</div>
</div>
</div>
<div class="maincenter">
<div class="content bgNone">
<div class="contentBox">
<h1><?php echo validHTML(stripslashes($cause[0]['name']));?></h1>
<img src="images/uploads/<?php echo $cause[0]['image'];?>" width="856" height="349" name="<?php echo validHTML(stripslashes($cause[0]['name']));?>" />
<?php echo stripslashes($cause[0]['description']);?>
<?php
if (isset($_GET['causeId'])) {
?>
<p align="center">
<div class="box_Donate">
<?
$cause= $db->select("SELECT user_id,causeId FROM causes WHERE causeId='".$_GET['causeId']."'");
$user= $db->select("SELECT fundee_amount,fundee_total,fundee_payout,Short_Link FROM ".$glob['dbprefix']."users WHERE user_id='".$cause[0]['user_id']."'");
if ($user[0]['fundee_amount'] > 0) {
$percentage = round($user[0]['fundee_amount']/$user[0]['fundee_total']*100);
}
$left = $percentage * 2;
if(isset($cause[0]['user_id'])) {
?>

<?php if ((!empty($user[0]['Short_Link'])) && (isset($cc_session->ccUserData['user_id']))) { ?>
<p><a class="donate_cause" href="http://digitalcollectionplate.com/u/<?php echo $user[0]['Short_Link']?>">Donate To This Cause Now!</a></p>
<?php } elseif (!empty($user[0]['Short_Link'])) { ?>
<p><a class="donate_cause" href="http://digitalcollectionplate.com/preview/<?php echo $user[0]['Short_Link']?>">Donate To This Cause Now!</a></p>
<?php } ?>
<?php if ($user[0]['fundee_amount'] > 0) { ?>
<p class="progressBar">
	<span><em style="left:<?php if(isset($left)){echo $left;} ?>px"><?php if(isset($percentage)){echo $percentage;} ?>%</em></span> 
</p>
	<?php if ($percentage >= 30 ) { ?>
		<span class="percentage"><?php echo $percentage; ?>% Raised</span>
	<?php } elseif ($percentage >= 1) { ?>
		<span class="percentage white_goal"><?php echo $percentage; ?>% Raised</span>
	<?php } else { ?>
		<span class="percentage less_than_goal">< 1% Raised</span>	
	<?php } ?>
<?php } ?>
<?php if ($user[0]['fundee_total'] > 0) { ?>
<h2 class="goal_needed">Goal: $<?php echo number_format($user[0]['fundee_total'],2,".",",");?></h2>
<?php } ?>
<h2 class="goal_met">Amount Raised: $<?php echo number_format($user[0]['fundee_amount'],2,".",",");?></h2>
<h2 class="goal_payout">Winner's Payout: <?php if ((!ctype_alpha($user[0]['fundee_payout'])) && (is_numeric($user[0]['fundee_payout']))) {echo "$".number_format($user[0]['fundee_payout'],2,".",",");} else {echo $user[0]['fundee_payout'];}?></h2>
</div>
</p>
<?
}
}
?>
<p align="center">
<div class="box_paginate">
 <?php
 if (isset($_GET['causeId'])) {
    $curid= intval($_GET['causeId']);
	// Next id
    $query= "SELECT causeId FROM causes WHERE causeId>{$curid} LIMIT 1";
	$numrows = $db->numrows($query);
	$causeArray = $db->select($query);
     if ($numrows>0) {
        $nextid = $causeArray[0]['causeId'];
    }

    // Prev id
	$query= "SELECT causeId FROM causes WHERE causeId<{$curid} LIMIT 1";
	$numrows = $db->numrows($query);
	$causeArray = $db->select($query);    
    
    if ($numrows>0) {
        $previd = $causeArray[0]['causeId'];
    }
}
?>
<?php if (isset($previd)) { ?>
<div class="previous"><a href="causes.php?causeId=<?php echo $previd?>" title="Previous Fundraiser"><?php if(isset($nextid)){echo '&laquo; Previous Fundraiser';} else {echo '&laquo; First Fundraiser';} ?></a></div>
<?php }?>

<?php if (isset($nextid)) { ?>
<div class="next"><a href="causes.php?causeId=<?php echo $nextid?>" title="Next Fundraiser">Next Fundraiser &raquo;</a></div>
<?php }?>
</div>
</p>
</div>
</div>
</div> 
<div class="maindiv footer">
<?php require_once 'includes/footer.php';?>
</div>
</body>
</html>