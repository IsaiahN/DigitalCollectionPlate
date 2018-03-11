<?php 
if(!defined('CC_INI_SET')){ die("Access Denied"); }

permission("maintenance", 'read', true);

if (isset($_GET['filemanager'])) {
	include_once CC_ROOT_DIR.CC_DS.'classes'.CC_DS.'filemanager.class.php';
	$filemanager	= new Filemanager();
	$filemanager->buildDatabase(true);
	$msg = "<p class='infoText'>Image database has been updated.</p>";
}

if($_GET['emptyTransLogs']==1) {

	$truncate = $db->misc("TRUNCATE TABLE `".$glob['dbprefix']."transactions`"); 
	
	if ($truncate) {
		$msg = "<p class='infoText'>Payment transaction logs have been cleared.</p>";
	} else {
		$msg = "<p class='warnText'>Payment transaction logs have not been cleared.</p>";
	}

}elseif($_GET['uploadSize']==1) {
	$dirArray = walkDir(CC_ROOT_DIR .CC_DS."images".CC_DS."uploads", true, 0, 0, false, $int = 0);
	$size = 0;
	
	if(is_array($dirArray)){

		foreach($dirArray as $file) {
			
			if(file_exists($file)){
				$size = filesize($file) + $size;
			}
		
		}
	
	}

	$rebuild['uploadSize'] = $size;
	$msg = writeDbConf($rebuild,"config", $config, true);
	
} else if ($_GET['catCount'] == 1) {
	
	## Lets override the default execution time
	@set_time_limit(0);
	$success = false;
	
	
	
	## Set the number of products in all categories to 0
	$record['noProducts'] = 0;
	$update = $db->update($glob['dbprefix'].'category', $record, '');
	
	## Count primary categories of products
#	$prodquery	= sprintf("SELECT COUNT(productId) as Count, cat_id FROM %sinventory WHERE disabled = '0' GROUP BY cat_id", $glob['dbprefix']);
#	$products	= $db->select($prodquery);
#	if ($products) {
#		foreach ($products as $product) {
#			$db->categoryNos($product['cat_id'], '+', $product['Count']);
#		}
#		$success = true;
#	}
	
	## Delete records from cats_idx if the productId isn't in the inventory
	$idxquery = sprintf("DELETE FROM %1\$scats_idx WHERE productId NOT IN (SELECT DISTINCT productId FROM %1\$sinventory WHERE disabled = '0')", $glob['dbprefix']);
	$db->misc($idxquery);
	
	## Delete duplicate cat_idx rows credit to Sir Willaim. Thanks Bill!!
	$sql = "SELECT * FROM ".$glob['dbprefix']."cats_idx ORDER BY `productId`, `cat_id` ASC";
	$results = $db->select($sql);
	
	$noResults = count($results);
	
	if($results) {
		for($i=0, $noResults; $i<$noResults; $i++) {
			if($thiscat == $results[$i]['cat_id'] && $thisprod == $results[$i]['productId']) {
				$results[$i]['flag'] = true;
				$flagged = true;
			} else {
				$results[$i]['flag'] = false;
			}
			$thiscat = $results[$i]['cat_id'];
			$thisprod = $results[$i]['productId'];
		} // end for loop
	
		if($flagged == true) {
			foreach($results as $product) {
				if($product['flag'] == true) {
					$db->delete($glob['dbprefix']."cats_idx", "`id` = ".$db->MySQLSafe($product['id']));
				}
			} // end foreach loop

		} 

	} 
		
	## Count the number of products in the cats_idx table by category
	$countQuery	= sprintf("SELECT COUNT(cat_id) as count, cat_id FROM %1\$scats_idx WHERE cat_id IN(SELECT DISTINCT cat_id FROM %1\$scats_idx WHERE 1) GROUP BY cat_id", $glob['dbprefix']);
	$catCount	= $db->select($countQuery);
	
	if ($catCount) {
		foreach ($catCount as $category) {
			## Set the number of products in each category
			$db->categoryNos($category['cat_id'], '+', $category['count']);
		}
		$success = true;
	}
	
	buildCatList();
		
	if ($success) {
		$msg .= "<p class='infoText'>Category recount successfull.</p>";
	} else {
		$msg .= "<p class='warnText'>There are no products in the database so count could not be completed.</p>";
	}
	
} else if ($_GET['prodViews'] == 1) {

	
	// set noProducts in all categories to 0
	$record['popularity'] = $db->mySQLSafe(0);
	$update = $db->update($glob['dbprefix']."inventory", $record, $where="");
	
	if($update) {
	
		$msg .= "<p class='infoText'>Product views all set to zero.</p>";
	
	} else {
	
		$msg .= "<p class='warnText'>Product views could not be reset.</p>";
	
	}
	
} elseif($_GET['clearSearch']==1) {
	// set noProducts in all categories to 0
	$truncate = $db->misc("TRUNCATE TABLE `".$glob['dbprefix']."search`"); 
	
	if($truncate == TRUE) {
	
		$msg = "<p class='infoText'>Search terms reset.</p>";
	
	} else {
	
		$msg = "<p class='warnText'>Search terms could not be reset.</p>";
	
	}
	
} elseif($_GET['orderCount']==1) {
	// set noOrders for all products to 0
	$record['noOrders'] = $db->mySQLSafe(0);
	$update = $db->update($glob['dbprefix']."customer", $record, $where="");
	
	// get all customers
	$customers = $db->select("SELECT * FROM ".$glob['dbprefix']."customer");
	
	if($customers==TRUE){
		for ($i=0; $i<count($customers); $i++){
			$noOrders = $db->numrows("SELECT * FROM ".$glob['dbprefix']."order_sum WHERE customer_id=".$db->mySQLSafe($customers[$i]['customer_id']));
			$record['noOrders'] = $noOrders;
			$result = $db->update($glob['dbprefix']."customer", $record, "customer_id=".$db->mySQLSafe($customers[$i]['customer_id']));
				
		}
		
		$msg = "<p class='infoText'>Custmers order count successful recalculated.</p>";
	} else {
		$msg = "<p class='warnText'>No customers exist in the database.</p>";
	}

/* Removed as it can't be used due to mult folder paths	
} else if ($_GET['thumbs'] == 1) {
	$path = CC_ROOT_DIR .CC_DS."images".CC_DS."uploads".CC_DS."thumbs";
	$dirArray = walkDir($path, false, 0, 0, false, $int = 0);
	unset($dirArray['max']);
	if (is_array($dirArray)) {
		foreach ($dirArray as $file) {
			$masterFilename = str_replace(CC_ROOT_DIR .CC_DS."images".CC_DS."uploads".CC_DS."thumbs".CC_DS."thumb_","",$file);
			// delete files that dont contain thumb_
			if (!strstr($file, "thumb_")) {
				echo $file." - arse<hr />";
				unlink($file);
			} else if (!file_exists(CC_ROOT_DIR .CC_DS."images".CC_DS."uploads".CC_DS. $masterFilename)) {
				unlink($file);
			}
		} 
		$msg = "<p class='infoText'>".$lang['admin']['misc_redundant_thumbs_gone']."</p>";
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['misc_thumbs_folder_empty']."</p>";
	}
*/
	
} else if ($_GET['clearLogs'] == 1) {
	$sql = sprintf("TRUNCATE TABLE %sadmin_log", $glob['dbprefix']);
	$db->misc($sql);
	
	$msg = '<p class="warnText">Admin logs have been deleted.</p>';
	
} else if ($_GET['clearSession'] == 1) {
	$sql = sprintf('TRUNCATE TABLE %sadmin_sessions', $glob['dbprefix']);
	$db->misc($sql);
	
	$msg = '<p class="warnText">Admin sessions have been cleared.</p>';
}

require $glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php";

?>
<p class="pageTitle">Rebuild &amp; Recount</p>

<?php if (isset($msg)) echo msg($msg); ?>
<table border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
	<td class="tdTitle" colspan="2">Operation</td>
  </tr>
  <tr>
	<td class="tdText">Update Image Database</td>
	<td class="tdText"><input name="button" type="button" value="Update" class="submit" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=maintenance/rebuild&amp;filemanager=1');return document.returnValue" /></td>
  </tr>
  <tr>
	<td class="tdText">Recalculate upload folder size</td>
	<td class="tdText"><input name="button" type="button" value="Update" class="submit" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=maintenance/rebuild&amp;uploadSize=1');return document.returnValue" /></td>
  </tr>
  <tr>
	<td class="tdText">Rebuild category product count</td>
	<td class="tdText"><input name="button" type="button" value="Update" class="submit" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=maintenance/rebuild&amp;catCount=1');return document.returnValue" /></td>
  </tr>
  <tr>
	<td class="tdText">Rebuild number customer orders</td>
	<td class="tdText"><input name="button" type="button" value="Update" class="submit" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=maintenance/rebuild&amp;orderCount=1');return document.returnValue" /></td>
  </tr>
  <tr>
	<td class="tdText">Reset number product views</td>
	<td class="tdText"><input name="button" type="button" value="Update" class="submit" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=maintenance/rebuild&amp;prodViews=1');return document.returnValue" /></td>
  </tr>
  <tr>
	<td class="tdText">Clear search history<strong></strong></td>
	<td class="tdText"><input name="button" type="button" value="Update" class="submit" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=maintenance/rebuild&amp;clearSearch=1');return document.returnValue" /></td>
  </tr>
  
  <tr>
	<td class="tdText">Delete admin logs<strong></strong></td>
	<td class="tdText"><input name="button" type="button" value="Update" class="submit" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=maintenance/rebuild&amp;clearLogs=1');return document.returnValue" /></td>
  </tr>
  <tr>
	<td class="tdText">Delete admin sessions<strong></strong></td>
	<td class="tdText"><input name="button" type="button" value="Update" class="submit" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=maintenance/rebuild&amp;clearSession=1');return document.returnValue" /></td>
  </tr>
<?php
if ($config['cache'] ==1) {
?>
  <tr>
	<td class="tdText">Clear Cache</td>
	<td class="tdText"><input name="button" type="button" value="Update" class="submit" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=maintenance/rebuild&amp;clearCache=1');return document.returnValue" /></td>
  </tr>
<?php
}
?>
<tr>
	<td class="tdText">Clear Payment Transaction Logs<strong></strong></td>
	<td class="tdText"><input name="button" type="button" value="Update" class="submit" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=maintenance/rebuild&amp;emptyTransLogs=1');return document.returnValue" /></td>
  </tr>

</table>