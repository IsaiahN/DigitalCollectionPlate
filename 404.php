<?php require_once 'includes/common.php';?>
<script type="text/JavaScript">
setTimeout("location.href = 'http://www.digitalcollectionplate.com/';",1500);
</script>
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
<h1><?php 
$title=explode(":",$result[0]['title']); 
echo "<span class=\"title1\">".$title[0]."</span>:"."<span class=\"title2\">".$title[1]."</span>";
?></h1>
<?php echo stripslashes($result[0]['content']);?>
</div>
</div>
</div> 
<div class="maindiv footer">
<?php require_once 'includes/footer.php';?>
</div>
</body>
</html>
