<?php
	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	$root = $_SERVER["DOCUMENT_ROOT"];
	require_once $root . "/compose.php";
	class_alias('PA\Auth\Auth', 'Auth');
	
	\PA\Snippets\Header::printHeader("SUSU Performing Arts - PA Awards results");

	if (Auth::isLoggedIn()) {
		if (Auth::getUserInfo()['iSolutionsUsername'] == "gkjt1g15" || Auth::getUserInfo()['iSolutionsUsername'] == "it3g16" || Auth::getUserInfo()['iSolutionsUsername'] == "at10g15") {
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
		<p>Please login <a href="/archive/login?continue=/voteres/results.php">here</a> to view the results!</p>
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
				"bcm" => "Best Crew Member",
				"dbts" => "Behind the Scenes - Dance",
				"mbts" => "Behind the Scenes - Music",
				"tbts" => "Behind the Scenes - Theatrical",
				"sbts" => "Behind the Scenes - Tech",
				//"outstanding" => "Outstanding Society"
			    ];
		
		foreach($sections as $section_code => $section_name) {
			echo "<div class=\"well well-pa\"><h3>$section_name</h3>";
			
			echo "<p>Number of votes cast: ";
			echo getNumberOfVotes($section_code, $db);
			echo "</p>";
			
			$data = $db->query("SELECT REPLACE(".$section_code."_1, '\n', '') as '1', REPLACE(".$section_code."_2, '\n', '') as '2', REPLACE(".$section_code."_3, '\n', '') as '3' FROM perform.awards_vote_19");
			//$data = $db->query("SELECT REPLACE(REPLACE(".$section_code."_1, '\n', ''), CHAR(13)) as '1', REPLACE(REPLACE(".$section_code."_2, '\n', ''), CHAR(13)) as '2', REPLACE(REPLACE(".$section_code."_3, '\n', ''), CHAR(13)) as '3' FROM perform.awards_vote_18");
			$rows = [];
			
			while ($row = $data->fetch_assoc()) {
				array_push($rows, $row);
			}
			
			$elim = [];
			if ($section_code == "bcm") {
					$elim = ["George Tucker"];
				}

			if (isset($_GET['elim_mult_awards'])) {
				// If a person has won multiple awards, add them to elim for the categories you want other people to win
				if ($section_code == "bcm") {
					$elim = [];
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
				if($candidate == "No vote\n")
					continue;
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
		$res = mysqli_fetch_assoc($db->query("SELECT COUNT(".$code."_1) as noOfVotes FROM perform.awards_vote_19 WHERE ".$code."_1 != 'No vote\n'"));
		
		return $res['noOfVotes'];
	}
	
	exit();
	
	// SQL to count first pref SELECT dbp_1 as Candidates, COUNT(dbp_1) as Votes FROM `awards_vote_17` WHERE dbp_1 != 'No vote' GROUP BY dbp_1

?>



