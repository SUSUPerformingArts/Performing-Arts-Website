<?php
    /* File prints the current committee out */

    $root = $_SERVER["DOCUMENT_ROOT"];

    require_once $root . "/compose.php";


    function echoMemberSquare($memberId, $firstName, $lastName, $position, $image, $email, $bio = null){
        $name = ucwords($firstName . " " . $lastName);
        $position = ucwords($position);
        $bio = ucfirst($bio);
    ?>
        <figure class="text-center committee-member" id="<?php echo "committee-" . strtolower(explode(' ',trim($position))[0]); ?>">
            <img src="/img/committee/<?php echo $image; ?>" alt="<?php echo $name; ?>" class="center-block img-circle img-responsive img-committee">
            <figcaption>
                <p class="h4">
                <?php
                    if(isset($memberId)){
                        echo '<a href="/archive/members/' . $memberId . '">' . $name . '</a>';
                    }else{
                        echo $name;
                    }
                ?>
                </p>
                <p><?php echo $position; ?></p>
                <p><a href="mailto:<?php echo $email; ?>" class="btn btn-pa">Email <?php echo explode(' ',trim($name))[0]; ?></a></p>
            </figcaption>
        </figure>
    <?php
    }

    function displayCurrentCommittee(){
        // Get the database
        $db = PA\Database::getArchiveDatabase();

        // Get the most recent year from the database
        $qu = "SELECT `year` FROM `PA_CommitteeMember` ORDER BY year DESC LIMIT 1";

        $result = $db->query($qu); // No input

        $row = $result->fetch_assoc();
        $year = $row["year"];

        $result->free();
        $db->close();

        displayCommittee(intval($year), true);
    }


    function displayCommittee($year){
        // Get the database
        $db = PA\Database::getArchiveDatabase();

        /* It's quite a simple query
            SELECT cm.firstName, cm.lastName, cm.image, cm.bio, cp.title, cp.email
                FROM `PA_CommitteeMember` cm
                INNER JOIN `PA_CommitteePosition` cp ON cm.positionId = cp.id
                WHERE cm.year = "2014" ORDER BY cmpositionId ASC


        */
        $query = "SELECT cm.`memberId`, cm.`firstName`, cm.`lastName`, cm.`image`, cm.`bio`, cp.`title`, cp.`email` FROM `PA_CommitteeMember` cm
                INNER JOIN `PA_CommitteePosition` cp ON cm.`positionId` = cp.`id`
                WHERE cm.`year` = ? ORDER BY cm.`positionId` ASC";

        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $year);
        $prep->execute();
        $result = $prep->get_result();
        

        // Calcuate the row layout (maybe more complicated later)
        $rows = $result->num_rows;

        $col_l = 3;
        $row_cut = 12/$col_l;
        $i = 0;
        echo "<div class=\"row\">";
        while($row = $result->fetch_assoc()){
            if($i%$row_cut == 0){
                if($i != 0){
                    //echo "</div>";
                }
                //echo "<div class=\"row\">";
                echo "<div class=\"clearfix visible-md-block visible-lg-block\"></div>";
            }

            echo "<div class=\"committee-member-container col-lg-3 col-md-3 col-sm-4 col-xs-6\">";

            echoMemberSquare($row["memberId"], $row["firstName"], $row["lastName"], $row["title"], $row["image"], $row["email"], $row["bio"]);

            echo "</div>";

            $i++;
        }
        echo "</div>";

        $result->free();
        $db->close();

    }




    // Table version
    function echoMemberRow($member){
        $memberId = $member["memberId"];
        $name = ucwords($member["firstName"] . " " . $member["lastName"]);
        $position = ucwords($member["title"]);
    ?>
        <dt><?php echo $position; ?></dt>
        <dd>
            <?php
                if(isset($memberId)){
                    echo '<a href="/archive/members/' . $memberId . '">' . $name . '</a>';
                }else{
                    echo $name;
                }
            ?>
        </dd>
    <?php
    }

    function echoCommitteeTable($year, $committee){
        ?>
        <h2 class="h3"><?php echo $year; ?> - <?php echo $year + 1; ?></h2>
        <hr>
        <dl class="dl-tabbed">
        <?php 
            foreach ($committee as $member) {
                echoMemberRow($member);
            }
        ?>
        </dl>
        <?php
    }

    function echoCommitteesTable(){
        // Get the database
        $db = PA\Database::getArchiveDatabase();

        $query = "SELECT cm.`year`, cm.`memberId`, cm.`firstName`, cm.`lastName`, cm.`image`, cm.`bio`, cp.`title`, cp.`email` FROM `PA_CommitteeMember` cm
                INNER JOIN `PA_CommitteePosition` cp ON cm.`positionId` = cp.`id`
                ORDER BY cm.`year` DESC, cm.`positionId` ASC";

        $result = $db->query($query);

        $committees = [];
        while($row = $result->fetch_assoc()){
            $year = $row["year"];

            if(!array_key_exists($year, $committees)){
                $committees[$year] = [];
            }

            $committees[$year][] = $row;
        }

        foreach ($committees as $year => $committee) {
            echo '<div class="well well-pa">';
            echoCommitteeTable($year, $committee);
            echo '</div>';
        }
    }
