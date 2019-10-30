<?php

/*** UoS LADP accessor, generic soton connection settings
 * @author Corin Chaplin (cdc1g12) corindc@gmail.com
 */

namespace PA\Ldap;


// Class for accessing user information via LDAP
abstract class LdapConnect {
	
	protected $ldaphost = "ldaps://nlbldap.soton.ac.uk";
	protected $dn = "OU=User,DC=soton,DC=ac,DC=uk";

	protected $ldapconn;



	function __construct(){
		// Connect
		$this->ldapconn = ldap_connect($this->ldaphost);
	}

}

