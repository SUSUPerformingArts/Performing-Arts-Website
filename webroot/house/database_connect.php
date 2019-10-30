<?php
function server_connect($errmsg) {
	$DBConnect = @mysql_connect("localhost","perform","Pur1fy#") 
		Or die("<p>$errmsg</p>");
	return $DBConnect;
}

function database_connect($errmsg) {
	@mysql_select_db("perform_site")
		Or die("<p>$errmsg</p>");
}

function permission_check($permission1, $permission2, $permission3, $permission4, $errmsg) {
	$DBConnect = server_connect("Unable to connect to server");
	database_connect("Unable to connect to database");
	if(isset($_SESSION['username'])) {
		$username = $_SESSION['username'];
		$SQLstring = "SELECT * FROM `House_User` WHERE username='$username'";
		$QueryResult = @mysql_query($SQLstring)
			Or die("<p>Unable to execute the query.</p>");
	
		$Row = mysql_fetch_assoc($QueryResult);
	
		if(($Row['permissions'] == $permission1) || ($Row['permissions'] == $permission2) || ($Row['permissions'] == $permission3) ||($Row['permissions'] == $permission4)) {
			die("<h3>$errmsg</h3>");
			//echo '<meta http-equiv="refresh" content="3; URL=/house">';
		}
	}
	else {
		die("<h3>You are not logged in! Please login to view or add items.</h3>");
		//echo '<meta http-equiv="refresh" content="3; URL=/house">';
	}
	
}

function query($DBConnect, $SQLstring, $errmsg) {
	$QueryResult = @mysql_query($SQLstring)
		Or die("<p>$errmsg</p>"
		. "<p>Error code " . mysql_errno($DBConnect)
		. ": " . mysql_error($DBConnect)) . "</p>";
	return $QueryResult;
}

function displayTable ($type, $errmsg) {
	$SQLstring = "SELECT * FROM `House_Item` WHERE type='$type'";
	
	$DBConnect = server_connect("Unable to connect to server");
	database_connect("Unable to connect to database");

	$QueryResult = query($DBConnect,$SQLstring, "Unable to select the database.");
	
	if (mysql_num_rows($QueryResult) == 0)
		die("<p>There $errmsg in the database!</p>");
	
	$numRows = mysql_num_rows($QueryResult);
	$count = 0;

	echo "<section class='table-wrapper'>";
		echo "<table class='table table-bordered table-condensed cf' border='1'>";
			echo "<thead>";	
					echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Checked Out?</th><th>Retired?</th><th>Added By</th><th>Location</th><th></th></tr>";
			echo "</thead>";
			echo "<tbody>";
				
					$Row = mysql_fetch_assoc($QueryResult);
					$itemID = $Row['id'];
					do {		
						$itemID = $Row['id'];
						$name = $Row['name'];
						$description = $Row['description'];
						$checkStatus = $Row['checkStatus'];
						$Retired = $Row['Retired'];
						$addedBy = $Row['added_by'];
						$location = $Row['containerID'];		

						echo "<tr><td>$itemID</td>";
						if($type == "theatricalCostume")
							echo "<td><a href='/house/itemCostume.php?id=$itemID&type=$type'>$name</a></td>";
						elseif($type == "theatricalProp")
							echo "<td><a href='/house/itemProp.php?id=$itemID&type=$type'>$name</a></td>";
						elseif($type == "theatricalSet")
							echo "<td><a href='/house/itemSet.php?id=$itemID&type=$type'>$name</a></td>";
						elseif($type == "drapes")
							echo "<td><a href='/house/itemDrapes.php?id=$itemID&type=$type'>$name</a></td>";
						elseif($type == "music")
							echo "<td><a href='/house/itemMusic.php?id=$itemID&type=$type'>$name</a></td>";
						elseif($type == "dance")
							echo "<td><a href='/house/itemDance.php?id=$itemID&type=$type'>$name</a></td>";
						elseif($type == "misc")
							echo "<td><a href='/house/itemMisc.php?id=$itemID&type=$type'>$name</a></td>";

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

						$Row = mysql_fetch_assoc($QueryResult);
						$itemID = $Row['id'];
						++$count;
					} while ($count<$numRows);
				
echo"			</tr>
			</tbody>
		</table>
	</section>
";

	mysql_free_result($QueryResult);
	mysql_close($DBConnect);

}

function displayThumb($type, $errmsg) {
	$SQLstring = "SELECT * FROM `House_Item` WHERE type='$type'";
	
	$DBConnect = server_connect("Unable to connect to server. Check you are still logged in.");
	database_connect("Unable to connect to database. Check connection.");

	$QueryResult = query($DBConnect, $SQLstring, "Unable to execute query.");

	if (!mysql_num_rows($QueryResult))
		die("<p>There $errmsg in the database!</p>"); 

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

		$dir = '/house/';
		$filename = $dir . $imageURL;
		
		echo '<li class="span3" style="list-style-type: none; border-bottom: dotted; border-width: 1px; margin-bottom: 30px">';
		echo "<p style='display: block; height: 150px; width: 200px; margin-left: auto; margin-right:auto'><img src='".$filename."' width='180' height='180' orientation: 90deg></p>";
		echo "<h4 style='height: 30px; width: 100%'>$name</h4>";

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

		echo '<div style="font-size:0.85em"><p style="height: 60px; width: 250px; word-wrap: break-word">'.$description.'</p></div>';
		echo "<h5 style='height: 30px; width: 100%'>Location: $location</h5>";

		if ($Retired) {
			echo "<td><h5>Action: </h5>
				<form>
					<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value'>
						<option value=''></option>
						<option value='/house/item.php?id=$id&type=$type'>Un-Retire Item</option>
 					</select>
				</form></td></tr>";
		}
		elseif ($checkStatus) {
			echo "<td><h5>Action: </h5>
				<form>
					<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value'>
						<option value=''></option>
 						<option value='/house/checkoutitem.php?id=$id'>Check-out Item</option>
 						<option value='/house/setcontainer.php?id=$id'>Set Container</option>
						<option value='/house/retireitem.php?id=$id'>Retire Item</option>
 					</select>
				</form></td></tr>";
		}
		elseif (!$checkStatus) {
			echo "<td><h5>Action: </h5>
				<form>
					<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value'>
						<option value=''></option>
 						<option value='/house/checkinitem.php?id=$id'>Check-in Item</option>
 						<option value='/house/setcontainer.php?id=$id'>Set Container</option>
						<option value='/house/retireitem.php?id=$id'>Retire Item</option>
 					</select>
				</form></td></tr>";
		}
		else {
			echo "<td><h5>Action: </h5>
				<form>
					<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value'>
						<option value=''></option>
 						<option value='/house/checkoutitem.php?id=$id'>Check-out Item</option>
 						<option value='/house/checkinitem.php?id=$id'>Check-in Item</option>
 						<option value='/house/setcontainer.php?id=$id'>Set Container</option>
						<option value='/house/retireitem.php?id=$id'>Retire Item</option>
 					</select>
				</form></td></tr>";
		}
		echo '<br /></div></li>';
		$Row = mysql_fetch_assoc($QueryResult);
		++$count;
		
	} while ($count<$numRows);

	mysql_free_result($QueryResult);
	mysql_close($DBConnect);
	echo '</div>';
}
?>