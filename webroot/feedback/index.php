<?php
error_reporting(-1);
ini_set('display_errors', 'On');

$webroot = $_SERVER["DOCUMENT_ROOT"];
$php_dir = $webroot . "/php/";
$routes_dir = "routes/";
$templates_dir = './templates';


require_once $webroot . '/compose.php';


use PA\Auth\Auth, PA\Database;


/**
 * Setup the Twig View
 */
$twig_view = new \Slim\Views\Twig();
$twig_view->parserExtensions = [
    new \Slim\Views\TwigExtension(),

    new Jralph\Twig\Markdown\Extension(
	    new Jralph\Twig\Markdown\Parsedown\ParsedownExtraMarkdown
	),

    new \PA\Twig\SnippetsExtension(),
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
));


// Set mode and debug
$app->config('mode', ($app->request->getHost() === "perform.susu.org") ? 'production' : 'development');
$app->config('debug', $app->config('mode') === 'development');

// Default conditions for routes
\Slim\Route::setDefaultConditions([

]);


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
    $subject = "[ERROR][FEEDBACK] ";

    // Generate an ID for this message
    $uid = uniqid('fb_');

    $uri = $app->request->getRootUri() . $app->request->getResourceUri();

    // Build the message
    $code = $exception->getCode();
    $exMessage = htmlspecialchars($exception->getMessage());
    $file = $exception->getFile();
    $line = $exception->getLine();
    $trace = $exception->getTraceAsString();


    $message = "The feedback section has encountered an error.";
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



function limitWords($str, $num = 50){
    $arr = str_word_count($str, 1);
    if(count($arr) > $num){
        $arr = array_slice($arr, 0, $num);
        $arr[] = '...';
    }
    return implode(' ', $arr);
}

function getLabels($item){
    $app = \Slim\Slim::getInstance();
    $labels = [];
    // Get response label
    if(isset($item['response']) && count($item['response']) > 0){
        $labels[] = [
            'class' => 'info',
            'text' => 'Has response',
            'link' => '$this'
        ];
    }

    // Get development ID
    if(isset($item['websiteDevelopmentId'])){
        $labels[] = [
            'class' => $item['cssClass'],
            'text' => $item['progressName'],
            'link' => $app->urlFor('feature', [ 'feature' => $item['websiteDevelopmentId'] ])
        ];
    }


    return (count($labels) > 0) ? $labels : null;
}



/**
 * Define the Slim application routes
 * These are deligated to seperate files for brevity
 */

// Main page
$app->get(
    '/',
    function () use ($app) {
        $db = Database::getSiteDatabase();

        // Get the feedback/suggestions
        $query = "SELECT f.*, d.`title` AS 'developmentTitle', p.`name` AS 'progressName', p.`cssClass`
                FROM `WebsiteFeedback` f
                LEFT JOIN `WebsiteDevelopment` d ON f.`websiteDevelopmentId` = d.`id`
                LEFT JOIN `WebsiteDevelopmentProgress` p ON d.`progressId` = p.`id`
                ORDER BY f.`createTimestamp` DESC";

        $result = $db->query($query);

        $feedback = []; $suggestions = [];
        while($row = $result->fetch_assoc()){
            $row['body'] = limitWords($row['body']);
            $row['labels'] = getLabels($row);
            if($row['suggestion']){
                $suggestions[] = $row;
            }else{
                $feedback[] = $row;
            }
        }
        $result->free();

        // Get development level names - Do not include won't fix in the naming
        $query = 'SELECT `id`, `name` FROM `WebsiteDevelopmentProgress`
                WHERE `id` != 4';

        $result = $db->query($query);
        $progressNames = [];
        while($row = $result->fetch_assoc()){
            $progressNames[$row['id']] = $row['name'];
        }
        $result->free();

        // Get the development features
        $query = 'SELECT * FROM `WebsiteDevelopment`
                WHERE `progressId` != 4
                ORDER BY `createTimestamp` DESC';

        $result = $db->query($query);
        $development = [];
        while($row = $result->fetch_assoc()){
            $n = $row['progressId'];
            if(!isset($development[$n])){
                $development[$n] = [];
            }

            $row['body'] = limitWords($row['body']);

            $development[$n][] = $row;
        }
        $result->free();

        $app->render('index.twig', [
            'progressNames' => $progressNames,
            'feedback' => $feedback,
            'suggestions' => $suggestions,
            'development' => $development
        ]);
    }
)->name("home");


function feedbackRoute($id, $suggestion){
    $app = \Slim\Slim::getInstance();
    $db = Database::getSiteDatabase();

    $suggestion = !!$suggestion;
    $suggestionVar = $suggestion ? 1 : 0;

    $query = "SELECT f.`title`, f.`body`, f.`response`, f.`websiteDevelopmentId`, f.`suggestion`, d.`title` AS 'developmentTitle'
            FROM `WebsiteFeedback` f
            LEFT JOIN `WebsiteDevelopment` d ON f.`websiteDevelopmentId` = d.`id`
            WHERE f.`id` = ?";

    $prep = $db->prepare($query);
    $prep->bind_param('i', $id);

    $prep->execute();
    $result = $prep->get_result();

    $item = $result->fetch_assoc();

    if(is_null($item)){
        $app->notFound();
        return;
    }

    if($item['suggestion'] != $suggestionVar){
        $urlTitle = ($item['suggestion'] == 1) ? 'suggestion' : 'feedback';
        $app->redirect($app->urlFor($urlTitle, [ $urlTitle => $id ]));
    }

    if($item['websiteDevelopmentId']){
        $development = [
            'id' => $item['websiteDevelopmentId'],
            'title' => $item['developmentTitle']
        ];
    }else{
        $development = null;
    }

    $app->render('feedback.twig', [
        'suggestion' => $suggestion,
        'title' => $item['title'],
        'body' => $item['body'],
        'response' => $item['response'],
        'development' => $development
    ]);
}


$app->get(
    '/suggestion/:suggestion',
    function($suggestion) use ($app) {
        feedbackRoute($suggestion, true);
    }
)->name('suggestion');

$app->get(
    '/feedback/:feedback',
    function($feedback) use ($app) {
        feedbackRoute($feedback, false);
    }
)->name('feedback');

$app->get(
    '/feature/:feature',
    function($feature) use ($app) {
        $db = Database::getSiteDatabase();

        $fid = $feature;

        $query = "SELECT d.`title`, d.`body`, d.`updates`, UNIX_TIMESTAMP(d.`updateTimestamp`) AS 'updateTimestamp', p.`name` AS 'progressName', p.`cssClass`
                FROM `WebsiteDevelopment` d
                LEFT JOIN `WebsiteDevelopmentProgress` p ON d.`progressId` = p.`id`
                WHERE d.`id` = ?";

        $prep = $db->prepare($query);
        $prep->bind_param('i', $fid);

        $prep->execute();
        $result = $prep->get_result();

        $item = $result->fetch_assoc();

        if(is_null($item)){
            $app->notFound();
            return;
        }

        $updateTimestamp = new DateTime();
        $updateTimestamp->setTimestamp($item['updateTimestamp']);

        $app->render('feature.twig', [
            'title' => $item['title'],
            'body' => $item['body'],
            'updates' => $item['updates'],
            'updateTimestamp' => $updateTimestamp,
            'progressName' => $item['progressName'],
            'cssClass' => $item['cssClass'],
        ]);
    }
)->name('feature');






$app->get(
    '/submit',
    function () use ($app){
        $app->render('submit.twig');
    }
)->name('submit');


$app->post(
    '/submit',
    function () use ($app){
        if(!Auth::isLoggedIn()){
            $app->flash('errors', ['Please log in to submit feedback.']);
            $app->redirect('/archive/login', 401);
            return;
        }

        $title = trim($app->request->post('suggest_title'));
        $body = trim($app->request->post('suggest_body'));
        $suggestion = $app->request->post('suggest_suggestion') ? 1 : 0;

        if(!$title){
            $app->flashNow('errors', [ 'Title is required' ]);
            $app->status(400);
            $app->render('submit.twig');
            return;
        }

        $db = Database::getSiteDatabase();
        $memberId = Auth::getUserId();
        $now = time();

        $query = 'INSERT INTO `WebsiteFeedback` (`title`, `body`, `suggestion`, `memberId`, `createTimestamp`, `updateTimestamp`)
                VALUES (?, ?, ?, ?, FROM_UNIXTIME(?), FROM_UNIXTIME(?))';

        $prep = $db->prepare($query);
        $prep->bind_param('ssiiii',
            $title, $body, $suggestion, $memberId, $now, $now);

        $prep->execute();

        $newId = $db->insert_id;
        $urlName = ($suggestion === 1) ? 'suggestion' : 'feedback';

        $app->redirect($app->urlFor($urlName, [ $urlName => $newId ]));
    }
);








/**
 * Run the Slim application
 */
$app->run();
