<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }
require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");
?>
<p class="pageTitle">Server Info</p>
<p><span class="copyText">The information below shows your current server environment settings. This contains all kinds of information  which may need to be changed if you are experiencing problems with ccshop. N.B. If you have a shared server or virtual hosting it is likely that you will have limited access to modify settings. The</span> <a href='http://www.php.net/ini_set' target='_blank' class='txtLink'>ini_set()</a> <span class='copyText'> function can often be used to override these settings.</span></p>
<center class="copyText">
<?php
ob_start();
phpinfo();
$phpinfo = ob_get_contents();
ob_end_clean();

// rip out head tags and content
$phpinfo = preg_replace("/(\<head)(.*?)(head>)/si", "", $phpinfo);
// add class to links
$phpinfo = str_replace("<a href", "<a class=\"txtLink\" href", $phpinfo);
// remove doctype
$phpinfo = preg_replace("/(\<!DOCTYPE)(.*?)(\">)/si", "", $phpinfo);
// remove other elements
$phpinfo = str_replace(array("<body>","</body>","<html>","</html>","<hr />"), "", $phpinfo);
// reclass
$phpinfo = str_replace("class=\"h\"","class=\"tdTitle\"",$phpinfo);
// reclass & style
$phpinfo = str_replace("class=\"e\"","class=\"tdText\" style=\"font-weight: bold;\"",$phpinfo);
$phpinfo = str_replace("class=\"v\"","class=\"tdText\"",$phpinfo);
// no cell spacing
$phpinfo = str_replace("<table","<table class=\"mainTable\" cellspacing=\"0\"",$phpinfo);
// bump up cell padding
$phpinfo = str_replace("cellpadding=\"3\"","cellpadding=\"4\"",$phpinfo);
echo $phpinfo;
?> 
</center>