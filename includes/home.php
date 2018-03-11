<div class="starRow">
<?php 
$middleNavArray=$db->select("SELECT * FROM ".$glob['dbprefix']."pages WHERE  status='1' AND home_menu='1' ORDER BY priority ASC");
  if($middleNavArray== true) {
  	for($i=0;$i<count($middleNavArray);$i++){
  $string= strtolower($middleNavArray[$i]['url']);
	$url=str_replace(" ","-",$string);
  ?>
   <div class="small_co">
   <img src="images/icons/<?=$url?>.jpg" alt="<?=$middleNavArray[$i]['title']?>" width="202" height="64"  />
   <?php
$description=limit_text($middleNavArray[$i]['content'],'140');
echo strip_tags($description);
?>  
<br /><a href="<?php echo $url;?>.php" title="<?=$middleNavArray[$i]['title']?>">Read More<img src="images/icon1.jpg" width="15" height="10" style="padding-bottom:0px;" /></a></div>

 <?php 
 if(($i+1) % 3!= 0 && $i<count($middleNavArray)-1){
	 echo("<div class=\"banner_divider\"></div>");
}
if(($i+1) % 3== 0 && $i<count($middleNavArray)-1){echo "</div><div class=\"starRow\">";}
 } 
}
?>
</div>