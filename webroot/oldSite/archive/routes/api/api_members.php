<?php


function api_members_buildSelectColumns($table = 'All_Member'){
    $query = "SELECT `id`, `firstName`, `lastName`, `chosenName`, ";

    switch ($table) {
        case 'Member':
            $query .= "0";
            break;
        
        case 'Suggested_Member':
            $query .= "1";
            break;

        default:
            $query .= "`suggested`";
    }

    $query .= " AS 'suggested' ";
    $query .= "FROM `" . $table . "` ";
    return $query;
}

function api_members_addUrls($member){
    $app = \Slim\Slim::getInstance();
    $urlString = "member";
    if(isset($member['suggested']) && $member['suggested']){
        $urlString = "suggested_" . $urlString;
    }else{
        $member['uri'] = rtrim($app->request->getUrl() . $app->urlFor("api_member", [ "member" => $member['id'], "type" => null ]), ".");
    }

    $member['profileUrl'] = $app->request->getUrl() . $app->urlFor($urlString, [ "member" => $member['id'] ]);

    return $member;
}

function api_members_addImages($member){
    $app = \Slim\Slim::getInstance();
    $member['images'] = array_map(function($i) use ($app) {
        return $app->request->getUrl() . $i;
    }, members_getImages($member['id']));

    return $member;
}

function api_members_addAllExtraData($member){
    return api_members_addImages(api_members_addUrls($member));
}


$app->get(
    '/api/members(\.:type)',
    function() use ($app){
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();


        $get = $app->request->params();
        $limit = (isset($get["limit"]) && is_numeric($get["limit"])) ? intval($get["limit"]) : null;

        // Get the table we should be going from
        $table = "All_Member"; // All_Member is default
        if(isset($get["type"])){
            if($get["type"] == "normal"){
                $table = "Member";
            }
            if($get["type"] == "suggested"){
                $table = "Suggested_Member";
            }
        }

        $result;

        // Conditions for just names
        if(isset($get['name'])){
            $name = '%' . trim($get["name"]) . '%';

            $query = api_members_buildSelectColumns($table);
            $query .= "WHERE CONCAT(`firstName`, ' ', `lastname`) LIKE ? OR CONCAT(`chosenName`, ' ', `lastname`) LIKE ?";
            if($limit){
                $query .= " LIMIT ?";
            }


            $prep = $db->prepare($query);

            if($limit){
                $prep->bind_param("ssi",
                        $name, $name, $limit);
            }else{
                $prep->bind_param("ss",
                        $name, $name);
            }

            $prep->execute();
            $result = $prep->get_result();
        }else{
            $fn = isset($get['firstName']) ? '%'. trim($get['firstName']) . '%' : null;
            $ln = isset($get['lastName']) ? '%'. trim($get['lastName']) . '%' : null;
            $cn = isset($get['chosenName']) ? '%'. trim($get['chosenName']) . '%' : null;
            $givenNameOnly = (isset($get['givenNameOnly']) && $get['givenNameOnly'] != "false") ? true : false;
            if($fn || $ln || $cn){
                // Conditions for first and last names seperate
                // Build query
                $query = api_members_buildSelectColumns($table);
                $query .= "WHERE ";

                $tps = "";
                $args = [];
                $needAnd = false;

                if($ln){
                    $query .= "`lastName` LIKE ? ";
                    $tps .= "s";
                    $args[] =& $ln;
                    $needAnd = true;
                }
                if($fn){
                    if($needAnd){
                        $query .= " AND ";
                    }
                    $query .= "(`firstName` LIKE ? ";
                    $tps .= "s";
                    $args[] =& $fn;
                    if(!$givenNameOnly){
                        $query .= "OR `chosenName` LIKE ? ";
                        $tps .= "s";
                        $args[] =& $fn;
                    }
                    $query .= ") ";
                    $needAnd = true;
                }
                if($cn){
                    if($needAnd){
                        $query .= " AND ";
                    }
                    $query .= "`chosenName` LIKE ? ";
                    $tps .= "s";
                    $args[] =& $cn;
                }

                if($limit){
                    $query .= " LIMIT ?";
                    $tps .= "i";
                    $args[] =& $limit;
                }

                $prep = $db->prepare($query);

                array_unshift($args, $prep, $tps);
                call_user_func_array('mysqli_stmt_bind_param', $args);

                $prep->execute();
                $result = $prep->get_result();
            }else{
                // Everything..
                $query = api_members_buildSelectColumns($table);
                if($limit){
                    $query .= " LIMIT ?";
                }

                $prep = $db->prepare($query);

                if($limit){
                    $prep->bind_param("i",
                            $limit);
                }

                $prep->execute();
                $result = $prep->get_result();
            }
        }



        $searchResult = [];
        while($row = $result->fetch_assoc()){
            $searchResult[] = api_members_addAllExtraData(derivePreferredName($row));
        }
        $result->free();




        // Set response as JSON
        $res = api_negotiateContent($searchResult);

        echo $res;
    }
)->name("api_members");





$app->get(
    '/api/members/:member(\.:type)',
    function ($member, $type = 'json') use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $memberId = $member;

        // Select main society information
        $query = api_members_buildSelectColumns('All_Member');
        $query .= " WHERE `id` = ?";

        $prep = $db->prepare($query);

        $prep->bind_param("i",
                $memberId);

        $prep->execute();
        $result = $prep->get_result();

        $theMember = $result->fetch_assoc();
        $theMember = api_members_addUrls(derivePreferredName($theMember));


        // Get the society information
        $query = "SELECT soc.`id`, soc.`name`, sp.`year`, comm.`name` AS 'committeePosition' FROM `SocietyMember` sp
            LEFT JOIN `Society` soc ON sp.`societyId` = soc.`id`
            LEFT JOIN `CommitteePosition` comm ON sp.`committeePositionId` = comm.`id`
            WHERE sp.`memberId` = ? ORDER BY soc.`name` ASC, sp.`year` DESC";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();

        $theMember['societies'] = array_map('api_members_addAllExtraData', $result->fetch_all(MYSQLI_ASSOC));


        // Get the show information
        $query = "SELECT s.`id`, s.`slug`, s.`name` AS 'showName', r.`name` AS 'roleName', sr.`notes`, s.`societyId`, s.`societySlug`, s.`societyName`, s.`year`, s.`academicYear`
            FROM `ShowRole` sr
            LEFT JOIN `Show_WithExpandedInfo` s ON sr.`showId` = s.`id`
            LEFT JOIN `Role` r ON sr.`roleId` = r.`id`
            WHERE sr.`memberId` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();

        $theMember['shows'] = array_map('api_shows_addUrls', $result->fetch_all(MYSQLI_ASSOC));

        // Set response as JSON
        $res = api_negotiateContent($theMember);

        echo $res;
    }
)->name("api_member");


