<?php require_once 'includes/common.php';
# http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}
?>
<body>
<div class="topbg2 maindiv">
<div class="maincenter">
<div class="header">
<?php require_once 'includes/header.php';?>
</div>
<a href="index.<?php echo $ext;?>" class="left"><img alt="Digital Collection Plate | Logo" src="images/logos/logo2.jpg"  class="left logo2" /> </a>
<div class="menu2">
<?php require_once 'includes/nav.php';?>
</div>
</div>
</div>
</div>
<div class="maincenter">
<div class="content bgNone">
<div class="contentBox">
<h1><?php echo validHTML(stripslashes($result[0]['title']));?></h1>
<?php echo stripslashes($result[0]['content']);?>

<?php
$query= "SELECT `organization`,`Short_Link`,`about`,`website` FROM `users` WHERE `user_level` = '2' AND `activated` = '1' AND `organization` <> '' AND privacy = '0' LIMIT 100";
$fundraiserArray= $db->select($query);
if ($fundraiserArray==TRUE) {
echo 	'<table id="fundraiser_list">
	<th>Fundraiser Name</th><th>Website</th><th>About</th><th>Donation Page</th>';
for ($i=0; $i<count($fundraiserArray); $i++) { 
echo '<tr><td class="fundraiser_name">'.$fundraiserArray[$i]['organization'].'</td>';
	if(!empty($fundraiserArray[$i]['website'])) {
	if(startsWith($fundraiserArray[$i]['website'], 'http')) {
		echo '<td><a href="'.$fundraiserArray[$i]['website'].'">Visit Fundraiser\'s Website</a></td>';
		} else {
		echo '<td><a href="http://'.$fundraiserArray[$i]['website'].'">Visit Fundraiser\'s Website</a></td>';	
		}
	} else {echo '<td>N/A</td>';}
	if(!empty($fundraiserArray[$i]['about'])) {
		echo '<td class="fundraiser_list_about">'.$fundraiserArray[$i]['about'].'</td>';
	} else {echo '<td class="fundraiser_list_about">No Description has been yet added.</td>';}

	if ((($fundraiserArray[$i]['Short_Link'])) && (isset($cc_session->ccUserData['user_id']))) { 
		echo '<td><a href="http://digitalcollectionplate.com/u/'.$fundraiserArray[$i]['Short_Link'].'">Donate Now</a></td></tr>';
	} else {
		echo '<td><a href="http://digitalcollectionplate.com/preview/'.$fundraiserArray[$i]['Short_Link'].'">Donate Now</a></td></tr>';
	}
}
echo '</table>';
}
?>
</div>
</div>
</div> 
<div class="maindiv footer">
<?php require_once 'includes/footer.php';?>
<script type="text/javascript" src="js/jquery.shorten.1.0.js"></script>
<script type="text/javascript">
$(".fundraiser_list_about").shorten({
    "showChars" : 100,
    "moreText"  : "Read More",
    "lessText"  : "Less",
});
</script>
</div>
</body>
</html>