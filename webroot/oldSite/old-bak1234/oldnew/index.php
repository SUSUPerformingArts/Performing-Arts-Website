 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SUSU Performing Arts</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
jQuery(document).ready(function($) {
 
	$(".scroll").click(function(event){		
		event.preventDefault();
		$('html,body').animate({scrollTop:$(this.hash).offset().top}, 500);
	});
});
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-35100441-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
    <script type="text/javascript">

    google.load("feeds", "1");

    function initialize() {
      var feed = new google.feeds.Feed("http://www.susu.tv/series/performing-arts/feed/");
      feed.load(function(result) {
        if (!result.error) {
          var container = document.getElementById("feed");
          for (var i = 0; i < result.feed.entries.length; i++) {
            var entry = result.feed.entries[i];
            var div = document.createElement("div");
            var h = document.createElement("h4");
            var p = document.createElement("p");
            var link = document.createElement("a");
            link.setAttribute('href', entry.link);
            link.appendChild(document.createTextNode(entry.title));
            h.appendChild(link);
            p.appendChild(document.createTextNode(entry.contentSnippet));
            div.appendChild(h);
            div.appendChild(p);
            container.appendChild(div);
          }
        }
      });
    }
    google.setOnLoadCallback(initialize);

    </script>
        <!-- Fav and touch icons -->
<!-- status bar -->
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<!-- hide safari chrome -->
<meta name="apple-mobile-web-app-capable" content="yes" />
<link rel="apple-touch-startup-image"
      href="apple-touch-startup-image-640x920.png" />
<link rel="apple-touch-icon-precomposed" href="touch-icon-iphone.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="touch-icon-ipad.png" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="touch-icon-iphone-retina.png" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="touch-icon-ipad-retina.png" />

</head>

<body>

<div class="nav">
	<ul>
    	<li><a href="#top" class="scroll">Top</a></li>
        <li><a class="scroll" href="#getinvolved">Get Involved</a></li>
        <li><a href="#theatrical" class="scroll">Theatrical</a></li>
        <li><a href="#music" class="scroll">Music</a></li>
        <li><a href="#dance" class="scroll">Dance</a></li>
        <li><a href="#tech" class="scroll">Tech</a></li>
        <li><a href="#committee" class="scroll">Your Committee</a></li>
        <li><a href="#resources" class="scroll">Resources</a></li>
        </ul>
        <div class="social">
        </div>
</div>
<div id="top" class="masthead">
<img src="images/pa.png" width="320" height="227" alt="SUSU Performing Arts" />

<h3>Theatre | Music | Dance | Tech</h3>

</div><br />
<div class="main">

	<div id="getinvolved" class="sectionheader">
    
    	<h1>Get Involved</h1>
    </div>
	<!--<div class="row"> -->
	<div class="block">
    	<h2>Open to All</h2>
        <hr width="90%" size="1" />
        <p>Performing Arts at SUSU is open to all union members, there are tonnes of ways to get involved from auditions, to free taster sessions and of course coming along to see a show!</p>
    </div>
    <div class="block">
    <h2>The Bunfight</h2>
    <hr width="90%" size="1" />
    <p>Look out for the Performing Arts tent at the bunfight on the grass outside The Stags.</p>
    <p>Inside you'll find all the Performing Arts societies and tonnes of friendly people to answer your questions</p>
    </div>

    <div class="block">
    	<h2>Latest Media</h2>
    	<hr width="90%" size="1" />
    	<div id="feed"></div>
    </div>

<div class="block">
    <h2>Follow Us</h2>
    <hr width="90%" size="1" />
    <div class="social">
  <a href="http://www.facebook.com/SUSUPerformingArts"><img src="images/fbtransparent.png" width="30" height="30" alt="Facebook" /></a>
  <a href="http://www.twitter.com/SUSUperform"><img src="images/twittertransparent.png" width="30" height="30" alt="Twitter" /></a>
  </div>
   </div>
    
    <div id="theatrical" class="sectionheader">
    <h1>Theatrical Societies</h1>
    </div>
    
    <?php
	$query="SELECT * FROM freshers_societies WHERE type='t'";
	include "database-connect.php";
	$num=mysql_numrows($result);
	for($i=0;$i<$num;$i++){
		$title = mysql_result($result,$i,"name");
		$text = mysql_result($result,$i,"text");
		$web = mysql_result($result,$i,"web");
		$image = mysql_result($result,$i,"image");
		$facebook = mysql_result($result,$i,"facebook");
		$twitter = mysql_result($result,$i,"twitter");
		echoSoc($title,$text,$image,$web,$facebook,$twitter);
	}
	?>
    
    
    <div id="music" class="sectionheader">
    <h1>Music Societies</h1>
    </div>
   <?php
	$query="SELECT * FROM freshers_societies WHERE type='m'";
	include "database-connect.php";
	$num=mysql_numrows($result);
	for($i=0;$i<$num;$i++){
		$title = mysql_result($result,$i,"name");
		$text = mysql_result($result,$i,"text");
		$web = mysql_result($result,$i,"web");
		$image = mysql_result($result,$i,"image");
		$facebook = mysql_result($result,$i,"facebook");
		$twitter = mysql_result($result,$i,"twitter");
		echoSoc($title,$text,$image,$web,$facebook,$twitter);
	}
	?>
    
    <div id="dance" class="sectionheader">
    <h1>Dance Societies</h1>
    </div>
       <?php
	$query="SELECT * FROM freshers_societies WHERE type='d'";
	include "database-connect.php";
	$num=mysql_numrows($result);
	for($i=0;$i<$num;$i++){
		$title = mysql_result($result,$i,"name");
		$text = mysql_result($result,$i,"text");
		$web = mysql_result($result,$i,"web");
		$image = mysql_result($result,$i,"image");
		$facebook = mysql_result($result,$i,"facebook");
		$twitter = mysql_result($result,$i,"twitter");
		echoSoc($title,$text,$image,$web,$facebook,$twitter);
	}
	?>
    
    <div id="tech" class="sectionheader">
    <h1>StageSoc</h1>
    </div>
    
    <div class="block wide">
   	<p>StageSoc is the SUSU backstage society.  We cover the complete range of theatrical backstage crafts; from lighting to sound and stage work, and are responsible for providing complete technical support for SUSU Performing Arts.
We are involved in a huge number of productions every year; we exceeded 20 shows last year, each with different requirements and challenges.</p><p>  It isn't all work, when we aren't involved in productions, we have weekly social events making us a friendly and busy society.</p><p>
If you have been involved backstage before or want to try something new, come along to our welcome meeting at 7.30 Tuesday 2nd October in the Annex Theatre to find out more.</p>
<div class="social">
		<a href="http://stagesoc.susu.org/"><img src="images/website.png" width="30" height="30" alt="Web" /></a>
			<a href="http://www.facebook.com/stagesoc"><img src="images/fbtransparent.png" width="30" height="30" alt="Facebook" /></a>
            </div>
</div>
    <div id="committee" class="sectionheader">
    
    	<h1>Your Committee</h1>
    </div>
    
    <?php
		echoCommittee("Claire Gilbert", "Performing Arts Officer", "claire.jpg", "<p>Claire's Bio</p>","perform@susu.org");
				echoCommittee("Josh Tipping", "Dance Societies Rep", "josh.png", "<p>Josh's Bio</p>","dance@susuperformingarts.org");
				echoCommittee("Hannah Holliday", "Theatrical Societies Rep", "hannah.png", "<p>Hannah's Bio</p>", "theatrical@susuperformingarts.org");
				echoCommittee("Peter Bridgwood", "Music Societies Rep", "peter.png", "<p>Peter's Bio</p>","music@susuperformingarts.org");
				echoCommittee("Peter 'Peewee' Ward", "Marketing Officer", "peewee.png", "<p>Peewee's Bio</p>", "marketing@susuperformingarts.org");
				echoCommittee("Jade Thompson", "Events Officer", "jade.png", "<p>Jade's Bio</p>","events@susuperformingarts.org");
				echoCommittee("Matt Hicken", "Financial Officer", "matt.png", "<p>Matt's Bio</p>", "finance@susuperformingarts.org");
				echoCommittee("Shane Murphy", "VP Student Engagement", "shane.png", "<p>Shane's Bio</p>","vpengage@susu.org");
				echoCommittee("James Mozden", "StageSoc President", "mozy.png", "<p>Mozy's Bio</p>", "president@stagesoc.org.uk");
				echoCommittee("Joe Hart", "Web Officer", "joe.png", "<p>Joe is a second year Computer Science student who is involved with Theatre Group, Comedty Society and Showstoppers. He does a bit too much PA and too little of his degree.</p>", "web@susuperformingarts.org");

	?>
        <div id="resources" class="sectionheader">
    
    	<h1>Resources</h1></a></div>
            <div class="block">
    <h2>Google Docs</h2>
    <hr width="90%" size="1" />
    <p>Here is a link to Performing arts resources. In there you'll find documents on how to make a budget, budget templates, claim forms and much much more.</p>
    <p><a href="https://docs.google.com/folder/d/0B8QnE0Kdupy-ektjS1ZtTGV6akU/edit?pli=1">PA Documents</a></p>
    </div>
    </div>
    
</body>
</html>
<?php
	function echoCommittee($name, $position, $image, $blurb, $email){
			    echo '<div class="block">';
    			echo '<img src="images/'.$image.'" alt="'.$position.'" class="committee" />';
    echo '<h2>'.$name.'</h2>';
    echo '<h4>'.$position.'</h4>';
	echo '<h4><a href="mailto:'.$email.'">'.$email.'</a></h4>';
   /* echo '<hr width="90%" size="1" />';
    echo $blurb;*/
    echo '</div>';
	}
	
	function echoBlock($title,$text){
		    echo '<div class="block">';
    echo '<h2>'.$title.'</h2>';
    echo '<hr width="90%" size="1" />';
    echo $text;
    echo '</div>';
	}
	
	function echoSoc($title, $text, $image, $web, $facebook, $twitter){
		echo '<div class="block">';
		echo '<h2>'.$title.'</h2>';
		echo '<hr width="90%" size="1">';
		if($image!=""){
			echo '<img class="right" src="/images/logos/'.$image.'" alt="'.$title.'" />';
		}
		echo $text;
		echo '<div class="social">';
		if($web!=""){
			echo '<a href="'.$web.'"><img src="images/website.png" width="30" height="30" alt="Twitter" /></a>';
		}
		
		if($twitter!=""){
			echo '<a href="http://www.twitter.com/'.$twitter.'"><img src="images/twittertransparent.png" width="30" height="30" alt="Twitter" /></a>';
		}
		
		if($facebook!=""){
			echo '<a href="'.$facebook.'"><img src="images/fbtransparent.png" width="30" height="30" alt="Facebook" /></a>';
		}
		echo '</div>';
		
		echo '</div>';
	} 
?>