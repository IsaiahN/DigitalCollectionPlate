<div class="tabbedheader">Recent News</div>
<div class="seprator2 long"></div>
<?php
$sqlQuery="WHERE status='3'";
$query = "SELECT ".$glob['dbprefix']."users.user_id, `donation_id`, `first_name`, `last_name`, ".$glob['dbprefix']."users.ip_address,".$glob['dbprefix']."donations.status, `donation_amount`, ".$glob['dbprefix']."users.email_address FROM ".$glob['dbprefix']."donations INNER JOIN ".$glob['dbprefix']."users ON ".$glob['dbprefix']."donations.user_id = ".$glob['dbprefix']."users.user_id ".$sqlQuery." AND ".$glob['dbprefix']."donations.organization='".$ccUserData['organization']."' ORDER BY `donation_id` DESC";
$poPerPage = 12;
$donations= $db->select($query, $poPerPage, $_GET['po']);
$numrows = $db->numrows($query);
$pagination = paginate($numrows, $poPerPage, $_GET['po'], 'po');


?>