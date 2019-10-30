<?php

	// About page
	$root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";

    \PA\Snippets\Header::printHeader("SUSU Performing Arts - All committees");

?>

	<div class="well well-pa">
		<h1>All Performing Arts Committees</h1>
	</div>

	<?php
		include("../committee.php");
		echoCommitteesTable();
	?>




<?php
	\PA\Snippets\Footer::printFooter();
