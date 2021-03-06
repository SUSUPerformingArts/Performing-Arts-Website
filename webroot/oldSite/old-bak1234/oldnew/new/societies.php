<?php
$area = "Societies";
include 'header.php';
?>
<div class="row-fluid">
	<div class="well">
		<h1>Societies</h1>
	</div>
</div>
<div class="well">
<div class="row-fluid" id="theatrical">
	<h2>Theatrical</h2>
	<hr/>
</div>
<div class="row-fluid">
   <?php
	$query="SELECT * FROM pa_societies WHERE type='Theatrical' ORDER BY name";
	include "database-connect.php";
	$num=mysql_numrows($result);
	$count=0;
	for($i=0;$i<$num;$i++){
		$id = mysql_result($result,$i,"id");
		$name = mysql_result($result,$i,"name");
		$subtitle = mysql_result($result,$i,"subtitle");
		$description = mysql_result($result,$i,"description");
		$website = mysql_result($result,$i,"website");
		$facebook = mysql_result($result,$i,"facebook");
		$twitter = mysql_result($result,$i,"twitter");
		echoSoc($id,$name,$description,$logo,$website,$facebook,$twitter);
		if($count>2)
		{
			echo '</div><div class="row-fluid">';
			$count=0;
		}
		else
		{
			$count++;
		}
	}
	?>
</div>
</div>
<div class="well">
<div class="row-fluid" id="music">
	<h2>Music</h2>
	<hr/>
</div>
<div class="row-fluid">
   <?php
	$query="SELECT * FROM pa_societies WHERE type='Music' ORDER BY name";
	include "database-connect.php";
	$num=mysql_numrows($result);
	$count=0;
	for($i=0;$i<$num;$i++){
		$id = mysql_result($result,$i,"id");
		$name = mysql_result($result,$i,"name");
		$subtitle = mysql_result($result,$i,"subtitle");
		$description = mysql_result($result,$i,"description");
		$website = mysql_result($result,$i,"website");
		$facebook = mysql_result($result,$i,"facebook");
		$twitter = mysql_result($result,$i,"twitter");
		echoSoc($id,$name,$description,$logo,$website,$facebook,$twitter);
		if($count>2)
		{
			echo '</div><div class="row-fluid">';
			$count=0;
		}
		else
		{
			$count++;
		}
	}
	?>
</div>
</div>
<div class="well">
<div class="row-fluid" id="dance">
	<h2>Dance</h2>
	<hr/>
</div>
<div class="row-fluid">
   <?php
	$query="SELECT * FROM pa_societies WHERE type='Dance' ORDER BY name";
	include "database-connect.php";
	$num=mysql_numrows($result);
	$count=0;
	for($i=0;$i<$num;$i++){
		$id = mysql_result($result,$i,"id");
		$name = mysql_result($result,$i,"name");
		$subtitle = mysql_result($result,$i,"subtitle");
		$description = mysql_result($result,$i,"description");
		$website = mysql_result($result,$i,"website");
		$facebook = mysql_result($result,$i,"facebook");
		$twitter = mysql_result($result,$i,"twitter");
		echoSoc($id,$name,$description,$logo,$website,$facebook,$twitter);
		if($count>2)
		{
			echo '</div><div class="row-fluid">';
			$count=0;
		}
		else
		{
			$count++;
		}
	}
	?>
</div>
</div>
<div class="well">
<div class="row-fluid" id="tech">
	<h2>Tech</h2>
	<hr/>
</div>
<div class="row-fluid">
   <?php
	$query="SELECT * FROM pa_societies WHERE type='Tech' ORDER BY name";
	include "database-connect.php";
	$num=mysql_numrows($result);
	$count=0;
	for($i=0;$i<$num;$i++){
		$id = mysql_result($result,$i,"id");
		$name = mysql_result($result,$i,"name");
		$subtitle = mysql_result($result,$i,"subtitle");
		$description = mysql_result($result,$i,"description");
		$website = mysql_result($result,$i,"website");
		$facebook = mysql_result($result,$i,"facebook");
		$twitter = mysql_result($result,$i,"twitter");
		echoSoc($id,$name,$description,$logo,$website,$facebook,$twitter);
		if($count>2)
		{
			echo '</div><div class="row-fluid">';
			$count=0;
		}
		else
		{
			$count++;
		}
	}
	?>
</div>
</div>
<?php

include 'footer.php';

	function echoSoc($id,$name, $description, $logo, $website, $facebook, $twitter){
		echo '<div class="span3">';
		echo '<h3>'.$name.'</h3>';
		if($logo!=""){
			echo '<img class="span6 offset3" src="/img/logos/'.$logo.'" alt="'.$title.'" />';
		}
				echo '<div class="row-fluid">';
		if(strlen($description)>210){


		$description = substr($description,0,210);
		$description = $description."...";
	}
		echo $description;
		echo '</div>';
		echo '<div class="row-fluid">';
		//echo '<div class=" span8 offset6">';
		echo '<a class="btn btn-primary pull-right" href="society.php?id='.$id.'">Read More</a>';
		echo '</div>';
		echo '<div class="row-fluid">';
		echo '<table class="table span12 ovrflo-hidden borderless">';
		if($website!=""){
			if(strlen($website)>25)
			{
			$short = substr($website,0,22).'...';
			}
			else
			{
				$short = $website;
			}
			/* DIV CODE USING GRID
			echo '<div class="row-fluid">';
			echo '<div class="span2">';
			echo '<a href="'.$website.'"><img src="img/ico/web.png" width="30px" height="30px" alt="Twitter"  /></a>';
			echo '</div>';
			echo '<div class="span10 socialdiv">';
			echo '<a href="'.$website.'"><p class="social-link" id="fittext">'.$short.'</p></a>';
			echo '</div></div>';*/

			//CODE USING TABLES
			echo '<tr>';
			echo '<td>';
			echo '<a href="'.$website.'"><img src="img/ico/web.png" width="30px" height="30px" alt="Twitter"  /></a>';
			echo '</td>';
			echo '<td class="ovrflo-hidden">';
			echo '<a href="'.$website.'"><p class="social-link" id="fittext">'.$short.'</p></a>';
			echo '</td>';
			echo '</tr>';



		}
		
		if($twitter!=""){
			echo '<tr>';
			echo '<td>';
			echo '<a href="http://twitter.com/'.$twitter.'"><img src="img/ico/twitter.png" width="30px" height="30px" alt="Twitter"  /></a>';
			echo '</td>';
			echo '<td class="ovrflo-hidden">';
			echo '<a href="http://twitter.com/'.$twitter.'"><p class="social-link" id="fittext">@'.$twitter.'</p></a>';
			echo '</td>';
			echo '</tr>';
		}
		
		if($facebook!=""){
			if(strlen($facebook)>20)
			{
			$short = str_replace("http://www.facebook.com","fb.com",$facebook);
			$short = substr($short,0,18).'...';
			}
			else
			{
				$short = $facebook;
			}
			/* DIV CODE USING GRID
			echo '<div class="row-fluid">';
			echo '<div class="span2">';
			echo '<a href="'.$facebook.'"><img src="img/ico/web.png" width="30px" height="30px" alt="Twitter"  /></a>';
			echo '</div>';
			echo '<div class="span10 socialdiv">';
			echo '<a href="'.$facebook.'"><p class="social-link" id="fittext">'.$short.'</p></a>';
			echo '</div></div>';*/

			//CODE USING TABLES
			echo '<tr>';
			echo '<td>';
			echo '<a href="'.$facebook.'"><img src="img/ico/fb.png" width="30px" height="30px" alt="Twitter"  /></a>';
			echo '</td>';
			echo '<td class="ovrflo-hidden">';
			echo '<a href="'.$facebook.'"><p class="social-link" id="fittext">'.$short.'</p></a>';
			echo '</td>';
			echo '</tr>';
		}

		echo '</table>';
		echo '</div>';
		echo '</div>';

	} 
?>