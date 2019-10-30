<?php	
	
if ( $app->auth->isLoggedIn() ) {	

	if ($app->auth->hasRole(\Delight\Auth\Role::ADMIN)) {
	
	    $app->route('/admin', function() use ($app) {
	
			$app->view('admin/dashboard');
	
	    });
	
	} else {
		$app->http('403');
	}
	
} else {
	$app->redirect('/login');
}	
