<?php


$app->get(
    '/venues',
    function () use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Get all the venues
        $query = "SELECT * FROM `Venue`";

        $result = $db->query($query); // No input

        $venues = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();

        $db->close();
        

        $app->render("venues/venues_index.twig", [
            "venues" => $venues
        ]);
    }
)->name("pa_venues");
// Alias
$app->get(
    '/venue',
    function () use ($app) {
        $app->redirect($app->urlFor('venues'), 301);
    }
);



$app->get(
    '/venues/:venue',
    function ($venue) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Can only access by ID for now
        $venueId = $venue;

        // Get the info about the venue
        $query = "SELECT `id`, `name`, `description`, Y(`location`) as 'venueLat', X(`location`) as 'venueLon' FROM `Venue` WHERE `id` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $venueId);
        $prep->execute();
        $result = $prep->get_result();

        $theVenue = $result->fetch_assoc();
        $result->free();

        if(!isset($theVenue)){
            // Flash something here please
            $app->notFound();
            return;
        }

        // Get the future shows
        /*$query = "SELECT s.`id`, s.`name`, s.`societyId`, soc.`slug` AS 'societySlug', soc.`name` AS 'societyName', D.`showDate`, YEAR(D.`showDate`) AS 'year'
            FROM `Show` s
            INNER JOIN (SELECT se.`showId`, MAX(se.`showDate`) AS 'showDate' FROM `ShowEvent` se GROUP BY se.`showId`) D ON s.`id` = D.`showId`
            INNER JOIN `Society` soc ON s.`societyId` = soc.`id`
            WHERE D.`showDate` > NOW() AND s.`venueId` = ?
            ORDER BY D.`showDate` ASC";*/
        $query = "SELECT s.`id`, s.`slug`, s.`name`, s.`societyId`, s.`societySlug`, s.`societyName`, s.`year`, s.`academicYear`
            FROM `Show_WithExpandedInfo` s
            WHERE s.`lastShowDate` > NOW() AND s.`venueId` = ?
            ORDER BY s.`lastShowDate` ASC";


        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $venueId);
        $prep->execute();
        $result = $prep->get_result();

        /* Complete later for historical shows
        $shows = [];
        while($row = $result->fetch_assoc()){
            $year = $row["year"];
            if(!isset($year)){
                $shows[$year] = [];
            }

            $shows[$year][] = $row;
        }
        $result->free();*/

        $upcomingShows = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();


        $db->close();
         
        $dir = "/home/perform/webroot/img/archive/venues/".$theVenue['id'];
        if (file_exists($dir)) {
           if (count(scandir($dir))>2) {
              $theVenue['image'] = "/img/archive/venues/".$theVenue['id']."/".scandir($dir)[2];
           } else {
              $theVenue['image'] = null;
           }
        } else {
           $theVenue['image'] = null;
        }

        $app->render("venues/venues.twig", [
            "venue" => $theVenue,
            "upcomingShows" => $upcomingShows
        ]);

    }
)->name("pa_venue");
// Alias
$app->get(
    '/venue/:path+',
    function ($path) use ($app) {
        $url = $app->urlFor("pa_venues");
        if(count($path) > 0){
            $url = $url . "/" . implode("/", $path);
        }
        $app->redirect($url, 301);
    }
);


// Shows at venue for given year
$app->get(
    '/venues/:venue/:year',
    function ($venue, $year) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Can only access by ID for now
        $venueId = $venue;

        // Get the info about the venue
        $query = "SELECT `id`, `name`, `description`, Y(`location`) as 'venueLat', X(`location`) as 'venueLon' FROM `Venue` WHERE `id` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $venueId);
        $prep->execute();
        $result = $prep->get_result();

        $theVenue = $result->fetch_assoc();
        $result->free();

        if(!isset($theVenue)){
            // Flash something here please
            $app->notFound();
            return;
        }

        // Get the future shows

        $query = "SELECT s.`id`, s.`slug`, s.`name`, s.`societyId`, s.`societySlug`, s.`societyName`, s.`year`, s.`academicYear`
            FROM `Show_WithExpandedInfo` s
            WHERE s.`academicYear` = ? AND s.`venueId` = ?
            ORDER BY s.`lastShowDate` ASC";


        $prep = $db->prepare($query);
        $prep->bind_param("ii",
            $year, $venueId);
        $prep->execute();
        $result = $prep->get_result();


        $yearShows = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();

        $db->close();

        $app->render("venues/venues.twig", [
            "venue" => $theVenue,
            "pastShows" => $yearShows,
            "year" => $year
        ]);
    }
)->name("pa_venue_yearShows");

