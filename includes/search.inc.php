<div class="tabbedheader">Search Fundraisers</div><br/> 
<div class="seprator2"></div>
<div class="box3">
<?php
if (!isset($_POST["keyword"])) {
?>

<form action="dashboard.php" class="tableft" method="post">
<div class="search_row">
<div class="col1">Search For a Fundraiser:</div>
<div class="col2"><input type="text" name="keyword" class="textbox"/></div>
<div class="col3"><input type="submit" value="Submit Search" class="submit"/></div>
</div>
</form>
</div>
<?php
} else {
$rowsPerPage=11;
//$qry ="SELECT `about`,`avatar`,`Short_Link`,`organization`,`signup_date` FROM `users` WHERE `organization` LIKE '%%'";
$qry= "SELECT about, avatar, Short_Link, organization, signup_date FROM users WHERE MATCH (organization,Short_Link,username) AGAINST ('" . stripslashes (str_replace ("&quot;", "\"", ($_POST['keyword']))) . "' IN BOOLEAN MODE) AND user_level = '2'";

$page = (isset($_GET['page'])) ? $_GET['page'] : 0;
$results= $db->select($qry, $rowsPerPage, $page);
$numrows = $db->numrows($qry);
$pagination = paginate($numrows, $rowsPerPage, $page, "page", "txtLink", 7, array('delete'));

		
		
		echo "<h3>Search Results >> &quot;".$keyword."&quot;</h3>";
		for ($i=0; $i<count($results); $i++) { 
			   
		// Checks About for text   
		if (!empty($results[$i]['about'])) {
			if (strlen($results[$i]['about']) >= 120) {
				$about = substr($results[$i]['about'],0,120);
				$about = $about." ...";
			}
			else {$about = $results[$i]['about'];}
		} 
		else {$about = "No description is currently available for this fundraiser.";}  
		
		// Checks Avatar for image
		if ($results[$i]['avatar'] != "") {
			$avatar = htmlentities($results[$i]['avatar']);
		} 
		else {$avatar = "default_avatar.jpg";}   
		
		echo '
		<div class="box3Inner">
			<div class="imgbox">
				<a href="/u/'.$results[$i]['Short_Link'].'"><img alt="'.$results[$i]['organization'].' - DigitalCollectionPlate" src="images/avatar/'.$avatar.'" /></a> 
			</div>
			<span class="username"><a href="/u/'.$results[$i]['Short_Link'].'">'.$results[$i]['organization'].'</a></span> (Joined '.activityTiming(strtotime($results[$i]['signup_date'])).' ago)<br/>
			<span class="about_text">'.$about.'</span>  
		</div></div>
		';
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