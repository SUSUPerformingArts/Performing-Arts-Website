<?php



    if(!isset($_GET["id"])){
        header("Location: ../");
        exit();
    }
    $id = $_GET["id"];



    // Homepage mate
    $root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";

    $db = PA\Database::getArchiveDatabase();

    if(is_numeric($id)){
        // Get the slug and redirect if it's an ID
        $query = 'SELECT `slug` FROM `Society` WHERE `id` = ?';
        $prep = $db->prepare($query);
        $prep->bind_param("i",
            $id);
        $prep->execute();
        $result = $prep->get_result();

        $s = $result->fetch_assoc();
        $result->free();

        header("Location: ?id=" . $s['slug']);
        exit;
    }

    // Get parsedown instance
    $Parsedown = new Parsedown();



    // Get the data
    $query = "SELECT * FROM `Society` WHERE `slug` = ?";

    $prep = $db->prepare($query);
    $prep->bind_param("s",
        $id);
    $prep->execute();
    $result = $prep->get_result();

    $soc = $result->fetch_assoc();

    $result->free();
    $db->close();


    if(!isset($soc["id"])){
        http_response_code(404);
        \PA\Snippets\Header::printHeader("SUSU Performing Arts - Society not found");
        echo "<div class=\"well well-pa\"><h1>Error - Society does not exist</h1></div>";
        echo "<p><a href=\"/societies\">See all societies</a></p>";
    }else{
    \PA\Snippets\Header::printHeader("SUSU Performing Arts - " . $soc["name"]);


    $logoPrefix = '/img/societies/logos/';
    $logoSuffixJpg = '/logo.jpg';
    $logoSuffixPng = '/logo.png';
    $logo = null;
    // Prefer png
    if(is_file($root . ($logoPrefix . $soc['id'] . $logoSuffixPng))){
        $logo = $logoPrefix . $soc['id'] . $logoSuffixPng;
    }else if(is_file($root . ($logoPrefix . $soc['id'] . $logoSuffixJpg))){
        $logo = $logoPrefix . $soc['id'] . $logoSuffixJpg;
    }

?>
    <div class="well well-pa">
        <h1>
            <?php if(isset($logo)){ ?>
            <img class="soc-logo img-thumbnail" src="<?php echo $logo; ?>">
            <?php } ?>
            <?php echo $soc["name"]; ?>
        </h1>
        <?php
            if(isset($soc["subtitle"])){
                echo "      <p>" . $soc["subtitle"] . "</p>\n";
            }
        ?>
    </div>

    <div class="row">
        <div class="col-sm-8 soc-main-description">
            <?php echo $Parsedown->text(htmlspecialchars($soc["description"])); ?>
            <p><a style="padding-left: 30px" href="/archive/societies/<?php echo $id ?>">
                <!--<img src="/img/logos/pa_gray.png" alt="Performing Arts Logo">-->
                See upcoming events and members  &#8658;
            </a></p>
            
            <p><a style="padding-left: 30px" href="/schedule/?soc=<?php echo $soc['id'] ?>">
                See rehearsal schedule &#8658;
            </a></p>
        </div>

        <div class="col-sm-4 soc-main-contact">
            <?php
                if(isset($soc["email"]) && $soc["email"] != ""){
                    echo "<div class=\"soc-contact-url\">";
                    echo "<a href=\"mailto:" . $soc["email"] . "\">";
                    echo "<img src=\"/img/icons/email.png\" alt=\"Email Logo\"> ";
                    echo $soc["email"];
                    echo "</a>";
                    echo "</div>";
                }

                if(isset($soc["website"]) && $soc["website"] != ""){
                    echo "<div class=\"soc-contact-url\">";
                    echo "<a href=\"" . $soc["website"] . "\">";
                    echo "<img src=\"/img/icons/web.png\" alt=\"Website Logo\"> ";
                    echo $soc["website"];
                    echo "</a>";
                    echo "</div>";
                }

                $susuPrefix = "https://www.susu.org/groups/";
                if(isset($soc["susuPage"]) && $soc["susuPage"] != ""){
                    echo "<div class=\"soc-contact-url\">";
                    echo "<a href=\"" . $susuPrefix . $soc["susuPage"] . "\">";
                    echo "<img src=\"/img/icons/susu.png\" alt=\"Website Logo\"> ";
                    echo "SUSU Groups page";
                    echo "</a>";
                    echo "</div>";
                }

                if(isset($soc["facebookPage"]) && $soc["facebookPage"] != ""){
                    echo "<div class=\"soc-contact-url\">";
                    echo "<a href=\"https://fb.com/" . $soc["facebookPage"] . "\">";
                    echo "<img src=\"/img/icons/fb.png\" alt=\"Facebook Logo\"> ";
                    echo "https://fb.com/" . $soc["facebookPage"];
                    echo "</a>";
                    echo "</div>";
                }

                if(isset($soc["facebookGroup"]) && $soc["facebookGroup"] != ""){
                    echo "<div class=\"soc-contact-url\">";
                    echo "<a href=\"" . $soc["facebookGroup"] . "\">";
                    echo "<img src=\"/img/icons/fb.png\" alt=\"Facebook Logo\"> ";
                    echo $soc["facebookGroup"];
                    echo "</a>";
                    echo "</div>";
                }

                if(isset($soc["twitter"]) && $soc["twitter"] != ""){
                    echo "<div class=\"soc-contact-url\">";
                    echo "<a href=\"https://twitter.com/" . $soc["twitter"] . "\">";
                    echo "<img src=\"/img/icons/twitter.png\" alt=\"Twitter Logo\"> ";
                    echo $soc["twitter"];
                    echo "</a>";
                    echo "</div>";
                }

                if(isset($soc['instagram']) && $soc['instagram'] != ""){
                    echo "<div class=\"soc-contact-url\">";
                    echo "<a href=\"https://instagram.com/" . $soc['instagram'] . "\">";
                    echo "<img src=\"/img/icons/instagram.png\" alt=\"Instagram Logo\"> ";
                    echo $soc['instagram'];
                    echo "</a>";
                    echo "</div>";
                }

				
                // Link to PA archive
            ?>
            <div class="soc-contact-url">
                <a style="padding-left: 30px" href="/archive/societies/<?php echo $id ?>">
                    <!--<img src="/img/logos/pa_gray.png" alt="Performing Arts Logo">-->
                    See upcoming events and members
                </a>
            </div>
            <div class="soc-contact-url">
                <a style="padding-left: 30px" href="/schedule/?soc=<?php echo $soc['id'] ?>">
                    See rehearsal schedule
                </a>
            </div>
        </div>
    </div>







<?php
    }


    \PA\Snippets\Footer::printFooter();