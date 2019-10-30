<?php

function shows_getImages($showId){
    $app = \Slim\Slim::getInstance();

    $url_path = $app->config('images.shows') . $showId . '/';
    $server_path = realpath($app->config('webroot') . $url_path);


    if($server_path != ''){
        $paths = array_diff(scandir($server_path), array('..', '.'));

        $names = array_map(function($p){
            return basename($p, '.jpg');
        }, $paths);

        $paths = array_map(function($p) use ($url_path) {
            return $url_path . $p;
        }, $paths);

        return array_combine($names, $paths);
    }else{
        return [];
    }

}

function shows_getImage($showId, $type){
    $app = \Slim\Slim::getInstance();

    $url_path = $app->config('images.shows') . $showId . '/';
    $server_path = realpath($app->config('webroot') . $url_path);

    if(is_file($server_path . '/' . $type . '.jpg')){
        return $url_path . $type . '.jpg';
    }else{
        return false;
    }
}

function shows_getPreferredImage($showId){
    // Covers are preferred over posters
    $cover = shows_getImage($showId, 'cover');
    if($cover){
        return $cover;
    }else{
        return shows_getImage($showId, 'poster');
    }
}


function shows_getAllFutureShows(){
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    // Get upcoming shows
    /*$query = "SELECT s.`id`, s.`name`, s.`societyId`, soc.`slug` AS 'societySlug', soc.`name` AS 'societyName', soc.`type` AS 'societyType', D.`lastShowDate`, D.`firstShowDate`, YEAR(D.`firstShowDate`) AS 'year'
            FROM `Show` s
            INNER JOIN (SELECT se.`showId`, MAX(se.`showDate`) AS 'lastShowDate', MIN(se.`showDate`) AS 'firstShowDate' FROM `ShowEvent` se GROUP BY se.`showId`) D ON s.`id` = D.`showId`
            INNER JOIN `Society` soc ON s.`societyId` = soc.`id`
            WHERE D.`lastShowDate` > NOW()
            ORDER BY D.`firstShowDate` ASC";*/
    $query = "SELECT `id`, `slug`, `name`, `societyId`, `societySlug`, `societyName`, `societyType`, `lastShowDate`, `firstShowDate`, `year`, `academicYear`
        FROM `Show_WithExpandedInfo`
        WHERE `lastShowDate` > NOW()
        ORDER BY `firstShowDate` ASC";



    $result = $db->query($query); // No input vars

    $upcomingShows = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
    $db->close();

    return $upcomingShows;
}


function shows_getAllShowsForYear($year){
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    // Get upcoming shows
    /*$query = "SELECT s.`id`, s.`name`, s.`societyId`, soc.`name` AS 'societyName', soc.`slug` AS 'societySlug', soc.`type` AS 'societyType', D.`lastShowDate`, D.`firstShowDate`, YEAR(D.`firstShowDate`) AS 'year'
        FROM `Show` s
        INNER JOIN (SELECT se.`showId`, MAX(se.`showDate`) AS 'lastShowDate', MIN(se.`showDate`) AS 'firstShowDate' FROM `ShowEvent` se GROUP BY se.`showId`) D ON s.`id` = D.`showId`
        INNER JOIN `Society` soc ON s.`societyId` = soc.`id`
        WHERE D.`lastShowDate` < FROM_UNIXTIME(?) AND D.`firstShowDate` > FROM_UNIXTIME(?)
        ORDER BY D.`firstShowDate` ASC";*/

    $query = "SELECT `id`, `slug`, `name`, `societyId`, `societySlug`, `societyName`, `societyType`, `lastShowDate`, `firstShowDate`, `year`, `academicYear`
        FROM `Show_WithExpandedInfo`
        WHERE `academicYear` = ?
        ORDER BY `firstShowDate` ASC";


    $lowerDate = getStartofAcademicYear($year);
    $upperDate = getEndofAcademicYear($year);

    $prep = $db->prepare($query);
    $prep->bind_param("i",
        //($upperDate->getTimestamp()), ($lowerDate->getTimestamp()));
        $year);
    $prep->execute();
    $result = $prep->get_result();

    $yearShows = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    return $yearShows;
}



function shows_getShowUrl(array $opts){
    $app = \Slim\Slim::getInstance();
    // Resolve the ID
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    $slug = isset($opts['slug']) ? $opts['slug'] : null;
    $year = isset($opts['year']) ? $opts['year'] : (isset($opts['academicYear']) ? $opts['academicYear'] : null);
    $society = isset($opts['society']) ? $opts['society'] : null;
    $id = isset($opts['id']) ? $opts['id'] : (isset($opts['show']) ? $opts['show'] : null);
    $name = isset($opts['name']) ? $opts['name'] : null;
    $yearless = ((isset($ops['yearless']) && $ops['yearless']) || $year === false);

    if($yearless){
        $year = "yearless";
    }

    // Already resolved, form the URL
    if($year && $society){
        // Try to get a slug
        if(!$slug && $name){
            $slug = admin_shows_createShowSlug($name);
        }

        // Convert to slug if numeric
        if(is_numeric($society)){
            $query = 'SELECT `slug` FROM `Society` WHERE `id` = ?';

            $prep = $db->prepare($query);
            $prep->bind_param("i",
                $society);
            $prep->execute();
            $result = $prep->get_result();

            $soc = $result->fetch_assoc();
            $result->free();
            $society = $soc['slug'];
        }

        if($slug){
            return $app->urlFor('pa_show', [ 'show' => $slug, 'year' => $year, 'society' => $society ]);
        }
    }


    if($id){
        $no_resolve = ((isset($opts['no_resolve']) && $opts['no_resolve'])
                    || (isset($opts['resolve']) && $opts['no_resolve'] === false));
        // Return the id url form
        if($no_resolve){
            return $app->urlFor("pa_show_byId", [ 'show' => $id ]);
        }

        $query = 'SELECT `slug`, `societySlug`, `academicYear` FROM `Show_WithExpandedInfo`
            WHERE `id` = ?';

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $id);
        $prep->execute();
        $result = $prep->get_result();

        $show = $result->fetch_assoc();
        $result->free();

        $year = isset($show['academicYear']) ? $show['academicYear'] : 'yearless';

        return $app->urlFor('pa_show', [ 'show' => $show['slug'], 'year' => $year, 'society' => $show['societySlug'] ]);
    }

    return $app->urlFor('pa_shows');
}


$app->get(
    '/shows',
    function () use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();
        
        $upcomingShows = shows_getAllFutureShows();

        // Get the possible type names
        $query = "SELECT * FROM `SocietyType`";

        $result = $db->query($query); // No input vars

        $societyTypes = [];
        while($row = $result->fetch_assoc()){
            if($row["id"] == 5){
                $row["name"] = "Multiple Societies";
            }
            $societyTypes[] = $row;
        }
        $result->free();


        // Can Adds shows?
        $canAdd = Auth::canAddShows();


        $app->render("shows/shows_index.twig", [
            "upcomingShows" => $upcomingShows,
            "societyTypes" => $societyTypes,
            "canAdd" => $canAdd
        ]);
    }
)->name("pa_shows");
// Alias
$app->get(
    '/show',
    function () use ($app) {
        $app->redirect($app->urlFor('pa_shows'), 301);
    }
);


$app->get(
    '/shows/year',
    function () use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $query = "SELECT a.`academicYear`, COUNT(DISTINCT(a.`societyId`)) AS 'societyNum', COUNT(a.`id`) AS 'showNum'
                FROM `Show_WithExpandedInfo` a
                WHERE a.`academicYear` IS NOT NULL
                GROUP BY a.`academicYear`
                ORDER BY a.`academicYear` DESC";

        $result = $db->query($query);

        $years = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();

        $app->render('shows/shows_years.twig', [
            'years' => $years
        ]);
    }
)->name("pa_shows_all_years");


$app->get(
    '/shows/year/:year',
    function ($year) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $yearShows = shows_getAllShowsForYear($year);

        // Get the possible type names
        $query = "SELECT * FROM `SocietyType`";

        $result = $db->query($query); // No input vars

        $societyTypes = [];
        while($row = $result->fetch_assoc()){
            if($row["id"] == 5){
                $row["name"] = "Multiple Societies";
            }
            $societyTypes[] = $row;
        }
        $result->free();


        // Can Adds shows?
        $canAdd = Auth::canAddShows();


        $app->render("shows/shows_index.twig", [
            "year" => $year,
            "upcomingShows" => $yearShows,
            "societyTypes" => $societyTypes,
            "canAdd" => $canAdd
        ]);
    }
)->name("pa_shows_year");


$app->get(
    '/shows/search',
    function () use ($app) {
        $app->render("shows/shows_search.twig");
    }
)->name("pa_shows_search");



/************* Function to render a show page ********************/
function renderShowPage($theShow){
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    $app = \Slim\Slim::getInstance();

    $showId = $theShow['id'];

    // Get dates
    $query = "SELECT UNIX_TIMESTAMP(`showDate`) AS 'date', `googleEventId` FROM `ShowEvent` WHERE `showId` = ?";

    $prep = $db->prepare($query);
    $prep->bind_param("i",
        $showId);
    $prep->execute();
    $result = $prep->get_result();




    $dates = [];
    $startDate = null;
    $endDate = null;
    $past = null;
    while($row = $result->fetch_assoc()){
        
        $dates[] = [
            "d" => $row["date"],
            "calId" => $row["googleEventId"]
        ];

    }
    if(count($dates) > 0){
        usort($dates, function($a, $b){
            return $a["d"] > $b["d"];
        });
        
        $endDate = end($dates)["d"];
        $startDate = reset($dates)["d"];

        // Detect whether this is the past or not
        $past = time() > $endDate;
    }

    // Get the Google Cal events if the show is in the future
    if(!$past){
        $calTypeId = admin_shows_getCalendarId($theShow['societyId']);

        foreach($dates as &$date) {
            if(isset($date["calId"])){
                try{
                    $e = PA_Google_Cal::getEvent($calTypeId, $date["calId"]);
                    $date["link"] = $e->htmlLink;
                }catch(Exception $e){}
            }
        }
    }

    // Get people
    /*$query = "SELECT m.`id`, m.`firstName`, m.`lastName`, m.`chosenName`, ra.`areaName`, r.`name` AS 'roleName', sr.`notes`
         FROM `ShowRole` sr INNER JOIN `Member` m ON sr.`memberId` = m.`id`
         INNER JOIN `Role` r ON sr.`roleId` = r.`id` INNER JOIN `RoleArea` ra ON r.`roleAreaId` = ra.`id`
         WHERE sr.`showId` = " . $showId . " ORDER BY r.name, ra.`id`";*/
    // Using the View that combines registered and suggested members
    $query = "SELECT sr.`memberId`, sr.`suggestedMemberId`, sr.`firstName`, sr.`lastName`, sr.`chosenName`, ra.`areaName`, r.`name` AS 'roleName', sr.`notes`
            FROM `All_ShowRole` sr 
            INNER JOIN `Role` r ON sr.`roleId` = r.`id` INNER JOIN `RoleArea` ra ON r.`roleAreaId` = ra.`id`
            WHERE sr.`showId` = ? ORDER BY r.`name`, ra.`id`";


    $prep = $db->prepare($query);
    $prep->bind_param("i",
        $showId);
    $prep->execute();
    $result = $prep->get_result();


    $roles = [];
    $memTot = 0;
    $memSeen = [];
    $suggestMemSeen = [];
    while($row = $result->fetch_assoc()){
        $area = $row["areaName"];
        $role = $row["roleName"];

        if(!isset($roles[$area])){
            $roles[$area] = [];
        }
        if(!isset($roles[$area][$role])){
            $roles[$area][$role] = [];
        }

        $row = derivePreferredName($row);

        // Increment total members in production if not seen before
        if(isset($row["memberId"])){
            if(!in_array($row["memberId"], $memSeen)){
                $memTot++;
                $memSeen[] = $row["memberId"];
            }
        }
        if(isset($row["suggestedMemberId"])){
            if(!in_array($row["suggestedMemberId"], $suggestMemSeen)){
                $memTot++;
                $suggestMemSeen[] = $row["suggestedMemberId"];
            }
        }


        $roles[$area][$role][] = $row;
    }


    $result->free();


    // Get the shows images 
    $images = shows_getImages($showId);

    // Get the society information
    $query = "SELECT s.*, st.`name` AS 'societyType' FROM `Society` s INNER JOIN `SocietyType` st ON s.`type` = st.`id` WHERE s.`slug` = ?";

    $prep = $db->prepare($query);
    $prep->bind_param("s",
        $theShow['societySlug']);
    $prep->execute();
    $society = $prep->get_result()->fetch_assoc();


    $society['logo'] = soc_getPreferredImage($society['id']);

    // Get the venue image
    $dir = "/home/perform/webroot/img/archive/venues/".$theShow['venueId'];
    if (file_exists($dir)) {
       if (count(scandir($dir))>2) {
          $theShow['venueImage'] = "/img/archive/venues/".$theShow['venueId']."/".scandir($dir)[2];
       } else {
          $theShow['venueImage'] = null;
       }
    } else {
       $theShow['venueImage'] = null;
    }


    $app->render("shows/shows.twig", [
        "show" => $theShow,
        "dates" => [ "start" => $startDate, "end" => $endDate, "past" => $past, "all" => $dates ],
        "roles" => $roles,
        "totalMembers" => $memTot,
        "images" => $images,
        "society" => $society
    ]);
}


/***************************
    The big assumption here is that a society does not do a show with the same name in the same year
****************************/
$app->get(
    '/societies/:society/:year/:show',
    function ($society, $year, $show) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Get the show info from the database..
        $query = "SELECT * FROM `Show_WithExpandedInfo`
            WHERE `academicYear` = ?
            AND `societySlug` = ? AND `slug` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("iss",
            $year, $society, $show);
        $prep->execute();
        $result = $prep->get_result();

        $theShow = $result->fetch_assoc();
        $result->free();

        if(!isset($theShow)){
            // Flash something here please
            $app->notFound();
            return;
        }

        renderShowPage($theShow);
    }
)->name("pa_show");

$app->get(
    '/societies/:society/:yearless/:show',
    function ($society, $year, $show) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Get the show info from the database..
        $query = "SELECT * FROM `Show_WithExpandedInfo`
            WHERE `academicYear` IS NULL
            AND `societySlug` = ? AND `slug` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("ss",
            $society, $show);
        $prep->execute();
        $result = $prep->get_result();

        $theShow = $result->fetch_assoc();
        $result->free();

        if(!isset($theShow)){
            // Flash something here please
            $app->notFound();
            return;
        }

        renderShowPage($theShow);
    }
)->conditions([ 'yearless' => 'yearless' ]);



$app->get(
    '/shows/:show',
    function ($show) use ($app) {
        // Convert ID to slug
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Get the slug and redirect
        $query = "SELECT `societySlug`, `slug`, `academicYear` FROM `Show_WithExpandedInfo` WHERE `id` = ?";
        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $show);
        $prep->execute();
        $result = $prep->get_result();

        $s = $result->fetch_assoc();
        $result->free();

        if(isset($s)){
            $params = [
                'show' => $s['slug'],
                'society' => $s['societySlug'],
                'year' => $s['academicYear']
            ];

            $app->redirect($app->urlFor('pa_show', $params), 301);
        }else{
            $app->notFound();
        }
    }
)->name("pa_show_byId")->setConditions(['show' => '[0-9]+']);

/***
$app->get(
    '/shows/:show',
    function ($show) use ($app) {
    	global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Can only access by ID for now
        $showId = $show;

        // Get the show info from the database..
        $query = "SELECT s.`id` AS 'showId', s.`name` AS 'showName', s.`description`, s.`ticketDetails`, s.`ticketSource`, s.`societyPage`,
            s.`societyId`, soc.`slug` AS 'societySlug', soc.`name` AS 'societyName', soc.`showBaseUrl`, soc.`type` AS 'societyTypeId',
            s.`venueId`, v.`name` AS 'venueName', Y(v.`location`) as 'venueLat', X(v.`location`) as 'venueLon'
            FROM `Show` s LEFT JOIN `Society` soc ON s.`societyId` = soc.`id` LEFT JOIN `Venue` v ON s.`venueId` = v.`id` WHERE s.`id` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $showId);
        $prep->execute();
        $result = $prep->get_result();

        $theShow = $result->fetch_assoc();
        $result->free();

        if(!isset($theShow)){
            // Flash something here please
            $app->notFound();
            return;
        }

        // Get dates
        $query = "SELECT UNIX_TIMESTAMP(`showDate`) AS 'date', `googleEventId` FROM `ShowEvent` WHERE `showId` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $showId);
        $prep->execute();
        $result = $prep->get_result();




        $dates = [];
        $startDate = null;
        $endDate = null;
        $past = null;
        while($row = $result->fetch_assoc()){
            
            $dates[] = [
                "d" => $row["date"],
                "calId" => $row["googleEventId"]
            ];

        }
        if(count($dates) > 0){
            usort($dates, function($a, $b){
                return $a["d"] > $b["d"];
            });
            
            $endDate = end($dates)["d"];
            $startDate = reset($dates)["d"];

            // Detect whether this is the past or not
            $past = time() > $endDate;
        }

        // Get the Google Cal events if the show is in the future
        if(!$past){
            $calTypeId = admin_shows_getCalendarId($theShow['societyId']);

            foreach($dates as &$date) {
                if(isset($date["calId"])){
                    try{
                        $e = PA_Google_Cal::getEvent($calTypeId, $date["calId"]);
                        $date["link"] = $e->htmlLink;
                    }catch(Exception $e){}
                }
            }
        }

        // Using the View that combines registered and suggested members
        $query = "SELECT sr.`memberId`, sr.`suggestedMemberId`, sr.`firstName`, sr.`lastName`, sr.`chosenName`, ra.`areaName`, r.`name` AS 'roleName', sr.`notes`
                FROM `All_ShowRole` sr 
                INNER JOIN `Role` r ON sr.`roleId` = r.`id` INNER JOIN `RoleArea` ra ON r.`roleAreaId` = ra.`id`
                WHERE sr.`showId` = ? ORDER BY r.`name`, ra.`id`";


        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $showId);
        $prep->execute();
        $result = $prep->get_result();


        $roles = [];
        $memTot = 0;
        $memSeen = [];
        $suggestMemSeen = [];
        while($row = $result->fetch_assoc()){
            $area = $row["areaName"];
            $role = $row["roleName"];

            if(!isset($roles[$area])){
                $roles[$area] = [];
            }
            if(!isset($roles[$area][$role])){
                $roles[$area][$role] = [];
            }

            $row = derivePreferredName($row);

            // Increment total members in production if not seen before
            if(isset($row["memberId"])){
                if(!in_array($row["memberId"], $memSeen)){
                    $memTot++;
                    $memSeen[] = $row["memberId"];
                }
            }
            if(isset($row["suggestedMemberId"])){
                if(!in_array($row["suggestedMemberId"], $suggestMemSeen)){
                    $memTot++;
                    $suggestMemSeen[] = $row["suggestedMemberId"];
                }
            }


            $roles[$area][$role][] = $row;
        }


        $result->free();
        $db->close();


        // Get the shows images 
        $images = shows_getImages($showId);


        // Find out if this show is editable
        $canEdit = Auth::canEditGivenShow($showId);



        $app->render("shows/shows.twig", [
            "show" => $theShow,
            "dates" => [ "start" => $startDate, "end" => $endDate, "past" => $past, "all" => $dates ],
            "roles" => $roles,
            "totalMembers" => $memTot,
            "images" => $images,
            "canEdit" => $canEdit
        ]);
    }
)->name("show");
*/
// Alias
$app->get(
    '/show/:path+',
    function ($path) use ($app) {
        $url = $app->urlFor("pa_shows");
        if(count($path) > 0){
            $url = $url . "/" . implode("/", $path);
        }
        $app->redirect($url, 301);
    }
);



