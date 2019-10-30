<?php

function members_getImages($memberId) {
    $app = \Slim\Slim::getInstance();

    $images = [];
    // Does a profile image exist?
    $img_path = $app->config('images.members');
    $webroot = $app->config('webroot');
    $image_name = 'profile.jpg';
    $image_path = $img_path . $memberId . '/' .$image_name;

    // Check & add URL to the images object
    if(is_file(realpath($webroot . $image_path))){
        $images['profile'] = $image_path;
    }

    return $images;
}



$app->get(
    '/members',
    function () use ($app) {
        /*** Removed: Now uses search mechanism
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Get all the members
        $query = "SELECT m.`id`, m.`firstName`, m.`lastName`, m.`chosenName`, COUNT(sp.`memberId`) AS 'societyNumber' FROM `Member` m
                INNER JOIN `SocietyMember` sp ON sp.`memberId` = m.`id`
                WHERE sp.`year` = " . getCurrentAcademicYear() . " GROUP BY sp.`memberId`";

        $result = $db->query($query); // Controlled query, don't prepare

        $members = [];
        while($row = $result->fetch_assoc()){
            $row = derivePreferredName($row);
            $members[] = $row;
        }
        $result->free();

        $db->close();
        
        
        $app->render("members/members_index.twig", [
            "members" => $members
        ]);
        */

        $app->render("members/members_index.twig");
    }
)->name("pa_members");
// Alias
$app->get(
    '/member',
    function () use ($app) {
        $app->redirect($app->urlFor('members'), 301);
    }
);

$app->get(
    '/members/:member',
    function ($member) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // At the moment only selection by ID
        $memberId = $member;

        /* Get the information on that member */
        $query = "SELECT * FROM `Member` WHERE id = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();

        $mem = $result->fetch_assoc();
        $result->free();

        if(!isset($mem)){
            // Flash something here please
            $app->notFound();
            return;
        }

        $mem = derivePreferredName($mem);


        $images = members_getImages($memberId);


        /* Get socieites the person is a part of */
        $query = "SELECT soc.`id`, soc.`slug`, soc.`name`, sp.`year` FROM `SocietyMember` sp
                LEFT JOIN `Society` soc ON sp.`societyId` = soc.`id`
                WHERE sp.`memberId` = ? ORDER BY soc.`name` ASC, sp.`year` DESC";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();

        $socs = [];
        $seensocs = [];
        $maxyear = 50;
        $minyear = PHP_INT_MAX;
        while($row = $result->fetch_assoc()){
            $sid = $row["id"];
            $yr = intval($row["year"], 10);
            $maxyear = max($maxyear, $yr);
            $minyear = min($minyear, $yr);

            if(isset($seensocs[$sid])){
                $socs[$seensocs[$sid]]["years"][] = $yr;
                sort($socs[$seensocs[$sid]]["years"]);
            }else{
                $row["years"] = [$yr];
                unset($row["year"]);

                $socs[] = $row;
                $seensocs[$sid] = sizeof($socs) - 1;
            }
        }
        $result->free();



        /* Get the committees they are on */
        $query = "SELECT soc.`id`, soc.`slug`, soc.`name`, sp.`year`, cp.`name` AS 'committeePosition' FROM `SocietyMember` sp
                LEFT JOIN `Society` soc ON sp.`societyId` = soc.`id`
                INNER JOIN `CommitteePosition` cp ON sp.`CommitteePositionId` = cp.`id`
                WHERE sp.`memberId` = ? ORDER BY sp.`year` DESC, soc.`name` ASC";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();

        $committees = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();


        /* Get the shows they have been in */
        // I AM DRUNK BUT THIS NEEDS TO BE UPDATED WITH SHOWDATE TO IT'S MAX INSTEAD OF ALL - Done this now, but keeping the comment here
        $query = "SELECT s.`id`, s.`slug`, s.`name` AS 'showName', r.`name` AS 'roleName', sr.`notes`, `year`, `academicYear`,
            s.`societyId`, s.`societySlug`, s.`societyName`
            FROM `ShowRole` sr
            LEFT JOIN `Show_WithExpandedInfo` s ON sr.`showId` = s.`id`
            LEFT JOIN `Role` r ON sr.`roleId` = r.`id`
            WHERE sr.`memberId` = ? ORDER BY s.`firstShowDate` DESC";


        $prep = $db->prepare($query);

        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();


        $shows = [];
        $currId = null;
        $currName = null;
        $showNum = 0;
        while($row = $result->fetch_assoc()){
            if($currId !== $row["id"]){
                $showNum++;
                $currId = $row["id"];
                $currName = $row["societyName"];
                if(!isset($shows[$currName])){
                    $shows[$currName] = [];
                }
            }

            $shows[$currName][] = $row;
        }
        $result->free();

        /* Get external profile links */
        $query = "SELECT soc.`id` AS 'societyId', soc.`name`, CONCAT(soc.`profileBaseURL`, sp.`societyProfileId`) AS 'profileUrl'
                FROM `SocietyMember` sp LEFT JOIN `Society` soc ON sp.`societyId` = soc.`id`
                WHERE soc.`profileBaseURL` IS NOT NULL AND sp.`societyProfileId` IS NOT NULL AND sp.`memberId` = ? GROUP BY soc.`id`";
        
        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();

        $externalProfiles = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();

        $query = "SELECT COUNT(id) AS 'num' FROM `Society` WHERE `type` != 5";

        $result = $db->query($query);
  
        $socNum = $result->fetch_assoc();
        $socNum = $socNum["num"];
  
        $result->free();
      
        $query = "SELECT cp.`title` 
              FROM `PA_CommitteeMember` cm
              INNER JOIN `PA_CommitteePosition` cp ON cm.`positionId` = cp.`id`
              WHERE cm.`memberId` = ?";
      
        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();
        $data = $result->fetch_assoc();

        $memberType;
        if ($data) {
            $memberType = $data['title'];

            if ((!strpos($memberType, "Representative")) && ($memberType!="Performing Arts Officer")) {
                $memberType = "PA " . $memberType;
            }
        } else {
            $memberType = "PA Member";
        }
      
        $result->free();

        $db->close();



        $app->render("members/members.twig", [
            "member" => $mem,
            "images" => $images,
            "societies" => $socs,
            "committees" => $committees,
            "allShows" => $shows,
            "showNum" => $showNum,
            "externalProfiles" => $externalProfiles,
            "years" => ["max" => $maxyear, "min" => $minyear],
            "memberType" => $memberType
        ]);
    }
)->name("pa_member");


// Suggested members
$app->get(
    '/members/s/:member',
    function ($member) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $memberId =  $member;

        // Get info about the member
        $query = "SELECT * FROM `Suggested_Member` WHERE id = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();

        $mem = $result->fetch_assoc();
        $result->free();


        if(!isset($mem)){
            // Flash something here please
            $app->notFound();
            return;
        }


        // Redirect to actual member if they exist
        if(isset($mem["memberId"])){
            $app->redirect($app->urlFor("pa_member", [ "member" => $mem["memberId"] ] ));
        }

        $mem = derivePreferredName($mem);


        /* Get socieites the person is a part of */
        $query = "SELECT soc.`id`, soc.`slug`, soc.`name`, sp.`year` FROM `Suggested_SocietyMember` sp
                LEFT JOIN `Society` soc ON sp.`societyId` = soc.`id`
                WHERE sp.`suggestedMemberId` = ? ORDER BY soc.`name` ASC, sp.`year` DESC";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();

        $socs = [];
        $seensocs = [];
        $maxyear = 50;
        $minyear = PHP_INT_MAX;
        while($row = $result->fetch_assoc()){
            $sid = $row["id"];
            $yr = intval($row["year"], 10);
            $maxyear = max($maxyear, $yr);
            $minyear = min($minyear, $yr);

            if(isset($seensocs[$sid])){
                $socs[$seensocs[$sid]]["years"][] = $yr;
                sort($socs[$seensocs[$sid]]["years"]);
            }else{
                $row["years"] = [$yr];
                unset($row["year"]);

                $socs[] = $row;
                $seensocs[$sid] = sizeof($socs) - 1;
            }
        }
        $result->free();



        /* Get the committees they are on */
        $query = "SELECT soc.`id`, soc.`slug`, soc.`name`, sp.`year`, cp.`name` AS 'committeePosition' FROM `Suggested_SocietyMember` sp
                LEFT JOIN `Society` soc ON sp.`societyId` = soc.`id`
                INNER JOIN `CommitteePosition` cp ON sp.`CommitteePositionId` = cp.`id`
                WHERE sp.`suggestedMemberId` = ? ORDER BY sp.`year` DESC, soc.`name` ASC";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();

        $committees = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();


        $query = "SELECT s.`id`, s.`slug`, s.`name` AS 'showName', r.`name` AS 'roleName', sr.`notes`, `year`, `academicYear`,
            s.`societyId`, s.`societySlug`, s.`societyName`
            FROM `ShowRole` sr
            LEFT JOIN `Show_WithExpandedInfo` s ON sr.`showId` = s.`id`
            LEFT JOIN `Role` r ON sr.`roleId` = r.`id`
            WHERE sr.`memberId` = ? ORDER BY s.`firstShowDate` DESC";


        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();


        $shows = [];
        $currId = null;
        $currName = null;
        while($row = $result->fetch_assoc()){
            if($currId !== $row["id"]){
                $currId = $row["id"];
                $currName = $row["societyName"];
                if(!isset($shows[$currName])){
                    $shows[$currName] = [];
                }
            }

            $shows[$currName][] = $row;
        }
        $result->free();

        $db->close();

        $potentualMatch = false;
        if(isset($_SESSION["user"])){
            if(strtolower($_SESSION["user"]["lastName"]) === strtolower($mem["lastName"])){
                $potentualMatch = true;
            }
        }


        $app->render("members/members_suggested.twig", [
            "member" => $mem,
            "allShows" => $shows,
            "societies" => $socs,
            "committees" => $committees,
            "years" => ["max" => $maxyear, "min" => $minyear],
            "potentualMatch" => $potentualMatch
        ]);
    }
)->name("pa_suggested_member");

// Alias
$app->get(
    'members/suggested/:member',
    function($member) use ($app) {
        $app->redirect($app->urlFor("pa_suggested_member", [ "member" => $member ]));
    }
);


// Alias
$app->get(
    '/member/:path+',
    function ($path) use ($app) {
        $url = $app->urlFor("pa_members");
        if(count($path) > 0){
            $url = $url . "/" . implode("/", $path);
        }
        $app->redirect($url, 301);
    }
);

