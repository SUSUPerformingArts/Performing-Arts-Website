<?php
/*
 * Extension for various PA functions
 * Most notably echoinh the header and footer
 **/
namespace PA\Twig;

class AuthExtension extends \Twig_Extension {
    public function getName(){
        return 'PA_Auth';
    }


    public function getFunctions(){
        // Get the Auth class' methods
        $methods = get_class_methods('\PA\Auth\Auth');
        $simpleFunctions = [];

        // Loop over and create a twig function for each one
        foreach ($methods as $method) {
            $simpleFunctions[] = new \Twig_SimpleFunction($method, '\PA\Auth\Auth::' . $method);
        }

        return $simpleFunctions;
    }

}
