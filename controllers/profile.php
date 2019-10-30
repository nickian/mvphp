<?php

$app->route('/profile', function() use ($app) {

	echo '<p>Create a profile page here</p>';

	if ( $app->auth->isLoggedIn() ) {
	    echo '<p>User is logged in: '. $app->auth->getEmail() . '</p>';
	}
	else {
	    echo '<p>Not logged in.</p>';
	}

});