<?php

    $root = $_SERVER["DOCUMENT_ROOT"];
    require $root . "/compose.php";

    use \PA\Auth\Auth;

    \PA\Snippets\Header::printHeader("SUSU Performing Arts :: Submit a Nuffield pitch");


    $now = new DateTime("now", new DateTimeZone("Europe/London"));
    $deadline = new DateTime("2016/05/30", new DateTimeZone("Europe/London"));


    function printPageFooter(){
        ?>
            </p>
            </div>
        <?php
        \PA\Snippets\Footer::printFooter();
    }

?>

    <div class="well well-pa">
        <p class="lead">

<?php


    if($now > $deadline){
        echo "Error, deadline passed";
        printPageFooter();
        exit;
    }

    if(!Auth::isLoggedIn()){
        echo "Error, please log in";
        printPageFooter();
        exit;
    }


    // Find the directory
    $userId = Auth::getUserId();
    $dir = 'submissions/' . $userId;

    @mkdir($dir);


    $storage = new \Upload\Storage\FileSystem($dir, true);

    try{
        $written = new \Upload\File('written', $storage);
        $budget = new \Upload\File('budget', $storage);
    }catch(\Exception $e){
        echo "Error, please upload both documents";
        printPageFooter();
        exit;
    }

    $written->setName("written_pitch");
    $budget->setName("budget");


    $written->addValidations([
        new \Upload\Validation\Mimetype([
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/octet-stream',
            'text/plain'
        ]),

        new \Upload\Validation\Extension([
            'pdf',
            'doc',
            'docx',
            'txt'
        ]),

        // Ensure file is no larger than 5M (use "B", "K", M", or "G")
        new \Upload\Validation\Size('10M')
    ]);


    $budget->addValidations([
        new \Upload\Validation\Mimetype([
            'application/pdf',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/octet-stream'
        ]),

        new \Upload\Validation\Extension([
            'pdf',
            'xls',
            'xlsx'
        ]),

        // Ensure file is no larger than 5M (use "B", "K", M", or "G")
        new \Upload\Validation\Size('10M')
    ]);


    try {
        // Success!
        $written->upload();
        $budget->upload();
    } catch (\Exception $e) {
        // Fail!
        $errs = array_merge($written->getErrors(), $budget->getErrors());

        echo "There is a problem with your files: ";
        echo "<ul>";
        foreach($errs as $err){
            echo "<li>" . $err . "</li>";
        }
        echo "</ul>";

        echo '<p>If you think this is in error please contact the <a href="mailto:web@susuperformingarts.org">Web Officer</a></p>';

        printPageFooter();
        exit;
    }

    // Try and read the log
    $logfile = $dir . '/log.json';
    $json;
    if(is_file($logfile)){
        $json = json_decode(@file_get_contents($logfile), true);
    }

    $count;
    if(isset($json['count'])){
        $count = $json['count'] + 1;
    }else{
        $count = 1;
    }

    // Create the log file
    $user = Auth::getUserInfo();
    $dt = new DateTime(null, new DateTimeZone("Europe/London"));
    $log = [
        'user' => [
            'id' => $user['id'],
            'username' => $user['iSolutionsUsername'],
            'name' => $user['fullName']
        ],

        'timestamp' => $dt->getTimestamp(),
        'datetime' => $dt->format('c'),

        'count' => $count
    ];

    file_put_contents($logfile, json_encode($log, JSON_PRETTY_PRINT));


    $files = @scandir($dir);
    $toKeep = [
        $written->getNameWithExtension(),
        $budget->getNameWithExtension(),
        'log.json'
    ];
    foreach($files as $file){
        if(!in_array($file, $toKeep)){
            unlink($dir . '/' . $file);
        }
    }

?>

    Success! Your pitch has been submitted. If you wish to change anything, please return to the upload page and resubmit. You can do this as many times as you need to before the deadline.










<?php
    printPageFooter();
