<?php




function admin_shows_convertMemberData($post){
    $results = [];

    // Group the show members
    if((isset($post["show_member_suggested"]) || isset($post["show_member"])) && isset($post["show_member-role"])){
        $oldMems = []; // Old members already there

        // New normal members
        $newMems = [];
        if(isset($post["show_member"])){
            foreach($post["show_member"] as $i => $mem){
                if(!isset($post["show_member-role"])){
                    continue;
                }
                $a = [
                    "id"     => $mem,
                    "roleId" => $post["show_member-role"][$i],
                    "notes"  => $post["show_member-notes"][$i]
                ];

                if(isset($post["show_member-old_showrole_id"][$i])){
                    $a["oldId"] = $post["show_member-old_showrole_id"][$i];
                    $oldMems[] = $a;
                }else{
                    $newMems[] = $a;
                }
            }
        }

        $results["show_members_new"] = $newMems;
        $results["show_members_old"] = $oldMems;

        $oldMemsSug = []; // Old members already there (suggested)

        // Suggested
        $sugMems = [];
        if(isset($post["show_member_suggested"])){
            foreach($post["show_member_suggested"] as $i => $mem){
                if(!isset($post["show_member-role"])){
                    continue;
                }
                $a = [
                    "id"     => $mem,
                    "roleId" => $post["show_member-role"][$i],
                    "notes"  => $post["show_member-notes"][$i]
                ];

                if(isset($post["show_member-old_showrole_id"][$i])){
                    $a["oldId"] = $post["show_member-old_showrole_id"][$i];
                    $oldMemsSug[] = $a;
                }else{
                    $sugMems[] = $a;
                }
            }
        }

        $results["show_members_new_suggesed"] = $sugMems;
        $results["show_members_old_suggested"] = $oldMemsSug;
    }else{
        $results["show_members_new"] = [];
        $results["show_members_old"] = [];
        $results["show_members_new_suggesed"] = [];
        $results["show_members_old_suggested"] = [];
    }

    // Add deleted memebers
    $results["show_member_delete"] = (isset($post["show_member_delete"])) ? $post["show_member_delete"] : [];
    $results["show_member_suggested_delete"] = (isset($post["show_member_suggested_delete"])) ? $post["show_member_suggested_delete"] : [];

    return $results;
}


$app->get(
    '/shows/edit/:show/members',
    $authenticate('can_edit_this_show'),
    function ($show) use ($app){
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $user = $_SESSION["user"];

        // Can only access by ID for now
        $showId = $show;

        $query = "SELECT s.`id` AS 'showId', s.`name` AS 'showName', 
            s.`societyId`, soc.`name` AS 'societyName'
            FROM `Show` s
            LEFT JOIN `Society` soc ON s.`societyId` = soc.`id` WHERE s.`id` = ?";


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



        // Get all the roles
        $roles = admin_getRoles();

        // Get current involved members
        $query = "SELECT sr.`id` AS `showRoleId`, sr.`memberId`, sr.`suggestedMemberId`, sr.`firstName`, sr.`lastName`, sr.`chosenName`, sr.`suggested`, sr.`roleId`, r.`name`, sr.`notes`, r.`roleAreaId`, ra.`areaName` as 'roleAreaName'
                FROM `All_ShowRole` sr
                INNER JOIN `Role` r ON sr.`roleId` = r.`id`
                INNER JOIN `RoleArea` ra ON r.`roleAreaId` = ra.`id`
                WHERE sr.`showId` = ? ORDER BY sr.`lastName`";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $showId);
        $prep->execute();
        $result = $prep->get_result();

        $members = [];
        while($row = $result->fetch_assoc()){
            $row = derivePreferredName($row);
            if(isset($row["suggestedMemberId"])){
                $row["id"] = $row["suggestedMemberId"];
            }else{
                $row["id"] = $row["memberId"];
            }

            $members[] = $row;
        }

        $db->close();

        $app->render('shows/shows_edit_members.twig', [
            'show' => $theShow,
            'roles' => $roles,
            'members' => $members
        ]);
    }
)->name('pa_show_edit_members');




$app->post(
    '/shows/edit/:show/members',
    $authenticate('can_edit_this_show'),
    function ($show) use ($app){
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $data = admin_shows_convertMemberData($app->request->post());

        // Can only access by ID for now
        $showId = $show;


        /***** Add/update members *****/
        // Update old ones
        // Non-suggested members
        if(count($data["show_members_old"]) > 0){
            $query = "UPDATE `ShowRole` SET
                        `roleId` = ?, `notes` = ?
                        WHERE `id` = ? AND `showId` = ?";
            $prep = $db->prepare($query);

            foreach ($data["show_members_old"] as $oldMember) {
                $prep->bind_param("isii",
                    $oldMember["roleId"], $oldMember["notes"], $oldMember["oldId"], $showId);
                $prep->execute();
            }
        }
        // Suggested members
        if(count($data["show_members_old_suggested"]) > 0){
            $query = "UPDATE `Suggested_ShowRole` SET
                        `roleId` = ?, `notes` = ?
                        WHERE `id` = ? AND `showId` = ?";
            $prep = $db->prepare($query);

            foreach ($data["show_members_old_suggested"] as $oldMember) {
                $prep->bind_param("isii",
                    $oldMember["roleId"], $oldMember["notes"], $oldMember["oldId"], $showId);
                $prep->execute();
            }
        }


        // Add the new ones
        // Non-suggested members
        if(count($data["show_members_new"]) > 0){
            $query = "INSERT INTO `ShowRole` (memberId, showId, roleId, notes)
                            VALUES (?, ?, ?, ?)";
            $prep = $db->prepare($query);

            foreach ($data["show_members_new"] as $newMember) {
                $prep->bind_param("iiis",
                    $newMember["id"], $showId, $newMember["roleId"], $newMember["notes"]);
                $prep->execute();
            }

        }
        // Suggested members
        if(count($data["show_members_new_suggesed"]) > 0){
            $query = "INSERT INTO `Suggested_ShowRole` (suggestedMemberId, showId, roleId, notes)
                            VALUES (?, ?, ?, ?)";
            $prep = $db->prepare($query);

            foreach ($data["show_members_new_suggesed"] as $newMember) {
                $prep->bind_param("iiis",
                    $newMember["id"], $showId, $newMember["roleId"], $newMember["notes"]);
                $prep->execute();
            }

        }

        // Delete old members
        if(count($data["show_member_delete"]) > 0){
            $query = "DELETE FROM `ShowRole` WHERE `id` = ? AND `showId` = ?";
            $prep = $db->prepare($query);

            foreach ($data["show_member_delete"] as $mem) {
                $prep->bind_param("ii",
                    $mem, $showId);
                $prep->execute();
            }

        }
        if(count($data["show_member_suggested_delete"]) > 0){
            $query = "DELETE FROM `Suggested_ShowRole` WHERE `id` = ? AND `showId` = ?";
            $prep = $db->prepare($query);

            foreach ($data["show_member_suggested_delete"] as $mem) {
                $prep->bind_param("ii",
                    $mem, $showId);
                $prep->execute();
            }

        }




        // Done!
        $app->flash("successes", [ "Show's members have been successfully edited" ]);
        $app->redirect(shows_getShowUrl([ "id" => $showId ]));
    }
)->name("pa_show_edit_members-post");

