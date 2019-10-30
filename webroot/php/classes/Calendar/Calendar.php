<?php

namespace PA\Calendar;

$root = $_SERVER["DOCUMENT_ROOT"];
require_once $root . "/compose.php";

if (!class_exists("Calendar")) {
	class Calendar {

		public static function escapeString($string) {
		  return str_replace("&", "and", str_replace("\n", "\\n", preg_replace('/([\,;])/','\\\$1', $string)));
		}

		public static function getSocJsonFeed($soc, $colour="black") {
			$parser = new \PA\ical\CalFileParser();
			$cal = $parser->parseString(self::getSocIcalFeed($soc));
			$events = array();
			
			foreach ($cal as $event) {
				if (isset($event['dtend;tzid=europe/london'])) {
					$s = $event['dtstart;tzid=europe/london'];
					$s = substr($s, 0, 4) . "-" . substr($s, 4, 2) . "-" . substr($s, 6, 5) . ":" . substr($s, 11, 2) . ":" . substr($s, 13, 2);
					$e = $event['dtend;tzid=europe/london'];
					$e = substr($e, 0, 4) . "-" . substr($e, 4, 2) . "-" . substr($e, 6, 5) . ":" . substr($e, 11, 2) . ":" . substr($e, 13, 2);
					$allDay = false;
				} else if (isset($event['dtend;value=date'])) {
					$s = $event['dtstart;value=date'];
					$s = substr($s, 0, 4) . "-" . substr($s, 4, 2) . "-" . substr($s, 6, 2);
					$e = $event['dtend;value=date'];
					$e = substr($e, 0, 4) . "-" . substr($e, 4, 2) . "-" . substr($e, 6, 2);
					$allDay = true;
				} else {
					continue;
				}
				
				$location = $event['location'] == "" ? "Southampton" : $event['location'];
				
				$t = $event['x-teamup-who'] . ": " . $event['summary'];
				$event = array('id'=>$event['url'], 'title'=>$t, 'start'=>$s , 'end'=>$e, 'allDay'=>$allDay, 'className'=>'event', 'details'=>$event['description'], 'location'=>$location);
				
				$event['color'] = $colour;
				array_push($events, $event);
			}
			
			return $events;
		}

		public static function getSocIcalFeed($soc) {
			$db = \PA\Database::getArchiveDatabase();
			$query = "SELECT socs.`teamup_edit_link`, socs.`name` FROM `Society` socs WHERE socs.`id` = ?";
			$prep = $db->prepare($query);

			$prep->bind_param("i", $soc);
			$prep->execute();
			$result = $prep->get_result();

			while($row = $result->fetch_assoc()){
				$url = $row['teamup_edit_link'];
				$soc_name = $row['name'];
			}

			$db->close();

			$client = new \GuzzleHttp\Client(array('headers' => array('Teamup-Token' => 'dde6f40ea11a17acc9ce675b19c14c2f1633aae17f4164051ffc7a590f36e105')));

			$res = $client->get("https://api." . substr($url, 8) .  "/events?startDate=1905-01-01&endDate=2299-01-01");
			$events = json_decode($res->getBody())->events;
			$ical = "BEGIN:VCALENDAR
			VERSION:2.0
			PRODID:-//Teamup Solutions AG//Teamup Calendar//EN
			METHOD:PUBLISH
			X-WR-CALNAME:$soc_name
			X-WR-CALDESC:Public API
			X-PUBLISHED-TTL:PT15M
			BEGIN:VTIMEZONE
			TZID:Europe/London
			TZURL:http://tzurl.org/zoneinfo-outlook/Europe/London
			X-LIC-LOCATION:Europe/London
			BEGIN:DAYLIGHT
			TZOFFSETFROM:+0000
			TZOFFSETTO:+0100
			TZNAME:BST
			DTSTART:19700329T010000
			RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU
			END:DAYLIGHT
			BEGIN:STANDARD
			TZOFFSETFROM:+0100
			TZOFFSETTO:+0000
			TZNAME:GMT
			DTSTART:19701025T020000
			RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU
			END:STANDARD
			END:VTIMEZONE
			";



			foreach($events as $event) {
				//var_dump($event);
				if ($event->readonly) continue;
				$event = (array) $event;
				
				$uid = $event['id'];
				$event_id = explode("-",$event['id']);
				$cal_id = self::escapeString($event_id[0]);
				if (isset($event_id[2]))
					$event_id = self::escapeString($event_id[2]);
				else
					$event_id = self::escapeString($event_id[0]);
				
				$location = self::escapeString($event['location']);
				
				
				$converter = new \League\HTMLToMarkdown\HtmlConverter();
				
				if (isset($event['notes']))
					$notes = "DESCRIPTION:" . self::escapeString($converter->convert($event['notes']));
				else
					$notes = "";
				
				$title = self::escapeString($event['title']);
				$who = self::escapeString($event['who']);
				$start = substr($event['start_dt'], 0, 19);
				$start = str_replace("-", "", str_replace(":", "", $start));
				$end = substr($event['end_dt'], 0, 19);
				$end = str_replace("-", "", str_replace(":", "", $end));
				$timezone = isset($event['tz']) ? $event['tz'] : "Europe/London";
				$timestamp = date('Ymd\THis\Z', time());
				$created = substr($event['creation_dt'], 0, 19);
				$created = str_replace("-", "", str_replace(":", "", $created)) . "Z";
				
				if (isset($event['update_dt'])) {
					$modified = substr($event['update_dt'], 0, 19);
					$modified = "
			LAST-MODIFIED:" . str_replace("-", "", str_replace(":", "", $modified)) . "Z";
				} else {
					$modified = "";
				}
				
				if ($event['all_day']) {
					$start_line = "DTSTART;VALUE=DATE:" . substr($start, 0, 8);
					$end_line = "DTEND;VALUE=DATE:" . substr($end, 0, 8);
				} else {
					$start_line = "DTSTART;TZID=$timezone:$start";
					$end_line = "DTEND;TZID=$timezone:$end";
				}
				
				$whoNoSpaces = str_replace(" ", "", $who);
				
				
				$ical .= "BEGIN:VEVENT
			UID:$uid
			$start_line
			SEQUENCE:0
			TRANSP:OPAQUE
			$end_line
			URL:https://teamup.com/event/show/key/ks522aeab3f9a8706c/event_id/$event_id
			LOCATION:$location
			SUMMARY:$title
			ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=OPT-PARTICIPANT;PARTSTAT=ACCEPTED;CN=$who;X
			 -NUM-GUESTS=0:$whoNoSpaces
			CLASS:PUBLIC$notes
			CATEGORIES:PA $soc_name
			DTSTAMP:$timestamp
			CREATED:$created$modified
			X-TEAMUP-WHO:$who
			END:VEVENT
			";
				
			}
			$ical .= "END:VCALENDAR";

			return $ical;
		}
	}
}