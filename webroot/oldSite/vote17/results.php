<?php
	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	$root = $_SERVER["DOCUMENT_ROOT"];
	require_once $root . "/compose.php";
	class_alias('PA\Auth\Auth', 'Auth');
	
	\PA\Snippets\Header::printHeader("SUSU Performing Arts - PA Awards results");

	if (Auth::isLoggedIn()) {
		if (Auth::isOnPACommittee()) {
?>
	<div class="well well-pa">
		<h1>The Results!</h1>
		<p>Here are the current results of the PA awards vote 2017. This obviously changes as people vote - it's not final!</p>
	</div>

<?php		
	printResults();
		} else {
?>

	<div class="well well-pa">
		<h3>Results - Access denied</h3>
		<p>Sorry, the results can only be viewed by the Performing Arts committee</p>
	</div>

<?php
		}
	} else {
?>

	<div class="well well-pa">
		<h3>Results - please login</h3>
		<p>Please login <a href="/archive/login?continue=/vote17/results.php">here</a> to view the results!</p>
	</div>

<?php
	}

	\PA\Snippets\Footer::printFooter();
	
	
	function printResults() {
		$db = PA\Database::getSiteDatabase();
		
		$sections = [
				"dbp" => "Best Performer - Dance", 
				"mbp" => "Best Performer - Music", 
				"tbp" => "Best Performer - Theatrical",
				"dbts" => "Behind the Scenes - Dance",
				"mbts" => "Behind the Scenes - Music",
				"tbts" => "Behind the Scenes - Theatrical",
				"bt" => "Best Technician / Best Technical Work",
				"os" => "Outstanding Society"
			    ];
		
		foreach($sections as $section_code => $section_name) {
			echo "<div class=\"well well-pa\"><h3>$section_name</h3>";
			
			echo "<p>Number of votes cast: ";
			echo getNumberOfVotes($section_code, $db);
			echo "</p>";
			
			$data = $db->query("SELECT ".$section_code."_1 as '1', ".$section_code."_2 as '2', ".$section_code."_3 as '3' FROM perform.awards_vote_17");
			$rows = [];
			
			while ($row = $data->fetch_assoc()) {
				array_push($rows, $row);
			}
			
			$elim = [];
			if (isset($_GET['elim_mult_awards'])) {
				if ($section_code == "dbp") {
					$elim = ["Chrysta Coker-Strickland (Contemporary, Ballet, Jazz)"];
				}
				
				if ($section_code == "os") {
					$elim = ["SUSO"];
				}

				if ($section_code == "mbts") {
					$elim = ["Thomas Kidman", "Gemma Wills"];
				}
			}
			
			$last_round = [];
			$rounds = [];

			while(true) {
				$round_results = [];
				foreach($rows as $row) {
					if (in_array($row['1'], $elim)) {
						if (in_array($row['2'], $elim)) {
							if (in_array($row['3'], $elim)) {
								$round_results = incrementVote($round_results, "No vote");
							} else {
								$round_results = incrementVote($round_results, $row['3']);
							}
						} else {
							$round_results = incrementVote($round_results, $row['2']);
						}
					} else {
						$round_results = incrementVote($round_results, $row['1']);
					}
				}
				unset($round_results['No vote']);
				$lowest_in_round = array_search(min($round_results), $round_results);

				if (in_array($lowest_in_round, $last_round)) {
					array_push($elim, $lowest_in_round);
				} else {
					array_unshift($elim, $lowest_in_round);
				}
				if (Auth::isPAWebmaster()) {
					array_push($rounds, $round_results);
				}
				if (sizeof($round_results) == 1) break;
				$last_round = $round_results;
			}
			echo "</p>";
			echo "<ol>";
			
			foreach($elim as $candidate) {
				echo "<li><p>$candidate";
				if (Auth::isPAWebmaster()) {
					echo " ( ";
					
					foreach($rounds as $round) {
						if (!isset($round[$candidate])) {
							 break;
						}
						echo $round[$candidate];
						echo " ";
					}
					
					echo ")";
				}
				echo "</p></li>";
			}
			
			echo "</ol></div>";
		}
		
	}
	
	function incrementVote($voteResults, $candidate) {
		if (isset($voteResults[$candidate])) {
			$voteResults[$candidate] = $voteResults[$candidate] + 1;
		} else {
			$voteResults[$candidate] = 1;
		}
		
		return $voteResults;
	}
	
	function getNumberOfVotes($code, $db) {
		mysqli_dump_debug_info($db);
		$res = mysqli_fetch_assoc($db->query("SELECT COUNT(".$code."_1) as noOfVotes FROM perform.awards_vote_17 WHERE ".$code."_1 != 'No vote'"));
		
		return $res['noOfVotes'];
	}
	
	exit();
	
	// SQL to count first pref SELECT dbp_1 as Candidates, COUNT(dbp_1) as Votes FROM `awards_vote_17` WHERE dbp_1 != 'No vote' GROUP BY dbp_1

?>


<?php
	
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
		str_replace(array("\r", "\n"), '', $_POST['mbts'][2]),
		str_replace(array("\r", "\n"), '', $_POST['mbts'][0]),
		str_replace(array("\r", "\n"), '', $_POST['mbts'][1]),
		str_replace(array("\r", "\n"), '', $_POST['tbts'][2]),
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