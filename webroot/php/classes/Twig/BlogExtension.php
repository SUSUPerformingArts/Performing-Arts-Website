<?php
/*
 * Extension for various PA functions
 * Most notably echoinh the header and footer
 **/
namespace PA\Twig;

if(!isset($webroot)){
    $webroot = $_SERVER["DOCUMENT_ROOT"];
}

// Require the header.php and footer.php files
require $webroot . "/header.php";
require $webroot . "/footer.php";

class BlogExtension extends \Twig_Extension {
    public function getName(){
        return 'PA_Blog';
    }


    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction("header", function($title = "SUSU Performing Arts Blog", $scripts = null, $css = []){
                $user = (isset($_SESSION["user"]))?$_SESSION["user"]:null;
                $app = \Slim\Slim::getInstance();
                $uri = $app->request->getRootUri() . $app->request()->getResourceUri();
                if($uri == "/"){
                    $uri = null;
                }

                $css[] = "/css/archive.css";
                printHead($title, $scripts, $css);
                printNav($uri, $user);
            }),



            new \Twig_SimpleFunction("footer", function($scripts = null){
                printFooter($scripts);
            })
        );
    }

}



?>
