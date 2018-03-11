<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }
permission("statistics","read",$halt=TRUE);
include_once($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");
include("classes".CC_DS."gd".CC_DS."phplot.php");
//todayVisitor
$timeLimit = time() - 57857;
$query = "SELECT * FROM ".$glob['dbprefix']."sessions LEFT JOIN ".$glob['dbprefix']."users ON ".$glob['dbprefix']."sessions.user_id = ".$glob['dbprefix']."users.user_id WHERE timeLast>".$timeLimit." ORDER BY timeLast DESC";
$todayVisitor= $db->numrows($query);
///Visitor
$query = "SELECT * FROM ".$glob['dbprefix']."sessions LEFT JOIN ".$glob['dbprefix']."users ON ".$glob['dbprefix']."sessions.user_id = ".$glob['dbprefix']."users.user_id  ORDER BY timeLast DESC";
$totalVisitor = $db->numrows($query);
//online
$timeLimit = time() - 900;
$query = "SELECT * FROM ".$glob['dbprefix']."sessions LEFT JOIN ".$glob['dbprefix']."users ON ".$glob['dbprefix']."sessions.user_id = ".$glob['dbprefix']."users.user_id WHERE timeLast>".$timeLimit." ORDER BY timeLast DESC";
$onlineVisitor = $db->numrows($query);
	
?>
<p class="pageTitle">Site Statistics</p>

<table width="100%" border="0" cellpadding="3" cellspacing="1" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle">Please choose the statistics you would like to view:</td>
  </tr>
  <tr>
    <td colspan="2">
	<ul>
	<li><a href="<?php echo $glob['adminFile']; ?>?_g=stats/index&amp;stats=online" class="txtLink">Users Online</a>(<?=$onlineVisitor?>)</li>
     <li><a href="<?php echo $glob['adminFile']; ?>?_g=stats/index&amp;stats=todays" class="txtLink">Users Todays</a>(<?=$todayVisitor?>)</li>
   
    <li><a href="<?php echo $glob['adminFile']; ?>?_g=stats/index&amp;stats=users" class="txtLink">Total Users to the site</a>(<?=$totalVisitor?>)</li>
	</ul>
	</td>
  </tr>
</table>
<?php 

$imageNo = 0;

switch ($_GET['stats'])
{

	
	
	case "prodViews";

	include("product.views.inc.php");
	
    break;  
	case "online";

	include("online.inc.php");

	break;
	case "users"; 
	
	include("users.inc.php");

	break;
	case "todays";

	include("todays.inc.php");
	
    break;
	

} // end switch
?>