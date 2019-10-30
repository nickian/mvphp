<?php

// Internal API for logged in user
if ( $app->auth->isLoggedIn() ) {
	$auth = true;
// External API requires key
} else {
	$auth = false;
}
	
$headers = apache_request_headers();

// Authorized
if ( $auth == true || ( isset($headers['X-API-KEY']) && $headers['X-API-KEY'] == APP_KEY ) ) {
	
	// We're always returning JSON
	$app->sendHeader('json');
	
	// Get accounts
	$app->route('/api', function() use ($app) {
		
		$api = array(
            'version' => '1.0',
            'endpoints' => array()
        );
		
		echo json_encode($api);

	});

// Not authorized
} else {
	$app->http(401);
	exit();	
}