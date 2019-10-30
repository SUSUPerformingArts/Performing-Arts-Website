<?php
/* Header file for the PA website
    Require this file and call:
        printHeader($title[, $scripts[, $css]]);
            Taking the page title, and optionally the additional scripts and css in the page

    Prints the meta information and the navbar
    also opens the main content tag (closed in footer.php)

    THIS FILE IS LEGACY SUPPORT
    USE THE NEW NAMESPACED VERSION WITH COMPOSER:
    PA\Snippets\Header::printHeader($title, $scripts, $css);
*/


$root = $_SERVER["DOCUMENT_ROOT"];
require_once $root . "/compose.php";

function printHeader($title = "SUSU Performing Arts", $scripts = null, $css = null, $jqueryVersion = null){
	PA\Snippets\Header::printHeader($title, $scripts, $css, $jqueryVersion);
}
