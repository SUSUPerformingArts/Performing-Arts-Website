<?php
include 'header.php';
permission_check("", "PaMember", "producer", "",  "You do not have permission to check out items!");
?>

<?php
	$DBConnect = server_connect("Unable to connect to server!");
	database_connect("Unable to connect to database!");
	
	$id=$_GET['id'];

	$SQLstring = "UPDATE house SET Retired='0' WHERE id=$id";
	$QueryResult = query($DBConnect, $SQLstring, "Unable to unretire item.");

	echo '<h4>Item unretired successfully</h4>';
	echo '<meta http-equiv="refresh" content="2; URL=/house">';
?>

<?php
include 'footer.php';
?>