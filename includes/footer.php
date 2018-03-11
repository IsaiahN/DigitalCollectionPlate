<div class="maincenter">
<div class="footerInner">
<h1>About Us</h1>
<div class="Fseprator"> </div>
<div class="maindiv" style="width:70%;line-height:15px;">
<p>
<?php
$res= $db->select("SELECT * FROM ".$glob['dbprefix']."pages where pageId='100'");
echo stripslashes($res[0]['content']);
?>
</p>
</div>
</div>
<div class="footerInner">
<h1>Information</h1>
<div class="Fseprator"> </div>
<ul>
<?php
$footerNavArray=$db->select("SELECT * FROM ".$glob['dbprefix']."pages WHERE  footer_menu='1' AND status='1' ORDER BY priority ASC");
if($footerNavArray== TRUE)
{
for ($i=0; $i<count($footerNavArray); $i++){
 	
$string= strtolower($footerNavArray[$i]['url']);
	$url=str_replace(" ","-",$string);
	if($navArray[$i]['url_openin']==1)
	{
	$target= 'target="_blank"';
	}else
	{
	$target= '';
	}

	
preg_match('@^(?:http://)?([^/]+)@i',$footerNavArray[$i]['url'], $matches);
$host = $matches[1];
// get last two segments of host name
preg_match('/[^.]+\.[^.]+$/', $host, $matches);
if($matches[0])
{
?>
<li ><a href="<?=$url?>" title="<?=$footerNavArray[$i]['title']?>" <?=$target?>><?=$footerNavArray[$i]['name']?></a></li>
<?	
}
else
{
?>
<li><a href="<?=$url.".".$ext?>" title="<?=$footerNavArray[$i]['title']?>" <?=$target?>><?=$footerNavArray[$i]['name']?></a></li>
<?	
}
	
}
}
?>
</ul>
</div>
<div class="footerInner">
<h1>Menu</h1>
<div class="Fseprator"> </div>
<ul>
<li <?php if($cpage=="index" || $cpage==""){?>class="active" <? }?>><a href="index.<?php echo $ext;?>">Home</a></li>
<?php
$navArray=$db->select("SELECT * FROM ".$glob['dbprefix']."pages WHERE  top_menu='1' AND status='1' ORDER BY priority ASC");
// build attributes
if($navArray== TRUE)
{
for ($i=0; $i<count($navArray); $i++){
 	
$string= strtolower($navArray[$i]['url']);
	$url=str_replace(" ","-",$string);
	if($navArray[$i]['url_openin']==1)
	{
	$target= 'target="_blank"';
	}else
	{
	$target= '';
	}
if($cpage==$url){
$css='class=active';
}
	// get host name from URL
preg_match('@^(?:http://)?([^/]+)@i',$navArray[$i]['url'], $matches);
$host = $matches[1];
// get last two segments of host name
preg_match('/[^.]+\.[^.]+$/', $host, $matches);
if($matches[0])
{
?>
<li ><a href="<?=$url?>" title="<?=$navArray[$i]['title']?>" <?=$target?>><?=$navArray[$i]['name']?></a></li>
<?	
}
else
{
?>
<li <?php echo $css;?>><a href="<?=$url.".".$ext?>" title="<?=$navArray[$i]['title']?>" <?=$target?>><?=$navArray[$i]['name']?></a></li>
<?	
}
	
}
}
?>
</ul>
</div>
<div class="footerInner footerInner2 ">
<h1>Contact us</h1>
<div class="maindiv">
<form id="contactUsForm" name="contactUsForm" action="index.<?=$ext?>" method="post">
<input type="text" name="name"  value="<?php if(isset($_POST['name'])){ echo $_POST['name'];}else{echo('Name');} ?>" required="required" onfocus="javascript:contactUsForm.name.value=''" />
<input type="text" name="email"  value="<?php if(isset($_POST['email'])){ echo $_POST['email'];}else{echo('Email');} ?>" required="required" onfocus="javascript:contactUsForm.email.value=''" />
<textarea rows="1" cols="1" name="message" required="required" onfocus="javascript:contactUsForm.message.value=''">Message</textarea>
<input type="image" src="images/buttons/send.jpg" name="submit"  />
<input type="hidden" name="action" value="send" />
</form>
</div>
</div>
</div>


