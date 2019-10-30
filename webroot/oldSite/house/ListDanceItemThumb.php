<?php
include 'header.php';
permission_check("", "", "", "", "You do not have permission to view items!");
?>

<div class="fluid-row">
	<h3>Dance Items</h3>
</div>

<div class="fluid-row">
<ul class="nav nav-tabs">
	<li><a href="ListDanceItem.php">As Table</a></li>
	<li class="active"><a href="#">As Thumbnails</a></li>
</ul>

<?php
	displayThumb("dance", "are no dance items");
?>
<?php
include 'footer.php';
?>