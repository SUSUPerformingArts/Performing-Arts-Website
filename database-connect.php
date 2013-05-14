<?php
include 'database_vars.php';

$mysql_connection = mysql_connect($server,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");
$result=mysql_query($query);

$last_insert_id=mysql_insert_id($mysql_connection);

mysql_close();
?>