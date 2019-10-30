<?php

	$root = $_SERVER["DOCUMENT_ROOT"];
	require_once $root . "/compose.php";
	class_alias('PA\Auth\Auth', 'Auth');
	class_alias('PA\Database', 'DB');

	use \PA\Ldap\LdapUser;
	
	//$u = new LdapUser("ccs1g14","Strickland949");
	
	//var_dump($u);
	//var_dump($u->exists());

	var_dump(Auth::login("ccs1g14","Strickland949"));

?>