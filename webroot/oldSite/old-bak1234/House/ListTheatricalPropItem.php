<?php
include 'header.php';
permission_check("", "", "", "", "You do not have permission to view items!");
?>

<div class="fluid-row">
	<h3>Theatrical Props</h3>
</div>

<div class="fluid-row">
<ul class="nav nav-tabs">
	<li class="active"><a href="#">As Table</a></li>
	<li><a href="ListTheatricalPropItemThumb.php">As Thumbnails</a></li>
</ul>

<?php
	displayTable("theatricalProp", "are no theatrical props");
?>	
</div>

<?php
	include 'footer.php';
?>