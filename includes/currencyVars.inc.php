<?php
if (!defined('CC_INI_SET')) die("Access Denied");
$page = sanitizeVar($_GET['_a']);

if (isset($override[$page]) && $override[$page] == true) {
	$cCode = $config['defaultCurrency'];
} else if (!empty($cc_session->ccUserData['currency'])) {
	$cCode = $cc_session->ccUserData['currency'];
	
} else if (!empty($order[0]['currency'])) {
	$cCode = $order[0]['currency'];
	
} else {
	
$cCode = $config['defaultCurrency'];
}
$query= sprintf("SELECT value, symbolLeft, symbolRight, decimalPlaces, name, decimalSymbol FROM %scurrencies WHERE code=%s", $glob['dbprefix'], $db->mySQLSafe($cCode));
$currencyVars= $db->select($query);
?>