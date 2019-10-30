<?php

if ( REGISTRATION_ENABLED ) {

	$app->route('/create-account', function() use ($app) {
		
		if ( !$app->auth->isLoggedIn() ) {
	    
		    if ( $app->action('post') ) {
		    	
				if ( $app->createAccount($_POST['email'], $_POST['password'], $_POST['repeat_password'], false) ) {
					
					$app->view('create-account', [
						'success' => true
					]);				
					
				} else {
					$app->view('create-account', [
						'errors' => $app->errors
					]);
				}
		    	
		    } else {
		    		$app->view('create-account');
		    }
	    
	    } else {
		    $app->redirect('/profile');
	    }
	    
	});
	
	$app->route('/create-account/verify/{selector}/{token}', function($params) use ($app) {
		
		if ( $app->confirmUser($params['selector'], $params['token']) ) {
	
			$app->redirect('/login', false, [
				'confirmed' => true
			]);
	
		} else {
			
			$app->view('create-account', [
				'errors' => $app->errors
			]);
		}
		
	});

} else{
	$app->http(404);
}