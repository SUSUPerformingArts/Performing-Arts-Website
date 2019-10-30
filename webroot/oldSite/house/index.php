
<?php
	include 'header.php';
	include 'browser.php';
	include 'mdetect.php';

	if((isset($_SESSION['username']))&&($_SESSION['loggedin'])) {
		$mdetect = new mdetect();
		if ($mdetect->isTierIphone) {
			echo "<p>You are logged in as ".$_SESSION['username']." on a mobile device.</p>";
		}
		else {
			$ua=getBrowser();
			echo "<p>You are logged in as ".$_SESSION['username']." on ".$ua['name']."</p>";
		}
	}
?>

<div class="row-fluid">
	<div class="span2">
		<h3>Items:</h3>
	</div>
        <div class="span3">
		<p><a href="addItem.php" class="btn btn-large btn-block btn-primary" type="button">Add Item</a>
	</div>
	<div class="span3">
		<p><a href="searchForm.php" class="btn btn-large btn-block btn-primary" type="button">Search for Items</a>
	</div>
</div>
<hr>
<div class="row-fluid">
	<div class="span25">
		<h3>Lists:</h3>
	</div>
	<div class="span3">
		<?php
			if($_SESSION['mobile'] == "true")
				echo '<p><a href="ListAllItemThumb.php" class="btn btn-large btn-block btn-warning" type="button">List All Items</a></p>';
			else
				echo '<p><a href="ListAllItem.php" class="btn btn-large btn-block btn-warning" type="button">List All Items</a></p>';
		?>
	</div>
	<div class="span3">
		<?php
			if($_SESSION['mobile'] == "true")
				echo '<p><a href="ListHouseItemThumb.php" class="btn btn-large btn-block btn-warning" type="button">List Items in House</a></p>';
			else
				echo '<p><a href="ListHouseItem.php" class="btn btn-large btn-block btn-warning" type="button">List Items in House</a></p>';
		?>
	</div>
	<div class="span3">
		<?php
			if($_SESSION['mobile'] == "true")
				echo '<p><a href="ListCheckItemThumb.php" class="btn btn-large btn-block btn-warning" type="button">List Items Checked Out</a></p>';
			else
				echo '<p><a href="ListCheckItem.php" class="btn btn-large btn-block btn-warning" type="button">List Items Checked Out</a></p>';
		?>
	</div>
	<div class="span3">
		<?php
			if($_SESSION['mobile'] == "true")
				echo '<p><a href="ListTheatricalSetItemThumb.php" class="btn btn-large btn-block btn-inverse" type="button">List Theatrical Set</a></p>';
			else
				echo '<p><a href="ListTheatricalSetItem.php" class="btn btn-large btn-block btn-inverse" type="button">List Theatrical Set</a></p>';
		?>
	</div>
	<div class="span3">
		<?php
			if($_SESSION['mobile'] == "true")
				echo '<p><a href="ListTheatricalPropItemThumb.php" class="btn btn-large btn-block btn-inverse" type="button">List Theatrical Props</a></p>';
			else
				echo '<p><a href="ListTheatricalPropItem.php" class="btn btn-large btn-block btn-inverse" type="button">List Theatrical Props</a></p>';
		?>
	</div>
	<div class="span3">
		<?php
			if($_SESSION['mobile'] == "true")
				echo '<p><a href="ListTheatricalCostumeItemThumb.php" class="btn btn-large btn-block btn-inverse" type="button">List Theatrical Costumes</a></p>';
			else
				echo '<p><a href="ListTheatricalCostumeItem.php" class="btn btn-large btn-block btn-inverse" type="button">List Theatrical Costumes</a></p>';
		?>
	</div>
	<div class="span3">
		<?php
			if($_SESSION['mobile'] == "true")
				echo '<p><a href="ListDrapesItemThumb.php" class="btn btn-large btn-block btn-inverse" type="button">List Drapes</a></p>';
			else
				echo '<p><a href="ListDrapesItem.php" class="btn btn-large btn-block btn-inverse" type="button">List Drapes</a></p>';
		?>
	</div>
	<div class="span3">
		<?php
			if($_SESSION['mobile'] == "true")
				echo '<p><a href="ListDanceItemThumb.php" class="btn btn-large btn-block btn-inverse" type="button">List Dance Items</a></p>';
			else
				echo '<p><a href="ListDanceItem.php" class="btn btn-large btn-block btn-inverse" type="button">List Dance Items</a></p>';
		?>
	</div>
	<div class="span3">
		<?php
			if($_SESSION['mobile'] == "true")
				echo '<p><a href="ListMusicItemThumb.php" class="btn btn-large btn-block btn-inverse" type="button">List Music Items</a></p>';
			else
				echo '<p><a href="ListMusicItem.php" class="btn btn-large btn-block btn-inverse" type="button">List Music Items</a></p>';
		?>
	</div>
	<div class="span3">
		<?php
			if($_SESSION['mobile'] == "true")
				echo '<p><a href="ListMiscItemThumb.php" class="btn btn-large btn-block btn-inverse" type="button">List Miscellaneous Items</a></p>';
			else
				echo '<p><a href="ListMiscItem.php" class="btn btn-large btn-block btn-inverse" type="button">List Miscellaneous Items</a></p>';
		?>
	</div>
</div>
<hr>
<div class="row-fluid">
	<div class="span2">
		<h3>Checkin:</h3>
	</div>
	<div class="span3">
		<p><a href="checkin.php" class="btn btn-large btn-block btn-success" type="button">Check in</a></p>
	</div>
</div>

<hr>
<div class="row-fluid">
	<div class="span2">
		<h3></h3>
	</div>
	<div class="span3">
		<p><a href="Login.php" class="btn btn-large btn-block btn-success" type="button">Login</a></p>
	</div>
	<div class="span3">
		<p><a href="Logout.php" class="btn btn-large btn-block btn-danger" type="button">Logout</a></p>
	</div>
	<div class="span3">
		<?php
			if((isset($_SESSION['loggedin'])) && ($_SESSION['loggedin'] == 1) && ((($_SESSION['username'] == "perform") && ($_SESSION['password'] == "Pur1fy#")) || (($_SESSION['username'] == "Facilities") && ($_SESSION['password'] == "sambucatequila")))) {
				echo '<p><a href="createUser.php" class="btn btn-large btn-block btn-primary" type="button">Create a New User</a></p>';
			}
		?>
	</div>
</div>

<?php
include 'footer.php';
?>