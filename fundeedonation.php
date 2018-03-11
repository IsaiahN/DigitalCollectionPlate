<?php require_once 'includes/common.php';?>
<?php
if ((isset($_GET['link'])) && (!empty($_GET['link']))) {
$organizationget = mysql_real_escape_string($_GET['link']);
$query = "SELECT `organization`, `text_page`, `about`, `email_address`, `address`, `phone`,`website`, `privacy`, `Short_Link`,`fundee_amount`,`fundee_total`, `fundee_payout` FROM `users` WHERE `Short_Link` = '".$organizationget."' AND `user_level` = '2'";
$link = $db->select($query);
$numrows_fundraiser = $db->numrows($query);
	if ($numrows_fundraiser < 1) {
		httpredir('/f/invalid');
	}
} else {
	httpredir('/f/invalid');
}


?>
<body>
<div class="topbg2 maindiv">
<div class="maincenter">
<div class="header">
<?php require_once 'includes/header.php';?>
</div>
<a href="index.<?=$ext;?>" class="left"><img alt="Digital Collection Plate | Logo" src="images/logos/logo2.jpg"  class="left logo2" /> </a>
<div class="menu2">
<?php require_once 'includes/nav.php';?>
</div>
</div>
</div>
</div>
<div class="maincenter">
<br />
<div class="content bgNone">
<div class="content">
        <div class="leftSide">
           <div id="tabContainer">
               <div class="tabs">
                    <ul>
                      <li id="tabHeader_1"><span class="tabActiveLeft" style="margin-left:0;">&nbsp;</span>Home</li>
                      <li id="tabHeader_2"><span class="tabActiveLeft">&nbsp;</span>Supporters</li>
                       <li id="tabHeader_3"><span class="tabActiveLeft">&nbsp;</span>Donate</li>
                       
                    </ul>
                 </div>
               <div class="tabscontent">
                    <div class="tabpage" id="tabpage_1">
<?php
if (!empty($link[0]["text_page"])) {
	echo 
	'<!-- AddThis Button BEGIN -->
	<div class="addthis_toolbox addthis_default_style ">
	<a class="addthis_button_preferred_1"></a>
	<a class="addthis_button_preferred_2"></a>
	<a class="addthis_button_preferred_3"></a>
	<a class="addthis_button_preferred_4"></a>
	<a class="addthis_button_compact"></a>
	<a class="addthis_counter addthis_bubble_style"></a>
	</div>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-50175a3452267071"></script>
	<!-- AddThis Button END -->';
	echo "<div id=\"text_page\">".stripslashes($link[0]["text_page"])."</div>";
} else {
	echo "No description has been added for this fundraiser yet.";
}
?>
                    </div>
                    <div class="tabpage" id="tabpage_2">
                  <div class="tabbedheader"><?php echo $link[0]["organization"]; ?> Supporters</div>
<div class="seprator2"></div>
<div class="box3 tiled">
<?php
$rowsPerPage=8;
$query= "SELECT `user_id`, `donation_amount`, `donation_date` FROM `donations` WHERE `organization` = '".$link[0]["organization"]."' ORDER BY `donation_date` DESC";
$page = (isset($_GET['page'])) ? $_GET['page'] : 0;
$results = $db->select($query, $rowsPerPage, $page);
$numrows = $db->numrows($query);
if($numrows > 0) {
$pagination = paginate($numrows, $rowsPerPage, $page, "page", "txtLink", 7, array('delete'));
if($results==TRUE){
for ($i=0; $i<count($results); $i++) { 
// get username, and organization + link - add avatar soon
$query= "SELECT `username`,`avatar` FROM `users` WHERE `user_id` = '".$results[$i]["user_id"]."' LIMIT 1";
$donator= $db->select($query);
$time = strtotime($results[$i]["donation_date"]);
?>
<div class="box3Inner">
<div class="imgbox">
<img alt="Default Avatar" width="45" height="41" src="images/avatar/<?php if($donator[0]['avatar']!=""){echo ($donator[0]['avatar']); }else{ echo("default_avatar.jpg");};?>" />
</div>
<span class="username"><?php echo $donator[0]['username'];?></span> (About <?php echo activityTiming($time);?> ago)<br/>
<?php $donator[0]['username'];?> just donated $<?php echo $results[$i]["donation_amount"];?> to <a href="http://www.digitalcollectionplate.com/u/<?php echo $link[0]['Short_Link']; ?>"><?php echo $results[$i]["organization"];?></a> 
</div>
							
<?
}
}
} else {
echo "	<h2 class=\"tableft\">There are currently no Supporters for this fundraiser.</h2><div class=\"clear\"></div><h5 class=\"tableft\">Would you like to be the first? </h5><br/>
	<img class=\"tableft\" src=\"images/general/no_donations".rand(1, 4).".png\" alt=\"DigitalCollectionPlate | Banner\"/>";
}//if no donations
?>
<div class="maindiv">
<div class="pagination">
<div class="pageNuber">
<?php echo $pagination; ?>
</div>
</div>
</div>
</div>
                    </div>
                    <div class="tabpage" id="tabpage_3">
                   <div id="donate_tab"><?php if ($_GET['act'] != "preview") {?> <iframe class="rewardtool" SRC="http://www.blvd-media.com/CentOffers.html?&pubid=1500&subid=<?php echo $link[0]["organization"]; ?>-<?php echo $cc_session->ccUserData['user_id']; ?>-<?php echo get_ip_address(); ?>" height="450px" scrolling="no" width="640px" FRAMEBORDER="0" -if "0" no border,	  otherwise "1" with border MARGINWIDTH ="0px" MARGINHEIGHT="0px"	  SCROLLING="no" -"no" no scrolling bar, "yes" show always, "auto" 	  showed when need>  Your browser does not support IFRAME </iframe>
                 <br />  <?php if(isset($cc_session->ccUserData['user_id'])){ echo "<span id=\"dcp_email\">Your DCP Email: ".$cc_session->ccUserData['username']."@digitalcollectionplate.com</span>"; } ?>
             	<?php } else { ?> <div id="iframe_placeholder"><span>To Donate to <?php echo $link[0]["organization"]; ?>, Please <a href="signin.html" target="_blank">Login</a>  or <a href="signup.html" target="_blank">Register</a>.</span></div><?php } ?>                    
                 <br />
                  <h2>Donate Time To <?php echo $link[0]["organization"]; ?> today!<br/></h2>
<p>
A Supporter participating in this cause will be drawn at random when the fundraiser goal is reached and will be awarded the Winner's Payout amount.<br>
</p>
<p>
<h2 id="center">Here's what to do:</h2>
<strong>Step 1</strong>Complete any survey or offer above to donate and be entered into the drawing. <br>
(The offer will X out upon completion) <br/>
<strong>Step 2</strong>Donate to <?php echo $link[0]["organization"]; ?> as often as you like. The completion of any survey or offer counts as one donation.<br/> Each donation increases your chance of being drawn for the Winner's Payout!
<strong>Step 3</strong>Win the drawing! Winner will be notified by e-mail. Winner's Payouts are paid by check.</p>
</div></div>
                    
                    <div class="tabpage" id="tabpage_4">
                    Comments
                    </div>   
                 </div>
 		   </div>	
           
    	   
        </div> 
        
        <div class="rightPanel" id="donation_page_panel">
        	<div class=" headingbg">
            	<h1>FUNDRAISER STATS</h1>
            </div>
            <ul class="ul">
            <?php
            
		$amount_needed = $link[0]['fundee_total'] - $link[0]['fundee_amount'];
       		if($amount_needed < 0) { // negative
       			$amount_needed = "<strong class=\"goal_met\">Fundrasing Goal Met</strong>";
       		} else {
       			$amount_needed = "<strong>Amount Needed: $".number_format($amount_needed,2,".",",")."</strong>";
       		}
       		
		$amount_donated = "$".number_format($link[0]['fundee_amount'],2,".",",");
		
            ?>
<li class="extended_line"><?php echo $amount_needed; ?></li>
<li class="extended_line"><strong>Donations: <?php echo $numrows; ?></strong></li>
<li class="extended_line"><strong>Amount Donated: <?php echo $amount_donated; ?></strong></li>
<li class="extended_line"><strong>Winner's Payout: <?php if ((!ctype_alpha($link[0]['fundee_payout'])) && (is_numeric($link[0]['fundee_payout']))) {echo "$".number_format($link[0]['fundee_payout'],2,".",",");} else {echo $link[0]['fundee_payout'];}?></li>
            </ul>
           
            <div class=" recent2">
            	<p>About Us</p>
            </div>
<?php
if (!empty($link[0]["about"])) {
	echo "<div id=\"donator_about\">".stripslashes($link[0]["about"])."</div>";
} else {
	echo "<div id=\"donator_about\">No About Us information has been added for this fundraiser yet.</div>";
}
?>
<?php if($link[0]["privacy"] == '0') { ?>
              <div class="recent2">
            	<p>CONTACT US</p>
            </div>
        
            <div class="david">
                <p>
<strong>Phone:</strong>&nbsp;&nbsp;<?php if(!empty($link[0]["phone"])) { echo $link[0]["phone"]; } else { echo "N/A";} ?><br /><br />
<strong>E-mail:</strong>&nbsp;&nbsp;<?php if(!empty($link[0]["email_address"])) { echo "<a href=\"mailto:".$link[0]["email_address"]."\">".$link[0]["email_address"]."</a>"; } else { echo "N/A";} ?><br /><br />
<strong>Website:</strong>&nbsp;&nbsp;<?php if(!empty($link[0]["website"])) { echo '<a href="'.$link[0]["website"].'" target="_blank">Visit Website</a>'; } else { echo "N/A";} ?><br /><br />
<strong>Address:&nbsp;&nbsp;</strong><?php if(!empty($link[0]["address"])) {echo "<div class=\"contact_address\">".$link[0]["address"]."</div>"; } else { echo "N/A";} ?>
<br /><br />
</p>
            </div>
<?php } ?>
        </div>
      </div>
    </div>
</div>
<script src="js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="js/acidTabs.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
$("#tabContainer").acidTabs({
     style: "three"											 
});
});
</script>	
</body>
</html>