<?php

	$root = $_SERVER["DOCUMENT_ROOT"];
	require_once $root . "/compose.php";
	class_alias('PA\Auth\Auth', 'Auth');

	$db = PA\Database::getSiteDatabase();

	if (!Auth::isLoggedIn()) {
		
		exit();
	}
	
	if (!Auth::isOnPaCommittee()) {
			exit();
		}
	
	$res = $db->query("SELECT username FROM perform.awards_vote_17 WHERE dbp_1 IS NULL");
	
	while ($row = $res->fetch_assoc()) {
		echo $row['username'];
		echo "@soton.ac.uk;";
	}
?>