<?php
$target = "images/avatar/";
$target = $target . basename($_FILES['file']['name']) ;
?>
<div class="tabbedheader">Account Settings</div>
<div class="seprator2 long"></div>
<br /><br /> 
<div class="maindiv">
<form  name="profileForm" id="profileForm" class="fundraisers_settings tableft" method="post" action="dashboard.php" enctype="multipart/form-data">
<div class="register_row">
<div class="register_cols">
<div class="left_col">Username:</div>
<div class="right_col">
<input name="username" class="textbox"  size="15" "readonly="readonly" value="<?php echo $cc_session->ccUserData['username'];?>" tabindex="1" type="text">
</div>
</div>
<div class="register_cols">
<div class="left_col">Short Link:</div>
<div class="right_col">
<input name="Short_Link" class="textbox" <?php if($cc_session->ccUserData['Short_Link']!=''){echo("readonly=\"readonly\"");} ?>   size="15" value="<?php echo $cc_session->ccUserData['Short_Link'];?>" tabindex="1" type="text">
</div>
</div>
</div>
<div class="register_row">
<div class="register_cols">
<div class="left_col">First Name:</div>
<div class="right_col">
<input name="first_name" class="textbox"  size="32" value="<?php echo $cc_session->ccUserData['first_name'];?>" tabindex="1" type="text">
</div>
</div>
<div class="register_cols">
<div class="left_col">Last Name:</div>
<div class="right_col">
<input name="last_name" class="textbox"  size="32" value="<?php echo $cc_session->ccUserData['last_name'];?>" tabindex="1" type="text">
</div>
</div>
</div>
<div class="register_row">
<div class="register_cols">
<div class="left_col">Phone:</div>
<div class="right_col">
<input name="phone" class="textbox"  size="32" value="<?php echo $cc_session->ccUserData['phone'];?>" tabindex="1" type="text">
</div>
</div>
<div class="register_cols">
<div class="left_col">Email Address:</div>
<div class="right_col">
<input name="email_address" class="textbox" size="32" readonly="readonly"  value="<?php echo $cc_session->ccUserData['email_address'];?>" tabindex="1" type="text">
</div>
</div>
</div>
<div class="register_row">
<div class="register_cols">
<div class="left_col">Organization:</div>
<div class="right_col">
<input name="organization" class="textbox"  size="32" value="<?php echo $cc_session->ccUserData['organization'];?>" tabindex="1" type="text">
</div>
</div>
<div class="register_cols">
<div class="left_col">Website:</div>
<div class="right_col">
<input name="website" class="textbox" size="32"   value="<?php echo $cc_session->ccUserData['website'];?>" tabindex="1" type="text">
</div>
</div>
</div>
<div class="register_row">
<div class="register_cols">
<div class="left_col">Address:</div>
<div class="right_col">
<textarea   name="address" rows="5" cols="23" required="required"><?php echo $cc_session->ccUserData['address'];?></textarea>
</div>
</div>
<div class="register_cols">
<div class="left_col">About me:</div>
<div class="right_col">
<textarea   name="about" rows="5" cols="23" required="required"><?php echo $cc_session->ccUserData['about'];?></textarea>
</div>
</div>
</div>
<div class="register_row">
<div class="register_cols">
<div class="left_col">Avatar:</div>
<div class="right_col">
<input type="file" name="file" id="file" size="18" class="textbox" />
</div>
</div>
<div class="register_cols">
<div class="left_col" title="This Includes the display of your Email, Address and Phone Number so that individuals may contact you.">Fundraiser Privacy</div>
<div class="right_col" title="This Includes the display of your Email, Address and Phone Number so that individuals may contact you."> <div class="left" id="1"></div>
 <div id="ajax"></div>
  <div class="clear"></div>

  <script type="text/javascript">
  
    $('#1').iphoneSwitch("on", 
     function() {
       $('#ajax').load('includes/privacyon.html');
      },
      function() {
       $('#ajax').load('includes/privacyoff.html');
      },
      {
        switch_on_container_path: 'images/iphone_switch_container_off.png'
      });
  </script></div>
</div>
</div>
<div class="register_row">
<div class="register_cols">
<div class="left_col">&nbsp;&nbsp;</div>
<div class="right_col">&nbsp;&nbsp;</div>
</div>
<div class="register_cols">
<div class="left_col"><input type="submit" value="Update Account" name="submit" class="submit" /></div>
<div class="right_col"></div>
</div>
</div>
</form>
</div>