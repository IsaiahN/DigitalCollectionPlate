<?php
$rowsPerPage = 50;
$query = "SELECT * FROM ".$glob['dbprefix']."sessions LEFT JOIN ".$glob['dbprefix']."users ON ".$glob['dbprefix']."sessions.user_id = ".$glob['dbprefix']."users.user_id  ORDER BY timeLast DESC";
if(isset($_GET['page'])){
	
		$page = $_GET['page'];
	
	} else {
		
		$page = 0;
	
	}
	
	// query database
	$results = $db->select($query, $rowsPerPage, $page);
	$numrows = $db->numrows($query);
	$pagination = paginate($numrows, $rowsPerPage, $page, "page");
?>
<p class='pageTitle'>Total Users to the site</p>
<p class="copyText">Users who have been active.</p>
<p class="copyText" style="text-align: right;"><?php echo $pagination; ?></p>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="mainTable">
  <tr>
    <td nowrap="nowrap" class="tdTitle">#</td>
    <td nowrap="nowrap" class="tdTitle">Users</td>
    <td nowrap="nowrap" class="tdTitle">Location</td>
    <td width="143" nowrap="nowrap" class="tdTitle">Session Start Time</td>
    <td width="242" nowrap="nowrap" class="tdTitle">Last Click Time</td>
    <td width="162" nowrap="nowrap" class="tdTitle">IP Address</td>
    <td width="198" nowrap="nowrap" class="tdTitle">Session Length</td>
  </tr>
<?php 
if($results==TRUE) 
{
  		
	for ($i=0; $i<count($results); $i++)
	{
		
		$rank = ($page * $rowsPerPage) + ($i + 1);
			
		$cellColor = cellColor($i);
		
?>

  <tr>
    <td class="<?php echo $cellColor; ?>" width="24"><span class="copyText"><?php echo $rank; ?>.</span></td>
    <td class="<?php echo $cellColor; ?>" width="124" nowrap='nowrap'><span class="copyText">
      <?php if($results[$i]['user_id']==0){ 
	echo 'Geust';
	} else {
	echo $results[$i]['title']." ".$results[$i]['firstName']." ".$results[$i]['lastName'];
	} ?>
    </span></td>
	<td class="<?php echo $cellColor; ?>" width="288"><a href="<?php echo $results[$i]['location']; ?>" class="txtLink"><?php echo $results[$i]['location']; ?></a></td>
    <td class="<?php echo $cellColor; ?>" nowrap='nowrap'><span class="copyText"><?php echo formatTime($results[$i]['timeStart']); ?></span></td>
	<td class="<?php echo $cellColor; ?>" nowrap='nowrap'><span class="copyText"><?php echo formatTime($results[$i]['timeLast']); ?></span></td>
    <td class="<?php echo $cellColor; ?>" nowrap='nowrap'><a href="javascript:;" class="txtLink" onclick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=misc/lookupip&amp;ip=<?php echo $results[$i]['ip']; ?>','misc',300,120,'yes,resizable=yes')"><?php echo $results[$i]['ip']; ?></a></td>
    <td class="<?php echo $cellColor; ?>" nowrap='nowrap'><span class="copyText"><?php echo sprintf("%.2f",($results[$i]['timeLast']-$results[$i]['timeStart'])/60); ?> <?php echo 'Mins';?></span></td>
  </tr>
		<?php } 	} else { ?>
  <tr>
    <td colspan="7"><span class="copyText">Sorry, there is no data to show.</span></td>
  </tr>  
  <?php } ?>
</table>
<p class="copyText" style="text-align: right;"><?php echo $pagination; ?></p>