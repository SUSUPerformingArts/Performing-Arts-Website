<?php

    // Homepage mate
    $root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";

    \PA\Snippets\Header::printHeader("SUSU Performing Arts Ball - Feedback");

?>


<iframe id="g_form" style="width: 100%; height: 2800px; border-style: none; position: relative; top: -5em;" src="https://docs.google.com/a/susuperformingarts.org/forms/d/1zlwK43CHNY_7hn1e2fFPAZp15qNscsxi9h_hioRGqdg/viewform"></iframe>

<a style="text-align: right; display: block; position: relative; top: -5em;" href="#">Go to top (see submission result)</a>

<?php
    \PA\Snippets\Footer::printFooter();
