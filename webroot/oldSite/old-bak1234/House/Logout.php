<?php
include 'header.php';
$_SESSION = array();
session_destroy();                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
?>

<?php
$_SESSION['username'] = "";
$_SESSION['password'] = "";
$_SESSION['loggedin'] = 0;
echo "<p><strong>Successfully logged out</strong></p>";
?>

<?php
echo '<meta http-equiv="refresh" content="1;URL=http://perform.susu.org/House/home.php">';
?>

<?php
include 'footer.php';
?>