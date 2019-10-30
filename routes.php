<?php

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