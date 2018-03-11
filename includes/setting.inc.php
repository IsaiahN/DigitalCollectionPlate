<div class="tabbedheader">Account Settings</div>
<div class="seprator2"></div>
<div class="box3">
<form  name="profileForm" id="profileForm" method="post" action="dashboard.php" enctype="multipart/form-data">
<div class="register_row">
<div class="register_cols">
<div class="left_col">Username:</div>
<div class="right_col">
<input name="username" class="textbox"  "readonly="readonly" size="15" value="<?php echo $cc_session->ccUserData['username'];?>" tabindex="1" type="text">
</div>
</div>
<div class="register_cols">
<div class="left_col">Avatar:</div>
<div class="right_col">
<input type="file" name="file" id="file" size="18" class="textbox" />
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
<input name="email_address" class="textbox" size="32"  value="<?php echo $cc_session->ccUserData['email_address'];?>" tabindex="1" type="text">
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