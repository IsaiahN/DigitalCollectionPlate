<?php


require_once 'ini.inc.php';
require_once 'includes'.CC_DS.'global.inc.php';
require_once 'includes'.CC_DS.'functions.inc.php';

## If you are behind a proxy, please configure the fields below
## Examples below are for GoDaddy hosting
$glob['proxyEnable'] = false;
$glob['proxyHost'] = ''; // e.g. proxy.shr.secureserver.net
$glob['proxyPort'] = ''; // e.g. 3128
$glob['proxyUser'] = ''; // leave this empty for godaddy
$glob['proxyPass'] = ''; // leave this empty for godaddy
switch ($key->result)
{
    case 0:
    case 1:
    case 2:
    case 3:
    case 4:
    case 5:
    case 6:
    case 7:
    case 8:
    case 9:
    case 10:
    case 11:
}
if (!isset($HTTP_SERVER_VARS['PHP_SELF']) &&  preg_match("/cc_controller.php/", $HTTP_SERVER_VARS['PHP_SELF']) || preg_match("/cc_controller.php/", $_SERVER['PHP_SELF']))
{
	exit("Access Denied");
}
require ("classes" . CC_DS . "db" . CC_DS . "db.php");
$db = new db();
$config = fetchdbconfig("config");
if (detectssl())
{
    $storeURL = $config['storeURL_SSL'];
    $rootRel = $config['rootRel_SSL'];
}
else
{
    $storeURL = $glob['storeURL'];
    $rootRel = $glob['rootRel'];
}
include_once ("classes" . CC_DS . "session" . CC_DS . "cc_admin_session.php");
$admin_session = new admin_session();
if (!preg_match("/logout|login|requestPass/", $_GET['_g']))
{
    $ccAdminData = $admin_session->get_session_data();
}
if (isset($_GET['_g']))
{
    if (!($_GET['_g'] == "modules") && !empty($_GET['module']) && substr($_GET['_g'], 0, 7) == "modules")
	{
        if ($_GET['_g'] == "modules" && !empty($_GET['module']))
		{
            $moduleData = explode("/", $_GET['module']);
            $module = $moduleData[0];
            $moduleType = $moduleData[0];
            $moduleName = $moduleData[1];
            $moduleScript = isset($moduleData[2]) ? $moduleData[2] : "index";
        }
        $moduleFile = CC_ROOT_DIR . CC_DS . "modules" . CC_DS . $module . CC_DS . $moduleName . CC_DS . CC_DS . "admin" . CC_DS . $moduleScript . ".inc.php";
        require_once (file_exists($moduleFile) ? $moduleFile : CC_ROOT_DIR . CC_DS . $glob['adminFolder'] . CC_DS . "modules" . CC_DS . "index.inc.php");
    }
	else
	{
        if ($_GET['_g'] == "modules" && !empty($_GET['module']))
		{
            $moduleData = explode("/", $_GET['module']);
            $module = $moduleData[0];
            $moduleType = $moduleData[0];
            $moduleName = $moduleData[1];
            $moduleScript = isset($moduleData[2]) ? $moduleData[2] : "index";
            $moduleFile = CC_ROOT_DIR . CC_DS . "modules" . CC_DS . $module . CC_DS . $moduleName . CC_DS . CC_DS . "admin" . CC_DS . $moduleScript . ".inc.php";
            require_once (file_exists($moduleFile) ? $moduleFile : CC_ROOT_DIR . CC_DS . $glob['adminFolder'] . CC_DS . "modules" . CC_DS . "index.inc.php");
        }
		else
		{
            require_once (mkpath($_GET['_g']));
        }
    }
}
else
{
    require_once ($glob['adminFolder'] . CC_DS . "sources" . CC_DS . "home" . CC_DS . "index.inc.php");
}
if (!isset($skipFooter))
{
    require_once ($glob['adminFolder'] . CC_DS . "includes" . CC_DS . "footer.inc.php");
}
$db->close();
?>
