<?php
include 'header.php';

permission_check("", "", "", "", "You do not have permission to view items!");
?>

<div class="fluid-row">
	<h3>Items Checked Out</h3>
</div>

<div class="fluid-row">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#">As Table</a></li>
		<li><a href="ListCheckItemThumb.php">As Thumbnails</a></li>
	</ul>
</div>

<?php
	$SQLstring = "SELECT * FROM house WHERE checkStatus='0'";
	
	$DBConnect = server_connect("Unable to connect to server");
	database_connect("Unable to connect to database");

	$QueryResult = query($DBConnect, $SQLstring, "Unable to select the database.");

	if (!mysql_num_rows($QueryResult))
		die("<p>There are no items in the database!</p>");
	echo "<p>1</p>";
	$numRows = mysql_num_rows($QueryResult);
	$count = 0;
?>
	<section class='table-wrapper'>
		<table class='table table-bordered table-condensed cf' border='1'>
			<thead class="cf">	
				<tr><th>ID</th>
				<th>Name</th>
				<th>Description</th>
				<th>Checked Out?</th>
				<th>Retired?</th>
				<th>Added By</th>
				<th>Location</th>
				<th></th></tr>
			</thead>
			<tbody>
				<?php
				if($numRows > 0) {
					$Row = mysql_fetch_assoc($QueryResult);
					$itemID = $Row['id'];
					do {
						$SQLstring2 = "SELECT * FROM checkout WHERE itemid=$itemID";
						$QueryResult2 = @mysql_query($SQLstring2)
							Or die("Unable to execute query!");

						$RowUser = mysql_fetch_assoc($QueryResult2);
		
						$itemID = $Row['id'];
						$name = $Row['name'];
						$description = $Row['description'];
						$checkStatus = $Row['checkStatus'];
						$Retired = $Row['Retired'];
						$Checkedby = $RowUser['userName'];
						$location = $Row['containerID'];		

						echo "<tr><td>$itemID</td>";
						echo "<td><a href='http://perform.susu.org/House/itemCheck.php?id=$itemID'>$name</a></td>";
						echo "<td class='tableDescription'>$description<a HREF=</td>";
						if(!$checkStatus)
							echo "<td>Yes</td>";
						else
							echo "<td>No</td>";
						echo "<td>$Checkedby</td>";
						if($Retired)
							echo "<td>Yes</td>";
						else
							echo "<td class='tableRetired'>No</td>";
						echo "<td>$location</td>";

						if ($Retired) {
							echo "<td class='tableAction'><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value' style='width:100%'>
								<option value=''></option>
								<option value='http://perform.susu.org/House/unretireitem.php?id=$itemID' style='font-size:1vw'>Un-Retire Item</option>
 								</select>
							</form></td></tr>";
						}
						elseif ($checkStatus) {
							echo "<td><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value' style='width:100%'>
									<option value=''></option>
 									<option value='http://perform.susu.org/House/checkout.php?id=$itemID' style='font-size:1vw'>Check-out Item</option>
 									<option value='http://perform.susu.org/House/setcontainer.php?id=$itemID' style='font-size:1vw'>Set Container</option>
									<option value='http://perform.susu.org/House/retireitem.php?id=$itemID' style='font-size:1vw'>Retire Item</option>
 								</select>
							</form></td></tr>";
						}
						elseif (!$checkStatus) {
							echo "<td><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value' style='width:100%'>
									<option value=''></option>
 									<option value='http://perform.susu.org/House/checkin.php?id=$itemID' style='font-size:1vw'>Check-in Item</option>
 									<option value='http://perform.susu.org/House/setcontainer.php?id=$itemID' style='font-size:1vw'>Set Container</option>
									<option value='http://perform.susu.org/House/retireitem.php?id=$itemID' style='font-size:1vw'>Retire Item</option>
 								</select>
						</form></td></tr>";
						}
						else {
							echo "<td class='tableAction'><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value' style='width:100%'>
									<option value=''></option>
 									<option value='http://perform.susu.org/House/checkout.php?id=$itemID' style='font-size:1vw'>Check-out Item</option>
 									<option value='http://perform.susu.org/House/checkin.php?id=$itemID' style='font-size:1vw'>Check-in Item</option>
 									<option value='http://perform.susu.org/House/setcontainer.php?id=$itemID' style='font-size:1vw'>Set Container</option>
									<option value='http://perform.susu.org/House/retireitem.php?id=$itemID' style='font-size:1vw'>Retire Item</option>
 								</select>
							</form></td></tr>";
						}

						$Row = mysql_fetch_assoc($QueryResult);
						$itemID = $Row['id'];
						++$count;
					} while ($count<$numRows);
				}
				?>
			</tr>
			</tbody>
		</table>
	</section>
<?php
	mysql_free_result($QueryResult);
	mysql_close($DBConnect);
?>	

<?php
	include 'footer.php';
?>