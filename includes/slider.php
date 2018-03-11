<?php
$causesArray= $db->select("SELECT * FROM ".$glob['dbprefix']."causes WHERE slide='1' AND status='1'  ORDER BY  causes.priority  ASC");
if($causesArray==true){
for($i=0;$i<count($causesArray);$i++)
{
?>
<a href="causes.php?causeId=<?php echo $causesArray[$i]['causeId']?>"><img src="images/uploads/<?php echo $causesArray[$i]['image']; ?>" alt="<?php echo $causesArray[$i]['name'];?>" title="<?php echo $causesArray[$i]['caption']; ?>" /></a>
<?
}
}
?>




