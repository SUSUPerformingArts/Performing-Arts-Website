<?php
include 'header.php';
permission_check("", "", "", "", "You do not have permission to view items!");
?>

<div class="fluid-row">
	<h3>Search Results</h3>
</div>

<div class="fluid-row">
<ul class="nav nav-tabs">
	<li><a href="searchResults.php">As Table</a></li>
	<li class="active"><a href="#">As Thumbnails</a></li>
</ul>

<?php
	$type=$_SESSION['type'];
	$keywords=$_SESSION['keywords'];

	$SQLstringType = "SELECT * FROM house WHERE type='$type'";
	
	$DBConnect = server_connect("Unable to connect to server");
	database_connect("Unable to connect to database");

	$QueryResultType = query($DBConnect, $SQLstringType, "Unable to execute query.");

	if ($type == "dance")
		$typeName = "are no dance items";
	elseif ($type == "music")
		$typeName = "are no music items";
	elseif ($type == "misc")
		$typeName = "are no miscellaneous items";
	elseif ($type == "drapes")
		$typeName = "are no drapes";
	elseif ($type == "theatricalProp")
		$typeName = "are no theatrical props";
	elseif ($type == "theatricalSet")
		$typeName = "is no theatrical set";
	elseif ($type == "theatricalCostume")
		$typeName = "are no theatrical costumes";

	if (mysql_num_rows($QueryResultType) == 0)
		die("<p>There $typeName in the database!</p>"); 

	$numRows = mysql_num_rows($QueryResultType);
	$count = 0;
	$col = 0;
	
			$Row = mysql_fetch_assoc($QueryResultType);
			$type = $Row['type'];
			$name = $Row['name'];
			$description = $Row['description'];
			$id = $Row['id'];
			$keyword = strtok($keywords, ";");
	
			do {
				do {
					if ((strpos($description, $keyword) !== false) || (strpos($name, $keyword) !== false)) {
				
						$itemID = $Row['id'];
						$name = $Row['name'];
						$description = $Row['description'];
						$checkStatus = $Row['checkStatus'];
						$Retired = $Row['Retired'];
						$addedBy = $Row['added_by'];
						$containerID = $Row['containerID'];
						$imageURL = $Row['imageURL'];

						$dir = 'http://perform.susu.org/House/';
						$filename = $dir . $imageURL;
		
						echo '<li class="span3" style="list-style-type: none">';
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

						if ($Retired) {
							echo "<td><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value'>
									<option value=''></option>
									<option value='http://perform.susu.org/House/unretireitem.php?id=$id'>Un-Retire Item</option>
 								</select>
							</form></td></tr>";
						}
						elseif ($checkStatus) {
							echo "<td><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value'>
									<option value=''></option>
 									<option value='http://perform.susu.org/House/checkoutitem.php?id=$id'>Check-out Item</option>
 									<option value='http://perform.susu.org/House/setcontainer.php?id=$id'>Set Container</option>
									<option value='http://perform.susu.org/House/retireitem.php?id=$id'>Retire Item</option>
 								</select>
							</form></td></tr>";
						}
						elseif (!$checkStatus) {
							echo "<td><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value'>
									<option value=''></option>
 									<option value='http://perform.susu.org/House/checkinitem.php?id=$id'>Check-in Item</option>
 									<option value='http://perform.susu.org/House/setcontainer.php?id=$id'>Set Container</option>
									<option value='http://perform.susu.org/House/retireitem.php?id=$id'>Retire Item</option>
 								</select>
							</form></td></tr>";
						}
						else {
							echo "<td><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value'>
									<option value=''></option>
 									<option value='http://perform.susu.org/House/checkoutitem.php?id=$id'>Check-out Item</option>
 									<option value='http://perform.susu.org/House/checkinitem.php?id=$id'>Check-in Item</option>
 									<option value='http://perform.susu.org/House/setcontainer.php?id=$id'>Set Container</option>
									<option value='http://perform.susu.org/House/retireitem.php?id=$id'>Retire Item</option>
 								</select>
							</form></td></tr>";
						}
					}
					$keyword = strtok(";");
				} while ($keyword != false);

			$Row = mysql_fetch_assoc($QueryResultType);
			$type = $Row['type'];
			$name = $Row['name'];
			$description = $Row['description'];
			$id = $Row['id'];
			$keyword = strtok($keywords, ";");
			++$count;
		} while ($count<$numRows);

	mysql_free_result($QueryResultType);
	mysql_close($DBConnect);
?>

</div>

<?php
	include 'footer.php';
?>