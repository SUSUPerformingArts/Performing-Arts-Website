<?php
$area = "Societies";
include 'header.php';

$id = $_GET["id"];

	$query="SELECT * FROM pa_societies WHERE id=$id";
	include "database-connect.php";
	$name = mysql_result($result,$i,"name");
	$subtitle = mysql_result($result,$i,"subtitle");
	$description = mysql_result($result,$i,"description");
	$website = mysql_result($result,$i,"website");
	$facebook = mysql_result($result,$i,"facebook");
	$twitter = mysql_result($result,$i,"twitter");

echo '<div class="row-fluid">';
echo '<div class="well">';
echo '<h1>'.$name.'</h1>';
echo '<h2>'.$subtitle.'</h2>';
echo '</div>';
echo '</div>';



include 'footer.php';
?>