<?php

    // About page
    $root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";
	class_alias('PA\Auth\Auth', 'Auth');
	
    \PA\Snippets\Header::printHeader("SUSU Performing Arts - Rehearsal Schedule", 
				array("/libs/moment.js","/libs/fullcalendar/js/fullcalendar/jquery.fullcalendar.min.js","/libs/vex/vex.combined.min.js", "/libs/marked.js"), 
				array("/libs/fullcalendar/css/fullcalendar/fullcalendar.css", "/libs/vex/vex.css", "/libs/vex/vex-theme-default.css"), 
				"2.1.4");
	
	//if (!isset($_GET['soc'])) $_GET['soc'] = "44";
?>

<script>
<?php
	$specificSoc = isset($_GET['soc']);
	$soc = $_GET['soc'];
	
	if (!$specificSoc) {
		echo "var musicRehearsals = " . json_encode(getEvents("https://perform.susu.org/schedule/feed.php?soc=42", "#2951b9")) . ";";
		echo "var danceRehearsals = " . json_encode(getEvents("https://perform.susu.org/schedule/feed.php?soc=41", "#f6c811")) . ";";
		echo "var theatricalRehearsals = " . json_encode(getEvents("https://perform.susu.org/schedule/feed.php?soc=43", "#2e8f0c")) . ";";
	}else{
		$qu = "SELECT GET_AREA_FROM_TYPE(socs.`type`, socs.`id`) AS type FROM Society AS socs WHERE NOT(GET_AREA_FROM_TYPE(`type`, `id`)=5) AND `id`=$soc"; 
		$db = \PA\Database::getArchiveDatabase();
		$result = $db->query($qu);
		$type = $result->fetch_assoc();
		$type = $type['type'];
		$result->free();
		$db->close();
		
		switch($type) {
			case "1":
				$colour = "#2e8f0c";
				break;
			case "2":
				$colour = "#2951b9";
				break;
			case "3":
				$colour = "#f6c811";
				break;
		}
		
		echo "var specificEvents = " . json_encode(getEvents("https://perform.susu.org/schedule/feed.php?soc=$soc", $colour)) . ";";
	}
	
	echo "var v = 'https://perform.susu.org/schedule/feed.php?soc=$soc';";
	
	function getEvents($feed, $colour) {
		$parser = new \PA\ical\CalFileParser();
		$cal = $parser->parse($feed);
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
			
			if (isset($colour))
				$event['color'] = $colour;
			
			array_push($events, $event);
		}
		
		return $events;
	}
?>
var firstLoad = true;
function updateEvents() {
	vex.defaultOptions.className = 'vex-theme-default';
	var events2 = [];
	
	if (typeof specificEvents == 'undefined') {
		if (document.getElementById('dance').checked) {
			events2 = events2.concat(danceRehearsals);
		}
		
		if (document.getElementById('music').checked) {
			events2 = events2.concat(musicRehearsals);
		}
		
		if (document.getElementById('theatre').checked) {
			events2 = events2.concat(theatricalRehearsals);
		}
	} else {
		events2 = events2.concat(specificEvents);
	}
	
	console.log(events2);
	
    $('[id=calendar]').each(function(index, element) {
       element = $(element);
	   var restore_date  = element.fullCalendar('getDate');
	   var restore_scroll = $(window).scrollTop();
	   element.html("");
	   element.fullCalendar( 'removeEvents' );
	   
		element.fullCalendar({
			defaultView: 'month',
			events: events2,
			firstDay: 1,
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
		
		if (!firstLoad)
			element.fullCalendar( 'gotoDate', restore_date)
			
		$('html, body').animate({scrollTop: restore_scroll + 'px'}, 0);
	});
	
	firstLoad = false;
}

$(document).ready(updateEvents);
$(document).ready(function(e) {
    $("#dance").change(updateEvents);
	$("#theatre").change(updateEvents);
	$("#music").change(updateEvents);
	$("#soc_select").change(function(e2) {
		window.location.href = window.location.href.split("?")[0] + ($(this).val() == "" ? "" : '?soc=' + $(this).val());
    });
	if (window.location.href.split("?soc=").length > 1) {
		$("#soc_select").val(window.location.href.split("?soc=")[1]);
	}
});

function showSubscribe() {
	var html = "<header><h2>Add a PA calendar to your calendar</h2></header>\
				<ul class='list-inline text-center'>\
					<li>Choose events to add: <select id='subscribe_calendar'><?php
					
		/* Get the data for the socieites */
		$qu = "SELECT socs.`id`, socs.`name` FROM `Society` socs";
	
		$db = \PA\Database::getArchiveDatabase();
		$result = $db->query($qu); // No input
		
		while($row = $result->fetch_assoc()){
			$id = $row['id'];
			$name = $row['name'];
			
			echo "<option value='$id' style='color:#000'>$name</option>";
		}
	
		$result->free();
		$db->close();
	?></select></li><li>Choose how to add: <select id='subscription_method'>\
				<option value='google_cal_information'>Google Calendar</option>\
				<option value='apple_cal_information'>iPhone and iPad</option>\
				<option value='imac_cal_information'>iMac</option>\
				<option value='outlook_cal_information'>Online Outlook</option>\
				<option value='download_cal_information'>Download Events (one off)</option></select></li></ul>\
	<div id='google_cal_information' class='cal_information' style='display:inline'>\
		<h4>Adding PA events to Google Calendar</h4>\
		<p>There are just a few steps that you need to do, and they can't be done from a phone (unless you can view the desktop site). So:\
		<ol><li>Open <a href='https://www.google.com/calendar' target='_blank'>Google Calendar</a> and sign in if you need to</li>\
		<li>In the bottom left, to the right of \'Other calendars\', click the down arrow and select \'Add by URL\'</li>\
		<li>Then copy the following URL into the box and click Add calendar</li>\
		</ol></p>\
		<p style='text-align:center;'><strong><span id='display_URL' /></strong></p>\
		<p>For more information click <a href='https://support.google.com/calendar/answer/37100?co=GENIE.Platform%3DDesktop&hl=en'>here</a> to view Google's help files\
		<img style='height:80px; float:right;' src='/img/icons/google_calendar.png' />\
		\
	</div><div id='apple_cal_information' class='cal_information' style='display:none'>\
		<h4>Adding PA events to Google Calendar</h4>\
		<p>There are just a few steps that you need to do which need doing from your iPad or iPhone So:\
		<ol><li>Open the settings app</li>\
		<li>On the left, open the \'Mail, Contacts, Calendars\'</li>\
		<li>Tap \'Add Account\'</li>\
		<li>Tap \'Add Subscribed Calendar\'</li>\
		<li>Copy and paste the following URL into the \'Server\' field and click next</li>\
		<li>Change any settings you with, then click save</li>\
		</ol></p>\
		<p style='text-align:center;'><strong><span id='display_URL' /></strong></p>\
		<p>For more information click <a href='http://www.imore.com/how-subscribe-calendars-your-iphone-or-ipad'>here</a> to view other help files\
		<img style='height:80px; float:right;' src='/img/icons/icloud.png' />\
		\
	</div><div id='imac_cal_information' class='cal_information' style='display:none'>\
		<h4>Adding PA events to iCloud/iCal on your mac</h4>\
		<p>There are just a few steps that you need to do, and must be done from an mac (not windows or a phone etc.). So:\
		<ol><li>Open the Calendar app</li>\
		<li>Go to file > New Calendar Subscription</li>\
		<li>Copy the following URL into the \'Subscibe to\' box, change any settings you wish to and click OK</li>\
		</ol></p>\
		<p style='text-align:center;'><strong><span id='display_URL' /></strong></p>\
		<p>For more information click <a href='https://support.apple.com/en-gb/HT202361'>here</a> to view Apple's help files\
		<img style='height:80px; float:right;' src='/img/icons/icloud.png' /></p>\
		\
	</div><div id='outlook_cal_information' class='cal_information' style='display:none'>\
		<h4>Adding PA events to Outlook Web App (ie. Live mail, hotmail, office365 etc.)</h4>\
		<p>There are just a few steps that you need to do, and they can't be done from a phone (unless you can view the desktop site). So:\
		<ol><li>Open the import page here <a href='https://calendar.live.com/import.aspx' target='_blank'>here</a> and sign in if you need to</li>\
		<li>On the left, select subscribe</li>\
		<li>Then copy the following URL into the \'Calendar URL\' box, choose a name, colour and charm, then click subscribe</li>\
		</ol></p>\
		<p style='text-align:center;'><strong><span id='display_URL' /></strong></p>\
		<p>For more information click <a href='https://support.office.com/en-us/article/Import-or-subscribe-to-a-calendar-in-Outlook-com-or-Outlook-on-the-web-CFF1429C-5AF6-41EC-A5B4-74F2C278E98C?ui=en-US&rs=en-001&ad=US'>here</a> to view Microsoft's help files\
		<img style='height:80px; float:right;' src='/img/icons/owa.png' />\
		\
	</div><div id='download_cal_information' class='cal_information' style='display:none'>\
		<h4>Download PA events as iCal (.ics) file</h4>\
		<p>This method does not have the auto-update that the other calendars do, but if you would like to download the events click below.</p>\
		<p style='text-align:center;'><strong><a id='display_URL' /></strong></p>\
		<p>This file can then be imported into many calendar applications\
		<img style='height:80px; float:right;' src='/img/icons/download.png' /></p>\
		\
	</div>";
	
	vex.open({
		content: html, 
		contentCSS: { width: '60vw' },
		afterOpen: function($vexContent) {
  			$("#subscribe_calendar").val($("#soc_select").val());
			$("#subscription_method").change(function(e) {
                $(".cal_information").each(function(index, element) {
                    $(element).css("display", "none");
                });
				
				$("#" + $("#subscription_method").val()).css("display", "inline");
            });
			function updateURL() {
				$("span[id=display_URL]").each(function(index, element) {
                    $(element).html("https://perform.susu.org/schedule/feed.php?soc=" + $("#subscribe_calendar").val());
                });
				$("a[id=display_URL]").each(function(index, element) {
                    $(element).attr("href", "https://perform.susu.org/schedule/feed.php?soc=" + $("#subscribe_calendar").val());
					$(element).attr("download", $("#subscribe_calendar option[value='" + $("#subscribe_calendar").val() + "']").text() + ".ics");
					$(element).html("https://perform.susu.org/schedule/feed.php?soc=" + $("#subscribe_calendar").val());
                });
			}
			$("#subscribe_calendar").change(updateURL);
			updateURL();
  		}
  });
	
}
</script>
<style>
	.event {
		cursor:pointer;
	}
	#calendar {
        user-select: none;
        -moz-user-select: none;
        -khtml-user-select: none;
        -webkit-user-select: none;
        -o-user-select: none;
	} 
</style>
	<div class="well well-pa">
		<h1>Rehearsal Schedule</h1>
	</div>
 
	<p class="lead">Check out the calendar to see which societies are meeting when</p>

	<ul class="list-inline text-center">
    <?php
		if (!$specificSoc) echo '
		<li><input type="checkbox" id="dance" checked /><label for="dance">Dance Calendar</label></li>
		<li><input type="checkbox" id="music" checked /><label for="music">Music Calendar</label></li>
		<li><input type="checkbox" id="theatre" checked /><label for="theatre">Theatrical Calendar</label></li>'; ?>
        <li><select id="soc_select" style="background-color:transparent">
        	
            <?php
				$allSocsText = $specificSoc ? "Show events from all societies" : "Show events for just one society";
				echo "<option value='' selected style='color:#000'>$allSocsText</option>";
				/* Get the data for the socieites */
				$qu = "SELECT socs.`id`, socs.`name` FROM `Society` socs";
			
				$db = \PA\Database::getArchiveDatabase();
				$result = $db->query($qu); // No input
				
				while($row = $result->fetch_assoc()){
					$id = $row['id'];
					$name = $row['name'];
					
					echo "<option value='$id' style='color:#000'>$name</option>";
				}
			
				$result->free();
				$db->close();
			?></select></li>
            
            <?php
				if ($specificSoc) {
					echo "<button class='btn btn-pa' onclick='showSubscribe();'>Subscribe</button>";
				}
				
				if (Auth::isOnPACommittee()) {
					if (!$specificSoc || true) { // NEEDS CHANGING - CURRENTLY GET ALL EDIT BUTTONS FOR SPECIFIC SOC VIEW
						?><select id='edit_calendar' style="background-color:transparent">
						<option value='0' style='color:#000'>Select society to edit as</option><?php

						if (Auth::isPAWebmaster()) echo "<option value='https://teamup.com/ks7d9f777030b2abdd' style='color:#000'>WEBMASTER - ADMIN</option>";

						/* Get the data for the socieites */
						$qu = "SELECT socs.`teamup_edit_link`, socs.`name` FROM `Society` socs";
					
						$db = \PA\Database::getArchiveDatabase();
						$result = $db->query($qu); // No input
						
						while($row = $result->fetch_assoc()){
							$link = $row['teamup_edit_link'];
							$name = $row['name'];
							
							echo "<option value='$link' style='color:#000'>$name</option>";
						}
					
						$result->free();
						$db->close();
						
	
						?></select>
						<script>
							$('#edit_calendar').change(function(){
								if($(this).val() != 0) {
									window.open($(this).val());
									$(this).val(0)
								}
							});
						</script>
						<?php
					} else {						
						
						echo "<li><button class='btn btn-pa' onclick='window.open(\"$href\",\"_blank\");'>Edit as $name</button></li>";
					}
				} else {
					$committees = Auth::getCurrentCommittees();
					foreach($committees as $committee) {
						$qu = "SELECT socs.`teamup_edit_link`, socs.`name` FROM `Society` socs WHERE socs.`id`=$committee";
					
						$db = \PA\Database::getArchiveDatabase();
						$result = $db->query($qu); // No input
						
						while($row = $result->fetch_assoc()){
							$href = $row['teamup_edit_link'];
							$name = $row['name'];
							
							echo "<li><button class='btn btn-pa' onclick='window.open(\"$href\",\"_blank\");'>Edit as $name</button></li>";
						}
					
						$result->free();
						$db->close();
					}
				}
			?>
    	<li><button class="btn btn-pa" onclick="window.open('https://teamup.com/ks522aeab3f9a8706c','_blank');">View in new tab</button></li>
	</ul>

	<div class="row">
		<div class="col-md-10 col-md-offset-1 well well-pa">
			<div class="embed-responsive hidden-xs" style="height:100%">
				<div id='calendar'></div>
			</div>
			<div class="visible-xs-block">
            	<div id='calendar'></div>
			</div>
		</div>
	</div>


<?php
    \PA\Snippets\Footer::printFooter();
