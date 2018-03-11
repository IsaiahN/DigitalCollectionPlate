<script type="text/javascript">
$(document).ready(function(){
	
	$(".accordion .drop_link:first").addClass("active");
	$(".accordion .drop_cntnt:not(:first)").hide();

	$(".accordion .drop_link").click(function(){
		$(this).next(".drop_cntnt").slideToggle("fast")
		.siblings(".drop_cntnt:visible").slideUp("fast");
		$(this).toggleClass("active");
		$(this).siblings(".drop_link").removeClass("active");
	});

});
</script>
<div class="tabbedheader">Recent News</div>
<div class="seprator2 long"></div>
<div class="innr_text">
<div class="accordion">
<div class="drop_link" style="display:none"></div>
<div class="drop_cntnt" style="display:none"></div>
<?
$newsArray= $db->select("SELECT * FROM ".$glob['dbprefix']."news where status=1 ORDER BY  date DESC");
if($newsArray==TRUE){
for($i=0;$i<count($newsArray);$i++){
?>
<div class="drop_link">
<h3><?php echo $newsArray[$i]['title'];?></h3>
</div> 
<div class="drop_cntnt"><?php echo $newsArray[$i]['description'];?></div>
<?
}
}

?>
</div>
</div>