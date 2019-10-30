<?php
include 'header.php';
permission_check("", "", "", "", "You do not have permission to view items!");
?>

<div class="fluid-row">
	<h3>Search Results</h3>
</div>

<div class="fluid-row">
<ul class="nav nav-tabs">
	<li class="active"><a href="#">As Table</a></li>
	<li><a href="searchResultsThumb.php">As Thumbnails</a></li>
</ul>

<?php
	if ((isset($_GET['type'])) && (isset($_GET['keywords']))) {
		$SearchType=$_GET['type'];
		$_SESSION['type']=$SearchType;
		$SearchKeywords=$_GET['keywords'];
		$_SESSION['keywords']=$SearchKeywords;
	}

	$type=$_SESSION['type'];
	$keywords=$_SESSION['keywords'];

	$SQLstringType = "SELECT * FROM `House_Item` WHERE type='$type'";
	
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
		die("<p>There $typeName in the database.</p>");
	
	$numRows = mysql_num_rows($QueryResultType);
	$count = 0;

	echo "<section class='table-wrapper'>";
		echo "<table class='table table-bordered table-condensed cf' border='1'>";
			echo "<thead>";	
				echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Checked Out?</th><th>Added By</th><th>Retired?</th><th>Location</th><th></th></tr>";
			echo "</thead>";
		echo "<tbody>";

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
		
						echo "<tr><td>$itemID</td>";
						echo "<td><a href='/house/itemSearch.php?id=$itemID'>$name</a></td>";

						echo "<td>$description</td>";
						if(!$checkStatus)
							echo "<td>Yes</td>";
						else
							echo "<td>No</td>";
					
						echo "<td>$addedBy</td>";
				
						if($Retired)
							echo "<td>Yes</td>";
						else
							echo "<td>No</td>";
		
						echo "<td>$location</td>";
		
						if ($Retired) {
							echo "<td><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value' style='width:100%'>
									<option value=''></option>
									<option value='/house/unretireitem.php?id=$itemID' style='font-size:1vw'>Un-Retire Item</option>
		 						</select>
							</form></td></tr>";
						}
						elseif ($checkStatus) {
							echo "<td><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value' style='width:100%'>
									<option value=''></option>
	 								<option value='/house/checkout.php?id=$itemID' style='font-size:1vw'>Check-out Item</option>
	 								<option value='/house/setcontainer.php?id=$itemID' style='font-size:1vw'>Set Container</option>
									<option value='/house/retireitem.php?id=$itemID' style='font-size:1vw'>Retire Item</option>
		 						</select>
							</form></td></tr>";
						}
						elseif (!$checkStatus) {
							echo "<td><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value' style='width:100%'>
									<option value=''></option>
 									<option value='/house/checkin.php?id=$itemID' style='font-size:1vw'>Check-in Item</option>
 									<option value='/house/setcontainer.php?id=$itemID' style='font-size:1vw'>Set Container</option>
									<option value='/house/retireitem.php?id=$itemID' style='font-size:1vw'>Retire Item</option>
	 							</select>
							</form></td></tr>";
						}
						else {
							echo "<td><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value' style='width:100%'>
									<option value=''></option>
 									<option value='/house/checkout.php?id=$itemID' style='font-size:1vw'>Check-out Item</option>
 									<option value='/house/checkin.php?id=$itemID' style='font-size:1vw'>Check-in Item</option>
 									<option value='/house/setcontainer.php?id=$itemID' style='font-size:1vw'>Set Container</option>
									<option value='/house/retireitem.php?id=$itemID' style='font-size:1vw'>Retire Item</option>
 								</select>
							</form></td></tr>";
						}	
					}
					$keyword = strtok(";");
				} while($keyword !== false);
		
			$Row = mysql_fetch_assoc($QueryResultType);
			$type = $Row['type'];
			$name = $Row['name'];
			$description = $Row['description'];
			$id = $Row['id'];
			$keyword = strtok($keywords, ";");
			++$count;
		} while ($count<$numRows );

	echo"</tr></tbody></table></section>";	
	
	mysql_free_result($QueryResultType);
	mysql_close($DBConnect);
	echo '</div>';
?>	

<?php
	include 'footer.php';
?>