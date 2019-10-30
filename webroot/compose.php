<?php

/** Registers the composer autoloader if not required already **/


if(!defined('PA_IS_COMPOSED')){

	require_once __DIR__ . '/vendor/autoload.php';
	
	require_once __DIR__ . "/php/classes/ical/CalFileParser.php";
	require_once __DIR__ . "/php/Graphite/Graphite.php";
	

}
?>