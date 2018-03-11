<?php
//write text file
$query = "SELECT * FROM ".$glob['dbprefix']."pages WHERE  status='1'  ORDER BY  priority  ASC";
$htaccessArray= $db->select($query);
$htaccessfile =$glob['adminFolder'].CC_DS.'sources'.CC_DS.'settings'.CC_DS.'seo-htaccess.txt';
$file = fopen($htaccessfile,"w");
$_htaccess="";
$_htaccess.="RewriteEngine On\n";
$_htaccess.="RewriteRule ^index.html$ index.php\n";
$_htaccess.="RewriteRule ^logout.html$ logout.php\n";
if($htaccessArray == TRUE){
	for ($i=0; $i<count($htaccessArray); $i++){ 
		 $string= strtolower($htaccessArray[$i]['url']);
		$link=str_replace(" ","-",$string);
		$_htaccess.="RewriteRule ^".$link.".html$ ".$link.".php\n";
	}
} 
$_htaccess .="RewriteCond %{QUERY_STRING} (.*)$\n";
$_htaccess .="RewriteRule  causes-([0-9]+)(\.[a-z]{3,4})?(.*)$  causes.php?causeId$1&%1 [NC]\n";
$_htaccess .="RewriteRule  news-([0-9]+)(\.[a-z]{3,4})?(.*)$  news.php?newsId$1&%1 [NC]\n";
$_htaccess.="RewriteRule ^u/(.*)$ http://www.digitalcollectionplate.com/fundeedonation.php?link=$1 [L,R,QSA]\n";
$_htaccess.="RewriteRule ^f/invalid http://www.digitalcollectionplate.com/dashboard.php?act=invalidfundee [L,R,QSA]\n";
$_htaccess.="ErrorDocument 404 /404.php\n";
fwrite($file, $_htaccess);
fclose($file);
?>



