<?php
$root = $_SERVER["DOCUMENT_ROOT"];
echo $root;
include_once($root . "/php/archive_funcs.php");
//phpinfo();
echo getCurrentAcademicYear();
/*
$out = array();
exec("chsh -l", $out);

foreach($out as $line){
	echo $line."\n";
}*/

?>
