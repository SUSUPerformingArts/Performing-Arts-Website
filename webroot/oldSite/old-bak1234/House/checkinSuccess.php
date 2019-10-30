<?php
	include 'header.php';
	permission_check("", "PaMember", "", "", "You do not have permission to check in items!");
?>

<?php
	$id = $_GET['id'];
	
	
	$DBConnect = server_connect("Unable to connect to server.");
	database_connect("Unable to connect to database.");

	date_default_timezone_set('UTC');

	$SQLstring = "UPDATE house SET checkStatus='1' WHERE id=$id";
	$QueryResult = query($DBConnect, $SQLstring, "Unable to execute query!");


	$SQLstring = "UPDATE checkout SET dateIn=NOW() WHERE itemID=$id AND dateIn IS NULL";
	$QueryResult = query($DBConnect, $SQLstring, "Unable to execute query.");

	echo "<h3>Check in successful!</h3>";
	echo '<meta http-equiv="refresh" content="2; URL=http://perform.susu.org/House/checkin.php">';
?>

<?php
	include 'footer.php';
?>