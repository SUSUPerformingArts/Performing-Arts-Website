<?php

function soc_getShowsForAcademicYear($socId, $year, $beforeNow = false){
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    $lowerDate = getStartofAcademicYear($year);
    $upperDate = null;
    if($beforeNow){
        $upperDate = new DateTime();
    }else{
        $upperDate = getEndofAcademicYear($year);
    }


    /*$query = "SELECT s.`id`, s.`name`, s.`societyId`, soc.`slug` AS 'societySlug', soc.`name` AS 'societyName', D.`firstShowDate`, D.`lastShowDate`, D.`firstShowDate` AS 'showDate', YEAR(D.`firstShowDate`) AS 'year
        FROM `Show` s
        INNER JOIN (SELECT se.`showId`, MAX(se.`showDate`) AS 'lastShowDate', MIN(se.`showDate`) AS 'firstShowDate' FROM `ShowEvent` se GROUP BY se.`showId`) D ON s.`id` = D.`showId`
        INNER JOIN `Society` soc ON s.`societyId` = soc.`id`
        WHERE D.`lastShowDate` < FROM_UNIXTIME(?) AND D.`firstShowDate` > FROM_UNIXTIME(?) AND s.`societyId` = ?
        ORDER BY D.`firstShowDate` ASC";*/
    $query = "SELECT s.`id`, s.`slug`, s.`name`, s.`societyId`, s.`societySlug`, s.`societyName`,
        s.`firstShowDate`, s.`lastShowDate`, s.`firstShowDate` AS 'showDate', `year`, `academicYear`
        FROM `Show_WithExpandedInfo` s
        WHERE s.`lastShowDate` < FROM_UNIXTIME(?) AND s.`firstShowDate` > FROM_UNIXTIME(?) AND s.`societyId` = ?
        ORDER BY s.`firstShowDate` ASC";


    $prep = $db->prepare($query);
    $prep->bind_param("iii",
        ($upperDate->getTimestamp()), ($lowerDate->getTimestamp()), $socId);
    $prep->execute();
    $result = $prep->get_result();

    $r = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
    $db->close();

    return $r;
}


// Extra function for stagesoc involvement!
function soc_getShowsForAcademicYear_withStagesoc($year, $beforeNow = false, $socId = null){
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    $lowerDate = getStartofAcademicYear($year);
    $upperDate = null;
    if($beforeNow){
        $upperDate = new DateTime();
    }else{
        $upperDate = getEndofAcademicYear($year);
    }


    $query = "SELECT s.`id`, s.`slug`, s.`name`, s.`societyId`, s.`societySlug`, s.`societyName`,
        s.`firstShowDate`, s.`lastShowDate`, s.`firstShowDate` AS 'showDate', `year`, `academicYear`
        FROM `Show_WithExpandedInfo` s
        WHERE s.`lastShowDate` < FROM_UNIXTIME(?) AND s.`firstShowDate` > FROM_UNIXTIME(?) AND s.`stagesoc` = TRUE
        ORDER BY s.`firstShowDate` ASC";


    $prep = $db->prepare($query);
    $prep->bind_param("ii",
        ($upperDate->getTimestamp()), ($lowerDate->getTimestamp()));
    $prep->execute();
    $result = $prep->get_result();

    $r = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
    $db->close();
    return $r;
}




function soc_getMembersForAcademicYear($socId, $year){
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    /*$query = "SELECT m.`id`, m.`chosenName`, m.`firstName`, m.`lastName`, com.`name` AS 'committeePosition'
            FROM `SocietyMember` sp
            INNER JOIN `Member` m ON sp.`memberId` = m.`id`
            LEFT JOIN `CommitteePosition` com ON sp.`committeePositionId` = com.`id`
            WHERE sp.`societyId` = ? AND sp.`year` = ? GROUP BY m.`id` ORDER BY m.`lastName`, -com.`id` DESC";*/

    $query = "SELECT sp.`memberId`, sp.`suggestedMemberId`, sp.`chosenName`, sp.`firstName`, sp.`lastName`, com.`name` AS 'committeePosition'
            FROM `All_SocietyMember` sp
            LEFT JOIN `CommitteePosition` com ON sp.`committeePositionId` = com.`id`
            WHERE sp.`societyId` = ? AND sp.`year` = ? GROUP BY sp.`memberId` ORDER BY -com.`id` DESC, sp.`lastName`";

    $prep = $db->prepare($query);
    $prep->bind_param("ii",
        $socId, $year);
    $prep->execute();
    $result = $prep->get_result();

    $r = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
    $db->close();
    return $r;
}



function soc_getUpcomingShows($socId = null){
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    /*$query = "SELECT s.`id`, s.`name`, s.`societyId`, soc.`slug` AS 'societySlug', soc.`name` AS 'societyName', D.`showDate`, YEAR(D.`showDate`) AS 'year'
            FROM `Show` s
            INNER JOIN (SELECT se.`showId`, MAX(se.`showDate`) AS 'showDate' FROM `ShowEvent` se GROUP BY se.`showId`) D ON s.`id` = D.`showId`
            INNER JOIN `Society` soc ON s.`societyId` = soc.`id`
            WHERE D.`showDate` > NOW() AND s.`societyId` = ?
            ORDER BY D.`showDate` ASC";*/
    $query = 'SELECT s.`id`, s.`slug`, s.`name`, s.`societyId`, s.`societySlug`, s.`societyName`, s.`firstShowDate` AS "showDate", `year`, `academicYear`
        FROM Show_WithExpandedInfo s
            WHERE s.`firstShowDate` > NOW() AND s.`societyId` = ?
            ORDER BY s.`firstShowDate` ASC';

    $prep = $db->prepare($query);
    $prep->bind_param("i",
        $socId);
    $prep->execute();
    $result = $prep->get_result();

    $r = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
    $db->close();
    
    return $r;
}

function soc_getUpcomingShows_withStagesoc($socId = null){
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();


    $query = 'SELECT s.`id`, s.`slug`, s.`name`, s.`societyId`, s.`societySlug`, s.`societyName`, s.`firstShowDate` AS "showDate", `year`, `academicYear`
        FROM Show_WithExpandedInfo s
            WHERE s.`firstShowDate` > NOW() AND s.`stagesoc` = TRUE
            ORDER BY s.`firstShowDate` ASC';


    $result = $db->query($query); // No variables, just query

    $r = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
    $db->close();

    return $r;
}




function soc_getImages($societyId) {
    $app = \Slim\Slim::getInstance();

    $images = [];
    // Does a profile image exist?
    $img_path = $app->config('images.societies');
    $webroot = $app->config('webroot');
    $image_names = [
        'logo_jpg' => 'logo.jpg',
        'logo_png' => 'logo.png'
    ];
    $image_path = $img_path . $societyId . '/';
	
    // Check & add URL to the images object
    foreach($image_names as $rname => $fname){
        if(is_file(realpath($webroot . $image_path . $fname))){
            $images[$rname] = $image_path . $fname;
        }
    }

    return $images;
}

function soc_getPreferredImage($societyId){
    $imgs = soc_getImages($societyId);
    if(isset($imgs['logo_png'])){
        return $imgs['logo_png'];
    }
    if(isset($imgs['logo_jpg'])){
        return $imgs['logo_jpg'];
    }
    return null;
}




// Middleware to convert to an ID to a slug
$soc_toSlug = function(\Slim\Route $route){
    $app = \Slim\Slim::getInstance();
    $params = $route->getParams();
    $society = $params['society'];

    // Convert ID to slug
    if(is_numeric($society)){
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Get the slug and redirect
        $query = "SELECT `slug` FROM `Society` WHERE `id` = ?";
        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $society);
        $prep->execute();
        $result = $prep->get_result();

        $s = $result->fetch_assoc();
        $result->free();

        if(isset($s)){
            $params['society'] = $s['slug'];

            $app->redirect($app->urlFor($route->getName(), $params), 301);
        }else{
            $app->notFound();
        }
        return;
    }
};

function renderSocieties($app, $type=0) {
	global $pd;
	// Get the database
	$db = $pd->getPerformArchive();

	// Get all the societies
	$query = "SELECT * FROM `Society` ORDER BY `name`";

	$result = $db->query($query); // No input

	$socs = $result->fetch_all(MYSQLI_ASSOC);
	$result->free();

	$db->close();
	
	$societies = array();
	
	foreach ($socs as $i => $soc) {
		$soc['image'] = soc_getPreferredImage($soc['id']);
		array_push($societies, $soc);
	}

	$app->render("societies/societies_index.twig", [
		"societies" => $societies,
		"type" => $type
	]);
}

$app->get(
    '/societies',
    function () use ($app) {
        renderSocieties($app);
    }
)->name("societies");
// Alias
$app->get(
    '/society',
    function () use ($app) {
        $app->redirect($app->urlFor('societies'), 301);
    }
);

$app->get(
    '/societies/:type',
    function ($type) use ($app) {
        if ($type=="music") {
			renderSocieties($app, 2);
		} elseif ($type=="theatrical") {
			renderSocieties($app, 1);
		} elseif ($type=="dance") {
			renderSocieties($app, 3);
		} else {
			renderSocieties();
		}
    }
)->name("pa_societies_by_type");



$app->get(
    '/society/:society',
    $soc_toSlug,
    function ($society) use ($app) {
    	global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $query = "SELECT s.*, st.`name` AS 'societyType' FROM `Society` s INNER JOIN `SocietyType` st ON s.`type` = st.`id` WHERE s.`slug` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("s",
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
		
        $app->render("societies/societies.twig", [
            "society" => $theSociety,
            "upcomingShows" => $upcomingShows,
            "pastShows" => $yearShows,
            "members" => $yearMember,
            "year" => $year,
            "yearsData" => $yearsData,
            "datelessShows" => $datelessShows
        ]);

    }
)->name("pa_society");
// Alias
$app->get(
    '/society/:path+',
    function ($path) use ($app) {
        $url = $app->urlFor("pa_societies");
        if(count($path) > 0){
            $url = $url . "/" . implode("/", $path);
        }
        $app->redirect($url, 301);
    }
);


// Shows at soceites shows for given year
$app->get(
    '/societies/:society/:year',
    $soc_toSlug,
    function ($society, $year) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Get the info about the society
        $query = "SELECT s.*, st.`name` AS 'societyType' FROM `Society` s INNER JOIN `SocietyType` st ON s.`type` = st.`id` WHERE s.`slug` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("s",
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

        // Can only access by ID for now
        $societyId = $theSociety['id'];

        // Get the this year's shows
        $yearShows = soc_getShowsForAcademicYear($societyId, $year);

        /***** Special condition for StageSoc *****/
        if($societyId == 25){
            $yearShows = array_merge($yearShows, soc_getShowsForAcademicYear_withStagesoc($year));
        }

        // Get years members
        $rows = soc_getMembersForAcademicYear($societyId, $year);

        $yearMember = [];
        foreach($rows as $row){
            $row = derivePreferredName($row);

            $yearMember[] = $row;
        }


        $db->close();

        $app->render("societies/societies_year.twig", [
            "society" => $theSociety,
            "pastShows" => $yearShows,
            "members" => $yearMember,
            "year" => $year
        ]);
    }
)->name("pa_society_yearShows");




// Route for showing shows that have no dates attached to them!
$app->get(
    '/societies/:society/yearless',
    $soc_toSlug,
    function ($society) use ($app){
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Get the info about the society
        $query = "SELECT s.*, st.`name` AS 'societyType' FROM `Society` s INNER JOIN `SocietyType` st ON s.`type` = st.`id` WHERE s.`slug` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("s",
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

        // Get shows, this is a much simplier query!
        $query = "SELECT s.`id`, s.`slug`, s.`name`, s.`societyId`, s.`societySlug`, s.`societyName`
                FROM `Show_WithExpandedInfo` s
                WHERE s.`academicYear` IS NULL
                AND s.`societyId` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $societyId);
        $prep->execute();
        $result = $prep->get_result();

        $yearShows = $result->fetch_all(MYSQLI_ASSOC);

        $db->close();

        $yearMember = null;
        $year = "?";

        $app->render("societies/societies_year.twig", [
            "society" => $theSociety,
            "pastShows" => $yearShows,
            "members" => $yearMember,
            "year" => $year
        ]);

    }

)->name("pa_society_yearNone");


