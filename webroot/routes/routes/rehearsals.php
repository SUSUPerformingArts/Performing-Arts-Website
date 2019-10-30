<?php

$app->get(
    '/rehearsals',
    function () use ($app) {

      // Get calendar
      $danceEvents = (\PA\Calendar\Calendar::getSocJsonFeed(41, "#ffa126"));
      $musicEvents = (\PA\Calendar\Calendar::getSocJsonFeed(42, "#2372b5"));
      $theatricalEvents = (\PA\Calendar\Calendar::getSocJsonFeed(43, "#208744"));

  		$app->render("rehearsals.twig", [
  			"danceEvents" => $danceEvents,
  			"musicEvents" => $musicEvents,
        "theatricalEvents" => $theatricalEvents
  		]);
   }
)->name("pa_rehearsals");
?>