<?php
error_reporting(-1);
ini_set('display_errors', 'On');

    use \PA\Ldap\LdapUser;

    // Another test file
    $root = $_SERVER["DOCUMENT_ROOT"];
    require $root . "/compose.php";

    \PA\Snippets\Header::printHeader("SUSU Performing Arts - Home");

    $uname;
    $pw;
    $datahere = false;

    if(isset($_POST["username"]) && $_POST["username"] !== ""){
    	if(isset($_POST["password"]) && $_POST["password"] === "pa is great"){
	        $uname = $_POST["username"];
	        $datahere = true;
	    }
    }
?>

<form action="" method="POST">
  <div class="form-group">
    <label for="username">University Username</label>
    <input type="text" name="username" class="form-control" id="username" placeholder="Username" <?php if(isset($_POST["username"])){ echo "value=\"". $_POST["username"] . "\""; } ?>>
  </div>
  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" name="password" class="form-control" id="password" placeholder="Password">
  </div>
  <button type="submit" class="btn btn-default">Submit</button>
</form>


<?php


    function invalid(){
        ?>
        <div class="alert alert-danger" role="alert">Invalid Username or Password</div>
        <?php
    }

    function valid($user){
        echo "<pre>";
        var_dump($user->getAllData());
        echo "</pre>";
    }

    if($datahere){
        $user = new LdapUser($uname);

        if(!$user->exists()){
            invalid();
        }else{
            valid($user);
        }
    }

?>


<?php
    \PA\Snippets\Footer::printFooter();
