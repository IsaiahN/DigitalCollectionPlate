<div class="headerTop" style="height:auto">
<div class="contact"><span>Phone: <?php echo $config['phone']; ?></span>
E-mail: <a href="mailto:<?php echo $config['masterEmail']; ?>"><?php echo $config['masterEmail']; ?></a>
<div class="right myaccount">
<span id="username_cp" class="username_thin"><?php if(isset($cc_session->ccUserData['user_id'])){ echo "Hello ".$cc_session->ccUserData['username']; } else { echo "Welcome&#44; Guest";}?></span>
<?php if (isset($cc_session->ccUserData['user_id'])) { ?>
<a href="dashboard.<?php echo $ext;?>"><img alt="Dashboard" src="<?php echo $glob['storeURL'];?>/images/buttons/myAccount.png"  class="left button_login" /> </a>
<a href="logout.<?php echo $ext;?>"><img alt="logout" src="<?php echo $glob['storeURL'];?>/images/buttons/logout.png"  class="left button_login" /> </a>
<?php } else { ?>
<a href="signup.<?php echo $ext;?>"><img alt="signup" src="<?php echo $glob['storeURL'];?>/images/buttons/signup.jpg"  class="left" /> </a>
<a href="signin.<?php echo $ext;?>"><img alt="login" src="<?php echo $glob['storeURL'];?>/images/buttons/login.jpg"  class="left" /> </a>
<?php } ?>
</div>
</div>