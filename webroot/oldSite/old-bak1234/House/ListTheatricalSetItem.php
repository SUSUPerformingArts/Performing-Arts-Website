<?php
	include 'header.php';
	permission_check("", "", "", "", "You do not have permission to view items!");
?>

<div class="fluid-row">
	<h3>Theatrical Set</h3>
</div>

<div class="fluid-row">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#">As Table</a></li>
		<li><a href="ListTheatricalSetItemThumb.php">As Thumbnails</a></li>
	</ul>

	<?php
		displayTable("theatricalSet", "is no theatrical set");
	?>	
</div>

<?php
	include 'footer.php';
?>