
<?php 

$area="About";
include 'header.php';
?>
<div class="row-fluid">
	<div class="well">
		<h2>About Performing Arts</h2>
	</div>
</div>
<div class="row-fluid">
	<div class="span4" id="aboutpa">
          <div class="span12" style="margin-right: 10px;">
		<a href="http://boxoffice.susu.org/view/235/performing-arts">
			<img style="width: 100%;" src="./img/pa-card.png" />
		</a></div>
	</div>
	<div class="span8" id="pacard">
		
		<div class="row-fluid">
			
		<h3>The Performing Arts Card</h3>
		<p>This acts as your membership to the Performing Arts, costing only £3 from the SUSU Box Office!</p>
		<p>The card means that you're insured during any rehearsals or performances just in case anything goes wrong, and it gets you money off tickets to other PA shows.</p>
		<p>Buy yours at the box office in person or buy one online <a href="http://boxoffice.susu.org/view/235/performing-arts">here</a>.</p>
		<p>You can find the details of the insurance policy <a href="https://docs.google.com/file/d/0B8QnE0Kdupy-b0YtbWQxLUtEeUE/preview">here.</a></p>
	</div>
</div>

	</div>
<div class="row" id="committee">
	<h2>Your Committee</h2>
</div>
	<div class="row">
    <?php
		echoCommittee("Claire","Gilbert", "Performing Arts Officer", "claire.jpg", "<p>Claire's Bio</p>","perform@susu.org");
				echoCommittee("Josh","Tipping", "Dance Societies Rep", "josh.png", "<p>Josh's Bio</p>","dance@susuperformingarts.org");
				echoCommittee("Hannah","Holliday", "Theatrical Societies Rep", "hannah.png", "<p>Hannah's Bio</p>", "theatrical@susuperformingarts.org");
				echoCommittee("Peter","Bridgwood", "Music Societies Rep", "peter.png", "<p>Peter's Bio</p>","music@susuperformingarts.org");
				echoCommittee("Peewee","Ward", "Marketing Officer", "peewee.png", "<p>Peewee's Bio</p>", "marketing@susuperformingarts.org");
				//echo '</div><div class="row">';
				echoCommittee("Jade","Thompson", "Events Officer", "jade.png", "<p>Jade's Bio</p>","events@susuperformingarts.org");
				
				echoCommittee("Matt","Hicken", "Financial Officer", "matt.png", "<p>Matt's Bio</p>", "finance@susuperformingarts.org");
				echoCommittee("Shane","Murphy", "VP Student Engagement", "shane.png", "<p>Shane's Bio</p>","vpengage@susu.org");
				echoCommittee("James","Mozden", "StageSoc President", "mozy.png", "<p>Mozy's Bio</p>", "president@stagesoc.org.uk");
				echoCommittee("Joe","Hart", "Web Officer", "joe.png", "<p>Joe is a second year Computer Science student who is involved with Theatre Group, Comedty Society and Showstoppers. He does a bit too much PA and too little of his degree.</p>", "web@susuperformingarts.org");

	?>

</div>

<?php include 'footer.php'; 

	function echoCommittee($firstname, $lastname, $position, $image, $blurb, $email){
	echo '<div class="span3 center">';
	echo '<div class="row center" style="margin-left: 0px;">';
    echo '<img src="./img/'.$image.'" alt="'.$position.'" class="img-circle committee" />';
    echo '</div>';
    echo '<h4>'.$firstname.' '.$lastname.'</h4>';
    echo '<p>'.$position.'</p>';
	echo '<p><a href="mailto:'.$email.'" class="btn btn-primary">Email '.$firstname.'</a></p>';
   /* echo '<hr width="90%" size="1" />';
    echo $blurb;*/
    echo '</div>';
	}
?>

