<?php

function admin_shows_createShowSlug($name){
    $slugify = new \Cocur\Slugify\Slugify();
    /*$pieces = str_word_count($name, 1);
    if(count($pieces) > 4){
        $name = implode(" ", array_splice($pieces, 0, 4));
    }*/
    return $slugify->slugify($name);
}


function admin_shows_invalidShowEdit($data, $showId = null){
    $app = \Slim\Slim::getInstance();
    // Convert to what is expected by the template
    /**** THIS IS ALL BROKEN AND NEEDS TO BE REDONE ***/
    $show = [
        "societyId" => $data["show_society"],
        "societyName" => $data["show_society_name"],
        "stagesoc" => $data["show_stagesoc"],
        "showName" => $data["show_name"],
        "description" => $data["show_description"],
        "venueId" => $data["show_venue"],
        "ticketDetails" => $data["show_tickets"],
        "ticketSource" => $data["tix"]
    ];

    if(isset($showId)){
        $images = [
            'preferred' => shows_getPreferredImage($showId)
        ];
    }

    // Get the show dates and convert them into arrays that the template expects
    $dates = array_map(function($date){
        return [ 'showDate' => $date ];
    }, $data["show_dates"]);

    $app->flashNow("errors", $data["errors"]);
    $app->render("shows/shows_create.twig", [
        "edit" => !!$showId,
#        "images" => $images,
        "societies" => $data["societies"],
        "venues" => $data["venues"],
        "show" => $show,
        "dates" => $dates,
        "errors" => $data["errors"]
    ], 400);
}


function admin_getAllowedSocieties(){
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    // Get all of the societies
    $query = "SELECT socs.`id`, socs.`slug`, socs.`name`, socs.`teamup_edit_link`, socs.`type` AS 'typeId', type.`name` AS 'type'
            FROM `Society` socs LEFT JOIN `SocietyType` type ON socs.`type` = type.`id`";


    if(!Auth::isOnPACommittee()){ // PA committee members can make for all!
        // Get the array of societies
        $comms = Auth::getCurrentCommittees();
        if(!isset($comms) || count($comms) == 0){
            return [];
        }
        
        $query .= " WHERE socs.`id` IN (" . implode(",", $comms) . ") ";
    }

    $query .= " ORDER BY socs.`type` ASC, `name` ASC";

    $result = $db->query($query); // Controlled input, plus there is no easy way to bind arrays for some stupid reason

    $socs = [];
    $curr = null;
    while($row = $result->fetch_assoc()){
        if($curr !== $row["type"]){
            $curr = $row["type"];
            if(!isset($socs[$curr])){
                $socs[$curr] = [];
            }
        }

        $socs[$curr][] = $row;
    }

    //$socs = ["Test" => [[ "name" => "Society 1" ], ["name" => "Society 2"]]];

    $result->free();
    $db->close();

    return $socs;
}

function admin_getAllowedVenues(){
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    // Get the venues
    $query = "SELECT `id`, `name`, Y(`location`) as 'lat', X(`location`) as 'lon' FROM `Venue`";

    $result = $db->query($query);

    $venues = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    $db->close();

    return $venues;
}

function admin_getRoles($societyTypeId = null){
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    $prep = null;

    $query = "SELECT r.`id`, r.`name`, r.`roleAreaId`, ra.`areaName` FROM `Role` r
        INNER JOIN `RoleArea` ra ON `roleAreaId` = ra.`id` WHERE r.`societyTypeId` IS NULL";

    if(isset($societyTypeId)){
        $query .= " OR  r.`societyTypeId` = ? ORDER BY `roleAreaId`, `name`";
        $prep = $db->prepare($query);
        $prep.bind_param("i", $societyTypeId);
    }else{
        $query .= " ORDER BY `roleAreaId`, `name`";
        $prep = $db->prepare($query);
    }

    $prep->execute();

    $result = $prep->get_result();

    $roles = [];
    while($row = $result->fetch_assoc()){
        $an = $row["areaName"];
        if(!isset($roles[$an])){
            $roles[$an] = [];
        }


        $roles[$an][] = $row;
    }


    return $roles;
}


// Checks post data and returns the errors
function admin_shows_checkPostData($post){
    $errors = [];

    // Check for the required items existance
    if(!isset($post["show_society"])){
        $errors[] = "Please select a society";
    }elseif(!is_numeric($post["show_society"])){
        $errors[] = "Invalid Society";
    }


    if(!isset($post["show_name"])){
        $errors[] = "Please provide a show name";
    }elseif(strlen($post["show_name"]) == 0){
        $errors[] = "Show name too short";
    }


    if(isset($post["show_venue"]) && $post["show_venue"] !== "" && !is_numeric($post["show_venue"])){
        $errors[] = "Invalid venue";
    }


    if(!isset($post["show_dates"]) || (count($post["show_dates"]) == 0)){
        $errors[] = "Please select show dates";
    }

    return [ "errors" => (count($errors) > 0), "messages" => $errors ];
}

// Identifies if ths given scociety ID exists in the array of societies
// Returns the venue on success and false on falure 
function admin_shows_getSocIfInArray($socId, $socsAllowed){
    $canSoc = false;
    foreach ($socsAllowed as $socList) {
        foreach ($socList as $soc) {
            if($soc["id"] == $socId){
                $canSoc = $soc;
                break 2;
            }
        }
    }

    return $canSoc;
}

// Identifies if ths given venue ID exists in the array of venues
// Returns the venue on success and false on falure 
function admin_shows_getVenueIfInArray($venueId, $venuesAllowed){
    $canVenue = false;
    foreach ($venuesAllowed as $venue) {
        if($venue["id"] == $venueId){
            $canVenue = $venue;
            break;
        }
    }

    return $canVenue;
}


function admin_shows_getCalendarId($societyId){
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    // Get the type of the society to determine which calendar to put it in
    $calendarId = null;
    // Special case for these, we need to put them in their own calendar
    switch ($societyId) {
        case 41:
            $calendarId = 3; // Dance socs
            break;

        case 42:
            $calendarId = 2; // Music socs
            break;

        case 43:
            $calendarId = 1; // Theatrical socs
            break;
        
        default: {
            // Get the society's type ID
            $query = "SELECT `type` FROM `Society` WHERE `id` = ?";

            $prep = $db->prepare($query);
            $prep->bind_param("i",
                $societyId);
            $prep->execute();
            $result = $prep->get_result();

            $calendarId = $result->fetch_assoc();
            $calendarId = $calendarId["type"];

            $result->free();
            $db->close();
            break;
        }
    }

    return $calendarId;
}

function admin_shows_getTeamCalendarId($societyId){
	switch(admin_shows_getCalendarId($societyId)) {
		case 1:
			return 1977700;
			break;
		case 2:
			return 1977699;
			break;
		case 3:
			return 1977684;
			break;
	}
	return 0;
}


// Create and validate the upload object
// Returns array of errors on if invalid or the \Upload\File object on success
// MOVE THIS OUTSIDE AND MAKE THE IMAGE UPLOAD ERRORS JUST A WARNING
// POSSBILY MAKE THEM ERRORS WHEN UPDATING A SHOW, BUT WARNINGS WILL ALSO DO FOR NOW!
function admin_shows_uploadImage($varName, $showId){
    $app = \Slim\Slim::getInstance();

    $img_path = $app->config('images.shows');
    $webroot = $app->config('webroot');
    $img_folder = $webroot . $img_path . $showId . '/'; // Folder for the user's images

    // Create if it does not exist
    if(!file_exists($img_folder)){
        mkdir($img_folder, 0775, true);
    }


    $img_folder = realpath($img_folder);

    $storage = new \Upload\Storage\FileSystem($img_folder, true); // Overwrite = true
    $file = new \Upload\File($varName, $storage);

    $file->addValidations([
        // Ensure file is an image
        new \Upload\Validation\Mimetype(['image/png', 'image/jpg', 'image/jpeg', 'image/gif']),

        // We don't need to worry about file size too much as it's going to be shrunk down
        new \Upload\Validation\Size('15M')
    ]);


    if($file->isUploadedFile()){
        $dim = $file->getDimensions();
        $type = null;

        if($dim['width'] > 1.5*($dim['height'])){
            // Cover photo
            $type = 'cover';
        }else{
            // Poster photo
            $type = 'poster';
        }
        $file->setName($type);

        try{
            $file->upload();


            // Do some imagemagick!

            // Max dimensions
            if($type === 'cover'){
                $max_width = "1300";
                $max_height = "600";
            }else{
                $max_width = "600";
                $max_height = "700";
            }

            $name_ex = $file->getNameWithExtension();
            $load = $img_folder . "/" . $name_ex; // Path to load

            $name = $file->getName();
            $save = $img_folder . "/" . $name . ".jpg"; // Path to save (always a jpg)

            $imagick = new Imagick();
            //$image->setOption('jpeg:size', ($max_width . "x" . $max_height)); // Can optimise loading of jpgs (can't make this work at the moment)
            $imagick->readImage(realpath($load));

            // Add compression to decrease size
            $imagick->setImageCompressionQuality(85);

            // Decrease size & strip
            if($dim["width"] > $max_width || $dim["height"] > $max_height){
                $imagick->thumbnailImage($max_width, $max_height, true);
            }

            // Unlink everything here (needs to be redone if we want multiple images)
            array_map('unlink', glob($img_folder . '/*'));


            $imagick->writeImage($save);
            $imagick->clear();

            /* Done above through full unlinking
            if($load != $save){
                unlink($load); // Delete the loaded file if it differs from the saved one (different file ext)
            }*/

            return true;
        }catch(\Exception $e){
            // Fail!
            return $file->getErrors();
        }
    }else{
        return true; // No file uploaded
    }
}



function admin_shows_convertDateData($post){
    $result = [];

    if(isset($post["show_dates"])){
        // Convert the dates
        foreach ($post["show_dates"] as $date) {
            if(!!$date){
                try {
                    $result[] = DateTime::createFromFormat("d/m/Y g:ia", trim($date), new DateTimeZone("Europe/London"));
                }catch(Exception $e){}
            }
        }
        array_unique($result, SORT_REGULAR);
    }

    return $result;
}


// Validates the show post data against a given user
// Returns the new data
// On failure valid = false and errors will contain the error messages
function admin_shows_validateAllDataForShow($user, $post){
    $result = [];

    // Get the valid societies
    $socs = admin_getAllowedSocieties();
    // Get valid venues
    $venues = admin_getAllowedVenues();


    // Trim text data
    $result["show_name"] = (isset($post["show_name"])) ? trim($post["show_name"]) : null;
    $result["show_description"] = (isset($post["show_description"])) ? trim($post["show_description"]) : null;
    $result["show_tickets"] = (isset($post["show_tickets"])) ? trim($post["show_tickets"]) : null;
    $result["show_ticketURL"] = (isset($post["show_ticketURL"])) ? trim($post["show_ticketURL"]) : null;

    // Convert the boolean data
    $result["show_stagesoc"] = (isset($post["show_stagesoc"]))?1:0;

    // TicketURL
    $result["tix"] = (isset($post["show_ticketsource"])) ? 1 : $post["show_ticketURL"];


    // Can't do the images yet
    $result["img_T"] = null;

    // Add the converted date data
    $result['show_dates'] = admin_shows_convertDateData($post);

    // Validate data
    $valid = admin_shows_checkPostData($post);

    // Get the full soc and venue data
    $soc = admin_shows_getSocIfInArray($post["show_society"], $socs);

    if(isset($post["show_venue"]) && $post["show_venue"] !== ""){
        $venue = admin_shows_getVenueIfInArray($post["show_venue"], $venues);
    }else{
        $venue = null;
    }

    $result["show_society"] = $soc['id'];
    $result["show_society_name"] = isset($post["show_society_name"]) ? $post["show_society_name"] : null;
    $result["show_venue"] = $venue['id'];

    $validated = true;
    $errors = [];
    if($valid["errors"] || $soc === false || $venue === false){
        $validated = false;

        if(isset($valid["messages"])){
            $errors = array_merge($errors, $valid["messages"]);
        }else{
            $errors = [];
        }

        if(!$soc){
            $errors[] = "You do not have permission to add shows for that society";
        }
        if(!$venue){
            $errors[] = "You do not have permission to add shows in that venue";
        }
    }

    $result["societies"] = $socs;
    $result["venues"] = $venues;

    $result["soc"] = $soc;
    $result["venue"] = $venue;

    $result["valid"] = $validated;
    $result["errors"] = $errors;

    return $result;
}



/************* Add a show **************/
$app->get(
    '/shows/add',
    $authenticate('can_add_shows'),
    function () use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();
        $user = Auth::getUserInfo();

        $post = $app->request->get();
        $ssoc = null;
        if(isset($post["society"])){
            $ssoc = $post["society"];
        }

        // Get all of the societies
        $socs = admin_getAllowedSocieties();
        // Get venues
        $venues = admin_getAllowedVenues();


        $app->render("shows/shows_create.twig", [
            "societies" => $socs,
            "venues" => $venues,
            "selectedSoc" => $ssoc
        ]);
    }
)->name("show_add");




/****** Actual adding *******/
$app->post(
    '/shows/add',
    $authenticate('can_add_shows'),
    function () use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();
        $user = Auth::getUserInfo();


        $post = $app->request->post();

        $data = admin_shows_validateAllDataForShow($user, $post);

        if(!$data["valid"]){
            admin_shows_invalidShowEdit($data);
            return;
        }


        // Everything is valid

        // Get the type of the society to determine which calendar to put it in
        $typeId = admin_shows_getCalendarId($data["show_society"]);

        // Generate a slug from the show name
        $slug = admin_shows_createShowSlug($data['show_name']);


        // Fill the database
        $query = "INSERT INTO `Show` (name, slug, description, societyId, venueId, ticketDetails, ticketSource, stagesoc, societyPage)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        
        $prep = $db->prepare($query);
        $prep->bind_param("sssiissis",
            $data["show_name"], $slug, $data["show_description"], $data["show_society"], $data["show_venue"], $data["show_tickets"], $data["tix"], $data["show_stagesoc"], $data["show_url_suffix"]);
        $prep->execute();
        // Error Check

        // Get ID for the just created show
        $newId = $db->insert_id;



        // Only create Google events in production
        if($app->config('mode') == 'production'){
            // Create the Google Calender Array
            $showLength = "+150 minutes"; // 2.5 hours
            $summary = $data["show_name"] . " performance";
            $showDescription = "(<a href='" . $app->request->getUrl() . shows_getShowUrl([ "id" => $newId, "no_resolve" => true ]) ."'>Show info</a>)<br>" .
                                "By <a href='" . $app->request->getUrl() . $app->urlFor("society", [ "society" => $data["soc"]["id"] ]) . "'>" . $data["soc"]["name"] . "</a><br>" . 
                                $data["show_description"];


            $eventBase = [
                'summary' => $summary,
                'location' => $data["venue"]["lat"] . "," . $data["venue"]["lon"],
                'description' => $showDescription,

                'start' => [],
                'end' => []
            ];


            // Arrays are assigned by value in php
            $calObjects = [];
            foreach ($data["show_dates"] as $date) {
                $ts = $date->getTimestamp(); // Get timestamp here, as it is before modification

                $e = $eventBase;
		$start = $date->format("Y-m-d") . "T" . $date->format("H:i:s");
		$e["start"]["dateTime"] = $date->format("c");
		$date->modify($showLength);
		$end = $date->format("Y-m-d") . "T" . $date->format("H:i:s");
                $e["end"]["dateTime"] = $date->format("c");
                
                // Create the event
                $calObj = PA_Google_Cal::createEvent($typeId, $e);
		
		
		
		// Add event to TeamUp Cal - addition Brooks Jul2016
		$client = new GuzzleHttp\Client(['headers' => ['Teamup-Token' => 'dde6f40ea11a17acc9ce675b19c14c2f1633aae17f4164051ffc7a590f36e105']]);
		
		$res = $client->post("https://api." . substr($data["soc"]["teamup_edit_link"], 8) . "/events", [
			'json' => [
				"subcalendar_id" => admin_shows_getTeamCalendarId($data["show_society"]),
				"start_dt" => $start,
				"end_dt" => $end,
				"all_day" => false,
				"rrule" => "",
				"title" => $data["show_name"],
				"who" => $data["soc"]["name"],
				"location" => $data["venue"]["name"],
				"notes" => "$showDescription"
			]
		]);


                // Back to the google cal
                $calId = $calObj->id;
                // Insert the show dates
                $query = "INSERT INTO `ShowEvent` (showId, showDate, googleEventId)
                            VALUES (?, FROM_UNIXTIME(?), ?)";

                $prep = $db->prepare($query);
                $prep->bind_param("iis", $newId, $ts, $calId);
                $prep->execute();
                // Error Check
            }
        }else{
            // Just add to DB
            foreach ($data["show_dates"] as $date) {
                $ts = $date->getTimestamp();

                $query = "INSERT INTO `ShowEvent` (showId, showDate)
                            VALUES (?, FROM_UNIXTIME(?))";

                $prep = $db->prepare($query);
                $prep->bind_param("ii", $newId, $ts);
                $prep->execute();
            }
        }




        // Upload the image
        $errors = admin_shows_uploadImage('show_image', $newId);

        if($errors !== true){
            array_unshift($errors, 'Warning! The image was not uploaded:');
            $app->flash("warnings", $errors);
        }
        
        

        // Done!
        // Determine whether to go to member editing next
        $url;
        if(isset($post['member-next']) && $post['member-next'] == "true"){
            $url = $app->urlFor('show_edit_members', [ "show" => $newId ]);
        }else{
            $url = shows_getShowUrl([ "id" => $newId ]);
        }
        $app->flash("successes", [ $data["show_name"] . " has been successfully created" ]);
        $app->redirect($url);
    }
)->name("show_add-post");





/********** Edit Show ***********/
$app->get(
    '/shows/edit/:show',
    $authenticate('can_edit_this_show'),
    function ($show) use ($app){
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $user = Auth::getUserInfo();

        // Can only access by ID for now
        $showId = $show;

        // Get the show info from the database..
        $query = "SELECT s.`id` AS 'showId', s.`name` AS 'showName', s.`description`, s.`ticketDetails`, s.`ticketSource`, s.`stagesoc`, s.`societyPage`,
            s.`societyId`, soc.`name` AS 'societyName', soc.`showBaseUrl`,
            s.`venueId`, v.`name` AS 'venueName'
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


        // Get the show dates
        $query = "SELECT * FROM ShowEvent WHERE `showId` = ? ORDER BY `showDate`";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $showId);
        $prep->execute();
        $result = $prep->get_result();

        $dates = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();

        // Get venues
        $venues = admin_getAllowedVenues();



        // Get the image to display
        $images = [
            'preferred' => shows_getPreferredImage($showId)
        ];


        $app->render("shows/shows_create.twig", [
            "edit" => true,
            "venues" => $venues,
            "show" => $theShow,
            "images" => $images,
            "dates" => $dates
        ]);
    }
)->name("show_edit");


/****** Actually edit the show *******/
$app->post(
    '/shows/edit/:show',
    $authenticate('can_edit_this_show'),
    function ($show) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();
        $user = Auth::getUserInfo();

        // Can only access by ID for now
        $showId = $show;

        $post = $app->request->post();

        $data = admin_shows_validateAllDataForShow($user, $post);

        if(!$data["valid"]){
            admin_shows_invalidShowEdit($data, $showId);
            return;
        }


        // Everything is valid


        // Get the type of the society to determine which calendar to put it in
        $typeId = admin_shows_getCalendarId($data["show_society"]);

        // Generate a slug from the show name
        $slug = admin_shows_createShowSlug($data["show_name"]);


        // Update database (Cannot change the society)
        $query = "UPDATE `Show` SET 
                    name = ?, slug = ?, description = ?, venueId = ?, ticketDetails = ?, ticketSource = ?, stagesoc = ?, societyPage = ?
                    WHERE `id` = ?";

        
        $prep = $db->prepare($query);
        $prep->bind_param("sssissisi",
            $data["show_name"], $slug, $data["show_description"], $data["show_venue"], $data["show_tickets"], $data["tix"], $data["show_stagesoc"], $data["show_url_suffix"], $showId);
        $prep->execute();
        // Error Check


        // Dates is the complicated thing
            // Get the current date data from the DB
        $query = "SELECT * FROM `ShowEvent` WHERE `showId` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $showId);
        $prep->execute();
        $result = $prep->get_result();

        $dates_toDelete = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();

        $databaseTimestamps = array_map(function($date){
            return (new DateTime($date["showDate"]))->getTimestamp();
        }, $dates_toDelete);

        // Compare all dates and match them up
        $dates_toUpdate = [];
        $dates_toAdd = [];
        foreach ($data["show_dates"] as $date) {
            // array_search returns the key
            $dateKey = array_search(($date->getTimestamp()), $databaseTimestamps);
            if($dateKey !== false){
                if(isset($dates_toDelete[$dateKey])){

                    if(isset($dates_toDelete[$dateKey]["googleEventId"]) || $app->config('mode') != 'production'){
                        $dates_toUpdate[] = $dates_toDelete[$dateKey];
                        // Remove the date from the databased accessed dates array
                        // This is because all items remaining in this array will be deleted
                        unset($dates_toDelete[$dateKey]);
                    }else{
                        // This one doesn't have a google id for some reason, purge it and remake the google event
                        $dates_toAdd[] = $date;
                    }
                }
            }else{
                $dates_toAdd[] = $date;
            }
        }



        $showLength = "+150 minutes"; // 2.5 hours
        $summary = $data["show_name"] . " performance";
        $showDescription = "(<a href='" . $app->request->getUrl() . shows_getShowUrl([ "id" => $showId, "no_resolve" => true ]) ."'>Show info</a>)<br>" .
                    "By <a href='" . $app->request->getUrl() . $app->urlFor("society", [ "society" => $data["soc"]["id"] ]) . "'>" . $data["soc"]["name"] . "</a><br>" . 
                    $data["show_description"];


        // Update old dates, this only needs to be done in the DB
        if($app->config('mode') == 'production'){ // Only update GC in production
            // Old dates - Update to GC 
            foreach($dates_toUpdate as $oldDate){
                PA_Google_Cal::editEvent($typeId, $oldDate["googleEventId"], function($event) use ($data, $summary, $showDescription) {
                    $event->setSummary($summary);
                    $event->setDescription($showDescription);
                    $event->setLocation($data["venue"]["lat"] . "," . $data["venue"]["lon"]);
                });
            }
        }

        // Remove old dates
        foreach($dates_toDelete as $removeDate){
            if($app->config('mode') == 'production'){
                try{
                    PA_Google_Cal::deleteEvent($typeId, $removeDate["googleEventId"]);
                }catch(Exception $e){}
            }

            $query = "DELETE FROM `ShowEvent` WHERE `id` = ? AND `showId` = ?";
            $prep = $db->prepare($query);
            $prep->bind_param("ii",
                $removeDate["id"], $showId);
            $prep->execute();
            $result = $prep->get_result();
        }

        // Add new date
        $eventBase = [
            'summary' => $summary,
            'location' => $data["venue"]["lat"] . "," . $data["venue"]["lon"],
            'description' => $showDescription,

            'start' => [],
            'end' => []
        ];

        // Arrays are assigned by value in php
        $calObjects = [];
        // Prep statement here (it's faster)
        $query = "INSERT INTO `ShowEvent` (showId, showDate, googleEventId)
            VALUES (?, FROM_UNIXTIME(?), ?)";
        $prep = $db->prepare($query);

        foreach ($dates_toAdd as $date) {
            $ts = $date->getTimestamp(); // Get timestamp here, before modification

            $calId = null;

            if($app->config('mode') == 'production'){
                $e = $eventBase;
                $e["start"]["dateTime"] = $date->format("c");
                $e["end"]["dateTime"] = $date->modify($showLength)->format("c");
            
                // Create the event
                $calObj = PA_Google_Cal::createEvent($typeId, $e);


                $calId = $calObj->id;
            }



            // Insert the show dates

            $prep->bind_param("iis", $showId, $ts, $calId);
            $prep->execute();
            // Error Check
        }



        if(isset($post['show_image-delete_input'])){
            // Delete the image 
            $img_path = $app->config('images.shows');
            $webroot = $app->config('webroot');
            $img_folder = realpath($webroot . $img_path . $showId . '/'); // Folder for the user's images

            array_map('unlink', glob($img_folder . '/*'));
        }else{
            // Upload the image
            $errors = admin_shows_uploadImage('show_image', $showId);

            if($errors !== true){
                array_unshift($errors, 'Warning! The image was not uploaded:');
                $app->flash("warnings", $errors);
            }
        }




        // Done!
        $url;
        if(isset($post['member-next']) && $post['member-next'] == "true"){
            $url = $app->urlFor('show_edit_members', [ "show" => $showId ]);
        }else{
            $url = shows_getShowUrl([ "id" => $showId ]);
        }
        $app->flash("successes", [ $data["show_name"] . " has been successfully edited" ]);
        $app->redirect($url);
    }
)->name("show_edit-post");


/*********** Delete a show ************/
$app->get(
    '/shows/delete/:show',
    $authenticate('can_edit_this_show'),
    function ($show) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $user = Auth::getUserInfo();

        // Can only access by ID for now
        $showId = $show;

        $query = "SELECT `id`, `name` FROM `Show` WHERE `id` = ?";

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

        $db->close();


        $app->render("shows/shows_delete.twig", [
            "show" => $theShow
        ]);
    }
)->name("show_delete");


/**** Actual deleting *****/
$app->delete(
    '/shows/delete/:show',
    $authenticate('can_edit_this_show'),
    function ($show) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $user = Auth::getUserInfo();

        // Can only access by ID for now
        $showId = $show;

        // Confirm that the show exists
        $query = "SELECT `id`, `name`, `societyId` FROM `Show` WHERE `id` = ?";

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


        // Delete all of the ShowRoles
        $query = "DELETE FROM `ShowRole` WHERE `showId` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $theShow["societyId"]);
        $prep->execute();

        // Get the society's type ID
        $query = "SELECT `type` FROM `Society` WHERE `id` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $theShow["societyId"]);
        $prep->execute();
        $result = $prep->get_result();

        $typeId = $result->fetch_assoc();
        $typeId = $typeId["type"];

        // Check post data
        $post = $app->request->post();
        if($post["confirm"] != "true"){
            $app->redirect(shows_getShowUrl([ "id" => $newId ]));
        }

        // Get the Google Ids
        $query = "SELECT `googleEventId` FROM `ShowEvent` WHERE `showId` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $showId);
        $prep->execute();
        $result = $prep->get_result();

        $googleIds = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();

        // Delete the Google Calendar events
        foreach($googleIds as $googleId){
            if(isset($googleId["googleEventId"])){
               PA_Google_Cal::deleteEvent($typeId, $googleId["googleEventId"]);
            }
        }

        // Delete the show events
        $query = "DELETE FROM `ShowEvent` WHERE `showId` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $showId);
        $prep->execute();
        $result = $prep->get_result();

        // Delete the actual show
        $query = "DELETE FROM `Show` WHERE `id` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $showId);
        $prep->execute();
        $result = $prep->get_result();

        $db->close();

        // Done
        $app->flash("successes", [ $theShow["name"] . " has been successfully deleted" ]);
        $app->redirect($app->urlFor("shows"));
    }
)->name("show_delete-post");




