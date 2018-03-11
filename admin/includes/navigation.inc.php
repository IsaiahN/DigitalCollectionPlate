<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }

$link401 = "href=\"javascript:alert('You do not have permission to access this.');\" class=\"txtNullLink\"";
?>
<div id="adminNavigation" style="width: 180px;">
  
	<div style="padding-left:1px">
		<a href="http://www.digitalcollectionplate.com" target="_blank">
		  <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/ccAdminLogo.gif"  width="180" height="54" border="0" alt="superlogic.co.uk" />
		</a>
	</div>

  <div id="menuList" class="navMenu" style="padding-top: 10px;">
   
	<span class="navTitle" onclick="Effect.toggle('navSiteLinks', 'blind');">Navigation</span>
	<ul id="navSiteLinks">
		<li><a href="<?php echo $GLOBALS['rootRel'].$glob['adminFile']; ?>" target="_self" class="txtLink">Admin Home</a></li>
		<li><a href="<?php echo $GLOBALS['rootRel']; ?>index.php" target="_blank" class="txtLink">Site Home</a></li>
              
	</ul>
	
	<span class="navTitle" onclick="Effect.toggle('navSiteConfig', 'blind');">Configuration</span>
	<ul class="navItem" id="navSiteConfig">
		<li><a <?php if(permission("settings","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=settings/index" class="txtLink"<?php } else { echo $link401; } ?>>General Settings</a></li>
       </ul>
   </ul>
    <span class="navTitle" onclick="Effect.toggle('navSiteAdminUsers', 'blind');">Admin Users</span>
	<ul class="navItem" id="navSiteAdminUsers">
		<li><a <?php if(permission("administrators","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=adminusers/administrators" class="txtLink"<?php } else { echo $link401; } ?>>Administrators</a></li>
		<li><a href="<?php echo $glob['adminFile']; ?>?_g=adminusers/sessions" class="txtLink">Admin Sessions</a></li>
		<li><a href="<?php echo $glob['adminFile']; ?>?_g=adminusers/logs" class="txtLink">Admin Logs</a></li>
	</ul>
      
     
    
    <span class="navTitle" onclick="Effect.toggle('navSiteUsers', 'blind');">Manage Users</span>
	<ul class="navItem" id="navSiteUsers">
		<li><a <?php if(permission("users","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=users/index" class="txtLink"<?php } else { echo $link401; } ?>>View Users</a></li>
     
		<li><a <?php if(permission("users","write")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=users/email" class="txtLink"<?php } else { echo $link401; } ?>>Newsletter</a></li>
		
	</ul>
	<span class="navTitle" onclick="Effect.toggle('navSiteWinners', 'blind');">Manage Winners</span>
	<ul class="navItem" id="navSiteWinners">
		<li><a <?php if(permission("winners","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=winners/index" class="txtLink"<?php } else { echo $link401; } ?>>View Winners</a></li>   
		
		
	</ul>
     <span class="navTitle" onclick="Effect.toggle('navSiteLevel', 'blind');">Manage Levels</span>
	<ul class="navItem" id="navSiteLevel">
		<li><a <?php if(permission("levels","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=levels/index" class="txtLink"<?php } else { echo $link401; } ?>>View Levels</a></li>
     
		<li><a <?php if(permission("levels","write")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=levels/index&amp;mode=new" class="txtLink"<?php } else { echo $link401; } ?>>Add Level</a></li>
		
	</ul>
    <span class="navTitle" onclick="Effect.toggle('navSiteDonations', 'blind');">Manage Donations</span>
	<ul class="navItem" id="navSiteDonations">
		<li><a <?php if(permission("donations","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=donations/index" class="txtLink"<?php } else { echo $link401; } ?>>View Donations</a></li>		
		
	</ul>
   <span class="navTitle" onclick="Effect.toggle('navSitePages', 'blind');">Manage Site Pages</span>
	<ul class="navItem" id="navSitePages">
		<li><a <?php if(permission("homepage","edit")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=pages/home" class="txtLink"<?php } else { echo $link401; } ?>>Homepage</a></li>
		<li><a <?php if(permission("pages","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=pages/index" class="txtLink"<?php } else { echo $link401; } ?>>Site Pages</a></li>
	</ul>
    <span class="navTitle" onclick="Effect.toggle('navSiteCauses', 'blind');">Manage Causes</span>
	<ul class="navItem" id="navSiteCauses">
<li><a <?php if(permission("causes","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=causes/index" class="txtLink"<?php } else { echo $link401; } ?>>View Causes</a></li>
<li><a <?php if(permission("causes","write")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=causes/index&amp;mode=new" class="txtLink"<?php } else { echo $link401; } ?>>Add Cause</a></li>
</ul>
 <span class="navTitle" onclick="Effect.toggle('navSiteNews', 'blind');">Manage News</span>
	<ul class="navItem" id="navSiteNews">
<li><a <?php if(permission("news","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=news/index" class="txtLink"<?php } else { echo $link401; } ?>>View News</a></li>
<li><a <?php if(permission("news","write")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=news/index&amp;mode=new" class="txtLink"<?php } else { echo $link401; } ?>>Add News</a></li>
</ul>
<span class="navTitle" onclick="Effect.toggle('navSiteFAQ', 'blind');">Manage FAQ</span>
	<ul class="navItem" id="navSiteFAQ">
<li><a <?php if(permission("faqs","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=faqs/index" class="txtLink"<?php } else { echo $link401; } ?>>View FAQs</a></li>
<li><a <?php if(permission("faqs","write")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=faqs/index&amp;mode=new" class="txtLink"<?php } else { echo $link401; } ?>>Add FAQ</a></li>
</ul>  
 <span class="navTitle" onclick="Effect.toggle('navSiteFilemanager', 'blind');">Image Manager</span>
	<ul class="navItem" id="navSiteFilemanager">
		<li><a <?php if(permission("filemanager","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=filemanager/index" class="txtLink"<?php } else { echo $link401; } ?>>Manage Images</a></li>
		<li><a <?php if(permission("filemanager","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=filemanager/index"  onclick="openPopUp('<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/includes/rte/editor/filemanager/browser/default/browser.html?Type=uploads&Connector=<?php echo urlencode($GLOBALS['rootRel'].$glob['adminFolder']); ?>%2Fincludes%2Frte%2Feditor%2Ffilemanager%2Fconnectors%2Fphp%2Fconnector.php','filemanager',700,600)" class="txtLink"<?php } else { echo $link401; } ?>>Upload Images</a></li>		
	</ul>
	<span class="navTitle" onclick="Effect.toggle('navSiteStats', 'blind');">Statistics</span>
	<ul class="navItem" id="navSiteStats">
		<li><a <?php if(permission('statistics','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=stats/index" class="txtLink"<?php } else { echo $link401; } ?>>View Stats</a></li>
	</ul>	
	
	<span class="navTitle" onclick="Effect.toggle('navSiteMisc', 'blind');">Misc</span>
	<ul class="navItem" id="navSiteMisc">
		<li><a href="<?php echo $glob['adminFile']; ?>?_g=misc/serverInfo" class="txtLink">Server Info</a></li>
	</ul>
	
	
	
	<span class="navTitle" onclick="Effect.toggle('navSiteMaintenance', 'blind');">Maintenance</span>
	<ul class="navItem" id="navSiteMaintenance">
		<li><a <?php if(permission("maintenance","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=maintenance/database" class="txtLink"<?php } else { echo $link401; } ?>>Database</a></li>
		<li><a <?php if(permission("maintenance","read")==true){ ?>href="<?php echo $glob['adminFile']; ?>?_g=maintenance/backup" class="txtLink"<?php } else { echo $link401; } ?>>Backup</a></li>
		
		
	</ul>
  </div>
</div>