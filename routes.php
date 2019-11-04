<?php

// This is subdomain
if ( $app->host_domain != APP_DOMAIN ) {

	// We have this subdomain registered in the hosts table
	if ( $app->getHost() ){

		// Subdomain Home
		$app->route('/', function() use ($app) {
			echo $this->host_name;
		});

		// Everything else on subdomain
		$app->route('/*', function() use ($app) {
		    // Return a 404 error
			$app->http(404);
		});

	// We don't have this subdomain in the hosts table
	} else {
		$app->http(404);
	}

// Primary Website
} else {

	// Home
	$app->route('/');

	// Login
	$app->route('/login');

	// Logout
	$app->route('/logout', function() use ($app) {
		if ( $app->auth->isLoggedIn() ) {
			$app->auth->logOut();
		}
		$app->redirect('/login');
	});

	// Create Account
	$app->route('/create-account');

	// Profile
	$app->route('/profile');

	// Admin Area
	$app->route('/admin');

	// API
	$app->route('/api');

	// Test
	$app->route('/test');

	// Everything else
	$app->route('/*', function() use ($app) {
	    // Return a 404 error
		$app->http(404);
	});

}