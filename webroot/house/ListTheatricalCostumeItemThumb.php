<?php
include 'header.php';
permission_check("", "", "", "", "You do not have permission to view items!");
?>

<div class="fluid-row">
	<h3>Theatrical Costumes</h3>
</div>

<div class="fluid-row">
<ul class="nav nav-tabs">
	<li><a href="ListTheatricalCostumeItem.php">As Table</a></li>
	<li class="active"><a href="#">As Thumbnails</a></li>
</ul>

<?php
	displayThumb("theatricalCostume", "are no theatrical costumes");
?>
<?php
include 'footer.php';
?>