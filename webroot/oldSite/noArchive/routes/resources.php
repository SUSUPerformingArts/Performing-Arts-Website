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
  		
  		$app->render("resources.twig", [
  			
  		]);
   }
)->name("pa_resources");
?>