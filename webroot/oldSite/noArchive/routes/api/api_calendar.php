<?php

function getSocIdFromSlug($slug) {
	global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    $query = "SELECT id FROM Society WHERE `Society`.`slug`=?";
    $prep = $db->prepare($query);
    $prep->bind_param("s", $slug);
    $prep->execute();

    return $prep->get_result()->fetch_assoc()['id'];
}

$app->get(
   '/api/cal/formatloc/:loc_string',
   function($loc_string) use ($app) {
      
      header('Content-Type: application/json');
      echo json_encode(\PA\Calendar\UniLocation::parse(str_replace("_", "/", $loc_string)));
   }
)->name("pa_api_cal_format");

$app->get(
    '/api/cal/ics/:soc',
    function($soc) use ($app){
		$app->response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
	    $app->response->headers->set('Content-Disposition', 'inline; filename=calendar.ics');
	   
		echo \PA\Calendar\Calendar::getSocIcalFeed(getSocIdFromSlug($soc));
    }
)->name("pa_api_iCal");

// Alias for users
$app->get(
    '/cal/:soc',
    function($soc) use ($app){
		$app->response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
	    $app->response->headers->set('Content-Disposition', 'inline; filename=calendar.ics');
	   
		echo \PA\Calendar\Calendar::getSocIcalFeed(getSocIdFromSlug($soc));
    }
)->name("pa_user_api_iCal");

// Alias to support legacy 'feed'
$app->get(
    '/schedule/feed.php',
    function() use ($app){
		$app->response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
	    $app->response->headers->set('Content-Disposition', 'inline; filename=calendar.ics');
	   
		echo \PA\Calendar\Calendar::getSocIcalFeed($app->request->get()["soc"]);
    }
)->name("pa_api_iCal_legacy");


$app->get(
    '/api/cal/json/:soc',
    function($soc) use ($app){
		$app->response->headers->set('Content-Type', 'application/json');
		$get = $app->request->get();

		if (isset($get['colour']))
			echo json_encode(\PA\Calendar\Calendar::getSocJsonFeed($soc, "#" . $get['colour']));
		else
			echo json_encode(\PA\Calendar\Calendar::getSocJsonFeed($soc));

    }
)->name("pa_api_cal_json");

$app->get(
    '/api/cal/embed/:society',
    function ($society) use ($app) {
    	global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $query = "SELECT s.*, st.`name` AS 'societyType' FROM `Society` s INNER JOIN `SocietyType` st ON s.`type` = st.`id` WHERE s.`id` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $society);
        $prep->execute();
        $result = $prep->get_result();

        $theSociety = $result->fetch_assoc();
        $result->free();

        if(!isset($theSociety)){
            // Flash something here please
            $app->notFound();
            return;
        }

        $societyId = $theSociety['id'];


        // Get the upcoming shows
        $upcomingShows = soc_getUpcomingShows($societyId);

        // Get this year's shows
        $year = getCurrentAcademicYear();
        $yearShows = soc_getShowsForAcademicYear($societyId, $year, true);

        /***** Special condition for StageSoc *****/
        if($societyId == 25){
            $upcomingShows = array_merge($upcomingShows, soc_getUpcomingShows_withStagesoc());
            $yearShows = array_merge($yearShows, soc_getShowsForAcademicYear_withStagesoc($year, true));
        }


        // Get current members
        $rows = soc_getMembersForAcademicYear($societyId, $year);

        $yearMember = [];
        foreach($rows as $row){
            $row = derivePreferredName($row);

            $yearMember[] = $row;
        }


        // Get the years that we have data for
        $query = "";
        if($societyId == 25){
            // With stagesoc
            $query = "(SELECT DISTINCT `year` FROM `All_SocietyMember` WHERE `societyId` = ?)
                    UNION DISTINCT
                    (
                        SELECT GET_ACADEMIC_YEAR(MAX(se.`showDate`)) AS 'year'
                        FROM `ShowEvent` se
                        INNER JOIN `Show` s ON se.`showId` = s.`id`
                        WHERE s.`societyId` = ? OR s.`stagesoc` = TRUE GROUP BY se.`showId`
                    ) ORDER BY `year` DESC";
        }else{
            // NO stagesoc
            $query = "(SELECT DISTINCT `year` FROM `All_SocietyMember` WHERE `societyId` = ?)
                    UNION DISTINCT
                    (
                        SELECT GET_ACADEMIC_YEAR(MAX(se.`showDate`)) AS 'year'
                        FROM `ShowEvent` se
                        INNER JOIN `Show` s ON se.`showId` = s.`id`
                        WHERE s.`societyId` = ? GROUP BY se.`showId`
                    ) ORDER BY `year` DESC";
        }

        $prep = $db->prepare($query);
        $prep->bind_param("ii",
            $societyId, $societyId);
        $prep->execute();
        $result = $prep->get_result();

        $yearsData = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();

        // Check if there are any shows with NO date data
        $query = "SELECT COUNT(s.`id`) AS 'c'
                FROM `Show` s
                LEFT JOIN `ShowEvent` se ON s.`id` = se.`showId`
                WHERE se.`showId` IS NULL AND s.`societyId` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $societyId);
        $prep->execute();
        $result = $prep->get_result();

        $rr = $result->fetch_assoc();
        $datelessShows = ($rr["c"] > 0)?true:false;

        $db->close();
		
		// Find the image
		$theSociety['logo'] = soc_getPreferredImage($theSociety['id']);
		
        $app->render("api/cal_iframe.twig", [
            "society" => $theSociety,
            "upcomingShows" => $upcomingShows,
            "pastShows" => $yearShows,
            "members" => $yearMember,
            "year" => $year,
            "yearsData" => $yearsData,
            "datelessShows" => $datelessShows
        ]);

    }
)->name("api_calendar_embed");
?>