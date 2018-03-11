<?php require_once 'includes/common.php';?>
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
<body>
<div class="topbg2 maindiv">
<div class="maincenter">
<div class="header">
<?php require_once 'includes/header.php';?>
</div>
<a href="index.<?php echo $ext;?>" class="left"><img alt="DigitalCollectionPlate | Logo" src="images/logos/logo2.jpg"  class="left logo2" /> </a>
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
<div class="innr_text">
<div class="accordion">
<div class="drop_link" style="display:none"></div>
<div class="drop_cntnt" style="display:none"></div>
<?
$faqsArray = $db->select("SELECT * FROM ".$glob['dbprefix']."faqs"); 
if($faqsArray==TRUE){
for($i=0;$i<count($faqsArray);$i++){
?>
<div class="drop_link">
<h3><?php echo $faqsArray[$i]['question'];?></h3>
</div> 
<div class="drop_cntnt"><?php echo $faqsArray[$i]['answer'];?></div>
<?
}
}

?>
</div>
</div>


</div>
</div>
</div> 
<div class="maindiv footer">
<?php require_once 'includes/footer.php';?>
</div>
</body>
</html>
