<?php
// send email if form is submit
if(isset($_POST['submit']))
{
	if(!isset($_POST['email']) || $_POST['email']=="name@example.com" || (validateEmail($_POST['email'])==FALSE) )
	{
		$errorMsgn="Please enter a valid email address.";
	}
	else
	{
		$emailArray = $db->select("SELECT customer_id, type FROM ".$glob['dbprefix']."customer WHERE email=".$db->mySQLSafe($_POST['email']));
		if ($emailArray == TRUE) 
		{
		
			$msgn= "<p class=\"error\">The email address ".$_POST['email']." has already been subscribed to our mailing list.</p>";
			?>
				<script type="text/javascript">
				setTimeout('window.location="index.php"',3000);
				</script>
				<?
		}
		else 
		{
			
			$record["lastName"] = $db->mySQLSafe($_POST['name']);
			$record["country"] = $db->mySQLSafe($_POST['c_code']);
			$record["email"] = $db->mySQLSafe($_POST['email']);
			$record["mobile"] = $db->mySQLSafe($_POST['mobile']); 
			$record['optIn1st'] = $db->mySQLSafe(1); 
			$record['htmlEmail'] = $db->mySQLSafe(1);
			$record["regTime"] = $db->mySQLSafe(time());
			$insert = $db->insert($glob['dbprefix']."customer", $record);
			if($insert==TRUE){
				$msgn= "<p class=\"error\">Thank you, ".$_POST['email']." has been subscribed to our mailing list.</p>";
				?>
				<script type="text/javascript">
				setTimeout('window.location="index.php"',3000);
				</script>
				<?
			} 		
	}	}
	
}	
?>
<script type="text/javascript">
function submit_form()
{
var digit = /[0-9]/i;
var name =	 $("input#name").val();
if(name=='') { 
$("input#name").addClass("error");
$("input#name").focus();
return false; } else { $("input#name").removeClass("error"); }

var email =	 $("input#email").val();
var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
if(email=='' || ! email.match(emailPattern)) { 
$("input#email").addClass("error");
$("input#email").focus();
return false; } else { $("input#email").removeClass("error"); }


var country =	 $("select#c_code").val();
if(country=='') { 
$("select#c_code").addClass("error");
$("select#c_code").focus();
return false; } else { $("input#c_code").removeClass("error"); }

var mobile 	=	 $("input#mobile").val();

if(mobile=='' || mobile.length != 12 || ! mobile.match(digit)) { 
$("input#mobile").addClass("error");
$("input#mobile").focus();
return false; 
} 
else { 
$("input#mobile").removeClass("error"); 
}
}
</script>
<?php if($cpage=="index" || $cpage==""){
?>
<div class="rightPanelRow">
<h2 style="text-transform:uppercase;">SUBSCRIBE NOW</h2>
<div class="form_box">
 <div>
            <?php
if(isset($msgn))
{ 
echo msg($msgn); 
}
else
{
?>
<h3 id="formhead">
 <?
if(isset($errorMsgn))
{ 
echo msg($errorMsgn);
}else{
echo("Subscribe to our mailing list below:");
}
?>
                </h3>
            	 <div id="myform"> 
                <form action="index.php" name="myform" id="myform"  method="post" onsubmit="return submit_form();">        
          			
          			<label>Name:</label>
                	<input type="text"  name="name" id="name" class="field1 required"/>
                	<label>Email:</label>
                	<input type="text" name="email" id="email" class="field1 required email"/>
                    <label>Country:</label>
                    <span class="selectbox1">
                    <select name="c_code" id="c_code" class="required">
 		                   <option value="Select">Select Country</option>
                            <?php
	$countries = $db->select("SELECT * FROM ".$glob['dbprefix']."countries"); 
	for($i=0; $i<count($countries); $i++)
	{
		$countryName = "";
		$countryName = $countries[$i]['printable_name'];
		if(strlen($countryName)>20)
		{
			$countryName = substr($countryName,0,20)."&hellip;";
		}
	
	?>
    
	<option value="<?php echo $countries[$i]['id']; ?>" <?php if(!isset($_POST['c_code']) && ($countries[$i]['id']==$config['siteCountry'])) echo "selected='selected'"; ?>><?php echo $countryName; ?></option>
	<?php 
	} 
	?>
	</select>
                            
                    
                    </span>
                    <label>Mobile:</label>
                    <input type="text" name="mobile" id="mobile" class="field1 required digits" maxlength="12" /><br />
                    
                   <input type="submit" name="submit" value=""  class="subscribe">
                </form>
                </div>
                 <div id="message" style="width:300px; height:100px;">
                </div>
            </div>
            <?
}
?>
</div>
</div>
<div class="rightPanelRow">
<?php
$bannersArray= $db->select("SELECT * FROM ".$glob['dbprefix']."banners where status='1' ORDER BY RAND() LIMIT 1");
if($bannersArray== TRUE){
?>
<a href="<?php echo $bannersArray[0]['url'];?>" title="<?php echo $bannersArray[0]['title '];?>" target="_blank"><img src="images/uploads/thumbs/thumb_<?php echo $bannersArray[0]['image'];?>"  width="246" height="150" border="0" alt="<?php echo $bannersArray[0]['title '];?>"></a>
<?
}
?>
</div>
<div class="rightPanelRow">
<a href="payment.php"><img src="images/pay_now.jpg" name="Imagemob" width="246" height="68" border="0" id="Imagemob"></a>
</div>
<div class="rightPanelRow">
<h2>FOREX RATES</h2>
<div class="rate_box">
<div style="margin-left:-3px;margin-top:3px; overflow:hidden;">
 <iframe frameborder="0" height="215" scrolling="no" src="http://173.193.110.134/mw/marketwatch.aspx" width="246"></iframe>
 </div>
 </div>
</div>
<?
}else
{
?>
<div class="form_box2">
<h2>SUBSCRIBE NOW</h2>
<div id="myform">
 <form action="index.php" name="myform" id="myform"  method="post" onsubmit="return submit_form();">        
          			
          			<label>Name:</label>
                	<input type="text"  name="name" id="name" class="field1 required"/>
                	<label>Email:</label>
                	<input type="text" name="email" id="email" class="field1 required email"/>
                    <label>Country:</label>
                    <span class="selectbox1">
                    <select name="c_code" id="c_code" class="required">
 		                   <option value="Select">Select Country</option>
                            <?php
	$countries = $db->select("SELECT * FROM ".$glob['dbprefix']."countries"); 
	for($i=0; $i<count($countries); $i++)
	{
		$countryName = "";
		$countryName = $countries[$i]['printable_name'];
		if(strlen($countryName)>20)
		{
			$countryName = substr($countryName,0,20)."&hellip;";
		}
	
	?>
    
	<option value="<?php echo $countries[$i]['id']; ?>" <?php if(!isset($_POST['c_code']) && ($countries[$i]['id']==$config['siteCountry'])) echo "selected='selected'"; ?>><?php echo $countryName; ?></option>
	<?php 
	} 
	?>
	</select>
                            
                    
                    </span>
                    <label>Mobile:</label>
                    <input type="text" name="mobile" id="mobile" class="field1 required digits" maxlength="12" /><br />
                    
                   <input type="submit" name="submit" value=""  class="subscribe">
                </form>
</div>        
</div>	
<div class="right_col">
<?php 
$rightNavArray=$db->select("SELECT * FROM ".$glob['dbprefix']."pages WHERE  status='1' AND right_menu='1' ORDER BY priority ASC");
  if($rightNavArray== true) {
  	for($i=0;$i<count($rightNavArray);$i++){
  $string= strtolower($rightNavArray[$i]['url']);
	$url=str_replace(" ","-",$string);
  ?>
  <a id="<?php echo $url;?>" href="<?php echo $url;?>.php" title="<?=$rightNavArray[$i]['title']?>"></a>
<? 
}
 }
?>
</div>
<?
}
?>
