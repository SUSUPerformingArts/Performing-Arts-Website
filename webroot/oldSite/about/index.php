<?php

    // About page
    $root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";

    \PA\Snippets\Header::printHeader("SUSU Performing Arts - About Us");

?>

    <div class="well well-pa">
        <h1>About Performing Arts</h1>
    </div>


    <!-- We need to put a proper about here, whoops -->
    <div style="margin-bottom: 1em;">
        <p>The Performing Arts supports <a href="/societies">over 30 societies</a> made up of people who enjoy the arts. Whether your passion is viewing, performing or managing the arts we can provide you with experiences and opportunities along side your degree. Our societies are divided into <a href="/societies#music">Music</a>, <a href="/societies#dance">Dance</a>, <a href="/societies#theatrical">Theatrical</a> and <a href="/societies#tech">Technical</a> societies, all the important aspects of the industry.</p>

        <p>With <a href="/calendar">shows happening every week</a>, we are a busy bunch! Our big events include Pure Dance, which showcases all our dance societies, weekly theatrical shows, and the numerous musical concerts in a HUGE variety of styles.</p>

        <p>Check out our <a href="/archive">archive</a> and <a href="https://fb.com/SUSUPerformingArts">Facebook page</a> for details of our upcoming shows, and how to get involved. If you have any questions do not hesitate to drop the <a href="#committee">Performing Arts Officer</a> an email, <a href="https://twitter.com/SUSUPerform">tweet us</a>, or message us on Facebook. We look forward to hearing from you.</p>

        <p>We look forward to seeing you soon either on stage or in the audience!</p>
    </div>


    <h2><a class="anchor" id="pacard"></a>The Performing Arts Card</h2>
    <div class="row">

        <div class="col-md-5">
            <div class="cardsvg text-center img-responsive hidden-xs"><?php echo file_get_contents("pa_card_animate.svg"); ?></div>
        </div>

        <div class="col-md-7">
            <p>This acts as your membership to the Performing Arts, costing only <b>£3</b> from the <a href="https://boxoffice.susu.org/">SUSU Box Office</a>! It also gets you plenty of handy deals:</p>

            <?php /*<p><em>N.B. Please note that the terms and conditions of the Performing Arts loyalty stamp card (issued with your PA Card) are at the discretion of the relevant committees, managers and production teams of the events to which their use pertains.</em></p> */ ?>

            <ul>
                <li>10% off drinks in The Stags and The Bridge all day Thursday</li>
                <li>PA prices to most <a href="/archive/shows">PA shows</a></li>
                <li><b>Prices as low as &pound;5</b> for selected tickets at the <a href="http://www.nuffieldtheatre.co.uk">Nuffield Theatre</a></li>
                <li><b>&pound;12</b> tickets to <a href="https://www.facebook.com/Music-Theatre-South-34078178027/">Musical Theatre South</a> shows</li>
                <li>Discount on Performing Arts ball at the end of the year!</li>
                <li style="font-weight: bold;">More to be announced: Watch this space</li>
            </ul>
        </div>

    </div>

    <div class="clearfix"></div>

    <h2><a class="anchor" id="committee"></a>Your Committee</h2>
    <div class="committee">

<?php
    include($root . "/committee/committee.php");
    displayCurrentCommittee();
?>

    <a class="btn btn-sm btn-primary" href="/committee/all"><span style="display: inline-block; transform: rotate(-180deg);">&#10157;</span> See previous committees</a>
    </div>




<script>
    
    window.addEventListener("load",function(){
        var pathArray = $('.cardsvg').find('path');
        var lengthArray = $('.cardsvg').find('path');

        setTimeout(function(){
            $('.st1').show();
            $('.st0').show();
            for (i=0;i<pathArray.length;i++){ 

                var path = pathArray[i];
                var length = path.getTotalLength();
                // Clear any previous transition
                path.style.transition = path.style.WebkitTransition = "none";
                // Set up the starting positions
                path.style.strokeDasharray = length + " " + length;
                path.style.strokeDashoffset = length;
                // Trigger a layout so styles are calculated & the browser
                // picks up the starting position before animating
                path.getBoundingClientRect();
                // Define our transition
                path.style.transition = path.style.WebkitTransition = "stroke-dashoffset 2s ease-in-out";

                // Go!
                path.style.strokeDashoffset = '0';

      }},1000);
});
</script>


<?php
    \PA\Snippets\Footer::printFooter();
