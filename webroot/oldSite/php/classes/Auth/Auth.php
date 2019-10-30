<?php

namespace PA\Auth;

use \PA\Ldap\LdapUser;

if(!class_exists('PA\Auth\Auth')){

    // Start the PHP session (http://docs.slimframework.com/sessions/native/)
    @session_cache_limiter(false);
    @session_start();

    if(!isset($webroot)){
        $webroot = $_SERVER['DOCUMENT_ROOT'];
    }

    // Require the ldap stuff
    require_once $webroot . '/php/archive_funcs.php';



    class Auth {

        private function __construct(){}

        private static $pd = null;
        private static $db = null;
        private static function getPerformDatabase(){
            //if(is_null(self::$db) || !self::$db->ping()){
                self::$db = \PA\Database::getArchiveDatabase();
            //}

            return self::$db;
        }

        public static function getPermissions(){
            if(!self::isLoggedIn() || !isset($_SESSION['user']['permissions'])){
                return [];
            }
            return $_SESSION['user']['permissions'];
        }

        public static function getCurrentCommittees(){
            $perms = self::getPermissions();
            if(!$perms || !isset($perms['committees'])){
                return [];
            }
            return $perms['committees'];
        }

        public static function getShowsForProdTeams(){
            $perms = self::getPermissions();
            if(!$perms || !isset($perms['prod_team_shows'])){
                return [];
            }
            return $perms['prod_team_shows'];
        }

        public static function getShowsForCommitteePositions(){
            $perms = self::getPermissions();
            if(!$perms || !isset($perms['committee_shows'])){
                return [];
            }
            return $perms['committee_shows'];
        }

        public static function getEditableShows(){
            $prodTeam = self::getShowsForProdTeams();
            $commPost = self::getShowsForCommitteePositions();

            return array_unique(array_merge($prodTeam, $commPost));
        }

        public static function getUserInfo($inf = false){
            if(self::isLoggedIn()){

                if(!$inf){
                    return $_SESSION['user'];
                }else{

                    if(isset($_SESSION['user'][$inf])){
                        return $_SESSION['user'][$inf];
                    }else{
                        return null;
                    }

                }

            }else{
                return null;
            }
        }

        // Alias for isLoggedIn
        public static function getUserId(){
            return self::isLoggedIn();
        }

        // Checks if a user is currently logged on and returns the user id if true
        public static function isLoggedIn(){
            if(isset($_SESSION['user']) && isset($_SESSION['user']['id'])){
                return $_SESSION['user']['id'];
            }else{
                return false;
            }
        }


        /******* Committee realted functions *******/

        // Checks if a user is on any current committees
        public static function isOnCommittees(){
            return count(self::getCurrentCommittees()) > 0;
        }

        public static function isOnGivenCommittee($committeeId){
            return in_array($committeeId, self::getCurrentCommittees());
        }

        // Checks if a user is currently on PA committee
        public static function isOnPACommittee(){
            $perms = self::getPermissions();
            return isset($perms['PA_committee']);
        }

        // Is or ever has been a PA webmaster
        public static function isPAWebmaster(){
            $perms = self::getPermissions();
            return isset($perms['PA_webmaster']);
        }



        /******* Show related functions ********/
        // Can add a show
        public static function canAddShows(){
            return self::isOnCommittees() || self::isOnPACommittee();
        }

        // Is able to edit at least one show
        public static function canEditShows(){
            $perms = self::getPermissions();
            return count($perms['committee_shows']) > 0
                || count($perms['prod_team_shows']) > 0
                || isset($perms['PA_committee']);
        }

        public static function canEditGivenShow($showId){
            $perms = self::getPermissions();
            return in_array($showId, self::getEditableShows())
                || isset($perms['PA_committee']);
        }

        /****** Society ******/
        // Is able to edit at least one society
        public static function canEditSocieties(){
            $perms = self::getPermissions();
            return count($perms['committees']) > 0
                || isset($perms['PA_committee']);
        }
        // Can edit a given society
        public static function canEditGivenSociety($societyId){
            $perms = self::getPermissions();
            return (isset($perms['committees']) && in_array($societyId, $perms['committees']))
                || isset($perms['PA_committee']);
        }




        /*********** Checking functions **************/
        // Check if user is in the database
        // Returns the user array or null if doesn't exist
        public static function checkUserInDatabase($username){
            $db = self::getPerformDatabase();

            $query = "SELECT * FROM `Member` WHERE `iSolutionsUsername` = ?";

            $prep = $db->prepare($query);
            $prep->bind_param("s",
                $username);
            $prep->execute();
            $result = $prep->get_result();

            $user = $result->fetch_assoc();
            $result->free();

            return $user;
        }

        public static function checkUserIdExists($id){
            $db = self::getPerformDatabase();

            $query = "SELECT * FROM `Member` WHERE `id` = ?";

            $prep = $db->prepare($query);
            $prep->bind_param("i",
                $id);
            $prep->execute();
            $result = $prep->get_result();

            $user = $result->fetch_assoc();
            $result->free();

            return $user;
        }


        /************ Data acquisition functions for login ***********/
        // Get committees the user in is for the current year
        public static function loadCurrentCommittees($userId = null){
            if(is_null($userId)){
                $userId = self::isLoggedIn();
            }
            if(!$userId){
                return [];
            }

            $db = self::getPerformDatabase();
            $committees = [];

            // Get all the committees they have been in, showing the latest year they were a part of it
            $query = "SELECT `societyId`, MAX(`year`) AS 'mYear' FROM `SocietyMember`
                    WHERE `memberId` = ?
                    AND `committeePositionId` IS NOT NULL
                    GROUP BY `societyId`";

            $prep = $db->prepare($query);
            $prep->bind_param("i",
                $userId);
            $prep->execute();
            $result = $prep->get_result();

            $committe_years = [];
            while($row = $result->fetch_assoc()){
                $committe_years[$row['societyId']] = $row['mYear'];
            }
            $committee_results = $result->fetch_all(MYSQLI_ASSOC);

            $result->free();

            // They are not in a committee, skip this step!
            if(count($committe_years) == 0){
                return [];
            }

            // For each of those committees check if the year they were in it is the current committee year
            $query = "SELECT `societyId`, MAX(`year`) AS 'mYear' FROM `SocietyMember`
                    WHERE `societyId` IN (" . implode(",", array_keys($committe_years)) . ")
                    AND `committeePositionId` IS NOT NULL
                    GROUP BY `societyId`";


            $result = $db->query($query);

            while($row = $result->fetch_assoc()){
                if($committe_years[$row['societyId']] == $row['mYear'] || $committe_years[$row['societyId']] == ($row['mYear']+1)) {
                    $committees[] = $row['societyId'];
                }
            }

            $result->free();

            return $committees;
        }

        /** Gets a user's editable shows **/
        // Shows that they are on the production team for (roleTypeId = 1)
        public static function loadShowsForProdTeams($userId = null){
            if(is_null($userId)){
                $userId = self::isLoggedIn();
            }
            if(!$userId){
                return [];
            }


            $db = self::getPerformDatabase();
            $showIds = [];

            // Get shows they are in the prod team for
            $query = "SELECT sr.`showId` FROM `ShowRole` sr
                    INNER JOIN Role r ON sr.`roleId` = r.`id`
                    WHERE r.`roleAreaId` = 1 AND sr.`memberId` = ? GROUP BY sr.`showId`";


            $prep = $db->prepare($query);
            $prep->bind_param("i",
                $userId);
            $prep->execute();
            $result = $prep->get_result();

            while($row = $result->fetch_assoc()){
                $showIds[] = $row["showId"];
            }
            $result->free();

            return $showIds;
        }

        // Shows that they are on the society's committee
        public static function loadShowsForCommitteePositions($userId = null){
            if(is_null($userId)){
                $userId = self::isLoggedIn();
            }
            if(!$userId){
                return [];
            }

            $db = self::getPerformDatabase();
            $showIds = [];


            // Get shows from the committees they are on
            $query = "SELECT s.`id` FROM `Show` s
                INNER JOIN `SocietyMember` sp ON s.`societyId` = sp.`societyId`
                WHERE sp.`committeePositionId` IS NOT NULL AND sp.`memberId` = ?";

            $prep = $db->prepare($query);
            $prep->bind_param("i",
                $userId);
            $prep->execute();
            $result = $prep->get_result();

            while($row = $result->fetch_assoc()){
                if(!in_array($row['id'], $showIds)){
                    $showIds[] = $row["id"];
                }
            }

            $result->free();

            return $showIds;
        }



        /************* Session mangement functions *****************/
        // Caclulates the permissions a user has
        public static function calculatePermissions($userId){
            $db = self::getPerformDatabase();
            $perms = [];

            // Current PA Committee members are admins + webmasters are always admins
            $query = "SELECT pam.`id`, pam.`positionId` FROM `PA_CommitteeMember` pam, (SELECT MAX(`year`) 'mYear' FROM `PA_CommitteeMember` GROUP BY `year`) m
                    WHERE (pam.`year` = m.`mYear` OR pam.`positionId` = 11) AND pam.`memberId` = ?";

            $prep = $db->prepare($query);
            $prep->bind_param("i",
                $userId);
            $prep->execute();
            $result = $prep->get_result();

            $inPACommittee = $result->fetch_assoc();
            $result->free();

            if(isset($inPACommittee)){
                $perms["PA_committee"] = $inPACommittee["positionId"];
                if($inPACommittee["positionId"] == 11){
                    $perms["PA_webmaster"] = true;
                }
            }

            $perms["committees"] = self::loadCurrentCommittees($userId);

            $perms["prod_team_shows"] = self::loadShowsForProdTeams($userId);
            $perms["committee_shows"] = self::loadShowsForCommitteePositions($userId);

            return $perms;
        }

        // Populates the session variable for the user
        public static function populateUserSession(array $user){
            $user = derivePreferredName($user);
            $user['permissions'] = self::calculatePermissions($user['id']);

            // Add to session variable
            $_SESSION['user'] = $user;
        }

        // Refreshes the information in the session variable from database
        public static function refreshSessionVariable(){
            if(!isset($_SESSION['user'])){
                return false;
            }

            $userId = $_SESSION['user']['id'];

            $db = self::getPerformDatabase();

            $query = "SELECT * FROM `Member` WHERE `id` = ?";

            $prep = $db->prepare($query);
            $prep->bind_param("i",
                $userId);
            $prep->execute();
            $result = $prep->get_result();

            $user = $result->fetch_assoc();
            $result->free();

            unset($_SESSION['user']);
            self::populateUserSession($user);

            return true;
        }


        /******* Admin functions ***********/
        // Impersonate a given user by ID
        public static function impersonateUser($userId){
            // Remove the current session variable
            unset($_SESSION['user']);

            // Set the userId to the user to be impersonated
            $_SESSION['user'] = [ 'id' => $userId, 'impersonated' => true ];

            // Refresh session variable
            self::refreshSessionVariable();
        }


        /********** Database manipulation functions *************/
        // Creates a new user via the LDAP
        public static function createNewUser($username, $password = null){
            $ldapObj;
            if($username instanceof LdapUser){
                $ldapObj = $username;
            }else{
                // Create the ldap object thing
                $ldapObj = new LdapUser($username, $password);
            }

            if(!$ldapObj->exists()){
                return;
            }

            $firstName = $ldapObj->getFirstName();
            $lastName = $ldapObj->getLastName();
            $username = $ldapObj->getUsername();

            if(!isset($firstName) || !isset($lastName) || !isset($username) || strlen($firstName) === 0 || strlen($lastName) === 0 || strlen($username) === 0){
                throw new LDAPUserExecption();
            }


            $db = self::getPerformDatabase();

            // Add the new user to the database
            $query = "INSERT INTO `Member` (`iSolutionsUsername`, `firstName`, `lastName`, `joinDate`)
                    VALUES (?, ?, ?, NOW())";

            $prep = $db->prepare($query);
            $prep->bind_param("sss",
                $username, $firstName, $lastName);
            $prep->execute();
            $db->close();

            // Hack this in because effort
            return self::checkUserInDatabase($username);
        }


        public static function updateFirstLogin($id){
            $db = self::getPerformDatabase();

            // Add the new user to the database
            $query = "UPDATE `Member`
                    SET `firstLoginDate` = NOW()
                    WHERE `id` = ?";

            $prep = $db->prepare($query);
            $prep->bind_param("i",
                $id);
            $prep->execute();
            $db->close();

            return true;
        }

        public static function incrementLoginCounter($id){
            $db = self::getPerformDatabase();

            // Add the new user to the database
            $query = "UPDATE `Member`
                    SET `loginCount` = `loginCount` + 1,
                        `lastLogin` = NOW()
                    WHERE `id` = ?";

            $prep = $db->prepare($query);
            $prep->bind_param("i",
                $id);
            $prep->execute();
            $db->close();

            return true;
        }



        /******* Login and auth functions *************/
        // Login function
        // Returns an array containing the user array and whether the user is new on success
        // Returns null of failure
        public static function login($username, $password){

            // Check user exists
            $user = self::checkUserInDatabase($username);
            // Assume user is not new (does not exist in database OR has not logged in before)
            $newUser = false;
		$success = false;
            if(!is_null($user)){
                $success = self::verifyPassword($user, $password);
            }

            if(!$success){

                // Create the ldap object thing
                $ldapObj = new LdapUser($username, $password);
                $success = $ldapObj->exists() && $ldapObj->isValidated();

                if(!$success){
                    return null;
                }


                if(is_null($user)){
                    try{
                        // Attempt to create the user
                        $user = self::createNewUser($ldapObj);
                        $newUser = true;
                    }catch(LDAPUserExecption $e){
                        return [ 'newUser' => true, 'user' => null ];
                    }
                }

            }

            if(!$newUser){
                $newUser = !isset($user['firstLoginDate']);
            }



            // Success!
            // Populate the session
            self::populateUserSession($user);
            self::incrementLoginCounter($user['id']);

            if($newUser){
                self::updateFirstLogin($user['id']);
            }

            return [
                'newUser' => $newUser,
                'user' => $user
            ];
        }

        public static function logout(){
            // Kill the session variable
            if(isset($_SESSION["user"])){
                unset($_SESSION["user"]);
            }
        }


        // Authentication works by defining the role required
        // If the user satisfies that role or greater then
        // return true. If not return false.
        public static function authenticate($role, $params = []){
            switch ($role) {
                case 'is_logged_in':
                    return self::isLoggedIn();

                case 'can_edit_shows':
                    return self::canEditShows()
                        || self::authenticateAdmin();
                    break;

                case 'can_edit_this_show':
                    return self::canEditGivenShow($params['show'])
                        || self::authenticateAdmin();
                    break;

                case 'can_add_shows' :
                    return self::canAddShows()
                        || self::authenticateAdmin();

                case 'can_edit_societies' :
                    return self::canEditSocieties()
                        || self::authenticateAdmin();

                case 'can_edit_this_society' :
                    return self::canEditGivenSociety($params['society'])
                        || self::authenticateAdmin();

                case 'is_on_a_committee' :
                    return count(self::getCurrentCommittees())>0
                        || self::authenticateAdmin();

                // If no role is defined or unrecognised authenicate an admin to be safe
                case 'on_PA_committee' :
                case 'is_PA_webmaster' :
                    return self::authenticateAdmin($role);
                    break;
                default:
                    return self::authenticateAdmin();
                    break;
            }
        }

        // Auth admin priveledges - PA committee or a PA webmaster
        private static function authenticateAdmin($role = 'is_PA_webmaster'){
            switch ($role) {
                case 'on_PA_committee' :
                    if(self::isOnPACommittee()){
                        return true;
                    }

                // Trickle down
                // A webmaster is never denied
                case 'is_PA_webmaster' :
                default:
                    return self::isPAWebmaster();
                    break;
            }
        }



        // Generates a random hex value for one off passwords
        private static function generateRandomHex($length = 32){
            if(!isset($length) || intval($length) <= 8 ){
              $length = 32;
            }
            if (function_exists('random_bytes')) {
                return bin2hex(random_bytes($length));
            }
            if (function_exists('mcrypt_create_iv')) {
                return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
            }
            if (function_exists('openssl_random_pseudo_bytes')) {
                return bin2hex(openssl_random_pseudo_bytes($length));
            }
        }

        public static function generateOneOffToken($username){
            $db = self::getPerformDatabase();

            // Generate the password, and the the hash from that
            $hex = self::generateRandomHex();
            $hash = password_hash($hex, PASSWORD_DEFAULT);

            $user = false;
            if(is_numeric($username)){
                // Check by ID
                $user = self::checkUserIdExists($username);
            }else{
                $user = self::checkUserInDatabase($username);
            }

            if(!$user){
                throw new InvalidUserException();
            }

            $userId = $user['id'];

            $query = "SELECT `userId` FROM `OneOffTokens`
                    WHERE `userId` = ?";

            $prep = $db->prepare($query);
            $prep->bind_param("s",
                $userId);

            $prep->execute();

            $exists = $prep->get_result()->fetch_assoc();

            // Update or add, depending
            if($exists){
                $query = "UPDATE `OneOffTokens`
                        SET `token` = ?, `used` = 0, generateTimestamp = NOW()
                        WHERE `userId` = ?";
            }else{
                $query = "INSERT INTO `OneOffTokens` (`generateTimestamp`, `token`, `userId`)
                        VALUES (NOW(), ?, ?)";
            }

            $prep = $db->prepare($query);
            $prep->bind_param("si",
                $hash, $userId);

            $res = $prep->execute();
            if(!$res){
                return false;
            }

            // Return hex password to user
            return $hex;
        }

        public static function loginWithToken($username, $token){
            $db = self::getPerformDatabase();

            $user = self::checkUserInDatabase($username);

            // User doesn't exist
            if(!$user){
                return false;
            }

            $query = "SELECT `token`
                    FROM `OneOffTokens`
                    WHERE `userId` = ? AND `used` = 0";

            $prep = $db->prepare($query);
            $prep->bind_param("i",
                $user['id']);

            $prep->execute();
            $hash = $prep->get_result()->fetch_assoc();


            $authorised = password_verify($token, $hash['token']);

            if(!$authorised){
                return false;
            }

            // Authed, delete token
            $query = "DELETE FROM `OneOffTokens`
                    WHERE `userId` = ?";

            $prep = $db->prepare($query);
            $prep->bind_param("i",
                $user['id']);

            $res = $prep->execute();

            // If delete failed then auth false to be safe
            if(!$res){
                return false;
            }

            // Everyone is happy, populate the user session
            self::populateUserSession($user);
            self::incrementLoginCounter($user['id']);

            // Return success
            return true;
        }

        // Check a user has a password, will return false if the user does not exist
        public static function hasPassword($userId){
            $db = self::getPerformDatabase();

            $query = 'SELECT `userId`
                    FROM `MemberPasswords`
                    WHERE `memberId` = ?';
            $prep = $db->prepare($query);

            $prep->bind_param('i',
                $userId);

            $prep->execute();
            $result = $prep->get_result()->fetch_assoc();

            return isset($result);
        }


        public static function createPassword($userId, $password){
            $user = self::checkUserIdExists($username);
            if(!$user){
                throw new InvalidUserException();
            }


            $hasPassword = self::hasPassword($userId);

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $db = self::getPerformDatabase();

            // Update if password exists
            if($hasPassword){
                $query = 'UPDATE `MemberPasswords`
                        SET `hash` = ?, `updateTimestamp` = NOW()
                        WHERE `memberId` = ?';
            }else{
                $query = 'INSERT INTO `MemberPasswords` (`hash`, `memberId` `updateTimestamp`)
                        VALUES (?, ?, NOW())';
            }
            $prep = $db->prepare($query);

            $prep->bind_param('is',
                $hash, $userId);

            $res = $prep->execute();

            return $res;
        }

        public static function verifyPassword($userId, $password){
            $user = self::checkUserIdExists($userId);
            if(!$user){
                throw new InvalidUserException();
            }


            $db = self::getPerformDatabase();

            $query = 'SELECT `hash`
                    FROM `MemberPasswords`
                    WHERE `memberId` = ?';
            $prep = $db->prepare($query);

            $prep->bind_param('i',
                $userId);

            $prep->execute();

            $result = $prep->get_result()->fetch_assoc();

            // Does not even have a password!
            if(!isset($result)){
                return false;
            }

            $hash = $result['hash'];
            $authorised = password_verify($password, $hash);

            return $authorised;
        }


    }

    class LDAPUserExecption extends \Exception {}
    class InvalidUserException extends \Exception {}

}
