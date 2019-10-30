<?php
// Login page
$app->get(
    '/api',
    function () use ($app) {
        // Get the flash data for errors
        $error = null;
        if(isset($app->flash['error'])) {
            $error = $app->flash['error'];
        }

        // The url to redirect to
        $urlRedirect = auth_getContinueUrl();

        // Redirect if already logged in
        if(Auth::isLoggedIn()){
            $app->redirect($urlRedirect);
        }


        // Render the login page
        $app->render("login.twig", [
            "urlRedirect" => $urlRedirect,
            "error" => $error
        ]);
    }
)->name("login");

?>