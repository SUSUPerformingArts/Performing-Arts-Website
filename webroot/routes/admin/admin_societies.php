<?php


// Edit page
$app->get(
    '/societies/edit/:society',
    $authenticate('can_edit_this_society'),
    function ($society) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $societyId = $society;


        // Get the current society details
        $query = "SELECT * FROM `Society` WHERE `id` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $societyId);
        $prep->execute();
        $result = $prep->get_result();

        $theSociety = $result->fetch_assoc();
        $result->free();
        $theSociety['societyType'] = $theSociety['type'] == 1 ? "Theatrical" : ($theSociety['type'] == 3 ? "Dance" : "Music");
        
        // Get members for the current year
        $year = getCurrentAcademicYear();
        $members = array_map('derivePreferredName', soc_getMembersForAcademicYear($societyId, $year));

        $app->render("societies/societies_edit.twig", [
        	"society" => $theSociety,
            "academicYear" => $year,
            "members" => $members
        ]);
    }
)->name("pa_society_edit");


/******* Actually edit the society *******/
$app->post(
	'/societies/edit/:society',
    $authenticate('can_edit_this_society'),
    function ($society) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $societyId = $society;

        $post = $app->request->post();

        // Trim everything down
        $subtitle = trim($post["society_edit_subtitle"]);
        $description = trim($post["society_edit_description"]);
        $email = trim($post["society_edit_email"]);
        $website = trim($post["society_edit_website"]);
        $facebookPage = trim($post["society_edit_facebookPage"]);
        $facebookGroup = trim($post["society_edit_facebookGroup"]);
        $twitter = trim($post["society_edit_twitter"]);
        $instagram = trim($post["society_edit_instagram"]);

        // Add to the database
        $query = "UPDATE `Society` SET
        		`subtitle` = ?, `description` = ?, `email` = ?, `website` = ?, `facebookPage` = ?, `facebookGroup` = ?, `twitter` = ?, `instagram` = ?
        		WHERE `id` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("ssssssssi",
            $subtitle, $description, $email, $website, $facebookPage, $facebookGroup, $twitter, $instagram, $societyId);
        $prep->execute();


        $society_member_add = $a = isset($post["society_member_add"]) ? array_unique($post["society_member_add"]) : [];
        $society_member_remove = $r = isset($post["society_member_remove"]) ? $post["society_member_remove"] : [];
        $society_member_add = array_diff($society_member_add, $r);
        $society_member_remove = array_diff($society_member_remove, $a);


        $year = getCurrentAcademicYear();
        $current_members = array_map(function($i){
            return $i['memberId'];
        }, soc_getMembersForAcademicYear($societyId, $year));

        $society_member_add = array_diff($society_member_add, $current_members);

        // Delete old ones..
        if(count($society_member_remove)){
            $query = "DELETE FROM `SocietyMember`
                    WHERE `memberId` = ? AND `societyId` = ? AND `year` = ?";

            $prep = $db->prepare($query);
            foreach ($society_member_remove as $memberId) {
                $prep->bind_param("iii",
                    $memberId, $societyId, $year);
                $prep->execute();
            }
        }

        if(count($society_member_add)){
            // Add the members
            $query = "INSERT INTO `SocietyMember` (`memberId`, `societyId`, `year`)
                    VALUES (?,?,?)";

            $prep = $db->prepare($query);
            foreach ($society_member_add as $memberId) {
                $prep->bind_param("iii",
                    $memberId, $societyId, $year);
                $prep->execute();
            }
        }

        // Done!
        $app->flash("successes", [ "Society successfully updated!" ]);
        $app->redirect($app->urlFor("pa_society", [ "society" => $societyId ]));
    }
)->name("pa_society_edit-post");

