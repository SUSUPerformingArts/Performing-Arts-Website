<?php

use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;
use PA\Color\HSLConverter;

$app->get(
    '/about',
    function () use ($app) {
      global $pd;
      // Get the database
      $db = $pd->getPerformArchive();
  		$root = $_SERVER["DOCUMENT_ROOT"];
  		
  		$query = "SELECT COUNT(id) AS 'num' FROM `Society` WHERE `type` != 5";

      $result = $db->query($query);
  
      $socNum = $result->fetch_assoc();
      $socNum = $socNum["num"];
  
      $result->free();
      
      $about = file_get_contents("static/about.md");
      
      $files = glob('/home/perform/webroot/img/photos/*.jpg');
      $photo = preg_replace("/.*webroot/i", "", $files[array_rand($files)]);
  	
  		$app->render("about/about.twig", [
        'noOfSocs' => $socNum,
        'about' => $about,
        'photo' => $photo
  		]);
   }
)->name("pa_about");

$app->get(
    '/about/committee',
    function () use ($app) {
      global $pd;
      // Get the database
      $db = $pd->getPerformArchive();
  		$root = $_SERVER["DOCUMENT_ROOT"];
  		
      $query = "SELECT cm.`memberId`, member.`firstName`, member.`lastName`, member.`chosenName`, cp.`title`, cp.`email` 
              FROM `PA_CommitteeMember` cm
              INNER JOIN `PA_CommitteePosition` cp ON cm.`positionId` = cp.`id`
              INNER JOIN `Member` member ON cm.`memberId` = member.`id`
              WHERE cm.`year` = ? ORDER BY cm.`positionId` ASC";
      
      $year = (int) getCurrentAcademicYear();
      $prep = $db->prepare($query);
      $prep->bind_param("i",
          $year);
      $prep->execute();
      $result = $prep->get_result();
      
      $paCommittee = [];
      while ($data = $result->fetch_assoc()) {
        if ($data['chosenName'] != "") {
          $data['firstName'] = $data['chosenName'];
        }
        
        $data['fullName'] = $data['firstName'] . " " . $data['lastName'];
        
        $imgDir = "/img/archive/members/";
        if (file_exists("." . $imgDir . $data['memberId'] . "/profile.jpg")) {
          $data['img'] = $imgDir . $data['memberId'] . "/profile.jpg";
        } else if (file_exists("." . $imgDir . $data['memberId'] . "/profile.png")) {
          $data['img'] = $imgDir . $data['memberId'] . "/profile.png";
        } else {
          $data['img'] = "/img/committee/placeholder.png";
        }
        
        
        $paCommittee[] = $data;
      }
      
      $result->free();
      
  		$app->render("about/pa_committee.twig", [
        'paCommittee' => $paCommittee
  		]);
   }
)->name("pa_committee");

$app->get(
    '/about/pacard',
    function () use ($app) {
      global $pd;
      // Get the database
      $db = $pd->getPerformArchive();
  		$root = $_SERVER["DOCUMENT_ROOT"];
  		
  		$intro = file_get_contents("static/pa_card_intro.md");
  	   $offers = json_decode(file_get_contents("static/pa_card_offers.json"), true);
  	   
  	   if (file_exists("./cache/bg_cache.json")) {
  	      $bgCache = json_decode(file_get_contents("./cache/bg_cache.json"), true);
  	   } else {
  	      $bgCache = [];
  	   }
  	   $cacheChanged = false;
  	   
  	   foreach ($offers as $i => $offer) {
         $img = "/home/perform/webroot/img/pa_card/" . $offer['image'] . ".png";
  	      list($width, $height, $type, $attr) = getimagesize($img);
  	      
  	      $offers[$i]['portrait'] = (($width/$height)<=1.6) ? 1 : 0;
  	      
  	      if (isset($bgCache[$offer['image']])) {
  	         $offers[$i]['colour'] = $bgCache[$offer['image']];
  	         
  	      } else {
  	         $cacheChanged = true;
  	         
  	         $palette = Palette::fromFilename($img);
     	      $colours = $palette->getMostUsedColors(1);
     	      
     	      foreach ($colours as $colour => $count) {
     	         $hsl = HSLConverter::hex2hsl(Color::fromIntToHex($colour));
     	         $hsl[1] = 0.7;
     	         $hsl[2] = 0.4;
     	         
     	         $offers[$i]['colour'] = HSLConverter::hsl2hex($hsl);
     	         $bgCache[$offer['image']] = $offers[$i]['colour'];
     	      }
  	      }
  	   }
      
      if ($cacheChanged) {
         file_put_contents("./cache/bg_cache.json", json_encode($bgCache));
      }
      
      shuffle($offers);
  	   
  		$app->render("about/pa_card.twig", [
         'intro' => $intro,
         'offers' => $offers
  		]);
   }
)->name("pa_card");

$app->get(
    '/about/dance',
    function () use ($app) {
      global $pd;
      // Get the database
      $db = $pd->getPerformArchive();
  		$root = $_SERVER["DOCUMENT_ROOT"];
  	
  		$app->render("about/dance.twig", [

  		]);
   }
)->name("pa_dance");
?>