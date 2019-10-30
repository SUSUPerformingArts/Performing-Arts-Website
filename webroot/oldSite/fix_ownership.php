<?php
	/*
		teamup calendar is great, but has no really good way for distinguishing between
		the socs. This does some fixes:
			1. All events made by a soc have that socs name as the who field
			2. All events with a soc name as the who field are made by that soc
		
		This isn't perfect and requires some accurate typing when people add events and,
		more importantly, that they use the same names. But that's just the way it goes 
		I guess and I can't be bothered to do the whole calendar hosted on the PA server
		so this is what we have for now... // Simeon Brooks 2016
	*/
	
	$root = $_SERVER['DOCUMENT_ROOT'];
	require_once $root . "/compose.php";
	
	$qu = "SELECT socs.`teampup_edit_link`, socs.`name` FROM `Society` socs"; 
			
	$db = \PA\Database::getArchiveDatabase();
	$result = $db->query($qu); // No input
	?><table><?
	while($row = $result->fetch_assoc()){
		$url = $row['teampup_edit_link'];
		$name = $row['name'];
		?><tr><td><? echo $name; ?></td><td><? echo $url; ?></td></tr><?
		
	}
	?></table><?
	$result->free();
	$db->close();
	
	
	
	
	
?>