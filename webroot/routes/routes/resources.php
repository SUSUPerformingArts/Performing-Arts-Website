<?php

use PA\Auth\Auth as Auth;

$app->get(
    '/resources',
    $authenticate('is_logged_in'),
    function () use ($app) {
      global $pd;
      // Get the database
      $db = $pd->getPerformArchive();
  		$root = $_SERVER["DOCUMENT_ROOT"];

      $api = file_get_contents("static/api.md");
  		
  		$app->render("resources.twig", [
  			'api' => $api
  		]);
   }
)->name("pa_resources");
?>