<?php

    // Homepage mate
    $root = $_SERVER["DOCUMENT_ROOT"];

    require_once $root . "/compose.php";

    require_once($root . "/header.php");

    printHeader("SUSU Performing Arts - Home");

?>

<?php
    $db = \PA\Database::getArchiveDatabase();

    $query = "SELECT COUNT(id) AS 'num' FROM `Society` WHERE `type` != 5";

    $result = $db->query($query);

    $socNum = $result->fetch_assoc();
    $socNum = $socNum["num"];

    $result->free();

?>


    <div id="fb-root"></div>
    <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=451768998216844";
    fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>



    <?php /* Mega top bit for big PA advert, yo */ ?>
    <div class="jumbotron">
        <div class="row">
            <div class="col-md-4">
                <img src="/img/logos/pa_black.png" alt="SUSU Performing Arts" class="img-responsive">
            </div>
            <div class="col-md-8">
                <p class="lead">Dance. Music. Theatre. Tech.</p>
                <p>With over <?php echo ((int) (($socNum-1)/10))*10; ?> societies ranging from Theatre Group to Folk Soc, and shows, concerts and events on a weekly basis, there's something for pretty much everyone who's passionate about performing, managing or viewing within the arts.</p>
                <p><a href="/about" class="btn btn-pa btn-lg">Learn More &raquo;</a>
                <a href="/societies" class="btn btn-pa btn-lg">Get Involved &raquo;</a>
                <a href="/calendar" class="btn btn-pa btn-lg">What's On &raquo;</a>
                <a href="/boxoffice" class="btn btn-pa btn-lg">Buy Tickets &raquo;</a></p>
            </div>
        </div>
    </div>


<?php /* Carousel man */
    $carouselEnabled = true;
    $carouselPrefix = "/img/archive/shows/";
    #$carouselImages = ["intothewoods.jpg", "romeojuliet.jpg", "thiswidenight.jpg"];
    $query = "SELECT s.`id`, s.`name`#, s.`societyId`, soc.`name` AS 'societyName', soc.`type` AS 'societyType', D.`lastShowDate`, D.`firstShowDate`, YEAR(D.`firstShowDate`) AS 'year'
            FROM `Show` s
            INNER JOIN (SELECT se.`showId`, MAX(se.`showDate`) AS 'lastShowDate', MIN(se.`showDate`) AS 'firstShowDate' FROM `ShowEvent` se GROUP BY se.`showId`) D ON s.`id` = D.`showId`
            INNER JOIN `Society` soc ON s.`societyId` = soc.`id`
            WHERE D.`lastShowDate` > NOW()
            ORDER BY D.`firstShowDate` DESC";


    $result = $db->query($query); // No input vars

    $upcomingShows = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    $carouselImages = [];

    while(count($carouselImages) < 5 && count($upcomingShows) > 0){
        $s = array_pop($upcomingShows);
        if(file_exists($root . $carouselPrefix . $s["id"] . "/cover.jpg")){
            array_push($carouselImages, [
                    "image" => $s["id"] . "/cover.jpg",
                    "id"    => $s["id"],
                    "name"  => $s["name"]
                ]);
        }
    }

    if($carouselEnabled && count($carouselImages) > 0){
?>
    <h2>
        Shows
    </h2>
    <div id="carousel-shows" class="carousel slide" data-ride="carousel">
  
        <ol class="carousel-indicators">
<?php 
    for($i = 0; $i < count($carouselImages); $i++){
        echo "            <li data-target=\"#carousel-shows\" data-slide-to=\"$i\"";
        if($i == 0){
            echo " class=\"active\"";
        }
        echo "></li>\n";
    }
?>
        </ol>

        <div class="carousel-inner" role="listbox">
<?php
    foreach ($carouselImages as $j => $img){
        if($j == 0){
            echo "            <div class=\"item active\">\n";
        }else{
            echo "            <div class=\"item\">\n";
        }
        echo "<a href=\"/archive/shows/" . $img["id"] . "\">";
        echo "                <img src=\"" . $carouselPrefix . $img["image"] . "\" title=\"" . $img["name"] . "\" alt=\"" . $img["name"] . "\">\n";
        echo "</a>";
        echo "            </div>\n";
    }
?>


<?php
    if(count($carouselImages) > 1){
?>
            <a class="left carousel-control" href="#carousel-shows" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#carousel-shows" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
<?php
    }
?>

        </div>
    </div>
    <div class="pull-right" style="margin: 10px 0px;">
        <a class="btn btn-pa" href="/archive/shows">See what's on &raquo;</a>
        <a class="btn btn-pa" href="/calendar">View the events calendar &raquo;</a>
    </div>
    <div class="clearfix"></div>

<?php

    }

?>

    <?php /* This is a 4 grid of stuff, I think this probably needs to be rejigged */ ?>
    <div class="row">
        <div class="col-md-6 col-lg-4">
            <h2><?php echo $socNum; ?> Societies</h2>
            <p>Our societies are divided into Musical, Theatrical and Dance. And of course there is StageSoc as well for those interested in tech.</p>
            <p><a class="btn btn-pa" href="/societies">Learn more &raquo;</a></p>
            <p><a class="btn btn-pa" href="/schedule">Rehearsal and meeting times &raquo;</a></p>
        </div>

        <!--<div class="col-md-6 col-lg-3">        
            <h2>Blog</h2>
        </div>-->

        <div class="col-md-12 col-lg-8">
            <h2>Follow us</h2>
            <div class="row">
                <div class="col-sm-6">
                    <div class="fb-page" data-href="https://www.facebook.com/SUSUPerformingArts" data-height="600" data-hide-cover="false" data-show-facepile="true" data-show-posts="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/SUSUPerformingArts"><a href="https://www.facebook.com/SUSUPerformingArts">SUSU Performing Arts</a></blockquote></div></div>
                </div>
                <div class="col-sm-6">
                    <a class="twitter-timeline" href="https://twitter.com/SUSUPerform" data-widget-id="277923812060823555">Tweets by @SUSUPerform</a>
                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                </div>
            </div>
        </div>

    </div>


<?php
    require_once($root . "/footer.php");
    printFooter();
?>
