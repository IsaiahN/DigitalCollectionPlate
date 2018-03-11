<?php
$query= "SELECT * FROM causes WHERE boxes='1' AND status='1' ORDER BY causeId ASC LIMIT 0 , 3";
$causes= $db->select($query);
if($causes==TRUE){
for ($i=0; $i<count($causes); $i++){ 
?>
<div class="box4">
<h1><?php echo $causes[$i]['name']?></h1>
<div class="imgbox2">
<img alt="<?php echo $causes[$i]['name']?>" src="images/uploads/<?php echo $causes[$i]['image']?>"  />
</div>
<p>
<?php 
$description=limit_text($causes[$i]['description'],'200');
echo strip_tags($description); 
?> 
</p>
<div class="maindiv">
<a href="causes.php?causeId=<?php echo $causes[$i]['causeId']?>" class="readMore2"><img alt="" src="images/buttons/readmore2.jpg"  /></a>
</div>
</div>
<?
}
}
?>

