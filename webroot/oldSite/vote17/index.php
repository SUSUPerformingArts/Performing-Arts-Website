<?php

    $root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";
    class_alias('PA\Auth\Auth', 'Auth');
    class_alias('PA\Database', 'DB');

    \PA\Snippets\Header::printHeader("SUSU Performing Arts - PA Awards vote");

?>
	
	<script src="//cdnjs.cloudflare.com/ajax/libs/Sortable/1.5.1/Sortable.min.js"></script>

    <div class="well well-pa">
        <h1>PA Awards 2017 - Vote</h1>
        <p>At the PA ball, a number of awards are given to those in performing arts who have been exceptional, on or off stage.
		<br />
		For all but one of the awards, they are chosen by a public vote... see below to cast yours!
		</p>
	<h3>Instructions</h3>
	<p>
		To vote, you can rank up to 3 people for each award by <strong>dragging</strong> their names into the top 3 (the top one is #1, then #2 and #3) positions. You do <strong>not</strong>:
		
		<ul>
			<li>have to vote on all awards</li>
			<li>have to select 3 people per award (you can select only 1 or 2, however this will not increase the weight of your vote)</li>
			<li>have to consider the ordering of nominees beyond the top 3 - this is not recorded</li>
		</ul>
		
		Once you click submit your vote is sealed and is uneditable - be careful that you haven't knocked someone out with a no-vote by accident etc so double check!
		<br />
		Have fun!
	</p>
    </div>

<?php 
	$voted = false;

	if (Auth::isLoggedIn()) {
		$res = mysqli_fetch_assoc(mysqli_query(DB::getSiteDatabase(), "SELECT COUNT(*) as numberOfVotes FROM perform.awards_vote_17 WHERE username=\"".Auth::getUserInfo()['iSolutionsUsername']."\""));
		$voted = $res['numberOfVotes'] > 0;
	}
	
	if (Auth::isLoggedIn() && !$voted) {?>

	<style>
		ul.candidates {
		  list-style-type: none;
		  margin: 0;
		  padding: 0;
		  cursor: pointer; cursor: hand;
		}

		ul.candidates li {
		  padding-left: 3px;
		  border-bottom: 1px solid #ccc;
		}

		ul.candidates li:last-child {
		  border: none;
		}

		ul.candidates li a {
		  text-decoration: none;
		  color: #ffffff;
		  display: block;
		}

		ul.candidates li:nth-of-type(1) {
		  background-color: #FF9200;
		}

		ul.candidates li:nth-of-type(2) {
		  background-color: #6B6B6B;
		}

		ul.candidates li:nth-of-type(3) {
		  background-color: #a06b00;
		}
	</style>
	
	<div class="well well-pa voting_form">
		<h2>Best Performer</h2>
		<hr>
		
		<div class="row">
			<div class="col-lg-4 col-md-3 col-sm-6">
				<h3>Dance</h3>
				<ul id="dbp" class="list-group candidates">
					<li><a>Amy Wallington (Ballet, Contemporary, Jazz, Tap)</a></li>
					<li><a>Emma Collins (Ballroom)</a></li>
					<li><a>Dilini Seneviratne (SKBD, Street, Ballet)</a></li>
					<li><a>Emily Bennett (Contemporary, Tap, Jazz)</a></li>
					<li><a>Claire Mason (Jazz, Tap, Ballet)</a></li>
					<li><a>Chrysta Coker-Strickland (Contemporary, Ballet, Jazz)</a></li>
					<li><a>Jack Rubens (Jazz, Tap, Contemporary)</a></li>
					<li><a>Alice Ainsworth (Contemporary, Jazz)</a></li>
					<li><a>Brittany Fuller (Jazz, Contemporary, Tap, Street)</a></li>
					<li><a>Clare Gentry (Tap, Ballet)</a></li>
					<li><a>Dellsia Harrop (Irish, Street)</a></li>
					<li><a>Eleanor Heale (Ballet)</a></li>
					<li><a>Kezia Mithra-Johnson (Street, Contemporary, Afrodynamix)</a></li>
					<li><a>Sarah Boazman (Ballet)</a></li>
					<li><a>Stephen Prince (Afrodynamix, Contemporary)</a></li>
					<li><a>Yvonne Zankar (Bhangra, Belly, Salsa)</a></li>
				</ul>
			</div>

			<div class="col-lg-4 col-md-4 col-sm-6">
				<h3>Music</h3>
				<ul id="mbp" class="list-group candidates">
					<li><a>Kath Roberts</a></li>
					<li><a>Izzy Tuffin-Donnevert</a></li>
					<li><a>Alga Mau</a></li>
					<li><a>Daisy Stephens</a></li>
					<li><a>David Littleton</a></li>
					<li><a>Elliott Titcombe</a></li>
					<li><a>Emily Watt</a></li>
					<li><a>Emma Atkins</a></li>
					<li><a>Emma Harrison</a></li>
					<li><a>Fiona Butterworth</a></li>
					<li><a>Gemma Wills</a></li>
					<li><a>Hannah Brierley</a></li>
					<li><a>Henrietta Cooke</a></li>
					<li><a>Joci Olexa</a></li>
					<li><a>Patrick Wakelam</a></li>
					<li><a>Sarah Dennison</a></li>
				</ul>
			</div>


			<div class="col-lg-4 col-md-4 col-sm-6">
				<h3>Theatrical</h3>
				<ul id="tbp" class="list-group candidates">
					<li><a>Charlie Randall (Theatre Group, Showstoppers, ComedySoc)</a></li>
					<li><a>Anand Sankar (Theatre Group, ComedySoc, Showstoppers)</a></li>
					<li><a>Jordan Gardner (Theatre Group, ComedySoc)</a></li>
					<li><a>James Cook (Showstoppers)</a></li>
					<li><a>Natalia May (Theatre Group)</a></li>
					<li><a>Philip Needle (LOpSoc)</a></li>
					<li><a>Bella Norris (Showstoppers)</a></li>
					<li><a>Lydia Edge (Showstoppers)</a></li>
					<li><a>Andy Banks (Showstoppers, Theatre Group)</a></li>
					<li><a>Emily Bradshaw (Theatre Group, Showstoppers)</a></li>
					<li><a>Phoebe Judd (Showstoppers, Theatre Group)</a></li>
					<li><a>Robbie Smith (ComedySoc, Showstoppers, Theatre Group)</a></li>
					<li><a>Sean Gilbody (ComedySoc, Theatre Group, Showstoppers)</a></li>
					<li><a>Abbie Roach (Showstoppers)</a></li>
					<li><a>Alex Heyre (Theatre Group)</a></li>
					<li><a>Charlotte Evans (Showstoppers)</a></li>
					<li><a>Izzy Tuffin-Donnevert (Chamber Opera)</a></li>
					<li><a>Amber Courage (LOpSoc)</a></li>
					<li><a>Joseph Hand (LOpSoc)</a></li>
					<li><a>Josh Vaatstra (Theatre Group, Showstoppers)</a></li>
					<li><a>Megan Hilling (Showstoppers)</a></li>
					<li><a>Amy Wardle (LOpSoc)</a></li>
					<li><a>Ben Walker (LOpSoc)</a></li>
					<li><a>Paige Williams (Theatre Group, ComedySoc)</a></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="well well-pa voting_form">
		<h2>Best Behind the Scenes</h2>
		<hr>
		
		<div class="row">
			<div class="col-lg-4 col-md-3 col-sm-6">
				<h3>Dance</h3>
				<ul id="dbts" class="list-group candidates">
					<li><a>Chrysta Coker-Strickland (Contemporary, Ballet, Jazz)</a></li>
					<li><a>Lucy Alford (Jazz, Contemporary)</a></li>
					<li><a>Alice Ainsworth (Contemporary, Jazz)</a></li>
					<li><a>Ameeta Kumar (SKBD)</a></li>
					<li><a>Emily Bennett (Contemporary, Tap, Jazz)</a></li>
					<li><a>Hannah Ward-Glenton (Ballroom)</a></li>
					<li><a>Angus Clark (Ballet)</a></li>
					<li><a>Brittany Fuller (Jazz, Contemporary, Tap, Street)</a></li>
					<li><a>Claire Mason (Jazz, Tap, Ballet)</a></li>
					<li><a>Emily Watt (Capoeria)</a></li>
					<li><a>Imogen Dower (Ballet, Tap, Contemporary)</a></li>
					<li><a>Stacey Barnett (Ballet)</a></li>
					<li><a>Stephen Prince (Afrodynamix, Contemporary)</a></li>
					<li><a>Tom Vartdal (Ballroom)</a></li>
				</ul>
			</div>

			<div class="col-lg-4 col-md-4 col-sm-6">
				<h3>Music</h3>
				<ul id="mbts" class="list-group candidates">
					<li><a>Thomas Kidman</a></li>
					<li><a>Grace Curtis</a></li>
					<li><a>Nyle Bevan-Clarke</a></li>
					<li><a>Chris Hartland</a></li>
					<li><a>Dan Keen</a></li>
					<li><a>Gemma Wills</a></li>
					<li><a>Greg White</a></li>
					<li><a>Henrietta Cooke</a></li>
					<li><a>Lucy Grant</a></li>
					<li><a>Serena Evans</a></li>
					<li><a>Simeon Brooks</a></li>
					<li><a>Sophie Hart</a></li>
					<li><a>Stacey Barnett</a></li>
					<li><a>Will Wilkins</a></li>
				</ul>
			</div>

			<div class="col-lg-4 col-md-4 col-sm-6">
				<h3>Theatrical</h3>
				<ul id="tbts" class="list-group candidates">
					<li><a>Charlie House (Showstoppers, Theatre Group)</a></li>
					<li><a>Paige Williams (Theatre Group, ComedySoc)</a></li>
					<li><a>Renata Stella (LOpSoc)</a></li>
					<li><a>Megan Hilling (Showstoppers)</a></li>
					<li><a>Angharad Morgan (Showstoppers, Theatre Group)</a></li>
					<li><a>Jacob Power (ComedySoc)</a></li>
					<li><a>Raffaella Patmore (Theatre Group)</a></li>
					<li><a>Kimberly Pearson (Theatre Group, Showstoppers)</a></li>
					<li><a>Felicia de Angeli (Theatre Group, Showstoppers)</a></li>
					<li><a>Amy Wardle (LOpSoc)</a></li>
					<li><a>David Child (LOpSoc)</a></li>
					<li><a>Ben McQuigg (Showstoppers)</a></li>
					<li><a>Flora Whitmarsh (Theatre Group, Showstoppers,ComedySoc)</a></li>
					<li><a>Grace Curtis (Chamber Opera)</a></li>
					<li><a>Isaac Treuhurz (LOpSoc)</a></li>
					<li><a>Jamie Martin (Showstoppers, Theatre Group)</a></li>
					<li><a>Joseph Lynch (StageSoc, Showstoppers, Theatre Group, ComedySoc, LOpSoc)</a></li>
					<li><a>Annabelle Williams (Showstoppers)</a></li>
					<li><a>Liam Chan (Chamber Opera, LOpSoc)</a></li>
					<li><a>Robbie Smith (ComedySoc, Showstoppers, Theatre Group)</a></li>
					<li><a>William Shere (Theatre Group)</a></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="well well-pa voting_form">
		<h2>Best Technician/Technical work</h2>
		<hr>		
		<div class="row">
			<div class="col-lg-4 col-md-3 col-sm-6">
				<ul id="bt" class="list-group candidates">
					<li><a>Edmund King (Stagesoc)</a></li>
					<li><a>Joseph Lynch (Stagesoc, Showstoppers)</a></li>
					<li><a>Tom Pell (Stagesoc,Lopsoc)</a></li>
					<li><a>Phoebe Lewis (Stagesoc)</a></li>
					<li><a>David Young (Stagesoc)</a></li>
					<li><a>Edward Holland (Chamber Opera)</a></li>
					<li><a>George Tucker (Stagesoc)</a></li>
					<li><a>Patrick Edwards (Stagesoc,Lopsoc,Showstoppers)</a></li>
					<li><a>Hannah Parsons (Stagesoc)</a></li>
					<li><a>Jaimin Shah (Thalla)</a></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="well well-pa voting_form">
		<h2>Outstanding Society</h2>
		<hr>		
		<div class="row">
			<div class="col-lg-4 col-md-3 col-sm-6">
				<ul id="os" class="list-group candidates">
					<li><a>Showstoppers</a></li>
					<li><a>Ballroom and Latin Society</a></li>
					<li><a>Ballet Society</a></li>
					<li><a>Contemporary dance</a></li>
					<li><a>Comedy Society</a></li>
					<li><a>StageSoc</a></li>
					<li><a>Chamber Opera</a></li>
					<li><a>SUSO</a></li>
					<li><a>Tap Dance</a></li>
					<li><a>FolkSoc</a></li>
					<li><a>Afrodynamix</a></li>
					<li><a>Theatre Group</a></li>
					<li><a>Jazz Band</a></li>
					<li><a>Jazzmanix</a></li>
					<li><a>LOpSoc</a></li>
					<li><a>Breakdancing</a></li>
					<li><a>SKBD</a></li>
					<li><a>Irish Dance</a></li>
					<li><a>Bhangra Dance</a></li>
					<li><a>Jazz Dance</a></li>
					<li><a>SU Philharmonic</a></li>
				</ul>
			</div>
		</div>
	</div>

	<button class="btn-danger voting_form" style="width:100%" id="submit_vote"><h4>Seal and submit my vote</h4></button>

	<script>
		$(".candidates").each(function(index,element) {
			for (var i = element.children.length; i >= 0; i--) {
				element.appendChild(element.children[Math.random() * i | 0]);
			}
			
			for (var i = 0; i < 3; i++) {
				$(element).prepend("<li><a>No vote</a></li>");
			}

			Sortable.create(element);
		});
		
		$("#submit_vote").click(function(event) {
			var vote = {};
			
			$(".candidates").each(function(index, award) {
				award = $(award);
				vote[award.attr('id')] = [award.children()[0].innerText, award.children()[1].innerText, award.children()[2].innerText];

			});
			
			vote.username = "<?php echo Auth::getUserInfo()['iSolutionsUsername']; ?>";
			
			$.post(
				"/vote17/submit.php",
				vote,
				function(response) {
					$(".voting_form").each(function(index,element) {
						$(element).css("display", "none");
					});

					if (response.success) {
						$("#successful_vote").css("display", "block");
					} else if (response.error === "Already voted") {
						$("#already_voted").css("display", "block");
					} else if (response.error === "Not logged in") {
						$("#login").css("display", "block");
					}
					
				},
				"json");
		});
	</script>

<?php	} else if ($voted) { ?>
	<div class="well well-pa">
		<h2>Already voted</h2>
        	
		<p>It appears that you have already voted in this election - thank you. If you believe that may be an error please do get in contact with the webmaster (Simeon Brooks) <a href="mailto:web@susuperformingarts.org">here</a>.</p>
	</div><?php	} else { ?>
	<div class="well well-pa">
		<h2>Login</h2>
        	
		<p>Please login <a href="https://perform.susu.org/archive/login?continue=/vote17/">here</a> to vote. You need a university login to do this - the vote is entirely annomous, it's only to stop people voting twice</p>
	</div>
<?php } ?>

<div id="successful_vote" class="well well-pa" style="display:none;">
	<h2>Thank you for voting</h2>
	<p>Your vote has been submitted! The results will be announced at the PA ball, if you wish to change your vote please get in contact with the webmaster (Simeon Brooks) at <a href="mailto:web@susuperformingarts.org">web@susuperformingarts.org</a></p>
</div>

<div id="already_voted" class="well well-pa" style="display:none;">
	<h2>Already voted</h2>
     	
	<p>It appears that you have already voted in this election - thank you. If you believe that may be an error please do get in contact with the webmaster (Simeon Brooks) <a href="mailto:web@susuperformingarts.org">here</a>.</p>
</div>

<div id="login" class="well well-pa" style="display:none;">
	<h2>Login</h2>
       	
	<p>Please login <a href="https://perform.susu.org/archive/login?continue=/vote17/">here</a> to vote. You need a university login to do this - the vote is entirely annomous, it's only to stop people voting twice</p>
</div>

<?php
    \PA\Snippets\Footer::printFooter();
?>