<?php
include 'header.php';
permission_check("", "", "", "", "You do not have permission to view items!");
?>

<div class="fluid-row">
	<h3>Theatrical Costumes</h3>
</div>

<div class="fluid-row">
<ul class="nav nav-tabs">
	<li class="active"><a href="#">As Table</a></li>
	<li><a href="ListTheatricalCostumeItemThumb.php">As Thumbnails</a></li>
</ul>

<?php
	displayTable("theatricalCostume", "are no theatrical costumes");
?>	
</div>

<?php
	include 'footer.php';
?>