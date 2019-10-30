<?php
#error_reporting(-1);
#ini_set('display_errors', 'On');

$ldaphost = "ldaps://nlbldap.soton.ac.uk";

$uname;
$pw;
if(isset($_POST["username"]) && $_POST["username"] !== "" && isset($_POST["password"]) && $_POST["password"] !== ""){
	$uname = $_POST["username"];
	$pw = $_POST["password"];

	// Connect to the ldap
	$ldapconn = ldap_connect($ldaphost);

	// Search for the user
	$dn = "OU=User,DC=soton,DC=ac,DC=uk";
	$filter = "(&(objectClass=person)(cn=$uname))";

	$srch = ldap_search($ldapconn, $dn, $filter);

	$entries = ldap_get_entries($ldapconn, $srch);

	$ndn = $entries[0]["distinguishedname"][0];

	$result = ldap_bind($ldapconn, $ndn, $pw);

	if($result){
		echo "Yes";
	}else{
		echo "No";
	}


}


?>
