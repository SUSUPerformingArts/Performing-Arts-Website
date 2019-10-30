<?php
$root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";
    class_alias('PA\Auth\Auth', 'Auth');
    class_alias('PA\Database', 'DB');


$app->get(
	'/vote',
	function () use ($app){
		$voted = false;

		if (Auth::isLoggedIn()) {
			$res = mysqli_fetch_assoc(mysqli_query(DB::getSiteDatabase(), "SELECT COUNT(*) as numberOfVotes FROM perform.awards_vote_18 WHERE username=\"".Auth::getUserInfo()['iSolutionsUsername']."\""));
			$voted = $res['numberOfVotes'] > 0;
		}


		$app.render("vote.twig", [
			"voted" => $voted,
			"isLoggedIn" => Auth::isLoggedIn(),
			"username" => Auth::getUserInfo()['iSolutionsUsername']
		]);
	}

)->name("vote");
