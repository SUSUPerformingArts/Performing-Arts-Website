<?php




$app->get(
    '/shows/api',
    function() use ($app){
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Set response as JSON
        $app->response->headers->set('Content-Type', 'application/json');

        $get = $app->request->get();
        $name = (isset($get["name"]) && $get["name"] != "") ? "%".trim($get["name"])."%" : null;
        $limit = (isset($get["limit"]) && is_numeric($get["limit"]))?intval($get["limit"]):20; // Get limit, default is 20

        if(!isset($name)){
            echo "[]";
            $app->stop();
        }

        $query = "SELECT s.*, se.`firstShowDate`, se.`lastShowDate`, YEAR(se.`lastShowDate`) as 'year' FROM `Show` s
                LEFT JOIN (SELECT `showId`, MAX(`showDate`) AS 'lastShowDate', MIN(`showDate`) AS 'firstShowDate' FROM `ShowEvent` GROUP BY `showId`) se ON se.`showId` = s.`id`
                WHERE s.`name` LIKE ?
                ORDER BY s.`name`
                LIMIT ?";

        $prep = $db->prepare($query);
        $prep->bind_param("si",
                $name, $limit);


        $prep->execute();
        $result = $prep->get_result();
        $searchResult = $result->fetch_all(MYSQLI_ASSOC);

        echo json_encode($searchResult);
    }
)->name("api_shows");






