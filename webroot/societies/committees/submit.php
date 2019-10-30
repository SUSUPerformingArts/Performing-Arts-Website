<?php

error_reporting(-1);
ini_set('display_errors', 'On');

    // About page
    $root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";

    \PA\Snippets\Header::printHeader("Please tell me your committee");



    $society =  isset($_POST["society"]) ? $_POST["society"] : null;
    $positions = isset($_POST["position"]) ? $_POST["position"] : null;
    $names = isset($_POST["name"]) ? $_POST["name"] : null;
    $usernames = isset($_POST["username"]) ? $_POST["username"] : null;
    if(is_null($positions) || is_null($names) || is_null($usernames)){
    	echo "error";
    	exit;
    }


    $db = \PA\Database::getArchiveDatabase();

    $query = "SELECT `id`, `name` FROM `Society`";
    $result = $db->query($query); // Controlled input
    $socs = [];
    while($row = $result->fetch_assoc()){
    	$socs[$row["id"]] = $row["name"];
    }

    $soc;
    if(isset($socs[$society])){
    	$soc = $socs[$society];
    }else{
    	echo "Society not valid";
    	exit;
    }

    /** Loop the post data here ***/
    $members = [];
    foreach ($positions as $key => $position) {
    	$position = trim($position);
    	$name = trim($names[$key]);
    	$username = trim($usernames[$key]);
    	if($position && $name && $username){
    		$members[] = [
    			"position" => $position,
    			"name" => $name,
    			"username" => $username
    		];
    	}
    }

    $j = [
    	"name" => $soc,
    	"members" => $members
    ];


    $dir = @scandir("results", SCANDIR_SORT_DESCENDING);
    $json = null;
    if(isset($dir[0])){
      if (strpos($dir[0], date("Y")) === 0) {
        $json = json_decode(@file_get_contents("results/" . $dir[0]), true);
      }
    }

    if(is_null($json)){
    	$json = [];
    }

    $json[$society] = $j;

    file_put_contents("results/" . date("c") ."_" . str_replace(' ', '-', strtoupper($soc)) . ".json", json_encode($json, JSON_PRETTY_PRINT));

?>

<div class="well well-pa">
	<div class="h2">You have submitted:</div>
	<a href="./">Go back</a>
</div>

<pre>
<?php echo json_encode($members, JSON_PRETTY_PRINT); ?>
</pre>



<?php
    \PA\Snippets\Footer::printFooter();
