<?php
$area = "Societies";
include 'header.php';

$id = $_GET["id"];

	$query="SELECT * FROM pa_societies WHERE id=$id";
	include "database-connect.php";
	$name = mysql_result($result,$i,"name");
	$subtitle = mysql_result($result,$i,"subtitle");
	$description = mysql_result($result,$i,"description");
	$website = mysql_result($result,$i,"website");
	$facebook = mysql_result($result,$i,"facebook");
	$twitter = mysql_result($result,$i,"twitter");

echo '<div class="row-fluid">';
echo '<div class="well">';
echo '<h1>'.$name.'</h1>';
echo '<h2>'.$subtitle.'</h2>';
echo '</div>';
echo '</div>';

echo '<div class="row-fluid">';
echo '<div class="span8">';
echo $description;
echo '</div>';
echo '<div class="span4">';
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
echo '<div class="row-fluid">';
echo '<h2>Upcoming Events</h2>';
echo '<div id="whatson"></div>';
echo '</div>';



include 'footer.php';
?>