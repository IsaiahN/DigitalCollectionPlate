<ul>
<li <?php if($cpage=="index" || $cpage=="" || $cpage=="signup" || $cpage=="signin" || $cpage=="fundeedonation" || $cpage=="sitemap" || $cpage=="causes" || $cpage=="thanks" || $cpage=="forgot-password" || $cpage=="faqs" || $cpage=="how-to" || $cpage=="privacy-policy" || $cpage=="terms-conditions"){?>class="active" <? }?>><a href="index.<?php echo $ext;?>">Home</a></li>
<?php
$recordset = mysql_query("SELECT * FROM `pages`  WHERE  status='1' AND top_menu='1'   ORDER BY  priority ASC"); // query
$all = array();
while($row = mysql_fetch_assoc($recordset)) {
      $all[$row['pageId']] = $row; // add everything on one big array
}

$top = array_filter($all, 'topdoc'); // extract the top parents (with parent = 0)
$depth = 0; // depth of our traversing, this is for indentation


foreach($top as $doc) { // for every top parent

      buildchild($doc); // build its children
}
?>
</ul>
<?
function buildchild($doc) { // the recursive builder
      global $depth, $fullindents,$ext;
      $depth++;
	  $string= strtolower($doc['url']);
	$links=str_replace(" ","-",$string);
	if($doc['url_openin']==1)
	{
	$target= 'target="_blank"';
	}else
	{
	$target= '';
	}
$config = fetchdbconfig("config");
if($config['sef']==1)
{
	$ext="html";
} 
elseif($config['sef']==0)
{
	$ext="php";
}
$curPageName=substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
$getExt = explode ('.', $curPageName);
$cpage=$getExt[0];
if($cpage==$links){
$css='class=active';
}

// get host name from URL
preg_match('@^(?:http://)?([^/]+)@i',$doc['url'], $matches);
$host = $matches[1];
// get last two segments of host name
preg_match('/[^.]+\.[^.]+$/', $host, $matches);
if($matches[0])
{
$link = '<a href="'.$links.'" title="'.$doc['title'].'" '.$target.'>';
}
else
{
$link = '<a href="'.$links.".".$ext.'" title="'.$doc['title'].'" '.$target.'>';
}

		
      if ($fullindents) {
          
		  
			 echo t() . "<li ".$css.">\n" . t(1) . $link . $doc['name'] . '</a>';  
		
			
      }
      else {
            
			
			echo t() . "<li ".$css.">" . $link . $doc['name'] .'</a>'; 
		
			
      }

      if ($children = haschildren($doc['pageId'])) { // check if the current element has children and build them
            $ul = true;
            $depth++;
            echo "\n" . t() . "<ul>\n";
            foreach($children as $child) {
                  buildchild($child);
            }
            echo t() . "</ul>";
            $depth--;
      }
      if ($fullindents) {
            echo "\n" . t() . "</li>\n";
      }
      else {
            if ($ul) echo "\n" . t();
            $ul = false;
            echo "</li>\n";
			
      }
      $depth--;
}
function haschildren($id) { // checks if an element has children
      global $all;
      foreach($all as $doc) {
            if ($doc['father_id'] == $id) {
                  $result[] = $doc;
                  unset($all[$doc['pageId']]);      // unset this value so that we dont
                                                            // search it again when looking through
                                                            // the array next time, this gives a
                                                            // ~20% increase in speed to the script
            }
      }
      if (count($result) > 0) {
            return $result; // return the children if found
      }
      return false; // otherwise return false
}
function t($x=null,$s="  ") { // indentation function, you can change spaces to \t (tabs) if you like
      global $depth;
      return str_repeat($s, $depth+$x);
}
function topdoc($row) { // used for the array_filter to weed out menues with parent != 0
      return $row['father_id'] == 0;
}
?>
