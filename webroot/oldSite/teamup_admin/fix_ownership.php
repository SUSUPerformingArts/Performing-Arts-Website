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
	
	$qu = "SELECT Society.`teamup_edit_link`, Society.`name` FROM `Society` WHERE `Society`.`type`<5"; 
	$db = \PA\Database::getArchiveDatabase();
	$result = $db->query($qu);
	$societies = array();
	while($row = $result->fetch_assoc()) $societies[$row['name']] = $row['teamup_edit_link'];
	$result->free();
	$db->close();
	
	$client = new GuzzleHttp\Client(array('headers' => array('Teamup-Token' => 'dde6f40ea11a17acc9ce675b19c14c2f1633aae17f4164051ffc7a590f36e105')));
	$allEvents = json_decode($client->get("https://api.teamup.com/ks46c3272ce2da9c0e/events?startDate=1901-01-01&endDate=2500-01-01")->getBody(), true); 
	$allEvents = $allEvents['events'];
	$unownedEvents = array(); 
	foreach($allEvents as $e) $unownedEvents[$e['subcalendar_id'] . $e['id']] = $e;
	
	// 1. FOR(EVENTS) NOT(EVENTS.readonly) => who=$name
	foreach($societies as $name=>$url) {
		$api_url = "https://api." . substr($url, 8) . "/events";
		$events = json_decode($client->get("$api_url?startDate=1901-01-01&endDate=2500-01-01")->getBody(), true);
		$events = $events['events'];
		
		foreach($events as $event) {
			if (! $event["readonly"]) {
				$event["who"] = $name;
				$id = $event["id"];
				$client->put("$api_url/$id", array('json' => $event));
				unset($unownedEvents[$event['subcalendar_id'] . $event['id']]);
			}
		}
	}
	
	
	// 2. IF NO REAL SOC (TYPE < 5) OWNS THE EVENT, AND THE WHO IS A SOC NAME, TAKE OWNERSHIP
	foreach($unownedEvents as $event) {
		// Identify the soc, by who then by [soc] then give up and continue
		$pattern = '/By <a href="https:\/\/perform\.susu\.org\/archive\/societies\/[0-9]*" target="_blank" rel="noreferrer noopener external">.*?<\/a>/';
		preg_match($pattern, $event['notes'], $matches);
		
		if (isset($event['who']) && $event['who'] != "") $name = $event['who'];
		else if (sizeof($matches) > 0) $name = preg_replace('/<.*?>/', "", substr($matches[0], 3));
		else continue;
		
		// Get URL. If not valid soc name then move on
		$apiUrl = false;
		foreach($societies as $socName=>$socUrl) {
			if ($socName == $name) {
				$apiUrl = $socUrl;
				break;
			}
		}
		
		if ($apiUrl == false) continue;
		
		
		// Force the who field to be right
		$event['who'] = $name;
		
		// Move ownership
		$client->delete("https://api.teamup.com/ks46c3272ce2da9c0e/events/" . $event['id'], array('json'=>$event));
		$client->post("https://api." . substr($apiUrl, 8) . "/events", array('json'=>$event));
		
		echo "Event: " . $event['title'] . " is owened by $name with url $apiUrl<br />";
	}
?> 