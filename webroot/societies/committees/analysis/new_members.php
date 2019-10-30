<?php



    error_reporting(-1);
    ini_set('display_errors', 'On');


    // About page
    $root = $_SERVER["DOCUMENT_ROOT"];
    require $root . '/compose.php';

    $db = \PA\Database::getArchiveDatabase();

    use \PA\Auth\Auth;

    $mapping = [];


    /** Read from the JSON file ***/
    $dir = @scandir("../results", SCANDIR_SORT_DESCENDING);
    $json = null;
    if(isset($dir[0])){
        $json = json_decode(@file_get_contents("../results/" . $dir[0]), true);
    }
    $people = [];

    foreach ($json as $soc) {
        $people = array_merge($people,
                    array_udiff($soc['members'], $people, function($a, $b){
                        return ($a['username'] == $b['username']) ? 0 : -1;
                    }));
    }



    $left = [];
    foreach ($people as $person) {
        $user = Auth::checkUserInDatabase($person['username']);
        if(is_null($user)){
            $left[] = $person;
        }
    }
    sort($left);
    echo implode("<br>", array_map(function($a){ return $a['username'] . " (" . $a['name'] . ")"; }, $left));

    echo "<br>Count: " . count($left);



    // Insert at your desire
    $fail = [];
    if(isset($_POST['add'])){
        $query_cName = "UPDATE `Member` SET
            `chosenName` = ?
            WHERE `id` = ?";
        $prep_cName = $db->prepare($query_cName);

        foreach ($left as $l) {
            $split = explode(" ", $l['name']);
            $fname = $split[0];
            try{
                $user = Auth::createNewUser($l['username']);

                if($fname != $user['firstName']){
                    // Change the chosen name
                    $id = $user['id'];
                    $prep_cName->bind_param("si",
                        $fname, $id);
                    $prep_cName->execute();
                }
            }catch(LDAPUserExecption $e){
                $fail[] = $l;
            }

        }

        echo "<br><br>INSERTED. FAILED: " . count($fail) . "<br>";
        echo implode("<br>", array_map(function($a){ return $a['username'] . " (" . $a['name'] . ")"; }, $fail));
    }


