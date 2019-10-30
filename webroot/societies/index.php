<?php

    // Homepage mate
    $root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";

    \PA\Snippets\Header::printHeader("SUSU Performing Arts - Societies");

?>

    <div class="well well-pa">
        <h1>Societies</h1>
        <p>Jump to: <a href="#theatrical">Theatrical</a> / <a href="#music">Music</a> / <a href="#dance">Dance</a> / <a href="#tech">Tech</a></p>
    </div>

<?php

    $logoPrefix = '/img/societies/logos/';
    $logoSuffix = '/logo.jpg';

    function echoSoc($id, $slug, $name, $description, $logo, $email, $website, $susuPage, $facebookPage, $facebookGroup, $twitter, $instagram){
        global $logoPrefix;

        echo "<h3>\n<a href=\"society/" . $slug . "\" class=\"white-link\">";
        if(isset($logo) && $logo != ""){
            /*echo "<img class=\"soc-logo img-thumbnail\" src=\"" . $logoPrefix . $logo . "\">";*/
        }
        echo $name;
        echo "\n</a></h3>\n";

        echo "<div class=\"soc-description\">\n";

        // Get parsedown instance
        $Parsedown = new Parsedown();
        $description = $Parsedown->text(htmlspecialchars($description));
        
        // Cut the description to not overwhelm!
        echo (strlen($description) > 300)?substr($description,0,300) . "...\n":$description;
        //echo $description;

        echo "</div>\n";

        echo "<div class=\"clearfix pull-right\">\n";
        echo "<a class=\"btn btn-pa\" href=\"society/" . $slug . "\">Read More <span class=\"glyphicon glyphicon-chevron-right\"></span></a>\n";
        echo "</div>\n";

        echo "<div class=\"clearfix\"></div>";

        echo "<div class=\"soc-contact\">\n";

        if(isset($email) && $email != ""){
            echo "<div class=\"soc-contact-url\">";
            echo "<a href=\"mailto:" . $email . "\">";
            echo "<img src=\"/img/icons/email.png\" alt=\"Email Logo\"> ";
            echo $email;
            echo "</a>";
            echo "</div>";
        }

        if(isset($website) && $website != ""){
            echo "<div class=\"soc-contact-url\">";
            echo "<a href=\"" . $website . "\">";
            echo "<img src=\"/img/icons/web.png\" alt=\"Website Logo\"> ";
            echo $website;
            echo "</a>";
            echo "</div>";
        }

        $susuPrefix = "https://www.susu.org/groups/";
        if(isset($susuPage) && $susuPage != ""){
            echo "<div class=\"soc-contact-url\">";
            echo "<a href=\"" . $susuPrefix . $susuPage . "\">";
            echo "<img src=\"/img/icons/susu.png\" alt=\"Website Logo\"> ";
            echo "SUSU Groups page";
            echo "</a>";
            echo "</div>";
        }

        if(isset($facebookPage) && $facebookPage != ""){
            echo "<div class=\"soc-contact-url\">";
            echo "<a href=\"https://fb.com/" . $facebookPage . "\">";
            echo "<img src=\"/img/icons/fb.png\" alt=\"Facebook Logo\"> ";
            echo "https://fb.com/" . $facebookPage;
            echo "</a>";
            echo "</div>";
        }

        if(isset($facebookGroup) && $facebookGroup != ""){
            echo "<div class=\"soc-contact-url\">";
            echo "<a href=\"" . $facebookGroup . "\">";
            echo "<img src=\"/img/icons/fb.png\" alt=\"Facebook Logo\"> ";
            echo $facebookGroup;
            echo "</a>";
            echo "</div>";
        }

        if(isset($twitter) && $twitter != ""){
            echo "<div class=\"soc-contact-url\">";
            echo "<a href=\"https://twitter.com/" . $twitter . "\">";
            echo "<img src=\"/img/icons/twitter.png\" alt=\"Twitter Logo\"> ";
            echo $twitter;
            echo "</a>";
            echo "</div>";
        }

        if(isset($instagram) && $instagram != ""){
            echo "<div class=\"soc-contact-url\">";
            echo "<a href=\"https://instagram.com/" . $instagram . "\">";
            echo "<img src=\"/img/icons/instagram.png\" alt=\"Instagram Logo\"> ";
            echo $instagram;
            echo "</a>";
            echo "</div>";
        }

        echo "</div>";
    }



    /* Get the data for the socieites */
    $qu = "SELECT socs.`id`, socs.`slug`, socs.`name`, socs.`description`, socs.`logo`, socs.`website`, socs.`susuPage`, socs.`facebookPage`, socs.`facebookGroup` , socs.`twitter`, socs.`instagram`, socs.`email`, type.`name` AS 'type'
        FROM `Society` socs
        LEFT JOIN `SocietyType` type ON socs.`type` = type.`id` WHERE type.`id` != 5 ORDER BY socs.`type` ASC, socs.`name` ASC";

    $db = \PA\Database::getArchiveDatabase();
    $result = $db->query($qu); // No input

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

    // Loop around the types and display them
    foreach($allSocs as $type => $socs){
        echo "<div class=\"well well-pa\">\n";
        echo "<h2 class=\"". strtolower($type) ."\"><a id=\"" . strtolower($type) . "\" class=\"anchor\"></a>" . $type . "</h2>\n";
        echo "<hr>\n";

        echo "<div class=\"row\">\n";
        foreach($socs as $i => $soc){
            if($i%4 == 0){
                echo "<div class=\"clearfix visible-lg-block visible-md-block\"></div>";
            }
            if($i%2 == 0){
                echo "<div class=\"clearfix visible-sm-block\"></div>";
            }

            echo "<div class=\"soc-block col-lg-3 col-md-3 col-sm-6\">\n";
            echoSoc($soc["id"], $soc['slug'], $soc["name"], $soc["description"], $soc["logo"], $soc["email"], $soc["website"], $soc["susuPage"], $soc["facebookPage"], $soc["facebookGroup"], $soc["twitter"], $soc["instagram"]);
            echo "</div>\n";
        }
        echo "</div>\n";

        echo "</div>\n";
    }


?>









<?php
    \PA\Snippets\Footer::printFooter();
