<?php
exit; //kill
error_reporting(-1);
ini_set('display_errors', 'On');

require "../compose.php";
require "calendar.php";


if(isset($_POST["name"])){
	$arr = [
		'summary' => $_POST["name"],
		'location' => 'The Annex Theatre, Southampton',
		'start' => array(
			'dateTime' => date("c"),
		),
		'end' => array(
			'dateTime' => date("c", strtotime("+2 hours")),
		),
	];

	$event = PA_Google_Cal::createEvent(0, $arr);

	echo "Event created!<br>";
	echo "Click <a href='" . $event->htmlLink . "' target='_blank'>here</a>";
}else{

?>

<form action="" method="post">
<input type="text" name="name" placeholder="Event Name">
<button type="submit">Create Event</button>
</form>

<?php
}
?>
