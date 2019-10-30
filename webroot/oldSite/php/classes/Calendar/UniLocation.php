<?php

namespace PA\Calendar;

$root = $_SERVER["DOCUMENT_ROOT"];
require_once $root . "/compose.php";

if (!class_exists("UniLocation")) {
   class UniLocation {
      
      public $appString;
      public $humanString;
      
      function __construct($as, $hs) {
         $this->appString = $as;
         $this->humanString = $hs;
      }
      
      public static function parse($string) {
         global $pd;
         $db = $pd->getPerformArchive();
         
         $query = "SELECT `name`, Y(`location`) as 'venueLat', X(`location`) as 'venueLon' FROM Venue WHERE name LIKE CONCAT('%',?,'%')";
         $prep = $db->prepare($query);
         $prep->bind_param("s", $string);
         $prep->execute();
         $result = $prep->get_result()->fetch_all(MYSQLI_ASSOC);
         
         if (count($result)>0) {
            return new UniLocation($result[0]['venueLat'].", ".$result[0]['venueLon'], $result[0]['name']);
         }
         
         
         
         if (substr_count($string, "/") === 1) {
            $parts = explode("/", $string);
            $left = trim($parts[0]);
            $right = trim($parts[1]);
            
            if (ctype_digit($left) && ctype_digit($right)) {
               $graph = new \Graphite();
               $uri = "http://id.southampton.ac.uk/room/$left-$right";
               $graph->load($uri);
               $data = $graph->resource($uri);
               
               if ($data->get("rdfs:label") != "[NULL]") {
                  return new UniLocation($data->get("geo:lat").",".$data->get("geo:long"), $data->all("rdfs:label")->join(" - "));
               }
            }
         }
         
         return new UniLocation($string, $string);
      }
   }
}


?>