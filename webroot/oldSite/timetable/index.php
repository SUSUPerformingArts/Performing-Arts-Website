<?php

    // About page
    $root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";

    \PA\Snippets\Header::printHeader("SUSU Performing Arts - Rehearsal Schedule", 
				array("/libs/moment.js","/libs/fullcalendar/js/fullcalendar/jquery.fullcalendar.min.js","/libs/vex/vex.combined.min.js", "/libs/marked.js"), 
				array("/libs/fullcalendar/css/fullcalendar/fullcalendar.css", "/libs/vex/vex.css", "/libs/vex/vex-theme-default.css"), 
				"2.1.4");

?>

<script>
<?php
	require_once $root . "/php/classes/ical/CalFileParser.php";
	"https://ics.teamup.com/feed/ksc21e36577debd936/0.ics";
	
	echo "var musicShows = " . json_encode(getEvents("https://ics.teamup.com/feed/ks522aeab3f9a8706c/1977699.ics", "#2951b9")) . ";";
	echo "var musicRehearsals = " . json_encode(getEvents("https://ics.teamup.com/feed/ks522aeab3f9a8706c/1977698.ics", "#2951b9")) . ";";
	echo "var danceShows = " . json_encode(getEvents("https://ics.teamup.com/feed/ks522aeab3f9a8706c/1977684.ics", "#f6c811")) . ";";
	echo "var danceRehearsals = " . json_encode(getEvents("https://ics.teamup.com/feed/ks522aeab3f9a8706c/1977685.ics", "#f6c811")) . ";";
	echo "var theatricalShows = " . json_encode(getEvents("https://ics.teamup.com/feed/ks522aeab3f9a8706c/1977700.ics", "#2e8f0c")) . ";";
	echo "var theatricalRehearsals = " . json_encode(getEvents("https://ics.teamup.com/feed/ks522aeab3f9a8706c/1977701.ics", "#2e8f0c")) . ";";
	
	function getEvents($feed, $colour) {
		$parser = new CalFileParser();
		$cal = $parser->parse($feed);
		$events = array();
		
		foreach ($cal as $event) {
			
			if (isset($event['dtend;tzid=europe/london'])) {
				$s = $event['dtend;tzid=europe/london'];
				$s = substr($s, 0, 4) . "-" . substr($s, 4, 2) . "-" . substr($s, 6, 5) . ":" . substr($s, 11, 2) . ":" . substr($s, 13, 2);
				$e = $event['dtend;tzid=europe/london'];
				$e = substr($e, 0, 4) . "-" . substr($e, 4, 2) . "-" . substr($e, 6, 5) . ":" . substr($e, 11, 2) . ":" . substr($e, 13, 2);
				$allDay = "false";
			} else if (isset($event['dtend;value=date'])) {
				$s = $event['dtend;value=date'];
				$s = substr($s, 0, 4) . "-" . substr($s, 4, 2) . "-" . substr($s, 6, 2);
				$e = $event['dtend;value=date'];
				$e = substr($e, 0, 4) . "-" . substr($e, 4, 2) . "-" . substr($e, 6, 2);
				$allDay = "true";
			} else {
				continue;
			}
			
			$t = $event['x-teamup-who'] . ": " . $event['summary'];
			$event = array('id'=>$event['url'], 'title'=>$t, 'start'=>$s , 'end'=>$e, 'allDay'=>$allDay, 'className'=>'event', 'details'=>$event['description'], 'location'=>$event['location']);
			
			if (isset($colour))
				$event['color'] = $colour;
			
			array_push($events, $event);
		}
		
		return $events;
	}
?>

function updateEvents() {
	vex.defaultOptions.className = 'vex-theme-default';
	var events2 = [];
	
	if (document.getElementById('dance').checked) {
		events2 = events2.concat(danceShows);
		events2 = events2.concat(danceRehearsals);
	}
	
	if (document.getElementById('music').checked) {
		events2 = events2.concat(musicShows);
		events2 = events2.concat(musicRehearsals);
	}
	
	if (document.getElementById('theatre').checked) {
		events2 = events2.concat(theatricalShows);
		events2 = events2.concat(theatricalRehearsals);
	}
	
    $('[id=calendar]').each(function(index, element) {
       element = $(element);
	   
	   element.fullCalendar( 'removeEvents' );
	   
	   element.fullCalendar({
        events: events2,
		eventClick: function(e, jsEvent, view) {
			var title_parts = e.title.split(":");
			var soc = title_parts[0];
			var title = title_parts[1];
			
			title_parts.forEach(function(v,i) {
				if (i<2) return;
				
				title += ":" + v;
			});
			
			var img = "/img/societies/logo_by_name/" + soc.replace(/\s/g, '').toLowerCase();
			
			if (e.location == null) e.location = "Southampton";
			var map = e.location.replace(/\s/g, '+');
			
			if (e.details == null) e.details = "";
			var desc = marked(e.details);
			
			var date = new Date(e.start);
			var options = {
				weekday: "long", year: "numeric", month: "short",
				day: "numeric", hour: "2-digit", minute: "2-digit", hour12: "true"
			};
			
			var printDate = date.toLocaleTimeString("en-gb", options);
			
			html = "<header><h2><img style='height:3em' src='" + img + "' onerror='this.style.display = \"none\";' />" + title + "</h2></header><p>" + desc + "</p>\
			<p align='center'>"+e.location + " | " + printDate + " | " + soc + "</p><iframe width='100%' height='250' frameborder='0' style='border:0' src='https://www.google.com/maps/embed/v1/place?key=AIzaSyCyOvyFMRWdCk2Wa0KSa_sNSyIi_RKN08U&q=" + map + "' allowfullscreen></iframe>";
			
			
			
			vex.open({content: html, contentCSS: { width: '60vw' }});
			
			
            return false;
		}
    });
});
}

$(document).ready(updateEvents);
$(document).ready(function(e) {
    $("#dance").change(updateEvents);
	$("#theatre").change(updateEvents);
	$("#music").change(updateEvents);
});
</script>
<style>
	.event {
		cursor:pointer;
		
	}
</style>
	<div class="well well-pa">
		<h1>Events Calendar</h1>
	</div>
 
	<p class="lead">Check out our calendar for all the shows happening around the Performing Arts!</p>

	<ul class="list-inline text-center">
		<li><input type="checkbox" id="dance" checked /><label for="dance">Dance Calendar</label></li>
		<li><input type="checkbox" id="music" checked /><label for="music">Music Calendar</label></li>
		<li><input type="checkbox" id="theatre" checked /><label for="theatre">Theatrical Calendar</label></li>
	</ul>

	<div class="row">
		<div class="col-md-10 col-md-offset-1 well well-pa">
			<div class="embed-responsive embed-responsive-4by3 hidden-xs">
				<div id='calendar'></div>
			</div>
			<div class="visible-xs-block">
            	<div id='calendar'></div>
			</div>
		</div>
	</div>


<?php
    \PA\Snippets\Footer::printFooter();
