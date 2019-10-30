<?php
include 'header.php';
permission_check("", "", "", "", "You do not have permission to view items!");
?>

<div class="fluid-row">
	<h3>Theatrical Props</h3>
</div>

<div class="fluid-row">
<ul class="nav nav-tabs">
	<li><a href="ListTheatricalPropItem.php">As Table</a></li>
	<li class="active"><a href="#">As Thumbnails</a></li>
</ul>

<?php
	displayThumb("theatricalProp", "are no theatrical props");
?>
<?php
include 'footer.php';
?>