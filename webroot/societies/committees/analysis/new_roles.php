<?php



    error_reporting(-1);
    ini_set('display_errors', 'On');

    // About page
    $root = $_SERVER["DOCUMENT_ROOT"];
    require $root . '/compose.php';

    $db = \PA\Database::getArchiveDatabase();

    use \PA\Auth\Auth;


    $mapping = json_decode(file_get_contents('mappings.json'), true);
    $year = 2017;

    // Get positions
    $query = "SELECT * FROM `CommitteePosition`";

    $result = $db->query($query);

    $positions = [];
    while($row = $result->fetch_assoc()){
        $positions[$row['name']] = (int) $row['id'];
    }


    /** Read from the JSON file ***/
    $dir = @scandir("../results", SCANDIR_SORT_DESCENDING);
    $json = null;
    if(isset($dir[0])){
        $json = json_decode(@file_get_contents("../results/" . $dir[0]), true);
    }

    $query = "SELECT * FROM `SocietyMember`
            WHERE `memberId` = ? AND `societyId` = ? AND `committeePositionId` = ? AND `year` = ?";
    $prep = $db->prepare($query);


    $roles = [];
    foreach ($json as $id => $soc) {
        foreach ($soc['members'] as $member) {
            $p = trim($member['position']);
            if(array_key_exists($p, $mapping)){
                $p = $mapping[$p];
            }
            if(array_key_exists($p, $positions)){
                $pos = $positions[$p];
                $user = Auth::checkUserInDatabase($member['username']);
                if($user){
                    $role = [
                        'societyId' => $id,
                        'socName' => $soc['name'],
                        'positionId' => $pos,
                        'positionName' => $p,
                        'username' => trim($member['username']),
                        'userId' => $user['id'],
                        'name' => trim($member['name'])
                    ];

                    $prep->bind_param("iiii",
                        $role['userId'], $role['societyId'], $role['positionId'], $year);
                    $prep->execute();
                    $res = $prep->get_result();

                    if(is_null($res->fetch_assoc())){
                        $roles[] = $role;
                    }
                }
            }
        }
    }

    echo "<pre>";
    var_dump($roles);
    echo "</pre>";


    // Insert at your desire
    $fail = [];
    if(isset($_POST['add'])){
        $query = 'INSERT INTO `SocietyMember` (`memberId`, `societyId`, `committeePositionId`, `year`)
            VALUES (?, ?, ?, ?)';
        $prep = $db->prepare($query);

        foreach ($roles as $role) {
            $prep->bind_param("iiii",
                $role['userId'], $role['societyId'], $role['positionId'], $year);
            $prep->execute();
        }

        echo "<br><br>INSERTED";
    }


