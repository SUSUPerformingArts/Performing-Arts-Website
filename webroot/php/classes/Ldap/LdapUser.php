<?php

/*** UoS LADP accessor, to verify users exist and a username/password combo
 * @author Corin Chaplin (cdc1g12) corindc@gmail.com
 */

namespace PA\Ldap;


class LdapUser extends LdapConnect {

	private $username;
	private $password;

	private $validated = false;
	private $exists = false;

	private $allData;
	private $udn;

	function __construct($username, $password = null){
		parent::__construct();

		$es = false;#function_exists("ldap_escape");

		$this->username = ($es)?ldap_escape($username):$username;

		if($password !== null && $password !== ""){
			$this->password = ($es)?ldap_escape($password):$password;
		}else{
			$this->password = null;
		}

		// Search for the user
		$filter = "(&(objectClass=person)(cn=" . $this->username ."))";

		$search = @ldap_search($this->ldapconn, $this->dn, $filter);

		// Username not valid
		if($search){
			$this->exists = true;

			$this->allData = @ldap_get_entries($this->ldapconn, $search)[0];

			// Get the user distinguished name
			$this->udn = $this->allData["distinguishedname"][0];

			if($this->password !== null){
				// Attempt to bind
				$this->validated = @ldap_bind($this->ldapconn, $this->udn, $this->password);
			}
		}
	}


	public function isValidated(){
		return $this->validated;
	}
	public function exists(){
		return $this->exists;
	}


	/* Data getters */
	// ALL
	public function getAllData(){
		return $this->allData;
	}

	// Names
	public function getFirstName(){
		return $this->allData["givenname"][0];
	}

	public function getLastName(){
		return $this->allData["sn"][0];
	}

	public function getFullName(){
		return $this->getFirstName() . " " . $this->getLastName();
	}

	// Uni info
	public function getUsername(){
		return $this->allData["cn"][0];
	}

	public function getIDNumber(){
		return $this->allData["employeeid"][0];
	}

	public function getDepartment(){
		return $this->allData["department"][0];
	}
}



