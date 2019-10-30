<?php

function api_shows_addUrls($show){
    $app = \Slim\Slim::getInstance();
    $show['showUrl'] = $app->request->getUrl() .shows_getShowUrl([ 'slug' => $show['slug'], 'society' => $show['societySlug'], 'year' => $show['academicYear'] ]);
    $show['uri'] = rtrim($app->request->getUrl() . $app->urlFor("api_show", [ "show" => $show['id'], "type" => null ]), ".");


    return $show;
}

function api_shows_addImages($show){
    $app = \Slim\Slim::getInstance();
    $img = shows_getPreferredImage($show['id']);
    if($img){
        $show['image'] = $app->request->getUrl() . $img;
    }

    return $show;
}

function api_shows_addAllExtraData($show){
    return api_shows_addImages(api_shows_addUrls($show));
}


$app->get(
    '/api/shows(\.:type)',
    function() use ($app){
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Set response as JSON
        $app->response->headers->set('Content-Type', 'application/json');

        $get = $app->request->get();
        $limit = (isset($get["limit"]) && is_numeric($get["limit"])) ? intval($get["limit"]) : null;

        $name = (isset($get["name"]) && $get["name"] != "") ? "%".trim($get["name"])."%" : null;
        $society = (isset($get["society"]) && $get["society"] != "") ? "%".trim($get["society"])."%" : null;

        $prep;

        /*$query = "SELECT s.`id`, s.`name`, s.`description`, s.`venueId`, v.`name` AS 'venueName', s.`ticketSource`, s.`stagesoc`, s.`societyId`, soc.`name` AS 'societyName',
                se.`firstShowDate`, se.`lastShowDate`, YEAR(se.`lastShowDate`) as 'year' FROM `Show` s
                LEFT JOIN (SELECT `showId`, MAX(`showDate`) AS 'lastShowDate', MIN(`showDate`) AS 'firstShowDate' FROM `ShowEvent` GROUP BY `showId`) se ON se.`showId` = s.`id`
                INNER JOIN `Society` soc ON s.`societyId` = soc.`id`
                INNER JOIN `Venue` v ON s.`venueId` = v.`id` ";*/
        $query = "SELECT * FROM `Show_WithExpandedInfo` s ";

        if($name && $society){
            $query .= " WHERE s.`name` LIKE ? AND s.`societyName` LIKE ? ";
            if($limit){
                $query .= " LIMIT ?";
                $prep = $db->prepare($query);
                $prep->bind_param("ssi",
                        $name, $society, $limit);
            }else{
                $prep = $db->prepare($query);
                $prep->bind_param("ss",
                        $name, $society);
            }
        }else{
            if($name){
                $query .= " WHERE s.`name` LIKE ? ";
                if($limit){
                    $query .= " LIMIT ?";
                    $prep = $db->prepare($query);
                    $prep->bind_param("si",
                            $name, $limit);
                }else{
                    $prep = $db->prepare($query);
                    $prep->bind_param("s",
                            $name);
                }
            }else{
                if($society){
                    $query .= " WHERE s.`societyName` LIKE ? ";
                    if($limit){
                        $query .= " LIMIT ?";
                        $prep = $db->prepare($query);
                        $prep->bind_param("si",
                                $society, $limit);
                    }else{
                        $prep = $db->prepare($query);
                        $prep->bind_param("s",
                                $society);
                    }
                }else{
                    if($limit){
                        $query .= " LIMIT ?";
                        $prep = $db->prepare($query);
                        $prep->bind_param("i",
                                $limit);
                    }else{
                        $prep = $db->prepare($query);
                    }
                }
            }
        }


        $prep->execute();
        $result = $prep->get_result();
        $searchResult = array_map('api_shows_addAllExtraData', $result->fetch_all(MYSQLI_ASSOC));

        $res = api_negotiateContent($searchResult);

        echo $res;
    }
)->name("api_shows");




$app->get(
    '/api/shows/:show(\.:type)',
    function ($show, $type = 'json') use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $showId = $show;

        $year = $app->request->get('year');

        // Select main show information
        $query = "SELECT s.`id`, s.`slug`, s.`name`, s.`description`, s.`venueId`, s.`venueName`, s.`ticketSource`, s.`stagesoc`, s.`societyId`, s.`societySlug`, s.`societyName`,
                s.`year`, s.`academicYear`, UNIX_TIMESTAMP(s.`firstShowDate`) AS 'firstShowDate', UNIX_TIMESTAMP(s.`lastShowDate`) AS 'lastShowDate'
                FROM `Show_WithExpandedInfo` s
                WHERE s.`id` = ?";
                //s.`id`, s.`slug`, s.`name`, s.`description`, s.`venueId`, v.`name` AS 'venueName', s.`ticketSource`, s.`stagesoc`, s.`societyId`, soc.`slug` AS 'societySlug', soc.`name` AS 'societyName'

        $prep = $db->prepare($query);

        $prep->bind_param("i",
                $showId);

        $prep->execute();
        $result = $prep->get_result();

        $theShow = $result->fetch_assoc();
        $theShow = api_shows_addAllExtraData($theShow);

        // Convert l/f showdates
        $theShow['firstShowDate'] = date('c', $theShow['firstShowDate']);
        $theShow['lastShowDate'] = date('c', $theShow['lastShowDate']);

        // Add soc info
        $soc = [
            'id' => $theShow['societyId'],
            'name' => $theShow['societyName'],
            'slug' => $theShow['societySlug'],
        ];
        // Add extra info
        $soc = api_societies_addUrls($soc);
        // Unset
        unset($theShow['societyId']);
        unset($theShow['societySlug']);
        unset($theShow['societyName']);
        
        $theShow['society'] = $soc;

        // Add venue info
        $venue = [
            'id' => $theShow['venueId'],
            'name' => $theShow['venueName'],
        ];
        ## ADD EXTRA INFO WHEN MADE

        // Unset
        unset($theShow['venueId']);
        unset($theShow['venueName']);

        $theShow['venue'] = $venue;



        // Get the date information
        $query = "SELECT UNIX_TIMESTAMP(`showDate`) as 'datetime', `googleEventId`
                FROM `ShowEvent`
                WHERE `showId` = ?
                ORDER BY `showDate`";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $showId);
        $prep->execute();
        $result = $prep->get_result();

        $theShow['dates'] = array_map(function($d){
            $d['datetime'] = date('c', $d['datetime']);
            return $d;
        }, $result->fetch_all(MYSQLI_ASSOC));


        // Get the member information
        $query = "SELECT m.`id`, m.`firstName`, m.`lastName`, m.`chosenName`, m.`suggested`, r.`name` AS 'roleName', sr.`notes`
            FROM `All_ShowRole` sr
            LEFT JOIN `All_Member` m ON sr.`memberId` = m.`id`
            LEFT JOIN `Role` r ON sr.`roleId` = r.`id`
            WHERE sr.`showId` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $showId);
        $prep->execute();
        $result = $prep->get_result();

        ##### GOTTA GROUP THE PEOPLE, Same entry with all roles
        $members = [];
        while($row = $result->fetch_assoc()){
            $id = $row['id'];
            if(!isset($members[$id])){
                $members[$id] = api_members_addUrls(derivePreferredName($row));
                $members[$id]['roles'] = [[
                    'role' => $row['roleName'],
                    'notes' => $row['notes']
                ]];
                unset($members[$id]['roleName']);
                unset($members[$id]['notes']);
            }else{
                $members[$id]['roles'][] = [
                    'role' => $row['roleName'],
                    'notes' => $row['notes']
                ];
            }
        }


        $theShow['members'] = array_values($members);

        // Set response as JSON
        $res = api_negotiateContent($theShow);

        echo $res;
    }
)->name("api_show");





