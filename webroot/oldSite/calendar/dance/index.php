<?php

    // About page
    $root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";

    \PA\Snippets\Header::printHeader("SUSU Performing Arts - Dance Calendar");

?>

	<div class="well well-pa">
		<h1>Dance Calendar</h1>
	</div>

	<p class="lead">Check out the dance shows happening around the Performing Arts!</p>
	<div class="row">
		<div class="col-md-8 col-md-offset-2 well well-pa">
			<div class="embed-responsive embed-responsive-4by3 hidden-xs">
				<iframe class="embed-responsive-item" src="https://www.google.com/calendar/embed?showTitle=0&amp;showTz=0&amp;height=600&amp;wkst=1&amp;bgcolor=%23ffffff&amp;src=susuperformingarts.org_jgmna537tum1nldn64as6hc57o%40group.calendar.google.com&amp;color=%2323164E&amp;ctz=Europe%2FLondon" width="800" height="600" scrolling="no"></iframe>
			</div>
			<div class="visible-xs-block">
				<iframe style="width: 100%" src="https://www.google.com/calendar/embed?showNav=0&amp;showDate=0&amp;showPrint=0&amp;showTz=0&amp;mode=AGENDA&amp;height=600&amp;wkst=1&amp;bgcolor=%23ffffff&amp;src=susuperformingarts.org_jgmna537tum1nldn64as6hc57o%40group.calendar.google.com&amp;color=%2323164&amp;ctz=Europe%2FLondon" width="800" height="600" scrolling="no"></iframe>
			</div>
		</div>
	</div>


<?php
    \PA\Snippets\Footer::printFooter();
