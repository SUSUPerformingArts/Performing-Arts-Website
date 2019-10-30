<SCRIPT LANGUAGE="javascript">
width = screen.width;
height = screen.height;
document.write("<B>You're set to "+width+ "X" +height+"</B>")
</SCRIPT>


<br>

<?php
$yo=$_GET['width'];
echo $yo;
?>

<br><br><br><br><br>



<HTML>
<TITLE>phpBuddy getting screen resolution</TITLE>
<HEAD>
<?
if(isset($HTTP_COOKIE_VARS["users_resolution"]))
$screen_res = $HTTP_COOKIE_VARS["users_resolution"];
else //means cookie is not found set it using Javascript
{
?>
<script language="javascript">
<!--
writeCookie();

function writeCookie()
{
var today = new Date();
var the_date = new Date("December 31, 2023");
var the_cookie_date = the_date.toGMTString();
var the_cookie = "users_resolution="+ screen.width +"x"+ screen.height;
var the_cookie = the_cookie + ";expires=" + the_cookie_date;
document.cookie=the_cookie
}
//-->
</script>
<?
}
?>
</HEAD>
<BODY>
<?php
echo "Your screen resolution is set at ". $screen_res;
?>
</BODY>
</HTML>

