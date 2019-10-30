<?php
	
$app->route('/login', function() use ($app) {

	if ( !$app->auth->isLoggedIn() ) {

		// The login form has been submitted
	    if ( $app->action('post') ) {
	    	
	    	if ( isset($_POST['remember']) && $_POST['remember'] == 1 ) {
	    	    $remember = true;
	    	} else {
		    		$remember = null;
	    	}
	    	
	    	if ( $app->login($_POST['user'], $_POST['password'], $remember) ) {
		    	
		    		// Did the user make a request to a protected page before being sent to the login screen?
		    		if ( isset($_SESSION['initial_request']) ) {
			    		
		    			$this->redirect($_SESSION['initial_request']);
		    	
		    		} else {
		    	
		    			$this->redirect('/');
		    	
		    		}
		    	
	    	} else {
				$app->view('login', [
					'errors' => $app->errors
				]);
	    	}
	
		// Show the login form
	    } else {
	    	$app->view('login');
	    }
    
    } else {
	    $app->redirect('/');
    }

});


$app->route('/login/recover', function() use ($app) {

	$vars = null;

    if ( $app->action('post') ) {
	    
    	if ( $app->sendRecoveryEmail($_POST['email']) ) {	
    		$vars = [ 'success' => true ];
    	} else {
			$vars = [ 'errors' => $app->errors ];
    	}
    	
    }
	    
	$app->view('recover', $vars);

});


// Reset the password
$app->route('/login/recover/{selector}/{token}', function($params) use ($app) {
	
	if ( $app->action('post') ) {
		
			$vars = [
				'password_form' => true
			];
		
			if (! $app->resetPassword($params['selector'], $params['token'], $_POST['password'], $_POST['password_repeat']) ) {
				$vars['errors'] = $app->errors;
			}
		
			$app->view('recover', $vars);					
		
	// Show the password form
	} else {
		
		$vars = [
			'password_form' => true
		];
		
		// Make sure this is a valid token
		if (! $app->verifyRecovery($params['selector'], $params['token']) ) {
			$vars['errors'] = $app->errors;
		}
		
		$app->view('recover', $vars);
		
	}
	
});