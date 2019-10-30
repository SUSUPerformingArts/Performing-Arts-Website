<?php

function api_societies_addUrls($society){
    $app = \Slim\Slim::getInstance();
    $society['societyUrl'] = $app->request->getUrl() . $app->urlFor("society", [ "society" => $society['slug'] ]);
    #$society['uri'] = rtrim($app->request->getUrl() . $app->urlFor("api_society", [ "society" => $society['id'], "type" => null ]), ".");


    return $society;
}

function api_societies_addImages($society){
    $app = \Slim\Slim::getInstance();
    $img = soc_getPreferredImage($society['id']);
    if($img){
    	$society['image'] = $app->request->getUrl() . $img;
    }

    return $society;
}

function api_societies_addAllExtraData($society){
    return api_societies_addImages(api_societies_addUrls($society));
}



$app->get(
    '/api/societies(\.:type)',
    function() use ($app){
        global $pd;
        // Get the database
        $db = $pd->getPerformArchive();

        // Set response as JSON
        $app->response->headers->set('Content-Type', 'application/json');

        $get = $app->request->get();
        $limit = (isset($get["limit"]) && is_numeric($get["limit"])) ? intval($get["limit"]) : null;

        $name = (isset($get["name"]) && $get["name"] != "") ? "%".trim($get["name"])."%" : null;
        $type = (isset($get["type"]) && $get["type"] != "") ? trim($get["type"]) : null;


        // DO SOME SHIT


        //$res = api_negotiateContent($searchResult);

        //echo $res;
        echo "null";
    }
)->name("api_societies");



