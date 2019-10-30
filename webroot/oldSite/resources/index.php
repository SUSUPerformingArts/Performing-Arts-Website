<?php

    // Resources page
    $root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";

    \PA\Snippets\Header::printHeader("SUSU Performing Arts - Resources");

?>

    <div class="well well-pa">
        <h1>Resources for current members</h1>
    </div>

    <p class="lead">Most of our resources can be found in our <a href="https://docs.google.com/folder/d/0B8QnE0Kdupy-ektjS1ZtTGV6akU/edit?pli=1">Google Drive folder</a>.</p>



    <h2><a href="https://drive.google.com/a/susuperformingarts.org/file/d/0B3v0EoAEW019eEktQTQ5QjlnZGs/view" class="white-link">The Performing Arts Handbook</a></h2>
    <p>
        Not sure how to best publicise your show? Looking for hints, tips and advice on any and all things Performing Arts related? Check out the Performing Arts Handbook 2015-16 <a href="https://drive.google.com/a/susuperformingarts.org/file/d/0B3v0EoAEW019eEktQTQ5QjlnZGs/view">here</a>!
    </p>
    <div class="pa-handbook"></div>

    <hr class="hr-smaller">

    <h3>Previous year's handbooks</h3>
    <ul>
        <li><a href="http://online.fliphtml5.com/srce/toeb/">2014-2015</a></li>
    </ul>


<?php
    \PA\Snippets\Footer::printFooter();
