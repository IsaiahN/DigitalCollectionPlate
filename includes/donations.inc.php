<div class="tabbedheader">Donations To Me</div>
<div class="seprator2 long"></div>
<?php
$sqlQuery="WHERE status='3'";
$query = "SELECT ".$glob['dbprefix']."users.user_id, donations.donation_id, users.first_name, users.last_name, ".$glob['dbprefix']."users.ip_address,".$glob['dbprefix']."donations.status, donation_amount, ".$glob['dbprefix']."users.email_address FROM ".$glob['dbprefix']."donations INNER JOIN ".$glob['dbprefix']."users ON ".$glob['dbprefix']."donations.user_id = ".$glob['dbprefix']."users.user_id ".$sqlQuery." AND ".$glob['dbprefix']."donations.organization='".$ccUserData['organization']."' ORDER BY donation_id DESC";
$poPerPage = 12;
$donations= $db->select($query, $poPerPage, $_GET['po']);
$numrows = $db->numrows($query);
$pagination = paginate($numrows, $poPerPage, $_GET['po'], 'po');
if (($donations) && ($numrows > 0)) {
echo '
<div class="donationInner">
<div class="maindiv">
<div class="bgLeft"></div>
<h1>Donators</h1>
<div class="bgRight"></div>
<h2>Amount</h2>
</div>
';
	$grandTotal=0;
	for ($i=0; $i<count($donations); $i++) {
		$class=cellColor($i, 'row', 'row row2'); 
	?>
		<div class="<?php echo $class;?>">
		<div class="donatorName"><p><?php echo $donations[0]['first_name']." ".$donations[0]['last_name'];?> completed a donation</p></div>
		<div class="amount">Donated: <span>$</span><?php echo $donations[$i]["donation_amount"];?></div>
		</div>
	<?
		$grandTotal+=$donations[$i]["donation_amount"];
	}
echo '
<div class="grandTotal">
<p>Grand Total: 	<span>$<?php echo number_format($grandTotal,2); ?></span></p>
</div>
</div>
';
} else {
echo "<h2 class=\"tableft\">You Have Not Yet Recieved Any Donations.</h2>";
}
echo "<img class=\"tableft\" src=\"images/general/no_donations".rand(1, 4).".png\" alt=\"DigitalCollectionPlate | Banner\"/>";
?>