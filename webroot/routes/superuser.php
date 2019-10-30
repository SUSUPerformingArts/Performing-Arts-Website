<?php
/***
 * File contains the superuser admin routes
 */


// Impersonate a user
$app->get(
    '/members/impersonate/:member',
    $authenticate('is_PA_webmaster'),
    function ($member) use ($app){
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();


        $memberId = $member;

        $query = "SELECT `id` FROM `Member` WHERE `id` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();

        $user = $result->fetch_assoc();
        $result->free();
        $db->close();

        if(isset($user)){
            Auth::impersonateUser($memberId);
        }

        $app->redirect($app->urlFor('pa_member', [ 'member' => $memberId ]));
    }
)->name("pa_super_member_impersonate");


$app->get(
    '/su/tokens',
    $authenticate('is_PA_webmaster'),
    function () use ($app) {
        $app->render('su/tokens.twig');
    }
)->name("pa_super_tokens");

$app->post(
    '/su/tokens/generate',
    $authenticate('is_PA_webmaster'),
    function () use ($app) {
        $user = $app->request()->post("user");
        $token = Auth::generateOneOffToken($user);

        if($token){
            $app->render('su/tokens_generate.twig', [
                'user' => $user,
                'token' => $token
            ]);
        }else{
            $app->flash("errors", [ "And error, whoops :/" ]);
            $app->redirect($app->urlFor("pa_super_tokens"), 400);
        }
    }
)->name("pa_super_tokens_generate");



/***
$app->get(
    '/dump',
    function () use ($app){
        echo '<pre>' . print_r($_SESSION, true) . '</pre>';
    }
);
***/

// For PA too for now
$app->get(
    '/su/members/create',
    $authenticate('on_PA_committee'),
    function () use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Get the societies
        $query = 'SELECT `id`, `name` FROM `Society` WHERE `type` != 5 ORDER BY `name`';
        $result = $db->query($query); // No user input

        $socs = $result->fetch_all(MYSQLI_ASSOC);

        // Get the committee positions
        $query = 'SELECT `id`, `name` FROM `CommitteePosition`';
        $result = $db->query($query); // No user input

        $positions = $result->fetch_all(MYSQLI_ASSOC);


        $app->render('su/member_create.twig', [
            'societies'          => $socs,
            'committeePositions' => $positions,
            'currentYear'        => getCurrentAcademicYear()
        ]);
    }
)->name('pa_super_member_create');



$app->map(
    '/su/members/create',
    $authenticate('on_PA_committee'),
    function () use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

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

            $societies = null;
            if(isset($p['member_society_id']) && isset($p['member_society_year'])){
                $societies = [];
                foreach($p['member_society_id'] as $i => $socId){
                    // See if they have a committee position
                    $commPos = null;
                    if(isset($p['member_society_comm'][$i]) && $p['member_society_comm'][$i] != ""){
                        $commPos = $p['member_society_comm'][$i];
                    }

                    // See if the profile ID exists
                    $profId = null;
                    if(isset($p['member_society_profile'][$i]) && $p['member_society_profile'][$i] != ""){
                        $profId = $p['member_society_profile'][$i];
                    }


                    $year = (isset($p['member_society_year'][$i])) ? $p['member_society_year'][$i] : false;
                    if(!!$year && is_numeric($year)){
                        $societies[] = [
                            'socId'     => $socId,
                            'year'      => $year,
                            'committee' => $commPos,
                            'profileId' => $profId
                        ];
                    }
                }
            }


            $data = [
                'firstName'           => $firstName,
                'lastName'            => $lastName,
                'chosenName'          => $chosenName,
                'iSolutionsUsername'  => $iSolutionsUsername,
                'societies'           => $societies
            ];
        }else{
            $app->response->headers->set('Content-Type', 'application/json');
            $data = json_decode($app->request->getBody(), true);
        }

        if(!isset($data['firstName']) || !isset($data['lastName'])){
            // Error
        }

        $newId = null;
        try{
            $res = admin_members_createMember($data['firstName'], $data['lastName'], $data['chosenName'], $data['iSolutionsUsername']);
        }catch(Exception $e){
            if($isPost){
                $app->flashNow('errors', [ $e->getMessage() ]);
                $app->render('su/members_create.twig', [], 400);
            }else{
                echo json_encode([
                    'error' => true,
                    'message' => $e->getMessage()
                ]);
            }
            $app->stop();
        }

        $newId = $res['id'];

        if(!$newId){
            if($isPost){
                // Error
                $app->flashNow('errors', [ 'Some error happened when inserting member' ]);
                $app->render('su/members_create.twig', [], 400);
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



        // Add the society data here

        $query = '';
        if($suggested){
            $query = 'INSERT INTO `Suggested_SocietyMember` (suggestedMemberId, societyId, committeePositionId, societyProfileId, year)
                    VALUES (?, ?, ?, ?, ?)';
        }else{
            $query = 'INSERT INTO `SocietyMember` (memberId, societyId, committeePositionId, societyProfileId, year)
                    VALUES (?, ?, ?, ?, ?)';
        }

        // Prep query here to make it a bit quicker
        $prep = $db->prepare($query);

        foreach ($data['societies'] as $society) {
            if(isset($society['year'])){
                $year = $society['year'];
            }else{
                $year = getCurrentAcademicYear();
            }

            $prep->bind_param('iiiii',
                $newId, $society['socId'], $society['committee'], $society['profileId'], $year);
            $prep->execute();
        }

        
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
)->via('POST', 'PUT')->name('pa_super_member_create-post');






// Yeah man, for PA comm too!
$app->get(
    '/su/members/edit/:member',
    $authenticate('on_PA_committee'),
    function ($member) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $memberId = $member;

        // Get the member
        $query = 'SELECT * FROM `Member` WHERE `id` = ?';

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();

        $theMember = $result->fetch_assoc();
        $result->free();

        $theMember = derivePreferredName($theMember);

        if(!isset($theMember)){
            // Flash something here please
            $app->notFound();
            return;
        }

        // Get the current society membership
        $query = 'SELECT * FROM `SocietyMember` WHERE `memberId` = ? ORDER BY `societyId`, `year` DESC';

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $memberId);
        $prep->execute();
        $result = $prep->get_result();

        $maxyear = 50;
        $minyear = PHP_INT_MAX;
        $societyMember = [];
        while($row = $result->fetch_assoc()){
            $soc = $row['societyId'];
            if(!isset($societyMember[$soc])){
                $societyMember[$soc] = [];
            }

            $societyMember[$soc][] = $row;

            $yr = intval($row['year'], 10);
            $maxyear = max($maxyear, $yr);
            $minyear = min($minyear, $yr);
        }
        $result->free();

        // Get all possible societies
        $query = 'SELECT `id`, `name` FROM `Society` WHERE `type` != 5 ORDER BY `name`';
        $result = $db->query($query); // No user input

        $socs = $result->fetch_all(MYSQLI_ASSOC);

        // Get the committee positions
        $query = 'SELECT `id`, `name` FROM `CommitteePosition`';
        $result = $db->query($query); // No user input

        $positions = $result->fetch_all(MYSQLI_ASSOC);


        $app->render('su/member_edit.twig', [
            'member'             => $theMember,
            'maxyear'            => $maxyear,
            'minyear'            => $minyear,
            'images'             => members_getImages($memberId),
            'societyMember'      => $societyMember,
            'societies'          => $socs,
            'committeePositions' => $positions,
            'currentYear'        => getCurrentAcademicYear()
        ]);
    }
)->name('pa_super_member_edit');





$app->post(
    '/su/members/edit/:member',
    $authenticate('on_PA_committee'),
    function ($member) use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        $memberId = $member;


        // Convert the post data into the correct form
        $p = $app->request->post();
        $chosenName = (isset($p['member_name_chosen'])) ? trim($p['member_name_chosen']) : null;

        $societies_add = null;
        if(isset($p['member_society_id']) && isset($p['member_society_year'])){
            $societies_add = [];
            foreach($p['member_society_id'] as $i => $socId){
                // See if they have a committee position
                $commPos = null;
                if(isset($p['member_society_comm'][$i]) && $p['member_society_comm'][$i] != ""){
                    $commPos = $p['member_society_comm'][$i];
                }

                // See if the profile ID exists
                $profId = null;
                if(isset($p['member_society_profile'][$i]) && $p['member_society_profile'][$i] != ""){
                    $profId = $p['member_society_profile'][$i];
                }


                $year = (isset($p['member_society_year'][$i])) ? $p['member_society_year'][$i] : false;
                if(!!$year && is_numeric($year)){
                    $societies_add[] = [
                        'socId'     => $socId,
                        'year'      => $year,
                        'committee' => $commPos,
                        'profileId' => $profId
                    ];
                }
            }
        }


        $societies_edit = null;
        if(isset($p['member_society_old_id']) && isset($p['member_society_old_year'])){
            $societies_edit = [];
            foreach($p['member_society_old_id'] as $i => $socId){
                // See if they have a committee position
                $commPos = null;
                if(isset($p['member_society_old_comm'][$i]) && $p['member_society_old_comm'][$i] != ""){
                    $commPos = $p['member_society_old_comm'][$i];
                }

                // See if the profile ID exists
                $profId = null;
                if(isset($p['member_society_old_profile'][$i]) && $p['member_society_old_profile'][$i] != ""){
                    $profId = $p['member_society_old_profile'][$i];
                }


                $year = (isset($p['member_society_old_year'][$i])) ? $p['member_society_old_year'][$i] : false;
                if(!!$year && is_numeric($year)){
                    $societies_edit[] = [
                        'id'        => $i,
                        'socId'     => $socId,
                        'year'      => $year,
                        'committee' => $commPos,
                        'profileId' => $profId
                    ];
                }
            }
        }


        $societies_delete = (isset($p['member_society_delete'])) ? $p['member_society_delete'] : null;


        // Change the chosenName
        if($chosenName === ''){
            $chosenName = null;
        }

        $query = 'UPDATE `Member` SET 
                    `chosenName` = ?
                    WHERE `id` = ?';

        $prep = $db->prepare($query);

        $prep->bind_param('si',
            $chosenName, $memberId);
        $prep->execute();



        if(isset($societies_add)){
            // Add societies
            $query = 'INSERT INTO `SocietyMember` (memberId, societyId, committeePositionId, societyProfileId, year)
                    VALUES (?, ?, ?, ?, ?)';


            // Prep query here to make it a bit quicker
            $prep = $db->prepare($query);

            foreach ($societies_add as $society) {
                if(isset($society['year'])){
                    $year = $society['year'];
                }else{
                    $year = getCurrentAcademicYear();
                }
                //var_dump($society);
                $prep->bind_param('iiiii',
                    $memberId, $society['socId'], $society['committee'], $society['profileId'], $year);
                $prep->execute();
            }
        }//exit;



        if(isset($societies_edit)){
            // Edit societies
            $query = 'UPDATE `SocietyMember` SET
                        `societyId` = ?, `committeePositionId` = ?, `societyProfileId` = ?, `year` = ?
                        WHERE `id` = ? AND `memberId` = ?';

            $prep = $db->prepare($query);

            foreach($societies_edit as $society){
                if(isset($society['year'])){
                    $year = $society['year'];
                }else{
                    $year = getCurrentAcademicYear();
                }

                $prep->bind_param('iiiiii',
                    $society['socId'], $society['committee'], $society['profileId'], $year, $society['id'], $memberId);
                $prep->execute();
            }
        }



        // Delete societies
        if(isset($societies_delete) && count($societies_delete) > 0){
            $query = 'DELETE FROM `SocietyMember` 
                        WHERE `id` = ?';

            $prep = $db->prepare($query);

            foreach ($societies_delete as $society) {
                $prep->bind_param('i',
                    $society);
                $prep->execute();
            }
        }

        
        $app->flash('successes', [ 'Member Updated!' ]);
        $app->redirect($app->urlFor('member', [ 'member' => $memberId ]));
    }
)->name('pa_super_member_edit-post');




/**** This code is for use in an admin member creat/edit function later

            $societies = [];
            foreach(array_unique($p['member_society_ids']) as $i => $socId){
                if(isset($p['member_society_years'][$i])){
                    $societies[] = [
                        'id'   => $socId,
                        'year' => (isset($p['member_society_years'][$i])) ? $p['member_society_years'][$i] : null
                    ];
                }
            }



if(!$newId){
            // Error
            $app->halt(400, 'Some error happened when inserting member');
        }

        // Add the societies!
        $query = '';
        if($suggested){
            $query = 'INSERT INTO `Suggested_SocietyMember` (suggestedMemberId, societyId, year)
                    VALUES (?, ?, ?)';
        }else{
            $query = 'INSERT INTO `Suggested_SocietyMember` (memberId, societyId, year)
                    VALUES (?, ?, ?)';
        }

        // Prep query here to make it a bit quicker
        $prep = $db->prepare($query);

        foreach ($data['societies'] as $society) {
            if(!isset($society['year'])){
                $year = getCurrentAcademicYear();
            }

            $prep->bind_param('iii',
                $newId, $society['id'], $year);
            $prep->execute();
        }

        // Done
        echo 'Done... Hopefully';
*****/








