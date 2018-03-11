<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }
require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'currencyVars.inc.php');
permission('orders','read', true);
$skipFooter = true;

$result = $db->select('SELECT * FROM '.$glob['dbprefix'].'order_sum INNER JOIN '.$glob['dbprefix'].'customer ON '.$glob['dbprefix'].'order_sum.customer_id = '.$glob['dbprefix'].'customer.customer_id WHERE '.$glob['dbprefix'].'order_sum.cart_order_id = '.$db->mySQLSafe($_GET['cart_order_id']));

// start: Flexible Taxes, by Estelle Winterflood
// count the number of additional taxes
$num_taxes = 1;
$config_tax_mod = fetchDbConfig('Multiple_Tax_Mod');
if ($config_tax_mod['status']) {
	for ($i=1; $i<3; ++$i) {
		if (!empty($result[0]['tax'.($i+1).'_disp'])) {
			++$num_taxes;
		}
	}

	// tax registration number(s)
	$reg_number = $db->select('SELECT reg_number FROM '.$glob['dbprefix'].'tax_details;');
	$reg_string = '';
	if (is_array($reg_number)) {
		for ($i = 0, $maxi = count($reg_number); $i < $maxi; ++$i)
		{
			if (!empty($reg_number[$i]['reg_number'])) {
				$reg_string .= $reg_number[$i]['reg_number'].'<br/>';
			}
		}
	}
}
// end: Flexible Taxes

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php echo sprintf("Order Invoice - %1\$s",$_GET['cart_order_id']);?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charsetIso; ?>" />
  <link rel="stylesheet" href="<?php echo $GLOBALS['rootRel'].$glob['adminFolder'];?>/styles/print.css" />
</head>
<body onload="window.print();">

<div id="header">
  <div id="printLabel">
  	<strong>Delivered to:</strong>
	<br />
	<div>
	<?php echo $result[0]['name_d'];
	 ?><br />
	<?php if (!empty($result[0]['companyName_d'])) echo $result[0]['companyName_d'].'<br/>'; ?>
	<?php echo $result[0]['add_1_d']; ?>,<br />
	<?php
	if (!empty($result[0]['add_2_d'])) {
		echo $result[0]['add_2_d'].",<br />";
	}
	echo $result[0]['town_d']; ?>,<br />
	<?php echo $result[0]['county_d']; ?><br />
	<?php echo $result[0]['postcode_d']; ?><br />
	<?php echo $result[0]['country_d']; ?>
	</div>
	<div class="sender">Return Address:<br /><?php echo $config['storeAddress']; ?></div>
  </div>
  <div id="storeLabel">
  	<img src="images/logos/ccLogo.gif" alt="" />
  </div>
</div>

<div class="info">
  <span class="orderid"><strong>Order ID:</strong> &nbsp; <?php echo $_GET['cart_order_id']; ?></span>
  <strong>Invoice / Receipt for:</strong> <?php echo formatTime($result[0]['time']);?>
</div>

<div class="product">
  <span class="price">Price</span>
  <strong>Product</strong>
</div>

<?php
$results = $db->select("SELECT * FROM ".$glob['dbprefix']."order_inv WHERE cart_order_id = ".$db->mySQLSafe($_GET['cart_order_id']));
for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {
?>
<div class="product">
  <span class="price"><?php echo (priceFormat($results[$i]['price']*$results[$i]['quantity'], true)); ?></span>
  <?php
  echo sprintf('%d x %s (%s)', $results[$i]['quantity'], $results[$i]['name'], $results[$i]['productCode']." ".$results[$i]['colour']." ".$results[$i]['length']);
  if (!empty($results[$i]['product_options'])) {
  	echo sprintf(' &raquo; <span class="options"><br />%s</span>', nl2br(stripslashes(str_replace("&amp;#39;","&#39;",$results[$i]['product_options']))));
  }
  ?>
</div>
<?php } ?>
<div id="totals">
  <div class="total">Subtotal: <strong><?php echo priceFormat($result[0]['prod_total'], true);?></strong></div>
<div class="total">Delivery Charge: <strong><?php echo priceFormat($result[0]['total_ship'], true);?></strong></div>

  <br />
  <div class="total">VAT: <strong><?php echo priceFormat($result[0]['total_tax'], true);?></strong></div>

  <br />
  <div class="total"><strong>Grand Total: <?php echo priceFormat($result[0]['subtotal'], true);?></strong></div>
</div>
<?php if (!empty($result[0]['extra_notes'])) { ?>
<div id="notes"><strong>Notes to send to customer:</strong> <?php echo $result[0]['extra_notes']; ?></div>
<?php } ?>
<div id="thanks">
  Thank you for shopping with us!
</div>
<div id="footer">
  <p><?php echo $config['storeAddress']; ?></p>
  <?php if (isset($reg_string)) echo "<p class='copyText'>".$reg_string."</p>"; ?>
</div>
</body>
</html>
