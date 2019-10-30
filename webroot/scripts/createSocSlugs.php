<?php

error_reporting(-1);
ini_set('display_errors', 'On');

$webroot = $_SERVER["DOCUMENT_ROOT"];


require_once($webroot . "/php/slugify/SlugifyInterface.php");
require_once($webroot . "/php/slugify/Slugify.php");

use Cocur\Slugify\Slugify;

$slugify = new Slugify();

require_once($webroot . "/database.php");

$pd = new PerformDatabase();
$db = $pd->getPerformArchive();

$query = "SELECT * FROM `Society`";

$result = $db->query($query);

$socs = $result->fetch_all(MYSQLI_ASSOC);

$slugs  = array_map(function($s) use ($slugify) {
	return [
		'id' => $s['id'],
		'slug' => $slugify->slugify($s['name'])
	];
}, $socs);


$query = "UPDATE `Society`
		SET `slug` = ?
		WHERE `id` = ?";

$prep = $db->prepare($query);

foreach($slugs as $slug){
	$prep->bind_param("si",
		$slug['slug'], $slug['id']);
	$prep->execute();
}

var_dump($slugs);
