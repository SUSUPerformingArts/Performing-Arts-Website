<?php
include 'header.php';
permission_check("", "", "", "", "You do not have permission to view items!");
?>

<div class="fluid-row">
	<h3>Dance Items</h3>
</div>

<div class="fluid-row">
<ul class="nav nav-tabs">
	<li class="active"><a href="#">As Table</a></li>
	<li><a href="ListDanceItemThumb.php">As Thumbnails</a></li>
</ul>

<?php
	displayTable("dance", "are no dance items");
?>	
</div>

<?php
	include 'footer.php';
?>