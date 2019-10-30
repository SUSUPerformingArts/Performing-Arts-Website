<?php
include 'header.php';
?>

<?php
$id=$_GET['id'];

$SQLstring = "SELECT * FROM `House_Item` WHERE id=$id";
$DBConnect = server_connect("Unable to connect to server!");
database_connect("Unable to connect to database!");
$QueryResult = query($DBConnect, $SQLstring, "Unable to execute query!");


$Row = mysql_fetch_assoc($QueryResult);
$name = $Row['name'];
$description = $Row['description'];
$location = $Row['containerID'];
$imageURL = $Row['imageURL'];
$dir = '/house/';
$filename = $dir . $imageURL;


echo "<h3 style='display: block; text-align: center'>$name</h3>";
echo "<p><img src='".$filename."' width='380' height='380' style='display: block; margin-left: auto; margin-right:auto'></p>";
echo "<h5 style='display: block; text-align: center'>$description</h5>";
echo "<h5 style='height: 30px; width: 100%'>Location: $location</h5>";
?>

<form action="ListMiscItem.php">
	<input type="submit" value="Back">
</form>

<?php
include 'footer.php';
?>