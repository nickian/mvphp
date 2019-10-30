<?php

// Reirect TO first parameter FROM second parameter (the request)
// If you use $app->redirect('somewhere') with only a "to" parameter, it will
// simply forward you there, regardless of the request, but won't send a 301 header.

// Example 301 redirect: This will redirect requests to "/register" to "/create-account"
$this->redirect('/create-account', '/register');

$this->redirect('/login', '/sign-in');

$this->redirect('/recover', '/forgot-password');