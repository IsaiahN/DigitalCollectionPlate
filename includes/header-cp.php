<div class="headerTop">
<a href="index.<?=$ext;?>"><img alt="" src="images/logo.png"  class="left" /> </a>
<div class="right myaccount">
<span id="username_cp"><?php if(isset($cc_session->ccUserData['user_id'])){ echo "Hello ".$cc_session->ccUserData['username']; } else { echo "Welcome&#44; Guest";}?></span>
<a href="dashboard.<?php echo $ext;?>"><img alt="Dashboard" src="<?php echo $glob['storeURL'];?>/images/buttons/myAccount.png"  class="left members" /> </a>
<a href="logout.<?php echo $ext;?>"><img alt="logout" src="<?php echo $glob['storeURL'];?>/images/buttons/logout.png"  class="left members" /> </a>
</div>