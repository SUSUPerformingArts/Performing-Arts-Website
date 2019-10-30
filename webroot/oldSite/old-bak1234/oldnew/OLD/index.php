 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SUSU Performing Arts</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="nav">
	<ul>
    	<li><a href="#Top">Top</a></li>
        <li><a href="#getinvolved">Get Involved</a></li>
        <li><a href="#theatrical">Theatrical</a></li>
        <li><a href="#music">Music</a></li>
        <li><a href="#dance">Dance</a></li>
        <li><a href="#tech">Tech</a></li>
        <li><a href="#committee">Your Committee</a></li>
        </ul>
        <div class="social">
        </div>
</div>
    <a name="Top">
<div class="masthead">
<img src="images/pa.png" width="320" height="227" alt="SUSU Performing Arts" />

<h3>Theatre | Music | Dance | Tech</h3>

</div><a name="getinvolved"><br /></a>
<div class="main">

	<div class="sectionheader">
    
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
    <h2>Something Else</h2>
    <hr width="90%" size="1" />
    <p>Something else can go here about getting involved</p>
    </div>
    
    

	
    <a name="venues"><div class="sectionheader"></a>
    <br />
    <h1>Venues</h1>
    </div>
    
    <div class="block">
    <h2>Thumbnails</h2>
    <p>Of venues and links to their websites go here.</p>
    </div>
    
    <div class="sectionheader">
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
		$facebook = mysql_result($result,$i,"facebook");
		$twitter = mysql_result($result,$i,"twitter");
		echoSoc($title,$text,"",$web,$facebook,$twitter);
	}
	?>
    
    
    <div class="sectionheader">
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
		$facebook = mysql_result($result,$i,"facebook");
		$twitter = mysql_result($result,$i,"twitter");
		echoSoc($title,$text,"",$web,$facebook,$twitter);
	}
	?>
    
    <div class="sectionheader">
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
		$facebook = mysql_result($result,$i,"facebook");
		$twitter = mysql_result($result,$i,"twitter");
		echoSoc($title,$text,"",$web,$facebook,$twitter);
	}
	?>
    
    <div class="sectionheader">
    <h1>StageSoc</h1>
    </div>
    
    <div class="block wide">
   	<p>StageSoc is the SUSU backstage society.  We cover the complete range of theatrical backstage crafts; from lighting to sound and stage work, and are responsible for providing complete technical support for SUSU Performing Arts.
We are involved in a huge number of productions every year; we exceeded 20 shows last year, each with different requirements and challenges.</p><p>  It isn't all work, when we aren't involved in productions, we have weekly social events making us a friendly and busy society.</p><p>
If you have been involved backstage before or want to try something new, come along to our welcome meeting at 7.30 Tuesday 2nd October in the Annex Theatre to find out more.</p>

    <div class="sectionheader">
    
    	<h1>Your Committee</h1>
    </div>
    
    <?php
		echoCommittee("Claire Gilbert", "Performing Arts Officer", "claire.jpg", "<p>Claire's Bio</p>");
				echoCommittee("Josh Tipping", "Dance Societies Rep", "josh.png", "<p>Josh's Bio</p>");
				echoCommittee("Hannah Holliday", "Theatrical Societies Rep", "hannah.png", "<p>Hannah's Bio</p>");
				echoCommittee("Peter Bridgwood", "Music Societies Rep", "peter.png", "<p>Peter's Bio</p>");
				echoCommittee("Peter 'Peewee' Ward", "Marketing Officer", "peewee.png", "<p>Peewee's Bio</p>");
				echoCommittee("Jade Thompson", "Events Officer", "jade.png", "<p>Jade's Bio</p>");
				echoCommittee("Matt Hicken", "Financial Officer", "matt.png", "<p>Matt's Bio</p>");
				echoCommittee("Shane Murphy", "VP Student Engagement", "shane.png", "<p>Shane's Bio</p>");
				echoCommittee("James Mozden", "StageSoc President", "mozy.png", "<p>Mozy's Bio</p>");
				echoCommittee("Joe Hart", "Web Officer", "joe.png", "<p>Joe is a second year Computer Science student who is involved with Theatre Group, Comedty Society and Showstoppers. He does a bit too much PA and too little of his degree.</p>");

	?>
    
</body>
</html>
<?php
	function echoCommittee($name, $position, $image, $blurb){
			    echo '<div class="block">';
    			echo '<img src="images/'.$image.'" alt="'.$position.'" class="committee" />';
    echo '<h2>'.$name.'</h2>';
    echo '<h4>'.$position.'</h4>';
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
		echo $text;
		if($web!=""){
			echo '<p><a href="'.$web.'">Website</a></p>';
		}
		
		if($twitter!=""){
			echo '<p>Twitter: @'.$twitter.'</p>';
		}
		echo '</div>';
	} 
?>