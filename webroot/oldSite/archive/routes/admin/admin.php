<?php







$app->get(
    '/admin',
    $authenticate('is_logged_in'),
    function () use ($app) {
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Get all information for the committees they are on
        $commIds = Auth::getCurrentCommittees();
        $comms;

        if(count($commIds) > 0){
            $query = "SELECT socs.`id`, socs.`slug`, socs.`name`, socs.`type` AS 'typeId', type.`name` AS 'type'
                    FROM `Society` socs
                    LEFT JOIN `SocietyType` type ON socs.`type` = type.`id`
                    WHERE socs.`id` IN (" . implode(",", $commIds) . ")
                    ORDER BY socs.`name` ASC";

            $result = $db->query($query);


            $comms = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
        }else{
            $comms = [];
        }


        // Get shows the person is the production team for
        $showIds = Auth::getShowsForProdTeams();
        $shows;

        if(count($showIds) > 0){
            $query = "SELECT `id`, `slug`, `name`, `societyId`, `societySlug`, `societyName`, `year`, `academicYear`
                FROM `Show_WithExpandedInfo`
                WHERE `id` IN (" . implode(",", $showIds) . ")
                ORDER BY `firstShowDate` DESC";

            $result = $db->query($query);

            $shows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
        }else{
            $shows = [];
        }


        $app->render('admin.twig', [
            'committees' => $comms,
            'shows' => $shows
        ]);
    }
);




