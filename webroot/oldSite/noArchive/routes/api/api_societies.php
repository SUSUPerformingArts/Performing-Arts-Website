<?php

function api_societies_addUrls($society){
    $app = \Slim\Slim::getInstance();
    $society['societyUrl'] = $app->request->getUrl() . $app->urlFor("pa_society", [ "society" => $society['slug'] ]);
    #$society['uri'] = rtrim($app->request->getUrl() . $app->urlFor("pa_api_society", [ "society" => $society['id'], "type" => null ]), ".");


    return $society;
}

function api_societies_addImages($society){
    $app = \Slim\Slim::getInstance();
    $img = soc_getPreferredImage($society['id']);
    if($img){
    	$society['image'] = $app->request->getUrl() . $img;
    }

    return $society;
}

function api_societies_addAllExtraData($society){
    return api_societies_addImages(api_societies_addUrls($society));
}

function api_get_full_society($db, $id) {
   $query = "SELECT id, slug, name, subtitle, type, description, website, facebookPage, facebookGroup, twitter, instagram, email, susuPage, welcomemeeting
               FROM `Society`
               WHERE `Society`.id=?";
   
   $prep = $db->prepare($query);
   $prep->bind_param("i", $id);
   $prep->execute();
   $result = $prep->get_result()->fetch_all(MYSQLI_ASSOC);
   
   $soc = json_decode(api_negotiateContent($result), true);
   if (count($soc) != 1) {
      echo "null";
      return;
   }
   $soc = $soc[0];
   $soc = api_societies_addAllExtraData($soc);
   
   $query = "SELECT m.firstName, m.lastName, m.chosenName, cp.name AS position, m.bio, m.id, m.iSolutionsUsername
               FROM Society AS s
               INNER JOIN SocietyMember AS sm ON sm.societyId=s.id
               INNER JOIN CommitteePosition AS cp ON sm.committeePositionId=cp.id
               INNER JOIN Member AS m ON sm.memberId=m.id
               WHERE s.id=?
                  AND sm.year=?";
   
   $prep = $db->prepare($query);
   $year = (int) getCurrentAcademicYear();
   $prep->bind_param("ii", $id, $year);
   $prep->execute();
   $committee = $prep->get_result()->fetch_all(MYSQLI_ASSOC);
   
   foreach($committee as $key => $committeeMember) {
      $committee[$key] = api_members_addAllExtraData($committeeMember);
   }
   
   $soc['committee'] = $committee;
   
   $query = "SELECT m.firstName, m.lastName, m.chosenName, m.bio, m.id, m.iSolutionsUsername
               FROM Society AS s
               INNER JOIN SocietyMember AS sm ON sm.societyId=s.id
               INNER JOIN Member AS m ON sm.memberId=m.id
               WHERE s.id=? AND sm.year=? AND ISNULL(sm.committeePositionId)";
   
   $prep = $db->prepare($query);
   $prep->bind_param("ii", $id, $year);
   $prep->execute();
   $members = $prep->get_result()->fetch_all(MYSQLI_ASSOC);
   
   foreach($members as $key => $member) {
      $members[$key] = api_members_addAllExtraData($member);
   }
   
   $soc['otherMembers'] = $members;
   
   return $soc;
}


$app->get( 
    '/api/societies',
    function() use ($app){
         global $pd;
         // Get the database
         $db = $pd->getPerformArchive();

         // Set response as JSON
         $app->response->headers->set('Content-Type', 'application/json');

         $get = $app->request->get();
         $limit = (isset($get["limit"]) && is_numeric($get["limit"])) ? intval($get["limit"]) : null;

         $name = (isset($get["name"]) && $get["name"] != "") ? "%".trim($get["name"])."%" : null;
         $type = (isset($get["type"]) && $get["type"] != "") ? trim($get["type"]) : null;

         
        // DO SOME SHIT


        //$res = api_negotiateContent($searchResult);
         var_dump(getCurrentAcademicYear());
        //echo $res;
        echo "null";
    }
)->name("pa_api_societies");

$app->get( 
    '/api/societies/:id',
    function($id) use ($app){
         global $pd;
         // Get the database
         $db = $pd->getPerformArchive();

         // Set response as JSON
         $app->response->headers->set('Content-Type', 'application/json');

         echo json_encode(api_get_full_society($db, $id));
    }
)->name("pa_api_society");

