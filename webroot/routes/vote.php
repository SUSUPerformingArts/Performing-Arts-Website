<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require_once $root . "/compose.php";
use \PA\Auth\Auth;
use \PA\Database;
class_alias('PA\Database', 'DB');


$app->get(
	'/vote',
	function () use ($app){
		$voted = false;
		$db = DB::getSiteDatabase();

		if (Auth::isLoggedIn()) {
			$voted = hasVoted($db, Auth::getUserInfo()['iSolutionsUsername']);
		}


		$app->render("vote/vote.twig", [
			"closed" => true,
			"voted" => $voted,
			"isLoggedIn" => Auth::isLoggedIn(),
			"username" => Auth::getUserInfo()['iSolutionsUsername'],
			"societies" => getSocieties($db)
		]);
	}

)->name("vote");

$app->post(
	'/vote',
	function() use ($app){
		$db = DB::getSiteDatabase();
		if(!Auth::isLoggedIn()){
			$res = [];
			$res['success'] = false;
			$res['error'] = "Not logged in";
			
			echo json_encode($res); 
			return;
		}
		$voted = hasVoted($db, Auth::getUserInfo()['iSolutionsUsername']);
		if($voted){
			$res = [];
			$res['success'] = false;
			$res['error'] = "Already voted";
			
			echo json_encode($res);
			return;
		}

		$pdo = DB::getSiteDatabase(Database::USE_PDO);

		$statement = $pdo->prepare("INSERT INTO `perform`.`awards_vote_19` (`username`, `dbp_1`, `dbp_2`, `dbp_3`, `mbp_1`, `mbp_2`, `mbp_3`, `tbp_1`, `tbp_2`, `tbp_3`, `bcm_1`, `bcm_2`, `bcm_3`, `dbts_1`, `dbts_2`, `dbts_3`, `mbts_1`, `mbts_2`, `mbts_3`, `tbts_1`, `tbts_2`, `tbts_3`, `sbts_1`, `sbts_2`, `sbts_3`, `outstanding_1`) VALUES (:username, :dbp_1, :dbp_2, :dbp_3, :mbp_1, :mbp_2, :mbp_3, :tbp_1, :tbp_2, :tbp_3, :bcm_1, :bcm_2, :bcm_3, :dbts_1, :dbts_2, :dbts_3, :mbts_1, :mbts_2, :mbts_3, :tbts_1, :tbts_2, :tbts_3, :sbts_1, :sbts_2, :sbts_3, :outstanding)");

		if(false===$statement){
			$res = [];
			$res['success'] = false;
			$res['error'] = "Error in prepare";
			
			echo json_encode($res);
			return;
		}

		$username = $app->request()->post('username');
		$dbp = $app->request()->post('dbp');
		$mbp = $app->request()->post('mbp');
		$tbp = $app->request()->post('tbp');
		$bcm = $app->request()->post('bcm');
		$dbts = $app->request()->post('dbts');
		$mbts = $app->request()->post('mbts');
		$tbts = $app->request()->post('tbts');
		$sbts = $app->request()->post('sbts');
		$dedication = $app->request()->post('dedication');
		$outstanding = $app->request()->post('outstanding');

		if(!$statement->execute(array(
			':username' => $username,
			':dbp_1'  => $dbp[0],
			':dbp_2'  => $dbp[1],
			':dbp_3'  => $dbp[2],
			':mbp_1'  => $mbp[0],
			':mbp_2'  => $mbp[1],
			':mbp_3'  => $mbp[2],
			':tbp_1'  => $tbp[0],
			':tbp_2'  => $tbp[1],
			':tbp_3'  => $tbp[2],
			':bcm_1'  => $bcm[0],
			':bcm_2'  => $bcm[1],
			':bcm_3'  => $bcm[2],
			':dbts_1' => $dbts[0],
			':dbts_2' => $dbts[1],
			':dbts_3' => $dbts[2],
			':mbts_1' => $mbts[0],
			':mbts_2' => $mbts[1],
			':mbts_3' => $mbts[2],
			':tbts_1' => $tbts[0],
			':tbts_2' => $tbts[1],
			':tbts_3' => $tbts[2],
			':sbts_1' => $sbts[0],
			':sbts_2' => $sbts[1],
			':sbts_3' => $sbts[2],
			':outstanding' => $outstanding))
		){
			die("Insert failed");
		}
		$res = [];
		//$res['mysqli'] = $statement->error;
		$res['success'] = true;
		echo json_encode($res);
	}
)->name('vote_submit');


function hasVoted($db, $username){
	$res = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as numberOfVotes FROM perform.awards_vote_19 WHERE username=\"".$username."\""));
	return $res['numberOfVotes'] > 0;

}

function getSocieties($db){
	$res = mysqli_query($db, "SELECT name FROM perform.Society");
	return $res;
}
