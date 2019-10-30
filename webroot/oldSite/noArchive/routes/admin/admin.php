<?php

use \PA\Auth\Auth;

function addNewMembers($db) {
  $query_cName = "UPDATE `Member` SET
      `chosenName` = ?
      WHERE `id` = ?";
  $prep_cName = $db->prepare($query_cName);
  $fail = [];
  
  foreach (getNewMembers($db) as $l) {
      $split = explode(" ", $l['name']);
      $fname = $split[0];
      try{
          if ($l['username'] == 'bhangra') continue;
          
          $user = Auth::createNewUser($l['username']);
          
          if($fname != $user['firstName']){
              // Change the chosen name
              $id = $user['id'];
              $prep_cName->bind_param("si",
                  $fname, $id);
              $prep_cName->execute();
          }
      }catch(Execption $e){
          
          $fail[] = $l;
      }
  }
  
  return $fail;
}

function getNewMembers($db) {
  $db = $db;

  $mapping = [];

  /** Read from the JSON file ***/
  $dir = @scandir("../societies/committees/results", SCANDIR_SORT_DESCENDING);
  $json = null;
  if(isset($dir[0])){
      $json = json_decode(@file_get_contents("../societies/committees/results/" . $dir[0]), true);
  }
  $people = [];

  foreach ($json as $soc) {
      $people = array_merge($people,
                  array_udiff($soc['members'], $people, function($a, $b){
                      return ($a['username'] == $b['username']) ? 0 : -1;
                  }));
  }



  $left = [];
  foreach ($people as $person) {
      $user = Auth::checkUserInDatabase($person['username']);
      if(is_null($user)){
          $left[] = $person;
      }
  }
  sort($left);
  
  return $left;
}

function addNewPositions($db) {
   $query = "INSERT INTO CommitteePosition (`name`) VALUES (?)";
  $ls = getNewPositions($db);
  $prep = $db->prepare($query);
    
  foreach ($ls as $l) { 
      $prep->bind_param("s", $l);
      $prep->execute();
  }
}

function getNewPositions($db) {
  $db = $db;
  $mapping = json_decode(file_get_contents('./routes/admin/mappings.json'), true);;



  /** Read from the JSON file ***/
  $dir = @scandir("../societies/committees/results", SCANDIR_SORT_DESCENDING);
  $json = null;
  if(isset($dir[0])){
      $json = json_decode(@file_get_contents("../societies/committees/results/" . $dir[0]), true);
  }
  $positions = [];

  foreach ($json as $soc) {
      $b = array_map(function($a) use ($mapping) {
              $p = trim($a['position']);
              if(array_key_exists($p, $mapping)){
                  $p = $mapping[$p];
              }
              return $p;
          }, $soc['members']);
      $positions = array_merge($positions, $b);
  }
  $positions = array_unique($positions);


  /** Get all the socs **/
  $query = "SELECT * FROM CommitteePosition
          WHERE `name` = ?";

  $prep = $db->prepare($query);

  $left = [];
  foreach ($positions as $position) {
      $prep->bind_param("s", $position);
      $prep->execute();
      $result = $prep->get_result()->fetch_assoc();
      if(is_null($result)){
          $left[] = $position;
      }
  }
  sort($left);
  
  return $left;
}

function addNewRoles($db) {
  $query = 'INSERT INTO `SocietyMember` (`memberId`, `societyId`, `committeePositionId`, `year`)
      VALUES (?, ?, ?, ?)';
      
  $roles = getNewRoles($db);
  $prep = $db->prepare($query);
  $year = (int) date("Y");

  foreach ($roles as $role) {
      $prep->bind_param("iiii",
          $role['userId'], $role['societyId'], $role['positionId'], $year);
      if (!$prep->execute());
  }
}

function getNewRoles($db) {
  $db = $db;
  
  $mapping = json_decode(file_get_contents('./routes/admin/mappings.json'), true);
  $year = (int) date("Y");

  // Get positions
  $query = "SELECT * FROM `CommitteePosition`";

  $result = $db->query($query);

  $positions = [];
  while($row = $result->fetch_assoc()){
      $positions[$row['name']] = (int) $row['id'];
  }


  /** Read from the JSON file ***/
  $dir = @scandir("../societies/committees/results", SCANDIR_SORT_DESCENDING);
  $json = null;
  if(isset($dir[0])){
      $json = json_decode(@file_get_contents("../societies/committees/results/" . $dir[0]), true);
  }

  $query = "SELECT * FROM `SocietyMember`
          WHERE `memberId` = ? AND `societyId` = ? AND `committeePositionId` = ? AND `year` = ?";
  $prep = $db->prepare($query);


  $roles = [];
  foreach ($json as $id => $soc) {
      foreach ($soc['members'] as $member) {
          $p = trim($member['position']);
          if(array_key_exists($p, $mapping)){
              $p = $mapping[$p];
          }
          if(array_key_exists($p, $positions)){
              $pos = $positions[$p];
              $user = Auth::checkUserInDatabase($member['username']);
              if($user){
                  $role = [
                      'societyId' => $id,
                      'socName' => $soc['name'],
                      'positionId' => $pos,
                      'positionName' => $p,
                      'username' => trim($member['username']),
                      'userId' => $user['id'],
                      'name' => trim($member['name'])
                  ];

                  $prep->bind_param("iiii",
                      $role['userId'], $role['societyId'], $role['positionId'], $year);
                  $prep->execute();
                  $res = $prep->get_result();

                  if(is_null($res->fetch_assoc())){
                      $roles[] = $role;
                  }
              }
          }
      }
  }

  return $roles;
}



$app->post(
    '/admin/addsubmittedcommittees',
    $authenticate('is_PA_webmaster'),
    function () use ($app) {
      global $pd;
      // Get the database
      $db = $pd->getPerformArchive();
    
      addNewMembers($db);
      addNewPositions($db);
      addNewRoles($db);
    }
)->name("pa_add_submitted_committees");

$app->get(
    '/admin',
    $authenticate('is_logged_in'),
    function () use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Get all information for the committees they are on
        $commIds = Auth::getCurrentCommittees();
        $comms;

        if(count($commIds) > 0){
            $query = "SELECT socs.`id`, socs.`slug`, socs.`name`, socs.`type` AS 'typeId', type.`name` AS 'type'
                    FROM `Society` socs
                    LEFT JOIN `SocietyType` type ON socs.`type` = type.`id`
                    WHERE socs.`id` IN (" . implode(",", $commIds) . ")
                    ORDER BY socs.`name` ASC";

            $result = $db->query($query);


            $comms = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
        }else{
            $comms = [];
        }


        // Get shows the person is the production team for
        $showIds = Auth::getShowsForProdTeams();
        $shows;

        if(count($showIds) > 0){
            $query = "SELECT `id`, `slug`, `name`, `societyId`, `societySlug`, `societyName`, `year`, `academicYear`
                FROM `Show_WithExpandedInfo`
                WHERE `id` IN (" . implode(",", $showIds) . ")
                ORDER BY `firstShowDate` DESC";

            $result = $db->query($query);

            $shows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
        }else{
            $shows = [];
        }

        // Get API Data
        $api = file_get_contents("static/api.md");
        
        if (Auth::authenticate('is_PA_webmaster')) {
          $newMembers = getNewMembers($db);
          $newRoles = getNewRoles($db);
          $newPositions = getNewPositions($db);
          
          $app->render('admin.twig', [
              'committees' => $comms,
              'shows' => $shows,
              'newMembers' => $newMembers,
              'newRoles' => $newRoles,
              'newPositions' => $newPositions,
              'api' => $api
          ]);
          
        } else {
          $app->render('admin.twig', [
              'committees' => $comms,
              'shows' => $shows,
              'api' => $api
          ]);
        }
        
    }
)->name("pa_admin");




?>