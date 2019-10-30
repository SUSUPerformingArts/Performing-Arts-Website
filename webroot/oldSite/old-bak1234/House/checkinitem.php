<?php
	include 'header.php';
	permission_check("", "PaMember", "", "", "You do not have permission to check in items!");
?>

<div class="fluid-row">
	<h3>Items you have Checked Out</h3>
</div>

<div class="fluid-row">

<?php
	$DBConnect = server_connect("Unable to connect to server.");
	database_connect("Unable to connect to database.");

	$username=$_SESSION['username'];

	$SQLstring = "SELECT itemID FROM checkout WHERE userName='$username' AND dateIn is NULL";
	$QueryResult = query($DBConnect, $SQLstring, "You have no checked out items!");

	if (mysql_num_rows($QueryResult) == 0)
		die("<p>You have not checked any items out!</p>");

	$numRows = mysql_num_rows($QueryResult);
	$count = 0;

	echo "<table class='table' border='1'>";
	echo "<tr><th class='tableIDTitle'>ID</th>
		<th class='tableNameTitle'>Name</th>
		<th class='tableDescriptionTitle'>Description</th>
		<th class='tableCheckTitle'>Checked Out?</th>
		<th class='tableRetiredTitle'>Retired?</th>
		<th class='tableAddedTitle'>Added By</th>
		<th class='tableContainerTitle'>Container ID</th>
		<th class='tableAction'>Check in</th></tr>";

	$RowID = mysql_fetch_assoc($QueryResult);
	$itemID = $RowID['itemID'];
	
	do {
		$SQLstring2 = "SELECT * FROM house WHERE id=$itemID";
		$QueryResult2 = query($DBConnect, $SQLstring2, "Unable to execute query!");

		$Row = mysql_fetch_assoc($QueryResult2);

		$id = $Row['id'];
		$name = $Row['name'];
		$description = $Row['description'];
		$checkStatus = $Row['checkStatus'];
		$Retired = $Row['Retired'];
		$addedBy = $Row['added_by'];
		$containerID = $Row['containerID'];		

		echo "<tr><td class='tableID'>$id</td>";
		echo "<td class='tableName'><a href='http://localhost/House/item.php?id=$id'>$name</a></td>";
		echo "<td class='tableDescription'>$description<a HREF=</td>";
		if(!$checkStatus)
			echo "<td class='tableCheck'>Yes</td>";
		else
			echo "<td class='tableCheck'>No</td>";
		if($Retired)
			echo "<td class='tableRetired'>Yes</td>";
		else
			echo "<td class='tableRetired'>No</td>";
		echo "<td class='tableAdded'>$addedBy</td>";
		echo "<td class='tableContainer'>$containerID</td>";

		echo "<td class='tableAction'><a href='http://perform.susu.org/House/checkinSuccess.php?id=$id'>Check in</a></td></tr>";


		$RowID = mysql_fetch_assoc($QueryResult);
		$itemID = $RowID['itemID'];
		++$count;
	} while ($count<$numRows);
	mysql_free_result($QueryResult);
	mysql_close($DBConnect);
?>
</div>	

<?php
	include 'footer.php';
?>	