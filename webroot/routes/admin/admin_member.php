<?php

// Edit page
$app->get(
    '/members/edit',
    $authenticate('is_logged_in'),
    function () use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();


        // Refresh the session data
        Auth::refreshSessionVariable();
        $user = $_SESSION["user"];

        // Get the society membership data
        $query = "SELECT sp.`id`, sp.`year`, s.`id` AS 'societyId', s.`slug` AS 'societySlug', s.`name` AS 'societyName', sp.`committeePositionId`, comm.`name` AS 'committeePosition', s.`profileBaseUrl`, sp.`societyProfileId`
                FROM `SocietyMember` sp
                INNER JOIN `Society` s ON sp.`societyId` = s.`id`
                LEFT JOIN `CommitteePosition` comm ON sp.`committeePositionId` = comm.`id`
                WHERE sp.`memberId` = ?
                ORDER BY sp.`year` DESC, s.`name`";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $user["id"]);
        $prep->execute();
        $result = $prep->get_result();


        $currentMembership = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();

        $extern_profiles = [];
        foreach($currentMembership as $m){
            $extern_profiles[$m["id"]] = [
                "id" => $m["id"],
            ];
        }

        // Get all socities for adding new ones
        $query = "SELECT socs.`id`, socs.`name`, socs.`profileBaseUrl`, type.`name` AS 'type'
                FROM `Society` socs LEFT JOIN `SocietyType` type ON socs.`type` = type.`id`
                WHERE `type` != 5
                ORDER BY socs.`type` ASC, `name` ASC";

        $result = $db->query($query); // No input

        $allSocs = [];
        $curr = null;
        while($row = $result->fetch_assoc()){
            if($curr !== $row["type"]){
                $curr = $row["type"];
                if(!isset($allSocs[$curr])){
                    $allSocs[$curr] = [];
                }
            }

            $allSocs[$curr][] = $row;
        }
        $result->free();
        $db->close();

        // Get images
        $images = members_getImages($user["id"]);


        $app->render("members/members_edit.twig", [
            "user" => $user, // Overwriting auto session one
            "images" => $images,
            "currentMembership" => $currentMembership,
            "allSocs" => $allSocs,
            "academicYear" => getCurrentAcademicYear(),
            "urlRedirect" => $app->request()->get("continue")
        ]);
    }
)->name("pa_member_edit");




/**** Actual editing the profile ****/
$app->post(
    '/members/edit',
    $authenticate('is_logged_in'),
    function () use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $user = $_SESSION["user"];

        $post = $app->request->post();


        $name = ucfirst(trim($post["user_pname"]));
        $bio = trim($post["user_bio"]);

        if($name === $user["firstName"]){
            $name = "";
        }

        $socs_delete = (isset($post["user_socs_delete"]))?array_unique($post["user_socs_delete"]):[];
        $socs_add = (isset($post["user_socs_add"]))?array_unique($post["user_socs_add"]):[];
        $socs_add = array_filter($socs_add, function($s){
            return (trim($s) !== "");
        });



        $currentYear = getCurrentAcademicYear();

        // Add everything to the database
        $query = "UPDATE `Member` SET
                `chosenName` = ?, `bio` = ?
                WHERE `id` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("ssi",
            $name, $bio, $user["id"]);
        $prep->execute();




        // Prep query once
        $query = "DELETE FROM `SocietyMember` WHERE `id` = ? AND `year` = ?";
        $prep = $db->prepare($query);
        foreach($socs_delete as $socpId => $do){
            if($do === "true"){
                $prep->bind_param("ii",
                    $socpId, $currentYear);
                $prep->execute();
            }
        }

        // Get societies you are a part of
        $query = "SELECT sp.`societyId` FROM `SocietyMember` sp
            WHERE sp.`memberId` = ?
            AND sp.`year` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("ii", $user["id"], $currentYear);
        $prep->execute();

        $result = $prep->get_result();
        $currentSocs = [];
        while($row = $result->fetch_assoc()){
            $currentSocs[] = $row["societyId"];
        }

        // Filter those out of the current array
        $socs_add = array_filter($socs_add, function($s) use ($currentSocs) {
            return !in_array(trim($s), $currentSocs);
        });


        $query = "INSERT INTO `SocietyMember` 
                (`memberId`, `societyId`, `year`)
                VALUES (?, ?, ?)";

        $prep = $db->prepare($query);
        foreach($socs_add as $soc){
            $prep->bind_param("iis", $user["id"], $soc, $currentYear);
            $prep->execute();
        }

        Auth::refreshSessionVariable();


        // Get image path config
        $img_path = $app->config('images.members');
        $webroot = $app->config('webroot');
        $img_folder = $webroot . $img_path . $user["id"] . '/'; // Folder for the user's images


        if(isset($post['user_image-delete'])){

            $img_location = $img_folder . 'profile.jpg';
            if(is_file($img_location)){
                unlink($img_location);
            }

        }else{

            // Upload for image last, so everything else updates if this fails!
            
            // Create if it does not exist
            if(!file_exists($img_folder)){
                mkdir($img_folder, 0775, true);
                //chown($img_folder, getmyuid());
                //chgrp($img_folder, "www-data");
            }

            $img_folder = realpath($img_folder);

            $storage = new \Upload\Storage\FileSystem($img_folder, true); // Overwrite = true
            $file = new \Upload\File('user_image', $storage);

            $file->setName('profile'); // Image name
            $file->addValidations([
                // Ensure file is an image
                new \Upload\Validation\Mimetype(['image/png', 'image/jpg', 'image/jpeg', 'image/gif']),

                // We don't need to worry about file size too much as it's going to be shrunk down
                new \Upload\Validation\Size('15M')
            ]);
            
            if($file->isUploadedFile()){

                $dim = $file->getDimensions(); // Have to get dimensions here or results will be null

                // Try to upload file
                try{
                    $file->upload();

                    // Do some imagemagick!

                    // Max dimensions
                    $max_width = "700";
                    $max_height = "500";

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


                    $imagick->writeImage($save);
                    $imagick->clear();

                    if($load != $save){
                        unlink($load); // Delete the loaded file if it differs from the saved one (different file ext)
                    }

                }catch(\Exception $e){
                    // Fail!
                    $errors = $file->getErrors();

                    // Flash image upload errors on fail
                    $app->flash("errors", $errors);
                }
            }
        }


        // Set headers so the (image) content is not cached
        $app->response->headers->set('Expires', 'Sun, 01 Jan 2014 00:00:00 GMT');
        $app->response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
        $app->response->headers->set('Cache-Control', 'post-check=0, pre-check=0');
        $app->response->headers->set('Pragma', 'no-cache');

        // Done!
        $app->flash("successes", [ "Your profile has been successfully edited (you may need to refresh the page to see your new profile picture)" ]);

        // Redirect to continue or not?
        $urlRedirect = $app->request()->get("continue");
        if(isset($urlRedirect)){
            $app->redirect($urlRedirect);
        }else{
            $app->redirect($app->urlFor("pa_member", [ "member" => $user["id"] ]));
        }
    }
)->name("pa_member_edit-post");



// Me aliases
$app->get(
    '/me',
    $authenticate('is_logged_in'),
    function () use ($app) {
        $id = $_SESSION["user"]["id"];
        $app->redirect($app->urlFor("pa_member", [ "member" => $id ]));
    }
)->name("pa_me");

$app->get(
    '/me/edit',
    $authenticate('is_logged_in'),
    function () use ($app) {
        $app->redirect($app->urlFor("pa_member_edit"), 301);
    }
)->name("pa_me_edit");


function admin_members_createMemberByUsername($iSolutionsUsername){
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    // Check if they exist
    $query = "SELECT COUNT(*) FROM `Member` WHERE iSolutionsUsername = ?";
    $prep = $db->prepare($query);

    $prep->bind_param('s',
        $iSolutionsUsername);
    $prep->execute();
    $result = $prep->get_result();

    $r = $result->fetch_row();
    $exist = $r[0] > 0;

    if($exist){
        throw new Exception("User already exists");
    }

    $user = new \PA\Ldap\LdapUser($iSolutionsUsername);
    if(!$user->exists()){
        // Error here
        throw new Exception('Username not found');
    }

    $firstName = $user->getFirstName();
    $lastName = $user->getLastName();


    $query = 'INSERT INTO `Member` (`firstName`, `lastName`, `iSolutionsUsername`, `joinDate`) 
                VALUES (?, ?, ?, NOW())';
    $prep = $db->prepare($query);

    $prep->bind_param('sss',
        $firstName, $lastName, $iSolutionsUsername);
    $prep->execute();

    return [
        'id' => $db->insert_id,
        'iSolutionsUsername' => $iSolutionsUsername,
        'firstName' => $firstName,
        'lastName' => $lastName
    ];
}




// Adds given details to the database, not being used currently
function admin_members_createMember($firstName, $lastName, $chosenName = null, $iSolutionsUsername = null){
    global $pd;
    // Get the database
    $db = $pd->getPerformArchive();

    // suggested member?
    $suggested = (!isset($iSolutionsUsername) || $iSolutionsUsername === '');


    // Check for the needed data
    if( $suggested && 
    (    !isset($firstName) || $firstName === ''
     ||  !isset($lastName) || $lastName === ''
    )
    ){
        // error here
        throw new Exception('Error: no first and/or last name');
    }



    if(!$suggested){
        // Get the data from LDAP to replace given data
        $user = new \PA\Ldap\LdapUser($iSolutionsUsername);
        if(!$user->exists()){
            // Error here
            throw new Exception('Username not found');
        }else{
            $oldFn = $firstName;
            $firstName = $user->getFirstName();
            $lastName = $user->getLastName();

            // Change the first name to the chosen name if it differs from the LDAP name
            if($oldFn !== '' && $firstName !== $oldFn && (!isset($chosenName) || $chosenName == '')){
                $chosenName = $oldFn;
            }
        }
    }


    // Add this member to the database
    if($suggested){
        $query = 'INSERT INTO `Suggested_Member` (`firstName`, `lastName`, `chosenName`) 
                VALUES (?, ?, ?)';
        $prep = $db->prepare($query);

        $prep->bind_param('sss',
            $firstName, $lastName, $chosenName);
        $prep->execute();

    }else{
        $query = 'INSERT INTO `Member` (`firstName`, `lastName`, `chosenName`, `iSolutionsUsername`, `joinDate`) 
                VALUES (?, ?, ?, ?, NOW())';
        $prep = $db->prepare($query);

        $prep->bind_param('ssss',
            $firstName, $lastName, $chosenName, $iSolutionsUsername);
        $prep->execute();
    }

    return [
        'id' => $db->insert_id,
        'iSolutionsUsername' => $iSolutionsUsername,
        'chosenName' => isset($chosenName) ? $chosenName : null,
        'firstName' => $firstName,
        'lastName' => $lastName
    ];
}


// For now only for PA comm
$app->get(
    '/members/create',
    $authenticate('is_PA_webmaster'),
    function () use ($app){

        $app->render('members/members_create.twig');
    }
)->name('pa_member_create');


$app->map(
    '/members/create',
    $authenticate('is_PA_webmaster'),
    function () use ($app) {
        // Put requests take a JSON string in the body and will return JSON
        // POST requests will take POST data and redirect
        $isPost = $app->request->isPost();
        $data;
        if($isPost){
            // Convert the post data into the correct form
            $p = $app->request->post();
            $firstName = (isset($p['member_name_first'])) ? trim($p['member_name_first']) : null;
            $lastName = (isset($p['member_name_last'])) ? trim($p['member_name_last']) : null;
            $chosenName = (isset($p['member_name_chosen'])) ? trim($p['member_name_chosen']) : null;

            $iSolutionsUsername = (isset($p['member_username'])) ? trim($p['member_username']) : null;

            $data = [
                'firstName'           => $firstName,
                'lastName'            => $lastName,
                'chosenName'          => $chosenName,
                'iSolutionsUsername'  => $iSolutionsUsername
            ];
        }else{
            $data = json_decode($app->request->getBody(), true);
        }

        if(!isset($data['firstName']) || !isset($data['lastName'])){
            // Error?
        }

        $create_results = null;
        $newId = null;
        try{
            //$newId = admin_members_createMember($data['firstName'], $data['lastName'], $data['chosenName'], $data['iSolutionsUsername']);
            $create_results = admin_members_createMemberByUsername($data['iSolutionsUsername']);
            $newId = $create_results['id'];
        }catch(Exception $e){
            if($isPost){
                $app->flashNow('errors', [ $e->getMessage() ]);
                $app->render('members/members_create.twig', [], 400);
            }else{
                echo json_encode([
                    'error' => true,
                    'message' => $e->getMessage()
                ]);
            }
            $app->stop();
        }

        if(!$newId){
            if($isPost){
                // Error
                $app->flashNow('errors', [ 'Some error happened when inserting member' ]);
                $app->render('members/members_create.twig', [], 400);
            }else{
                echo json_encode([
                    'error' => true,
                    'message' => 'Some error happened when inserting member'
                ]);
            }
            $app->stop();
        }


        // suggested member?
        $suggested = (!isset($iSolutionsUsername) || $iSolutionsUsername === '');

        // Return depending on PUT and PUSH
        $url = $suggested ? 'suggested_member' : 'member';
        if($isPost){
            // Redirect
            $app->redirect($app->urlFor($url, [ 'member' => $newId ]));
        }else{
            // Return JSON
            echo json_encode([
                'error' => false,
                'id' => $newId,
                'suggested' => $suggested,
                'url' => $url
            ]);
        }
    }
)->via('POST', 'PUT')->name('pa_member_create-post');


