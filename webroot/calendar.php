<?php


// Autoload Google APIs
require_once $_SERVER["DOCUMENT_ROOT"] . "/compose.php";



Class PA_Google_Client {

	private static $KEY_FILE_LOC = "/config/google_client_secret.json";
	
	private static $CLIENT_EMAIL = "863805083480-q5qvqk1gfp914vcp5d1in32v0oe4q4ks@developer.gserviceaccount.com";

	private static $USER_TO_IMPERSONATE = 'calendar@susuperformingarts.org';

	private static $client = null;

	// This is terrible, all static, I hate php
	private function __construct(){}


	public static function getClientObject($scopes){
		if(isset(self::$client)){
			//return self::$client;
		}

		$key_file = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . self::$KEY_FILE_LOC), true);


		$credentials = new Google_Auth_AssertionCredentials(
		    self::$CLIENT_EMAIL,
		    $scopes,
		    $key_file["private_key"],
		    'notasecret',                                 // Default P12 password
		    'http://oauth.net/grant_type/jwt/1.0/bearer', // Default grant type
		    self::$USER_TO_IMPERSONATE
		);
		
		$client = new Google_Client();
		// Set the timeout
		$client->setClassConfig('Google_IO_Curl', 'options',
		    [
		        @CURLOPT_CONNECTTIMEOUT => 20,
		        @CURLOPT_TIMEOUT => 21
		    ]
		);


		$client->setAssertionCredentials($credentials);
		if ($client->getAuth()->isAccessTokenExpired()) {
		  $client->getAuth()->refreshTokenWithAssertion();
		}


		self::$client = $client;

		return $client;
	}

}

#PA_Google_Client::getClientObject(["https://www.googleapis.com/auth/calendar"]);


class PA_Google_Cal extends PA_Google_Client {
	
	private static $SCOPES = ["https://www.googleapis.com/auth/calendar"];

	private static $CALENDAR_SHOWS_ID = [
		3 => "susuperformingarts.org_jgmna537tum1nldn64as6hc57o@group.calendar.google.com",
		2 => "susuperformingarts.org_1jabh48u8bj9ebe9hdgofv3q6c@group.calendar.google.com",
		1 => "susuperformingarts.org_mor1d8vnf89lhqq5fdognd0js8@group.calendar.google.com",

		0 => "susuperformingarts.org_if0isi351k56qgjupri03gsjuk@group.calendar.google.com" // Default
	];

	private static $service = null;


	private function __construct(){}


	public static function getCalService(){
		if(isset(self::$service) && self::$service != null){
			return self::$service;
		}

		$client = parent::getClientObject(self::$SCOPES);

		$service = new Google_Service_Calendar($client);

		self::$service = $service;
		return $service;
	}

	public static function getEvent($calId = 0, $eventId){
		$calId = intval($calId);
		if(!isset(self::$CALENDAR_SHOWS_ID[$calId])){
			$calId = 0; // Set to default
		}
		$service = self::getCalService();

		$event = $service->events->get(self::$CALENDAR_SHOWS_ID[$calId], $eventId);
		return $event;
	}


	public static function createEvent($calId = 0, $eventArray){
		$calId = intval($calId);
		if(!isset(self::$CALENDAR_SHOWS_ID[$calId])){
			$calId = 0; // Set to default
		}
		$service = self::getCalService();

		$event = new Google_Service_Calendar_Event($eventArray);

		$event = $service->events->insert(self::$CALENDAR_SHOWS_ID[$calId], $event);		
		return $event;
	}


	public static function deleteEvent($calId = 0, $eventId){
		$calId = intval($calId);
		if(!isset(self::$CALENDAR_SHOWS_ID[$calId])){
			$calId = 0; // Set to default
		}
		$service = self::getCalService();

		$service->events->delete(self::$CALENDAR_SHOWS_ID[$calId], $eventId);
	}

	public static function editEvent($calId = 0, $eventId, $func){
		if(!is_callable($func)){
			throw new ErrorException("Function is not a good one");
		}

		$service = self::getCalService();

		$calId = intval($calId);
		if(!isset(self::$CALENDAR_SHOWS_ID[$calId])){
			$calId = 0; // Set to default
		}

		$event = $service->events->get(self::$CALENDAR_SHOWS_ID[$calId], $eventId);

		// Call the function to edit the object
		$func($event);

		// Commit the update
		$updatedEvent = $service->events->update(self::$CALENDAR_SHOWS_ID[$calId], $event->getId(), $event);

		return $updatedEvent;
	}


}

