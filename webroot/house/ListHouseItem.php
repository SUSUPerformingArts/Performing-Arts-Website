<?php
include 'header.php';
permission_check("", "", "", "", "You do not have permission to view items!");
?>

<div class="fluid-row">
	<h3>Items in House</h3>
</div>

<div class="fluid-row">
<ul class="nav nav-tabs">
	<li class="active"><a href="#">As Table</a></li>
	<li><a href="ListHouseItemThumb.php">As Thumbnails</a></li>
</ul>
</div>

<?php
	$SQLstring = "SELECT * FROM `House_Item` WHERE checkStatus = '1'";
	
	$DBConnect = server_connect("Unable to connect to server");
	database_connect("Unable to connect to database");

	$QueryResult = query($DBConnect, $SQLstring, "Unable to select the database.");
	
	if (!mysql_num_rows($QueryResult))
		die("<p>There are no items in the database!</p>");
	
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
					$Row = mysql_fetch_assoc($QueryResult);
					do {
						$id = $Row['id'];
						$name = $Row['name'];
						$description = $Row['description'];
						$checkStatus = $Row['checkStatus'];
						$Retired = $Row['Retired'];
						$addedBy = $Row['added_by'];
						$location = $Row['containerID'];		

						echo "<tr><td>$id</td>";
						echo "<td><a href='/house/itemHouse.php?id=$id'>$name</a></td>";
						echo "<td>$description<a HREF=</td>";
						if(!$checkStatus)
							echo "<td>Yes</td>";
						else
							echo "<td>No</td>";
						if($Retired)
							echo "<td>Yes</td>";
						else
							echo "<td>No</td>";
						echo "<td>$addedBy</td>";
						echo "<td>$location</td>";

						if ($Retired) {
							echo "<td><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value' style='width: 100%'>
										<option value=''></option>
										<option value='/house/unretireitem.php?id=$id'>Un-Retire Item</option>
 									</select>
							</form></td></tr>";
						}
						elseif ($checkStatus) {
							echo "<td><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value' style='width: 100%'>
									<option value=''></option>
 									<option value='/house/checkoutitem.php?id=$id'>Check-out Item</option>
 									<option value='/house/setcontainer.php?id=$id'>Set Container</option>
									<option value='/house/retireitem.php?id=$id'>Retire Item</option>
 								</select>
							</form></td></tr>";
						}
						elseif (!$checkStatus) {
							echo "<td><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value' style='width: 100%'>
									<option value=''></option>
 									<option value='/house/checkinitem.php?id=$id'>Check-in Item</option>
 									<option value='/house/setcontainer.php?id=$id'>Set Container</option>
									<option value='/house/retireitem.php?id=$id'>Retire Item</option>
 								</select>
							</form></td></tr>";
						}
						else {
							echo "<td><strong>Action: </strong>
							<form>
								<select name='URL' onchange='window.location.href=this.form.URL.options[this.form.URL.selectedIndex].value' style='width: 100%'>
									<option value=''></option>
 									<option value='/house/checkoutitem.php?id=$id'>Check-out Item</option>
 									<option value='/house/checkinitem.php?id=$id'>Check-in Item</option>
 									<option value='/house/setcontainer.php?id=$id'>Set Container</option>
									<option value='/house/retireitem.php?id=$id'>Retire Item</option>
 								</select>
							</form></td></tr>";
						}
						$Row = mysql_fetch_assoc($QueryResult);
						++$count;
					} while ($count<$numRows);
				?>
			</tr>
			</tbody>
		</table>
	</section>

<?php
	mysql_free_result($QueryResult);
	mysql_close($DBConnect);
?>	
</div>

<?php
	include 'footer.php';
?>