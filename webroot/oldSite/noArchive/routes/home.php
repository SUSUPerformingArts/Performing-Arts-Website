<?php

$app->get(
    '/',
    function () use ($app) {
      global $pd;
      // Get the database
      $db = $pd->getPerformArchive();
  		$root = $_SERVER["DOCUMENT_ROOT"];
  	
  		$carouselPrefix = "/img/archive/shows/";
  		$query = "SELECT 
  				s.`id`, 
  				s.`name`, 
  				s.`societyId`, 
  				soc.`type` AS 'societyType', 
  				soc.`name` AS 'societyName', 
  				D.`lastShowDate`, 
  				D.`firstShowDate`, 
  				YEAR(D.`firstShowDate`) AS 'year'
  			FROM `Show` s
  			INNER JOIN 
  					(SELECT 
  						se.`showId`, 
  						MAX(se.`showDate`) AS 'lastShowDate', 
  						MIN(se.`showDate`) AS 'firstShowDate' 
  					FROM `ShowEvent` se 
  					GROUP BY se.`showId`) 
  				D ON s.`id` = D.`showId`
  			INNER JOIN `Society` soc ON s.`societyId` = soc.`id`
  			WHERE D.`lastShowDate` > NOW()
  			ORDER BY D.`firstShowDate` DESC";
  
  		$result = $db->query($query);
  
  		$upcomingShows = $result->fetch_all(MYSQLI_ASSOC);
  		$result->free();
  		$carouselImages = [];
  
  		while(count($carouselImages) < 5 && count($upcomingShows) > 0){
  			$s = array_pop($upcomingShows);
  			
  			if(file_exists($root . $carouselPrefix . $s["id"] . "/cover.jpg")){
  				array_push($carouselImages, [
  						"image" => $s["id"] . "/cover.jpg",
  						"id"    => $s["id"],
  						"socType"	=> $s["societyType"],
  						"name"  => $s["name"]
  					]);
  			}
  		}
  		
  		// Get a random society
          $query = "SELECT * FROM `Society` WHERE `type` != 5 ORDER BY RAND() LIMIT 1";
          $result = $db->query($query);
          $randomSoc = $result->fetch_assoc();
  		
          $imgs = soc_getImages($randomSoc['id']);
          if(isset($imgs['logo_png'])){
              $randomSoc['logo'] = $imgs['logo_png'];
          }else{
              if(isset($imgs['logo_jpg'])){
                  $randomSoc['logo'] = $imgs['logo_jpg'];
              }
          }
  		
  		$app->render("home.twig", [
  			"carouselImages" => $carouselImages,
  			'society' => $randomSoc
  		]);
   }
)->name("pa_home");
?>