<?php

$app->post(
    '/api/auth/:soc(\.:type)',
    function($soc, $type = 'json') use ($app){
	$username = strtolower($app->request()->post("username"));
        $password = $app->request()->post("password");

	$login_result = Auth::login($username, $password);
	if (is_null($login_result)) {
		echo api_negotiateContent(array("success"=>"false"));
		return;
	}


	$login_result = $login_result['user'];

	unset($login_result['joinDate']);
	unset($login_result['firstLoginDate']);
	unset($login_result['loginCount']);
	unset($login_result['lastLogin']);
	
	$login_result['committee'] = in_array($soc, Auth::loadCurrentCommittees($login_result['id']));
	$login_result['success'] = true;

	$res = api_negotiateContent($login_result);

        echo $res;

    }
)->name("api_auth");


?>