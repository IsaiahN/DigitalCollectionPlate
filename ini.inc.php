<?php
## Set error reporting to all but notices
@error_reporting(E_ALL ^ E_NOTICE);
## display errors
@ini_set('display_errors', true);
## Disable 'Register Globals' for security
@ini_set('register_globals', false);
## Disable '<?' style php short tags for xml happiness
@ini_set('short_open_tag', false);
## Set argument separator to &amp; from & for XHTML validity
@ini_set('arg_separator.output', '&amp;');
## Automatically detect line endings
@ini_set('auto_detect_line_endings', true);
## turn off magic quotes if on
@ini_set('magic_quotes_gpc', false);
@set_magic_quotes_runtime(false);

## NEW - Let's enable page compression by default, if output_buffering is not enabled
if (!ini_get('output_buffering')) {
	@ini_set('zlib.output_compression', true);
	@ini_set('zlib.output_compression_level', 5);
}
if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
	date_default_timezone_set('Europe/London');
}


/************* START INITIAL SECURITY CHECKS *************/

## Check for possible global overwrite and end script execution if detected

function unset_globals() {
	if (ini_get('register_globals')) {
		if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) {
			$die = "<h1 style='font-family: Arial, Helvetica, sans-serif; color: red;'>Security Warning</h1><p style='font-family: Arial, Helvetica, sans-serif; color: #000000;'>\nGLOBALS overwrite attempt detected! Script execution has been terminated.</p>\n";
			die($die);
		}
		
		## Variables that shouldn't be unset
		$skip = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
		$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
		foreach ($input as $key => $value) {
			if (!in_array($key, $skip) && isset($GLOBALS[$key])) {
				unset($GLOBALS[$key]);
			}
		}
	}
}

## Run the function
unset_globals();


function has_zend_optimizer() {
	# Detect Zend Optimizer
	ob_start();
	phpinfo(INFO_GENERAL);
	$info = ob_get_contents();
	ob_end_clean();
	
	$info = str_replace('&nbsp;', ' ', $info);
	return eregi('Zend Optimizer', $info);
} 

function has_ioncube_loader() {
	# Detect ionCube
	return extension_loaded('ionCube Loader');
}

class clean_data {
	function clean_data(&$data) {
		if (is_array($data)) {
			foreach ($data as $key => $val) {
				/*
				The keys should usually not contain any meta characters in their names.
				If so this is possibly an attack attempt.
				*/
				if (preg_match('#([^a-z0-9\-\_\:\@\|])#i', urldecode($key))) {
					echo urldecode($key);
					$die = "<h1 style='font-family: Arial, Helvetica, sans-serif; color: red;'>Security Warning</h1><p style='font-family: Arial, Helvetica, sans-serif; color: #000000;'>\nParsed array keys can not contain illegal characters! Script execution has been halted.</p><p style='font-family: Arial, Helvetica, sans-serif; color: #000000;'>It may be possible to fix this error by deleting your browsers cookies and refresh this page.</p>\n";
					die($die);
				}
				## keys to skip
				$skipKeys = array('FCKeditor');
				## Multi dimentional arrays.. dig deeper.
				if (is_array($val)) {
					$this->clean_data($data[$key]);
				} else if (!empty($val) && !in_array($key, $skipKeys)) {
					$data[$key] = $this->saftey($val);
				}
			}
		} else {
			$data = $this->saftey($val);
		}
	}
	
	function saftey($val) {
		## strip null bytes
		$val = str_replace("\0", '', $val);
		## add slashes if magic quotes is off
		$val = (!get_magic_quotes_gpc()) ? addslashes($val) : $val;
		return strip_tags($val);
	}
}

$clean = new clean_data($data);

$clean->clean_data($_GET);
$clean->clean_data($_POST);
$clean->clean_data($_COOKIE);
$clean->clean_data($_REQUEST);


/************* END INITIAL SECURITY CHECKS *************/

## Version Number
$ini['ver'] 		= 	'4.0.2';

## Brute Force Protection
$ini['bftime'] 		= 	600; 				// seconds
$ini['bfattempts'] 	= 	5;					// login attempts
define("CC_SESSION_NAME", "ccUser"); 		// default session name is ccUser, this can be changed
define("CC_ADMIN_SESSION_NAME", "ccAdmin"); 	// default session name is ccUser, this can be changed
if (!empty($_GET[CC_SESSION_NAME])){
	$GLOBALS[CC_SESSION_NAME] = $_GET[CC_SESSION_NAME];
	
} else if (!empty($_COOKIE[CC_SESSION_NAME])){
	$GLOBALS[CC_SESSION_NAME] = $_COOKIE[CC_SESSION_NAME];
}

if (!empty($_GET[CC_ADMIN_SESSION_NAME])){
	$GLOBALS[CC_ADMIN_SESSION_NAME] = $_GET[CC_ADMIN_SESSION_NAME];
	
} else if (!empty($_COOKIE[CC_ADMIN_SESSION_NAME])) {
	$GLOBALS[CC_ADMIN_SESSION_NAME] = $_COOKIE[CC_ADMIN_SESSION_NAME];
}

## Stop includes, etc from being executed outside of the main application
define('CC_INI_SET', NULL);

## Define a few environmental variables
define('CC_DS', DIRECTORY_SEPARATOR);
define('CC_PS', PATH_SEPARATOR);			# Is this needed?
define('CC_ROOT_DIR', dirname(__FILE__));

?>