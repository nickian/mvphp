<?php

spl_autoload_register(function($class){
	$file = __DIR__.'/models/'.$class.'.php';
	if ( file_exists($file) ) {
		require_once($file);
	}
});

// Installer
if ( !file_exists('../config.php') ) {
	require_once('install/install.php');
	exit();

} else {

	if ( is_dir(__DIR__.'/vendor') ) {
		require_once('../vendor/autoload.php');
	} else {
		die('"vendor" directory is missing. Run "composer install" from the project root.');
	}

	require_once('config.php');

	if ( DEBUG ) {
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
	}

	$app = new MVPHP();
	
	ini_set('session.cookie_domain', '.'.APP_DOMAIN);
	
	$auth = new \Delight\Auth\Auth($app->db);
	$app->auth = $auth;
	
	// Now we can register additional class objects here
	// Make sure they are in the "models" folder
	
	// This class is used for creating PDF files
	$docs = new Documents();

	require_once('routes.php');

}