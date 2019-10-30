<?php
include 'header.php';
permission_check("", "PaMember", "", "", "You do not have permission to check out items!");

?>

<?php
	$DBConnect = server_connect("Unable to connect to server");
	database_connect("Unable to connect to datatbase");

	$itemid = $_GET['id'];
	date_default_timezone_set('UTC');
	$date = date('Y-m-d');
	$username=$_SESSION['username'];

	$SQLstring = "UPDATE house SET checkStatus=0 WHERE id=$itemid";
	$QueryResult = query($DBConnect, $SQLstring, "Unable to checkout item!");
	
	$SQLstring = "INSERT INTO `checkout` (`checkID`, `itemID`, `username`, `dateOut`, `dateIn`) VALUES(NULL, '$itemid', '$username', '$date', NULL)";
	$QueryResult = query($DBConnect, $SQLstring, "Unable to checkout item");

	echo "<h3>Checkout Successful!</h3>";
?>

<?php
include 'footer.php';
?>