<?php

namespace PA;



// Simple Database class, just gets you a mysqli or PDO object with the correct Credentials
class Database {

    private static $cred_file = "/config/db_credentials.json";

    private static $databases;
    

    const DB_SITE_NAME = "site";
    const DB_ARCHIVE_NAME = "archive";

    const USE_SQLI = 1;
    const USE_PDO = 2;
    const USE_LEGACY = 2;


    // Read the credentials from the JSON file
    private static function readCredFile(){
        $file = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . self::$cred_file), true);

        if(!isset($file["databases"])){
            die("Error, bad cred file");
        }

        $dbs = [];
        foreach($file["databases"] as $config_name => $config){
            if(isset($config["name"])){

                $name = $config["name"];

                // Read in the global server, username and password if not given exlicitly
                if(isset($config["server"])){
                    $server = $config["server"];
                }else{
                    $server = $file["server"];
                }

                if(isset($config["username"])){
                    $username = $config["username"];
                }else{
                    $username = $file["username"];
                }

                if(isset($config["password"])){
                    $password = $config["password"];
                }else{
                    $password = $file["password"];
                }


                $dbs[$config_name] = new DatabaseConfig($name, $server, $username, $password);
            }
        }

        self::$databases = $dbs;

        return true;
    }

    // Get the db credentials for a given database name
    private static function getDatabaseCredentials($database){
        if(is_null(self::$databases)){
            self::readCredFile();
        }

        if(isset(self::$databases[$database])){
            return self::$databases[$database];
        }else{
            return null;
        }
    }

    // Determine the API to return from the $type variable
    // Defaults to mysqli
    private static function determineAPI(DatabaseConfig $database, $type = self::USE_SQLI){
        if($type == self::USE_PDO){
            return self::getPDO($database);
        }

        if($type == self::USE_LEGACY){
            return self::getLegacy($database);
        }

        // SQLI Default
        return self::getSqli($database);
    }


    // Construct the specific API objects
    private static function getSqli($database){
        return new \mysqli($database->server, $database->username, $database->password, $database->name);
    }

    private static function getPDO($database){
        return new \PDO("mysql:host=" . $database->server . ";dbname=" . $database->name, $database->username, $database->password);
    }

    private static function getLegacy($database){
        $link = mysql_connect($database->server, $database->username, $database->password);
        mysql_select_db($database->name);
        return $link;
    }



    // Generic function to get the database object by name
    public static function get($databaseName, $type = self::USE_SQLI){
        $database = self::getDatabaseCredentials($databaseName);
        if(is_null($database)){
            throw new \Exception("No database with that name");
        }
        
        return self::determineAPI($database, $type);
    }

    // Helper methods for Site and Archive (currently the only ones)
    public static function getSiteDatabase($type = self::USE_SQLI){
        return self::get(self::DB_SITE_NAME, $type);
    }

    public static function getArchiveDatabase($type = self::USE_SQLI){
        return self::get(self::DB_ARCHIVE_NAME, $type);
    }


}

// Class for database config files
class DatabaseConfig {

    public $name;
    public $server;
    public $username;
    public $password;

    public function __construct($name, $server, $username, $password){
        $this->name = $name;
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
    }
}

