<?php	
	
if ( $app->auth->isLoggedIn() ) {	

	if ($app->isAdmin()) {
	
	    $app->route('/admin', function() use ($app) {
	
			$app->view('admin/dashboard');
	
	    });
	
	} else {
		$app->http('403');
	}
	
} else {
	$app->requireLogin();
}	
