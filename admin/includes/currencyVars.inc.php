<?php
if (!defined('CC_INI_SET')) die("Access Denied");
$query = sprintf("SELECT value, symbolLeft, symbolRight, decimalPlaces, name FROM %scurrencies WHERE code=%s", $glob['dbprefix'], $db->mySQLSafe($config['defaultCurrency']));
$currencyVars = $db->select($query);
?>