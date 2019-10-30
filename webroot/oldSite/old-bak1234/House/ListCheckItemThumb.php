<?php
include 'header.php';
permission_check("", "", "", "", "You do not have permission to view items!");
?>

<div class="fluid-row">
	<h3>Items Checked Out</h3>
</div>

<div class="fluid-row">
<ul class="nav nav-tabs">
	<li><a href="ListCheckItem.php">As Table</a></li>
	<li class="active"><a href="#">As Thumbnails</a></li>
</ul>

<?php
	$SQLstring = "SELECT * FROM house WHERE checkStatus='0'";
	
	$DBConnect = server_connect("<p>Unable to connect to server.</p>");
	database_connect("<p>Unable to connect to database.</p>");

	$QueryResult = query($DBConnect, $SQLstring, "Unable to execute query.");

	if (!mysql_num_rows($QueryResult))
		die("<p>There are no checked out items in the database!</p>"); 

	$numRows = mysql_num_rows($QueryResult);
	$count = 0;
	$col = 0;
	
	$Row = mysql_fetch_assoc($QueryResult);
	
	do {
		$id = $Row['id'];
		$name = $Row['name'];
		$description = $Row['description'];
		$checkStatus = $Row['checkStatus'];
		$Retired = $Row['Retired'];
		$addedBy = $Row['added_by'];
		$location = $Row['containerID'];
		$imageURL = $Row['imageURL'];

		$dir = 'http://perform.susu.org/House/';
		$filename = $dir . $imageURL;
		
		echo '<li class="span3" style="list-style-type: none; border-bottom: dotted; border-width: 1px; margin-bottom: 30px">';
		echo "<p style='display: block; height: 200px; width: 200px; margin-left: auto; margin-right:auto'><img src='".$filename."' width='180' height='180'></p>";
		echo "<h4 style='height: 30px; width: 200px'>$name</h4>";

		if($checkStatus && !$Retired)
    		{
        		echo '<span class="label label-success">In House</span>';
    		}
    		elseif(!$checkStatus && !$Retired)
    		{
        		echo '<span class="label label-warning">Checked Out</span>';
    		}
    		elseif($Retired)
    		{
        		echo '<span class="label label-important">Retired</span>';
    		}

		echo '<div style="font-size:0.85em"><p style="height: 130px; width: 250px; word-wrap: break-word">'.$description.'</p></div>';
		echo "<h5 style='height: 30px; width: 100%'>Location: $location</h5>";

		if ($Retired) {
			echo "<td><strong>Action: </strong>
				<form>
					<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value'>
						<option value=''></option>
						<option value='http://localhost/House/unretireitem.php?id=$id'>Un-Retire Item</option>
 						</select>
				</form></td></tr>";
		}
			elseif ($checkStatus) {
			echo "<td><strong>Action: </strong>
				<form>
					<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value'>
						<option value=''></option>
 							<option value='http://localhost/House/checkoutitem.php?id=$id'>Check-out Item</option>
 							<option value='http://localhost/House/setcontainer.php?id=$id'>Set Container</option>
						<option value='http://localhost/House/retireitem.php?id=$id'>Retire Item</option>
 						</select>
				</form></td></tr>";
		}
			elseif (!$checkStatus) {
			echo "<td><strong>Action: </strong>
				<form>
					<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value'>
						<option value=''></option>
 							<option value='http://localhost/House/checkinitem.php?id=$id'>Check-in Item</option>
 							<option value='http://localhost/House/setcontainer.php?id=$id'>Set Container</option>
						<option value='http://localhost/House/retireitem.php?id=$id'>Retire Item</option>
 						</select>
				</form></td></tr>";
		}
			else {
			echo "<td><strong>Action: </strong>
				<form>
					<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value'>
						<option value=''></option>
 							<option value='http://localhost/House/checkoutitem.php?id=$id'>Check-out Item</option>
 							<option value='http://localhost/House/checkinitem.php?id=$id'>Check-in Item</option>
 							<option value='http://localhost/House/setcontainer.php?id=$id'>Set Container</option>
						<option value='http://localhost/House/retireitem.php?id=$id'>Retire Item</option>
 						</select>
				</form></td></tr>";
		}

		$Row = mysql_fetch_assoc($QueryResult);
		++$count;
		
	} while ($count<$numRows);

	mysql_free_result($QueryResult);
	mysql_close($DBConnect);
	echo '</div>';
?>

<?php
include 'footer.php';
?>