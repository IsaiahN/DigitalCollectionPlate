<div class="newsSectionMid">
<div class="newsSectionLeft"></div>
<div class="newsSectionRgt">
<?php
$news= $db->select("SELECT * FROM ".$glob['dbprefix']."news where showFeatured='1' AND status='1' ORDER BY  date ASC");
?>
<marquee onMouseOver="this.stop()" onMouseOut="this.start()" scrollamount="2" scrolldelay="1" direction="left" hspace="0">
<samp style="font-size:13px; font-weight:normal; font-family:Verdana, Geneva, sans-serif">
<?php
if($news== TRUE){
for ($i=0; $i<count($news); $i++){   
echo strip_tags($news[$i]['description']);
}
}
?></samp>
</marquee>
</div>
</div>
