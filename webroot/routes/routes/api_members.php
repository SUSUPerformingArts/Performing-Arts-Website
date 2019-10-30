<?php




$app->get(
    '/members/api',
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

        $query = "SELECT * FROM `All_Member` WHERE CONCAT(`firstName`, ' ', `lastname`) LIKE ? OR CONCAT(`chosenName`, ' ', `lastname`) LIKE ? ORDER BY `suggested` LIMIT ?";

        $prep = $db->prepare($query);
        $prep->bind_param("ssi",
                $name, $name, $limit);


        $prep->execute();
        $result = $prep->get_result();

        $searchResult = [];
        while($row = $result->fetch_assoc()){
            $searchResult[] = derivePreferredName($row);
        }
        $result->free();

        echo json_encode($searchResult);
    }
)->name("pa_api_members");






