<?php
/* Footer for the PA website
        Call the printFooter function to print out the footer
        Adds a copywrite footer & closes the main content tag
        Includes required javascript, for performance reasons

    THIS FILE IS FOR LEGACY SUPPORT
    USE THE NEW NAMESPACED VERSION WITH COMPOSER:
    PA\Snippets\Footer::printHeader($title, $scripts, $css);
*/


$root = $_SERVER["DOCUMENT_ROOT"];
require_once $root . "/compose.php";

function printFooter($scripts = null){
	PA\Snippets\Footer::printFooter($scripts);
}
