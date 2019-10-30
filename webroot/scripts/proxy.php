<?php
	$root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";
	
	if (isset($_POST['url']))
		$url = $_POST['url'];
	
	if (isset($_GET['url']))
		$url = $_GET['url'];
	
	$client = new GuzzleHttp\Client();
	$res = $client->get($url);
	
	echo($res->getBody());
?>



