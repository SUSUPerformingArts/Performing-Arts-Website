<?php
/*
 * Extension for various PA functions
 * Most notably echoinh the header and footer
 **/
namespace PA\Twig;

class ArchiveExtension extends SnippetsExtension {
    public function getName(){
        return 'PA_Archive';
    }


    public function getFunctions(){
        $arr = parent::getFunctions();
        return array_merge($arr, [
            new \Twig_SimpleFunction("header", function($title = "SUSU Performing Arts - Show Archive", $scripts = null, $css = []){

                $css[] = "/css/archive.css";
                \PA\Snippets\Header::printHead($title, $scripts, $css);

		$splitUrl = explode("/", $_SERVER['REQUEST_URI']); //$title
		if (sizeof($splitUrl) < 2) {
			\PA\Snippets\Header::printArchiveNav();
		} else if (strtolower($splitUrl[1]=="archive")) {
			\PA\Snippets\Header::printArchiveNav();
		} else {
			\PA\Snippets\Header::printNewNav();
		}

                
            }),

            new \Twig_SimpleFunction("getShowUrl", function(array $opts){
                return shows_getShowUrl($opts);
            })
        ]);
    }

}
