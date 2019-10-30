<?php

// Whoever added this, can you please email me on 'web@susuperformingarts.org'
//header('location: https://www.susu.org/opportunities/performing-arts.html');
//die();

error_reporting(-1);
ini_set('display_errors', 'On');

$webroot = $_SERVER["DOCUMENT_ROOT"];
$php_dir = $webroot . "/php/";
$routes_dir = "routes/";
$templates_dir = './templates';


require_once $webroot . '/compose.php';

// Use a class alias to simplify the use of auth functions
// Used instead of 'use' as this persists across includes
class_alias('PA\Auth\Auth', 'Auth');
class_alias('PA\Auth\LDAPUserExecption', 'LDAPUserExecption');
class_alias('PA\Auth\InvalidUserException', 'InvalidUserException');


/**
 * Setup the Twig View
 */
$twig_view = new \Slim\Views\Twig();
$twig_view->parserExtensions = [
    new \Slim\Views\TwigExtension(),

    new Jralph\Twig\Markdown\Extension(
	    new Jralph\Twig\Markdown\Parsedown\ParsedownExtraMarkdown
	),

    new \PA\Twig\ArchiveExtension(),
    new \PA\Twig\AuthExtension(),
];


$twig_view->appendData([
    'dateformatShort' => "d/m/Y",
    'dateformat' => "l, jS F Y \\a\\t g:ia"
]);

/**
 * Instantiate a Slim application
 */

$app = new \Slim\Slim(array(
    'view' => $twig_view,
    'cookies.secure' => true,
    'cookies.secret_key' => 'susupaisgreat',
    'cookies.lifetime' => '1 week',
    'templates.path' => $templates_dir,


    // Custom configs
    'webroot' => $webroot,
    'images.members' => '/img/archive/members/',
    'images.shows' => '/img/archive/shows/',
    'images.societies' => '/img/societies/logos/',
));


// Set mode and debug
$app->config('mode', ($app->request->getHost() === "perform.susu.org") ? 'production' : 'development');
$app->config('debug', $app->config('mode') === 'development');

// Default conditions for routes
\Slim\Route::setDefaultConditions(array(
    'year' => '(19|20)\d\d',
    'yearless' => 'yearless', // This allows using the option :yearless as a year, thus allowing it to be registered as an override for :year routes

    // Put these here for now
    #'society' => '[0-9]+',
    'venue' => '[0-9]+',
    #'show' => '[0-9]+',
    'member' => '[0-9]+',
));



// Require the database things
require_once $webroot . "/database.php";
$pd = new PerformDatabase();

// Misc functions
require_once $php_dir . "archive_funcs.php";

// Require the calendar
require_once $webroot . "/calendar/calendar.php";



// Some hooks
// Redirect to make the path lowercase if required
$app->hook('slim.before.router', function () use ($app) {
    $lPath = strtolower($app->environment['PATH_INFO']);

    if(substr($lPath, -1, 1) == '/' && $lPath != '/'){
        // Remove trailing slash
        $lPath = substr($lPath, 0, strlen($lPath) - 1);
    }

    if($app->environment['PATH_INFO'] != $lPath){
        $app->redirect($app->environment['SCRIPT_NAME'] . $lPath, 301);
    }
});



// Define error handlers
// Server error
$app->error(function (\Exception $exception) use ($app) {
    // Sends an email and renders a sorry message
    $from = "errors@perform.susu.org";
    $to = "webmaster@susuperformingarts.org";
    $subject = "[ERROR][ARCHIVE] ";

    // Generate an ID for this message
    $uid = uniqid('arc_');

    $uri = $app->request->getRootUri() . $app->request->getResourceUri();

    // Build the message
    $code = $exception->getCode();
    $exMessage = htmlspecialchars($exception->getMessage());
    $file = $exception->getFile();
    $line = $exception->getLine();
    $trace = $exception->getTraceAsString();


    $message = "The PA archive encountered an error.";
    $message .= "\n  UID: " . $uid;
    $message .= "\n    Accessing page: " . $uri;
    $message .= "\n\n\n";

    $message .= sprintf("   Type: %s \n", get_class($exception));
    if ($code) {
        $message .= sprintf("   Code: %s \n", $code);
    }
    if ($message) {
        $message .= sprintf("Message: %s \n", $exMessage);
    }
    if ($file) {
        $message .= sprintf("   File: %s \n", $file);
    }
    if ($line) {
        $message .= sprintf("   Line: %s \n", $line);
    }
    if ($trace) {
        $message .= "\n\n Trace: \n\n";
        $message .= $trace;
    }



    // Get app specific things

    // Post & Get data
    $message .= "\n\n\n\n -------------------------------------------------------------------------------------- \n\n\n\n";
    $post = $app->request->post();
    if(count($post) > 0){
        $message .= "POST data: \n\n";
        $message .= print_r($post, true);
    }


    $message .= "\n\n\n\n -------------------------------------------------------------------------------------- \n\n\n\n";
    $get = $app->request->get();
    if(count($get) > 0){
        $message .= "GET data: \n\n";
        $message .= print_r($get, true);
    }


    // User info
    $message .= "\n\n\n\n -------------------------------------------------------------------------------------- \n\n\n\n";
    if(Auth::isLoggedIn()){
        $message .= "Logged in as: \n\n";
        $message .= print_r(Auth::getUserInfo(), true);
    }


    $message .= "\n\n\n\n -------------------------------------------------------------------------------------- \n\n\n\n";


    $subject .= 'UID: ' . $uid . ' || File: ' . $uri;

    $headers = 'From: ' . $from;

    $success = mail($to, $subject, $message, $headers);

    $app->render('errors/error.twig', [
        'emailed' => $success,
        'uid' => $uid
    ]);
});

$app->notFound(function () use ($app) {
    $app->render('errors/404.twig', []);
});








/**
 * Define the Slim application routes
 * These are deligated to seperate files for brevity
 */

$app->get(
    '/help',
    function () use ($app) {
        $help = file_get_contents("help.markdown");
        $app->render("help.twig", [
            "help" => $help
        ]);
    }
)->name("pa_help_main");



require_once $routes_dir . "api/api.php";
require_once $routes_dir . "api/api_calendar.php";

// Home page
require_once $routes_dir . "home.php";

// Login pages
require_once $routes_dir . "auth.php";
require_once $routes_dir . "api/api_auth.php";

// Member pages
require_once $routes_dir . "members.php";
require_once $routes_dir . "api/api_members.php";

// Rehearsals page
require_once $routes_dir . "rehearsals.php";

// Show pages
require_once $routes_dir . "shows.php";
require_once $routes_dir . "api/api_shows.php";

// Venue pages
require_once $routes_dir . "venues.php";

// Society pages
require_once $routes_dir . "societies.php";
require_once $routes_dir . "api/api_societies.php";

// Resources
require_once $routes_dir . "resources.php";

// About
require_once $routes_dir . "about.php";

// Admin pages
require_once $routes_dir . "admin/admin_shows.php";
require_once $routes_dir . "admin/admin_shows_members.php";
require_once $routes_dir . "admin/admin_member.php";
require_once $routes_dir . "admin/admin_societies.php";

// Generic Admin
require_once $routes_dir . "admin/admin.php";

// SUPERUSER
require_once $routes_dir . "superuser.php";

//Voting 2k18
require_once $routes_dir . "vote.php";

/**
 * Run the Slim application
 */
$app->run();
