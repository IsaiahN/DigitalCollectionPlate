<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }
$msg = false;
permission('settings','read', true);
if($config['sef']==1){
include("seo-htaccess.php");
}
if (isset($_POST['install_htaccess']) && permission('settings','write', true)) {
	$htaccess = CC_ROOT_DIR.CC_DS.'.htaccess';
	$ht_new = file_get_contents($glob['adminFolder'].CC_DS.'sources'.CC_DS.'settings'.CC_DS.'seo-htaccess.txt');
	## Some hosting companies need a RewriteBase if we can detect them e.g. Mosso
	if($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) {
		$ht_new = str_replace("RewriteEngine On","RewriteEngine On\nRewriteBase ".$glob['rootRel'],$ht_new);
	}
	if (@file_exists($htaccess)) {
		## .htaccess file already exists - lets check if it already has the settings, and append them if it doesn't
		$ht_old = @file_get_contents($htaccess);
		if (!strstr($ht_old, $ht_new) && @cc_is_writable($htaccess)) {
			## Append the rewrite rules
			$fp = @fopen($htaccess, 'ab');
			if (@fwrite($fp, $ht_new, strlen($ht_new))) {
				$msg .= '<p class="infoText">.htaccess was successfully created.</p>';
			} else {
				$msg .= '<p class="warnText">.htaccess file could not be written. Please create it manually.</p>';
			}
			@fclose($fp);
		}
	} else {
		$fp = @fopen(CC_ROOT_DIR.CC_DS.'.htaccess', 'wb');
		if (!@fwrite($fp, $ht_new)) {
			$msg .= '<p class="warnText">.htaccess file could not be written. Please create it manually.</p>';
		} else {
			$msg .= '<p class="infoText">.htaccess was successfully created.</p>';
		}
		@fclose($fp);
	}
} elseif (isset($_POST['install_rewrite_script']) && permission('settings','write', true)) {
	## rewrite.script has to sit in web root folder
	$rewrite_script = $_SERVER['DOCUMENT_ROOT'].CC_DS.'rewrite.script';
	$ht_new = file_get_contents($glob['adminFolder'].CC_DS.'sources'.CC_DS.'settings'.CC_DS.'seo-rewrite.script.txt');
	$ht_new = str_replace("{VAL_ROOT_REL}",$glob['rootRel'],$ht_new);
	if (@file_exists($rewrite_script)) {
		## rewrite.script file already exists - lets check if it already has the settings, and append them if it doesn't
		$ht_old = @file_get_contents($rewrite_script);
		if (!strstr($ht_old, $ht_new) && @cc_is_writable($rewrite_script)) {
			## Append the rewrite rules
			$fp = @fopen($rewrite_script, 'ab');
			if (@fwrite($fp, $ht_new, strlen($ht_new))) {
				$msg .= '<p class="infoText">rewrite.script was successfully created.</p>';
			} else {
				$msg .= '<p class="warnText">rewrite.script file could not be written. Please create it manually.</p>';
			}
			@fclose($fp);
		}
	} else {
		$fp = @fopen($_SERVER['DOCUMENT_ROOT'].CC_DS.'rewrite.script', 'wb');
		if (!@fwrite($fp, $ht_new)) {
			$msg .= '<p class="warnText">rewrite.script file could not be written. Please create it manually.</p>';
		} else {
			$msg .= '<p class="infoText">rewrite.script was successfully created.</p>';
		}
		@fclose($fp);
	}
}

if (isset($_POST['config']) && permission('settings','write', true)) {
	
	## fix for Bug #147
	$fckEditor = (detectSSL() && !$config['force_ssl']) ?  str_replace($config['rootRel_SSL'],$glob['rootRel'],$_POST['FCKeditor']) : $_POST['FCKeditor'];
	$_POST['config']['offLineContent'] = base64_encode($fckEditor);

## fix for Bug #147
	$fckEditoradmission = (detectSSL() && !$config['force_ssl']) ?  str_replace($config['rootRel_SSL'],$glob['rootRel'],$_POST['admission']) : $_POST['admission'];
	$_POST['config']['admission'] =$fckEditoradmission;
	$config = fetchDbConfig('config');

	## DIRTY BUT MAKES SUPPORT EASIER!!
	if ($_POST['config']['ssl'] && !strstr($_POST['config']['rootRel_SSL'], '/')) {
		$msg .= "<p class='warnText'>The HTTPS Root Relative Path entered is not valid! SSL has not been enabled.</p>";
		$_POST['config']['force_ssl'] = false;
		$_POST['config']['ssl'] = false;

	}

	if ($_POST['config']['ssl'] && !strstr($_POST['config']['storeURL_SSL'], 'https')) {
		$msg .= "<p class='warnText'>The absolute HTTPS Absolute URL entered is not valid. SSL has not been enabled.</p>";
		$_POST['config']['force_ssl'] = false;
		$_POST['config']['ssl'] = false;
	}

	if ($_POST['config']['sqlSessionExpiry'] && $_POST['config']['sqlSessionExpiry']<7200) {
		$msg .= "<p class='infoText'>The minimum session time has been set to 2 hours (7200 seconds). This will prevent IE session problems.</p>";
		$_POST['config']['sqlSessionExpiry'] = 7200;
	}
	$msg .= writeDbConf($_POST['config'], 'config', $config, true);
}
$config = fetchDbConfig('config');

$jsScript = jsGeoLocation('siteCountry', 'siteCounty', '-- n/a --');

require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");
?>
<p class="pageTitle">Site Settings</p>

<?php if (isset($msg)) echo msg($msg); ?>
<link href="css/tabs.css" rel="stylesheet" type="text/css" />
<script src="js/jquery/c/jquery.js" type="text/javascript"></script>
<script src="js/jquery/c/jquery-ui.min.js" type="text/javascript"></script>
<script id="demo" type="text/javascript">
$(document).ready(function() {
	var tabs = $("#tabs").tabs();
	
});
</script>
<p class="copyText">Please edit your store configuration settings below:</p>

<form name="updateSettings" method="post" enctype="multipart/form-data" target="_self" action="<?php echo $glob['adminFile']; ?>?_g=settings/index">
<div id="tabs">
<ul>
<li><a href="#MetaData">Meta Data</a></li>
<li><a href="#Options">Options</a></li>
<li><a href="#GDSettings">GD Settings</a></li>
<li><a href="#TimeDate">Time & Date</a></li>
<li><a href="#OfflineSettings">Offline Settings</a></li>
<li><a href="#SearchEngineOptimization">Search Engine Optimization</a></li>
<li><a href="#Donation">Donation</a></li>
</ul>
<div id="MetaData">
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="mainTable">
	<tr>
		<td colspan="2" class="tdTitle" id="meta_data"><strong>Meta Data</strong></td>
	</tr>
	<tr>
	  <td width="30%" class="tdText"><strong>Global Browser Title:</strong></td>
	  <td align="left"><input name="config[siteTitle]" type="text" size="35" class="textbox" value="<?php echo $config['siteTitle']; ?>" /></td>
    </tr>
	<tr>
	  <td width="30%" align="left" valign="top" class="tdText"><strong>Global Meta Description:</strong></td>
	  <td align="left"><textarea name="config[metaDescription]" cols="35" rows="3" class="textbox"><?php echo $config['metaDescription']; ?></textarea></td>
    </tr>
	<tr>
	  <td width="30%" align="left" valign="top" class="tdText"><strong>Global Meta Keywords:</strong><br />
 (Comma Separated)</td>
	  <td align="left"><textarea name="config[metaKeyWords]" cols="35" rows="3" class="textbox"><?php echo $config['metaKeyWords']; ?></textarea></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong>Site/Company Name:</strong></td>
	  <td><input name="config[storeName]" type="text" size="35" class="textbox" value="<?php echo $config['storeName']; ?>" /></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong>Site Address:</strong></td>
	  <td><textarea name="config[storeAddress]" cols="35" rows="3" class="textbox"><?php echo $config['storeAddress']; ?></textarea></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong>Country:</strong></td>
      <td>
	  <?php
	  $countries = $db->select("SELECT * FROM ".$glob['dbprefix']."countries");
	  ?>

	<select name="config[siteCountry]" id="siteCountry" onChange="updateCounty(this.form);">
	<?php
	for($i = 0, $maxi = count($countries); $i < $maxi; ++$i)
	{
	?>
	<option value="<?php echo $countries[$i]['id']; ?>" <?php if($countries[$i]['id'] == $config['siteCountry']) echo 'selected="selected"'; ?>><?php echo $countries[$i]['printable_name']; ?></option>
	<?php
	}
	?>
	</select>
	  </td>
	</tr>
	<tr>
	  <td width="30%" class="tdText"><strong>County/State/Zone:</strong></td>
	  <td>
	    <?php
	  $counties = $db->select("SELECT * FROM ".$glob['dbprefix']."counties WHERE `countryId` = '".$config['siteCountry']."'");
	  ?>
	    <select name="config[siteCounty]" id="siteCounty">
	      <option value="" <?php if(empty($config['siteCounty'])) echo 'selected="selected"'; ?>>-- N/A --</option>
	      <?php
	  if($counties)
	  {
	   for($i = 0, $maxi = count($countries); $i < $maxi; ++$i)
	   { ?>
	      <option value="<?php echo $counties[$i]['id']; ?>" <?php if($counties[$i]['id']==$config['siteCounty']) echo 'selected="selected"'; ?>><?php echo $counties[$i]['name']; ?></option>
	      <?php
	    }
	  } ?>
	      </select></td>
	  </tr>
	<tr>
  <td>&nbsp;</td>
  <td><input name="submit" type="submit" class="submit" id="submit" value="Update Settings" /></td>
</tr>
</table>
</div>
<div id="Options">
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">

	<tr>
		<td colspan="2" class="tdTitle" id="styles_misc"><strong>Options</strong></td>
	</tr>
    <tr>
      <td class="tdText"><strong>Number of Records Per Page:</strong>(admin)</td>
      <td align="left"><input type="text" size="3" class="textbox" name="config[displayitemRows]" value="<?php echo $config['displayitemRows']; ?>" /></td>
    </tr>
    <tr>
      <td width="30%" class="tdText"><strong>Number of User Per Page</strong></td>
      <td align="left"><input type="text" size="3" class="textbox" name="config[displayUser]" value="<?php echo $config['displayUser']; ?>" /></td>
    </tr>
    <tr>
      <td width="30%" class="tdText"><strong>Number of Recent Winners</strong></td>
      <td align="left"><input type="text" size="3" class="textbox" name="config[displayRecentWinners]" value="<?php echo $config['displayRecentWinners']; ?>" /></td>
    </tr>
      <tr>
        <td width="30%" class="tdText"><strong>Directory Symbol:</strong></td>
        <td align="left"><input type="text" size="20" class="textbox" name="config[dirSymbol]" value="<?php echo $config['dirSymbol']; ?>" /></td>
      </tr>
	<tr>
	  <td width="30%" class="tdText"><strong>Email Name:</strong><br />
      (This is used as the send name of site emails.)</td>
	  <td align="left"><input type="text" size="35" class="textbox" name="config[masterName]" value="<?php echo $config['masterName']; ?>" /></td>
    </tr>
	<tr>
	<td width="30%" class="tdText"><strong>Email Address:</strong><br />
  (This is used as the email address in site emails.)</td>
		<td align="left"><input type="text" size="35" class="textbox" name="config[masterEmail]" value="<?php echo $config['masterEmail']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong>Mail Sending Method:</strong><br />
    (mail() recommended) </td>
		<td align="left">
			<select name="config[mailMethod]" class="textbox">
				<option value="mail" <?php if($config['mailMethod']=="mail") echo 'selected="selected"'; ?>>mail()</option>
				<option value="smtp" <?php if($config['mailMethod']=="smtp") echo 'selected="selected"'; ?>>SMTP</option>
			</select>		</td>
	</tr>
	<tr>
	  <td class="tdText">SMTP Host:</td>
	  <td align="left" class="tdText"><input type="text" size="25" class="textbox" name="config[smtpHost]" value="<?php echo $config['smtpHost']; ?>" />
      (Default: localhost)</td>
    </tr>
		<tr>
		  <td class="tdText">SMTP Port:</td>
		  <td align="left" class="tdText"><input type="text" size="3" class="textbox" name="config[smtpPort]" value="<?php echo $config['smtpPort']; ?>" />
	      (Default: 25)</td>
    </tr>
		<tr>
		  <td class="tdText">Use Authentication?</td>
		  <td align="left" class="tdText"><select name="config[smtpAuth]" class="textbox">
            <option value="false" <?php if($config['smtpAuth']=="false") echo 'selected="selected"'; ?>>No</option>
			<option value="true" <?php if($config['smtpAuth']=="true") echo 'selected="selected"'; ?>>Yes</option>
          </select>
		  (Default: No)</td>
    </tr>
		<tr>
		  <td class="tdText">SMTP Username:</td>
		  <td align="left"><input type="text" size="25" class="textbox" name="config[smtpUsername]" value="<?php echo $config['smtpUsername']; ?>" /></td>
    </tr>
		<tr>
		  <td class="tdText">SMTP Password:</td>
		  <td align="left"><input type="text" size="25" class="textbox" name="config[smtpPassword]" value="<?php echo $config['smtpPassword']; ?>" /></td>
    </tr>
		<tr>
	<td width="30%" class="tdText"><strong>Max Upload Filesize:</strong><br />
	  (Under <a href=\"http://www.google.com/search?q=2097152+bytes+to+megabytes\" target=\"_blank\" class=\"txtLink\">2097152 bytes</a> recommended)</td>
		<td align="left"><input type="text" size="10" class="textbox" name="config[maxImageUploadSize]" value="<?php echo $config['maxImageUploadSize']; ?>" /></td>
	</tr>
	<tr>
	  <td width="30%" class="tdText"><strong>Max Session Length:</strong><br />
      (Seconds)</td>
	  <td align="left"><input type="text" size="10" class="textbox" name="config[sqlSessionExpiry]" value="<?php echo $config['sqlSessionExpiry']; ?>" /></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong>Turn off rich text editor?</strong></td>
	  <td align="left" class="tdText">
	    <select name="config[richTextEditor]" class="textbox">
	      <option value="0" <?php if($config['richTextEditor']==0) echo 'selected="selected"'; ?>>Yes</option>
	      <option value="1" <?php if($config['richTextEditor']==1) echo 'selected="selected"'; ?>>No</option>
        </select> Height: <input type="text" name="config[rteHeight]" size="5" class="textbox" value="<?php echo $config['rteHeight']; ?>" />
	    
	    <select name="config[rteHeightUnit]" class="textbox">
	      <option value="%" <?php if($config['rteHeightUnit']=='%') echo 'selected="selected"'; ?>>%</option>
	      <option value="" <?php if(empty($config['rteHeightUnit'])) echo 'selected="selected"'; ?>>px</option>
        </select>
      </td>
    </tr>
	<td width="30%" class="tdText"><strong>Google Analytics ID</strong><br />
	  This can be found in the code provided by Google, and will look something like "UA-######-#"</td>
	  <td align="left"><input type="text" size="10" class="textbox" name="config[google_analytics]" value="<?php echo $config['google_analytics']; ?>" /></td>
	  </tr>
      <tr>
	  <td width="30%" class="tdText"><strong>Currency</strong></td>
	  <td align="left">
	  <?php
	  $currencies = $db->select("SELECT name, code FROM ".$glob['dbprefix']."currencies WHERE active = 1 ORDER BY name ASC");
		?>
		<select name="config[defaultCurrency]">
		<?php
		for($i = 0, $maxi = count($currencies); $i < $maxi; ++$i){
		?>
		<option value="<?php echo $currencies[$i]['code']; ?>" <?php if($currencies[$i]['code']==$config['defaultCurrency']) echo 'selected="selected"'; ?>><?php echo $currencies[$i]['name']; ?></option>
		<?php
		}
	  ?>
	  </select>	  </td>
    </tr>
    <tr>
	  <td width="30%" class="tdText"><strong>Phone:</strong></td>
	  <td><input type="text" size="20" class="textbox" name="config[phone]" value="<?php echo $config['phone']; ?>" /></td>
	  </tr>
 <tr>
   <td>&nbsp;</td>
   <td><input name="submit" type="submit" class="submit" id="submit" value="Update Settings" /></td>
</tr>
</table>
</div>
<div id="GDSettings">
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">

	<tr>
		<td colspan="2" class="tdTitle" id="gd_settings"><strong>GD Settings</strong></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong>GD Version:</strong></td>
		<td align="left">
			<select name="config[gdversion]" class="textbox">
				<option value="2" <?php if($config['gdversion']==2) echo 'selected="selected"'; ?>>2</option>
				<option value="0" <?php if($config['gdversion']==0) echo 'selected="selected"'; ?>>N/A</option>
			</select>		</td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong>Allow GIF Support: (Please make sure this is enabled on your server)</strong></td>
		<td align="left">
			<select name="config[gdGifSupport]" class="textbox">
				<option value="0" <?php if($config['gdGifSupport']==0) echo 'selected="selected"'; ?>>No</option>
				<option value="1" <?php if($config['gdGifSupport']==1) echo 'selected="selected"'; ?>>Yes</option>
			</select>		</td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong>Thumbnail Size:</strong></td>
		<td align="left"><input type="text" size="4" class="textbox" name="config[gdthumbSize]" value="<?php echo $config['gdthumbSize']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong>Max Image Size:</strong></td>
		<td align="left"><input type="text" size="4" class="textbox" name="config[gdmaxImgSize]" value="<?php echo $config['gdmaxImgSize']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong>GD Image Quality:</strong><br />
</td>
		<td align="left"><input type="text" size="3" class="textbox" name="config[gdquality]" value="<?php echo $config['gdquality']; ?>" /></td>
	</tr><tr>
<td>&nbsp;</td>
<td><input name="submit" type="submit" class="submit" id="submit" value="Update Settings" /></td>
</tr>
</table>
</div>
<div id="TimeDate">
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">

	<tr>
		<td colspan="2" class="tdTitle" id="time_and_date"><strong>Time &amp; Date</strong></td>
    </tr>
	<tr>
	<td width="30%" class="tdText"><strong>Time Format:</strong><br />
    (See <a href='http://www.php.net/strftime' target='_blank' class='txtLink'>www.php.net/strftime</a>)</td>
		<td align="left"><input type="text" size="20" class="textbox" name="config[timeFormat]" value="<?php echo $config['timeFormat']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong>Time Offset:</strong><br />
    (Seconds - Used for servers in different timezone)</td>
		<td align="left"><input name="config[timeOffset]" type="text" class="textbox" value="<?php echo $config['timeOffset']; ?>" size="20" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong>Date Format:</strong><br />
    (See <a href='http://www.php.net/date' target='_blank' class='txtLink'>www.php.net/date</a>)</td>
		<td align="left"><input type="text" size="35" class="textbox" name="config[dateFormat]" value="<?php echo $config['dateFormat']; ?>" /></td>
	</tr><tr>
<td>&nbsp;</td>
<td><input name="submit" type="submit" class="submit" id="submit" value="Update Settings" /></td>
</tr>
</table>
</div>
<div id="OfflineSettings">
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">
	<tr>
	  <td colspan="2" class="tdTitle" id="off_line_settings">Offline Settings</td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong>Turn off site?</strong></td>
	  <td align="left">
	  <select name="config[offLine]" class="textbox">
        <option value="1" <?php if($config['offLine']==1) echo 'selected="selected"'; ?>>Yes</option>
        <option value="0" <?php if($config['offLine']==0) echo 'selected="selected"'; ?>>No</option>
      </select></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong>Allow administrators to view site off line? (Requires admin session)</strong></td>
	  <td align="left">
	  <select name="config[offLineAllowAdmin]" class="textbox">
        <option value="1" <?php if($config['offLineAllowAdmin']==1) echo 'selected="selected"'; ?>>Yes</option>
        <option value="0" <?php if($config['offLineAllowAdmin']==0) echo 'selected="selected"'; ?>>No</option>
      </select></td>
    </tr>
	<tr>
	  <td valign="top" class="tdText"><strong>Off line message:</strong></td>
	  <td align="left">&nbsp;</td>
    </tr>
	<tr>
	  <td colspan="2" valign="top" class="tdText">
	    <?php
			require($glob['adminFolder']."/includes".CC_DS."rte".CC_DS."fckeditor.php");
			$oFCKeditor = new FCKeditor('FCKeditor');
			$oFCKeditor->BasePath = $GLOBALS['rootRel'].$glob['adminFolder'].'/includes/rte/';
			$oFCKeditor->Value = stripslashes(base64_decode($config['offLineContent']));
			if (!$config['richTextEditor']) {
				$oFCKeditor->off = true;
			}
			$oFCKeditor->Create();
		?>
	  </td>
    </tr>

	<tr>
	<td width="30%" class="tdText">&nbsp;</td>
	  <td align="left">
	  <input name="submit" type="submit" class="submit" id="submit" value="Update Settings" /></td>
	</tr>
</table>
</div>
<div id="SearchEngineOptimization">
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%" id="sef">
	<tr>
	  <td colspan="2" class="tdTitle">Search Engine Optimization</td>
    </tr>
	<tr>
	  <td width="30%" class="tdText">Use search engine friendly URL?</td>
	  <td align="left">
	  <select name="config[sef]" class="textbox">
        <option value="1" <?php if($config['sef']==1) echo 'selected="selected"'; ?>>Yes</option>
        <option value="0" <?php if($config['sef']==0) echo 'selected="selected"'; ?>>No</option>
      </select></td>
    </tr>
<?php
if($config['sef']) {
?>
	<?php
	if (in_array($config['sefserverconfig'], array(0))) {
	?>
	<tr>
	  <td valign="top" class="tdText"><p><strong>.htaccess</strong>
	  <br />To use either <em>&quot;Apache Rewrite&quot;</em> or <em>&quot;Apache Directory 'Lookback' And ForceType&quot;</em> it is required that a <em>&quot;.htaccess&quot;</em> file is created in the root directory of your store. To do this please open a text editor such as Notepad or TextEdit, copy and paste the contents of the text area opposite into it and save it as <em>&quot;htaccess.txt&quot;</em>. Upload this file to your server and rename it to <em>&quot;.htaccess&quot;</em>.</p>
<p>If a server error message is displayed please delete the .htaccess file and use either <em>&quot;Use FTP Generated FTP Pages&quot;</em> or <em>&quot;Apache Directory 'Lookback'&quot;</em>.</td>
	  <td align="left" class="tdText">
	  	<textarea cols="50" rows="15" wrap="off"><?php
	  	$htaccess_conts = file_get_contents($glob['adminFolder'].CC_DS.'sources'.CC_DS.'settings'.CC_DS.'seo-htaccess.txt');
	  	if($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) {
			$htaccess_conts = str_replace("RewriteEngine On","RewriteEngine On\nRewriteBase ".$glob['rootRel'],$htaccess_conts);
		}
		echo $htaccess_conts;
	  	?></textarea><br />
		<br />
		<input type="submit" name="install_htaccess" class="submit" id="install_htaccess" value="Install .htaccess" />
	  </td>
    </tr>
    <?php
	} 
}
?>
<tr>
	<td width="30%" class="tdText">&nbsp;</td>
	  <td align="left">
	  <input name="submit" type="submit" class="submit" id="submit" value="Update Settings" /></td>
	</tr>
</table>
</div>
<div id="Donation">
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="mainTable">
	<tr>
	  <td colspan="2" class="tdTitle" id="meta_data"><strong>Donation Information</strong></td>
	  </tr>
	<tr>
	  <td width="30%" class="tdText"><strong>Donate Your Time Today!:</strong></td>
	  <td><textarea name="config[donate]" cols="35" rows="3" class="textbox"><?php echo $config['donate']; ?></textarea></td>
	  </tr>
<tr>
  <td>&nbsp;</td>
  <td><input name="submit" type="submit" class="submit" id="submit" value="Update Settings" /></td>
</tr>
</table>
</div>
</div>
</form>