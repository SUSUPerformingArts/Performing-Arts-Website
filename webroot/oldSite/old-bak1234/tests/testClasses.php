<?php
require '../php/classes.php';

echo "<h1>Begin Test</h1>";

$query = "SELECT * FROM pa_societies";
include "../database-connect.php";
$obj = mysql_fetch_object($result, $Society);
$obj::echoName();
var_dump($obj);


?>

