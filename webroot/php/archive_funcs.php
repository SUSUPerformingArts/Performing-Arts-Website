<?php

// Gets the preferred and full name and adds it to the user object
function derivePreferredName($mem){
    $mem["preferredName"] = (!isset($mem["chosenName"]) || $mem["chosenName"] == null || $mem["chosenName"] == "")?$mem["firstName"]:$mem["chosenName"];
    $mem["fullName"] = $mem["preferredName"] . " " . $mem["lastName"];
    return $mem;
}

function getAcademicTurnMonth(){
    return 7; // REMEMBER TO CHANGE THE MYSQL FUNCTION TOO
}


function getCurrentAcademicYear(){
    $date = new DateTime();
    $n = getAcademicTurnMonth();
    $date->modify("-$n months"); // Move back so August 1st is January 1st
    return $date->format("Y");
}

function getStartofAcademicYear($year){
    $date = new DateTime();

    $date->setDate($year, getAcademicTurnMonth(), 1);
    $date->setTime(0, 0);
    return $date;
}

function getEndofAcademicYear($year){
    $date = new DateTime();

    $date->setDate($year + 1, getAcademicTurnMonth() - 1, 31);
    $date->setTime(23, 59);
    return $date;
}

function getAcademicYearForDate($datetime){
    if(is_int($datetime)){
        $date = new DateTime($datetime);
    }else{
        $date = new DateTime($datetime->getTimestamp());
    }

    $n = getAcademicTurnMonth();
    $date->modify("-$n months"); // Move back so August 1st is January 1st
    return $date->format("Y");
}

