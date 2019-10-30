<?php
include 'header.php';
permission_check("", "PaMember", "producer", "",  "You do not have permission to check out items!");
?>

<?php
	$DBConnect = server_connect("Unable to connect to server!");
	database_connect($DBConnect, "Unable to connect to database!");
	
	$id=$_GET['id'];

	$SQLstring = "UPDATE house SET Retired='1' WHERE id=$id";
	$QueryResult = query($DBConnect, $SQLstring, "Unable to retire item.");

	echo '<h4>Item retired successfully</h4>';
	echo '<meta http-equiv="refresh" content="2; URL=http://perform.susu.org/House/home.php">';
?>

<?php
include 'footer.php';
?>