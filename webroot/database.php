<?php
	/**

	THIS FILE IS FOR LEGACY SUPPORT
	USE THE NEW NAMESPACED VERSION WITH COMPOSER:
	PA\Database::getSiteDatabase($type = SQLI);
	**/

$root = $_SERVER["DOCUMENT_ROOT"];
require_once $root . "/compose.php";


if(!class_exists('PerformDatabase')){


	class PerformDatabase extends PA\Database {
		// Legacy instance methods
		public function getPerformSite($type = self::USE_SQLI){
		    return self::get(self::DB_SITE_NAME, $type);
		}

		public function getPerformArchive($type = self::USE_SQLI){
		    return self::get(self::DB_ARCHIVE_NAME, $type);
		}
	}


}
