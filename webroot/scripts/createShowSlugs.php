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

$query = "SELECT * FROM `Show`";

$result = $db->query($query);

$shows = $result->fetch_all(MYSQLI_ASSOC);

$slugs = array_map(function($s) use ($slugify) {
	$name = $s['name'];
	/*$pieces = str_word_count($name, 1);
    if(count($pieces) > 4){
        $name = implode(" ", array_splice($pieces, 0, 4));
    }*/
    $slug = $slugify->slugify($name);
	return [
		'id' => $s['id'],
		'slug' => $slug
	];
}, $shows);


$query = "UPDATE `Show`
		SET `slug` = ?
		WHERE `id` = ?";

$prep = $db->prepare($query);

foreach($slugs as $slug){
	$prep->bind_param("si",
		$slug['slug'], $slug['id']);
	$prep->execute();
}

echo "<pre>";
var_dump($slugs);
echo "</pre>";
