<?php
	
$app->route('/', function() use ($app) {
	$app->view('home');
});

