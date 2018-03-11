<?php
require_once '../../dcp_postback_include.php';
require_once 'ini.inc.php';
require_once "includes".CC_DS."global.inc.php";
require_once("classes".CC_DS."db".CC_DS."db.php" );
$db = new db( );
require_once("includes".CC_DS."functions.inc.php" );
require_once("classes".CC_DS."session".CC_DS."cc_session.php" );
require_once ("includes" . CC_DS . "currencyVars.inc.php");
$cc_session = new session( );

	$request_ip 	= get_ip_address();					// Retrieves the IP location of the request
	$blvd_ip 	= array("174.143.53.42","67.175.232.132","24.120.243.2","184.36.79.133"); 	// Sets the IP whitelist
	
	// Checks The Current IP Address against the Whitelist
	if (($request_ip == $blvd_ip['0'] ) || ($request_ip == $blvd_ip['1'] ) || ($request_ip == $blvd_ip['2'] ) || ($request_ip == $blvd_ip['3'] )) {
	$earn		= $_GET['Earn']; 	// Amount that the user has earned. // ex: 35 DCP points
	$earnpoints 	= $earn; 		// for user points.
	$earn   	= $earn/100; 		// Now is 0.35 cents
	$subid		= $_GET['SubId']; 	// Username of the user that earned the reward(s).
	$subid_piece 	= explode("-", $subid);	// Breaks "Organization-Userid-ip" into $subid_piece[0]="organization", $subid_piece[1]="user_id", $subid_piece[2]="127.0.0.1"
	

	if((!empty($subid)) &&(!empty($earn))) {
		// Insert Into Donation Database
		
		$record["donation_amount"]	= $db->mySQLSafe($earn);
		$record["user_id"]		= $db->mySQLSafe($subid_piece[1]);
		$record["donation_date"]	= $db->mySQLSafe(gmdate("Y-m-d H:i:s"));
		$record["organization"]		= $db->mySQLSafe($subid_piece[0]);
		$record["ip_address"]		= $db->mySQLSafe($subid_piece[2]);
		$record["status"]		= $db->mySQLSafe(3);
		$insert 			= $db->insert($glob['dbprefix']."donations", $record);
		
	
		
		// Increment User DCP Points
		$upPop['points'] = "points+".$earnpoints; 
		$db->update($glob['dbprefix']."users",$upPop,"user_id= ".$db->mySQLSafe($subid_piece[1]));
		
		// Increment Fundraiser Donated Amount
		$upPop2["fundee_amount"]="fundee_amount+".$earn;
		$db->update($glob['dbprefix']."users",$upPop2,"organization= ".$db->mySQLSafe($subid_piece[0]));
		echo "RewardTool&reg; Crediting Success";
		
		// Checks To See if Raffle Can Occur  EX: If fundee_amount is greater than fundee_total
		$query = 'SELECT count(`user_id`) as noRaised FROM '.$glob['dbprefix'].'users  WHERE fundee_amount > fundee_total AND `reward_user_id` = "0" AND `organization` = "'.$subid_piece[0].'"';
		$noRaised= $db->select($query);
		if ($noRaised[0]['noRaised']> 0 ) {
			// Begin Raffle & get winner id
			$qry_raffle = "SELECT `user_id` FROM `donations` WHERE `organization` = '".$subid_piece[0]."' ORDER BY RAND() LIMIT 1";
			$raffle= $db->select($qry_raffle);
			
			// Update fundraiser user with winning id
			$qry_update_fundraiser = "UPDATE `users` SET `reward_user_id`='".$raffle[0]['user_id']."' WHERE `organization`= '".$subid_piece[0]."'";
			$winner_raffle= $db->select($qry_update_fundraiser);
			
			// Insert into table winners
			$qry_insert_winner = "INSERT INTO `winners`(`winner_id`, `user_id`, `organization`, `date_won`) VALUES ('','".$raffle[0]['user_id']."','".$subid_piece[0]."',NOW())";
			$winner_raffle_insert= $db->select($qry_insert_winner);
		}
	
	} else {
		echo "RewardTool&reg; Crediting Failure";
	}
}	
else {
	echo "RewardTool&reg; Crediting Failure";
	exit;
}
?>