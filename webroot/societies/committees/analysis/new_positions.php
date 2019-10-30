<?php



    error_reporting(-1);
    ini_set('display_errors', 'On');

    // About page
    $root = $_SERVER["DOCUMENT_ROOT"];
    require $root . '/compose.php';

    $db = \PA\Database::getArchiveDatabase();

    use \PA\Auth\Auth;

    $mapping = json_decode(file_get_contents('mappings.json'), true);;



    /** Read from the JSON file ***/
    $dir = @scandir("../results", SCANDIR_SORT_DESCENDING);
    $json = null;
    if(isset($dir[0])){
        $json = json_decode(@file_get_contents("../results/" . $dir[0]), true);
    }
    $positions = [];

    foreach ($json as $soc) {
        $b = array_map(function($a) use ($mapping) {
                $p = trim($a['position']);
                if(array_key_exists($p, $mapping)){
                    $p = $mapping[$p];
                }
                return $p;
            }, $soc['members']);
        $positions = array_merge($positions, $b);
    }
    $positions = array_unique($positions);


    /** Get all the socs **/
    $query = "SELECT * FROM CommitteePosition
            WHERE `name` = ?";

    $prep = $db->prepare($query);

    $left = [];
    foreach ($positions as $position) {
        $prep->bind_param("s", $position);
        $prep->execute();
        $result = $prep->get_result()->fetch_assoc();
        if(is_null($result)){
            $left[] = $position;
        }
    }
    sort($left);
    echo implode("<br>", $left);

    echo "<br>Count: " . count($left);



    // Insert at your desire
    if(isset($_POST['add'])){
        $query = "INSERT INTO CommitteePosition (`name`)
                VALUES (?)";

        $prep = $db->prepare($query);

        foreach ($left as $l) {
            $prep->bind_param("s", $l);
            $prep->execute();
        }
        echo "<br><br>INSERTED";
    }
