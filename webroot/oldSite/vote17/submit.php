<?php

	$root = $_SERVER["DOCUMENT_ROOT"];
	require_once $root . "/compose.php";
	class_alias('PA\Auth\Auth', 'Auth');

	$db = PA\Database::getSiteDatabase();

	if (!Auth::isLoggedIn()) {
		$res = [];
		$res['success'] = false;
		$res['error'] = "Not logged in";
		
		echo json_encode($res);
		exit();
	}
	
	$votingCheck = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as numberOfVotes FROM perform.awards_vote_17 WHERE username=\"".Auth::getUserInfo()['iSolutionsUsername']."\""));
	if($votingCheck['numberOfVotes'] > 0) {
		$res = [];
		$res['success'] = false;
		$res['error'] = "Already voted";
		
		echo json_encode($res);
		exit();
	}
	
	$statement = $db->prepare("INSERT INTO `perform`.`awards_vote_17` (`username`, `dbp_1`, `dbp_2`, `dbp_3`, `mbp_1`, `mbp_2`, `mbp_3`, `tbp_1`, `tbp_2`, `tbp_3`, `dbts_1`, `dbts_2`, `dbts_3`, `mbts_1`, `mbts_2`, `mbts_3`, `tbts_1`, `tbts_2`, `tbts_3`, `bt_1`, `bt_2`, `bt_3`, `os_1`, `os_2`, `os_3`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
	
	$statement->bind_param("sssssssssssssssssssssssss", 
		str_replace(array("\r", "\n"), '', $_POST['username']),
        	str_replace(array("\r", "\n"), '', $_POST['dbp'][0]),
		str_replace(array("\r", "\n"), '', $_POST['dbp'][1]),
		str_replace(array("\r", "\n"), '', $_POST['dbp'][2]),
		str_replace(array("\r", "\n"), '', $_POST['mbp'][0]),
		str_replace(array("\r", "\n"), '', $_POST['mbp'][1]),
		str_replace(array("\r", "\n"), '', $_POST['mbp'][2]),
		str_replace(array("\r", "\n"), '', $_POST['tbp'][0]),
		str_replace(array("\r", "\n"), '', $_POST['tbp'][1]),
		str_replace(array("\r", "\n"), '', $_POST['tbp'][2]),
		str_replace(array("\r", "\n"), '', $_POST['dbts'][0]),
		str_replace(array("\r", "\n"), '', $_POST['dbts'][1]),
		str_replace(array("\r", "\n"), '', $_POST['dbts'][2]),
		str_replace(array("\r", "\n"), '', $_POST['mbts'][0]),
		str_replace(array("\r", "\n"), '', $_POST['mbts'][1]),
		str_replace(array("\r", "\n"), '', $_POST['mbts'][2]),
		str_replace(array("\r", "\n"), '', $_POST['tbts'][0]),
		str_replace(array("\r", "\n"), '', $_POST['tbts'][1]),
		str_replace(array("\r", "\n"), '', $_POST['tbts'][2]),
		str_replace(array("\r", "\n"), '', $_POST['bt'][0]),
		str_replace(array("\r", "\n"), '', $_POST['bt'][1]),
		str_replace(array("\r", "\n"), '', $_POST['bt'][2]),
		str_replace(array("\r", "\n"), '', $_POST['os'][0]),
		str_replace(array("\r", "\n"), '', $_POST['os'][1]),
		str_replace(array("\r", "\n"), '', $_POST['os'][2]));
	
	$statement->execute();
	
	$res = [];
	$res['success'] = true;
	echo json_encode($res);
?>