<?php
/*** Contains Auth routes, Auth logic is contained in /php/auth/auth.php */


function auth_redirectToLogin(){
    $app = \Slim\Slim::getInstance();
    $rootUri = $app->request()->getRootUri();
    $redir = $rootUri . $app->request()->getResourceUri();
    $app->flash('errors', ['Login required']);
    $app->redirect($rootUri . '/login?continue=' . $redir);
}

function auth_redirectToReferrer(){
    $app = \Slim\Slim::getInstance();
    $app->flash('errors', ['You do not have permission to access that page']);
    if(strlen($app->request->getReferrer()) > 0){
        $app->redirect($app->request->getReferrer());
    }else{
        $app->redirect($app->request->getRootUri());
    }
}

function auth_getContinueUrl(){
    $app = \Slim\Slim::getInstance();

    if($app->request()->get("continue") && $app->request()->get("continue") != $app->request->getRootUri() . "/logout" && $app->request()->get("continue") != $app->request->getRootUri() . "/login") {
        return $app->request()->get("continue");
    }else{
        return '/';
    }
}


$authenticate = function($role, array $extraData = null) {
    return function (\Slim\Route $route) use ($role, $extraData) {
        $params = $route->getParams();
        if(!is_null($extraData)){
            $params = array_merge($params, $extraData);
        }
        if(!Auth::isLoggedIn()){
            auth_redirectToLogin();
            return;
        }


        $authorised = Auth::authenticate($role, $params);

        if($authorised){
            return true;
        }else{
            // Failed the auth, redirect
            auth_redirectToReferrer();
        }
    };
};


// Login page
$app->get(
    '/login',
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
)->name("pa_login");



// Login logic here, if not authorised rerender the page
$app->post(
    '/login',
    function () use ($app){
        // Get data
        $username = strtolower($app->request()->post("username"));
        $password = $app->request()->post("password");

        // The url to redirect to
        $urlRedirect = auth_getContinueUrl();

        /******** Bypass Auth here during development ********/
        if($app->config("mode") == 'development'){
            if($username == "test" && $password == "pa is great"){
                $user = Auth::checkUserInDatabase("cdc1g12"); // Inpersonate me
                Auth::populateUserSession($user);
                $app->redirect($urlRedirect);
            }else{
                // Render the login page
                $app->render("login.twig", [
                    "urlRedirect" => $urlRedirect,
                    "error" => "Invalid username or password",
                    "username" => $username
                ]);
            }
            return;
        }


        // Actual login calls
        $login_result = Auth::login($username, $password);

        // null means login has failed
        if(is_null($login_result)){
            $app->render("login.twig", [
                "urlRedirect" => $urlRedirect,
                "error" => "Invalid username or password",
                "username" => $username
            ]);
            return;
        }

        $newUser = $login_result['newUser'];
        $user = $login_result['user'];

        // New user!
        if($newUser){
            // User making failed :(
            if(is_null($user)){
                $app->render("login.twig", [
                    "urlRedirect" => $urlRedirect,
                    "error" => "An error occurred creating your account, please contact the <a href='mailto:web@susuperformingarts.org'>Web Officer</a>",
                    "username" => $username
                ]);
                return;
            }


            // Yay, redirect to the member edit page
            $app->flash('successes', [
                'Welcome to Performing Arts, your account has been successfully created!',
                'To add yourself to committees for societies please contact the <a href="mailto:web@susuperformingarts.org">Web Officer</a> with the society, year and position for the committee.'
            ]);
            $app->redirect($app->urlFor('member_edit') . '?continue=' . $urlRedirect);
            return;
        }


        // Normal login successful, redirect!
        $app->redirect($urlRedirect);
    }
)->name("pa_login-post");


$app->get(
    '/logout',
    function () use ($app) {
        // The url to redirect to
        $urlRedirect = auth_getContinueUrl();

        // Kill the session variable
        Auth::logout();

        $app->redirect($urlRedirect);
    }
)->name("pa_logout");


$app->get(
    '/login/token',
    function () use ($app){
        // The url to redirect to
        $urlRedirect = auth_getContinueUrl();

        // Redirect if already logged in
        if(Auth::isLoggedIn()){
            $app->redirect($urlRedirect);
        }


        // Render the login page
        $app->render("login_token.twig", [
            "urlRedirect" => $urlRedirect
        ]);
    }
)->name("pa_login_token");



// Login logic for token auth
$app->post(
    '/login/token',
    function () use ($app){
        // Get data
        $username = strtolower($app->request()->post("username"));
        $password = $app->request()->post("password");

        // The url to redirect to
        $urlRedirect = auth_getContinueUrl();

        $authorised = Auth::loginWithToken($username, $password);

        if($authorised){
            // Normal login successful, redirect!
            $app->redirect($urlRedirect);
        }else{
            $app->render("login_token.twig", [
                "urlRedirect" => $urlRedirect,
                "error" => "Token or username invalid",
                "username" => $username
            ]);
        }
    }
)->name("pa_login_token-post");

