<?php
/*
 * Extension for the PA Snippets
 **/
namespace PA\Twig;

class SnippetsExtension extends \Twig_Extension {
    public function getName(){
        return 'PA_Snippets';
    }

    public function getFunctions(){
        return [
            new \Twig_SimpleFunction('snippet_*', function($name, $opts = null){
                $name = '\PA\Snippets\\' . ucwords($name);
                if(is_subclass_of($name, '\PA\Snippets\SnippetInterface')){
                    return $name::printSnippet($opts);
                }
            }),


            new \Twig_SimpleFunction("header", function($title = null, $scripts = null, $css = [], $jqueryVersion = null){
                \PA\Snippets\Header::printHeader($title, $scripts, $css, $jqueryVersion);
            }),
            
            new \Twig_SimpleFunction("fancySubHeader", function($theme="pa_container", $title="", $subTitle="", $tag="", $imagePath=null){
                \PA\Snippets\Header::printFancySubHeader($theme, $title, $subTitle, $tag, $imagePath);
            }),

            new \Twig_SimpleFunction("footer", function($scripts = null){
                \PA\Snippets\Footer::printFooter($scripts);
            })
            
        ];
    }

}
