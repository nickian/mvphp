<?php
/*
************************************************************************************

███╗   ███╗██╗   ██╗██████╗ ██╗  ██╗██████╗
████╗ ████║██║   ██║██╔══██╗██║  ██║██╔══██╗
██╔████╔██║██║   ██║██████╔╝███████║██████╔╝
██║╚██╔╝██║╚██╗ ██╔╝██╔═══╝ ██╔══██║██╔═══╝
██║ ╚═╝ ██║ ╚████╔╝ ██║     ██║  ██║██║
╚═╝     ╚═╝  ╚═══╝  ╚═╝     ╚═╝  ╚═╝╚═╝

MVPHP Version 1.0
https://github.com/nickian/mvphp
A simple, hackable framework for building Minimally Viable PHP web applications.

************************************************************************************
*/

class MVPHP {

	public $db;
	public $auth;
	public $uri; // URI string of request
	public $host_domain; // The full subdomain
	public $host_name; // Name of subdomain
	public $params; // array of request params
	public $login_intent;
	public $errors;
	public $routes;
	public $query_strings;
	public $settings;

	/*
	|--------------------------------------------------------------------------
	| Set a couple variables and create the database connection.
	|--------------------------------------------------------------------------
	*/

	public function __construct() {

		$this->uri = $_SERVER['REQUEST_URI'];
		$this->host_domain = $_SERVER['HTTP_HOST'];
		$this->host_name= explode('.', $this->host_domain)[0];
		$this->query_strings = [];
		$this->errors = [];
		$this->routes = ['registered' => [], 'match' => false];
		$this->login_intent = null;
		
		try {

		  $this->db = new PDO( 'mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASSWORD );
		  $this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

		} catch ( PDOException $e ) {

			echo 'Unable to connect to the database: '.$e;
			exit();

		}

		$this->initSettings();

	}


	///////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////

	//  CORE METHODS

	///////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////


	/*
	|--------------------------------------------------------------------------
	| Load settings from the database
	|--------------------------------------------------------------------------
	*/

	public function initSettings() {
		$sql = 'SELECT name, value FROM settings';
		$stmt = $this->db->prepare($sql);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$stmt->execute();
		$this->settings = [];
		foreach($stmt->fetchAll() as $setting) {
			$this->settings[$setting['name']] = $setting['value'];
		}
		include_once(APP_PATH.'/redirects.php');
	}


	/*
	|--------------------------------------------------------------------------
	| Look for the current subdomain in the database
	|--------------------------------------------------------------------------
	*/

    public function getHost() {
		$sql = 'SELECT * FROM hosts WHERE host = :host';
		$stmt = $this->db->prepare($sql);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$stmt->execute([
    		':host' => $this->host_name
		]);
		$host = $stmt->fetch();
		return $host;
    }


	/*
	|--------------------------------------------------------------------------
	| Routes - Determine the route and what to do with it, and push all query
	| strings back to global $_GET variables
	|--------------------------------------------------------------------------
	*/

    public function route($route, $f=false) {

	    // Save the registered route
	    $this->routes['registered'][] = $route;

	    // First, handle any additional query strings in the URI

		// Extract any query strings and send them to $this->query_strings
		$query_string = explode(
			'?',
			$this->uri
		);

		// If there are query strings...
		if ( count($query_string) > 1 ) {

			// Separate them
			$query_params = explode('&', $query_string[1]);
			$query_strings = [];

			// Parse the keys and the values
			foreach($query_params as $query_param) {

				$q = explode('=', $query_param);

					$query_strings[] = [
						'key' => $q[0],
						'value' => $q[1]
					];

			}

			// Push each of the queries back into the global $_GET variable
			foreach( $query_strings as $q ) {
				$_GET[$q['key']] = $q['value'];
			}

			$this->query_strings = $query_strings;
			$this->uri = $query_string[0];
		}

	    // Now determine what to do with the route

	    $this->params = explode('/', substr($this->uri, 1, strlen($this->uri)));

		// Break apart the URI
		$routes = explode(
			'/',
			substr( $route, 1, strlen($route) )
		);

		if ( in_array('*', $routes) ) {
			$f();
			return;
		}

		$route_count = count($routes);
		$uri_count = count($this->params);

		// Compare the number of parameters in the route with the URI
		if ( $route_count == $uri_count ) {

			// Create the URI/Parameter variable map
	        $map = [];

	        for ( $i = 0; $i < $route_count; $i++ ) {

				$route_component = $routes[$i];

                // This route component is variable
                if ( substr($route_component, 0, 1) == '{' ) {

	                // Get rid of the brackets
                    $route_component = substr($route_component, 1);
                    $route_component = substr($route_component, 0, strlen($route_component)-1);

					// Separate the route name from the data type
                    $route_components = explode(':', $route_component);

                    // Data type is defined
                    if ( count($route_components) > 1 ) {
	                    $constraint = $route_components[1];
	                    $name = $route_components[0];

	                // Data type is not defined, default to string
                    } else {
                   	    $constraint = 'string';
	                    $name = $route_component;
                    }

					// Alpha numeric string
                    if ( $constraint == 'string' ) {

                        if ( !preg_match('/^[a-zA-Z0-9._-]+$/', $this->params[$i]) ) {
                            return false;
                        } else {
                            $map[$name] = $this->params[$i];
                        }

					// Integer
                    } elseif ( $constraint == 'int' ) {

                        if ( !is_numeric($this->params[$i])) {
                            return false;
                        } else {
                            $map[$name] = $this->params[$i];
                        }

					// Regular expression
                    } elseif ( explode('=', $constraint)[0] == 'regex' ) {

	                    $pattern = explode( 'regex=', $constraint )[1];
	                    if ( !preg_match('/'.$pattern.'/', $this->params[$i]) ) {
		                    return false;
		                } else {
			                $map[$name] = $this->params[$i];
		                }
	                }

	            // Not a variable, look for literal match
	            } else {
	                if ( $route_component != $this->params[$i] ) {
		                return false;
	                }
	            }

			} // for

			// Exact match, no variables
			if ( $route == $this->uri ) {

				// This is a string, specifying a controller with a different name from route
				if ( gettype($f) == 'string' ) {

					if ( file_exists(APP_PATH.'/controllers/home.php') ) {

						$this->controller($f);
						return true;

					} else {
						return false;
					}

				// Nothing set for f, look for a controller that matches the route name
				} elseif ( !$f ) {

					// A blank route indicates the index/home
					if ( $route == '/' ) {

						if ( file_exists(APP_PATH.'/controllers/home.php') ) {

							$this->controller('home');
							return true;

						} else {
							return false;
						}

					} else {

						// Remove start and end slashses
						$controller = $this->sanitizeUri($this->uri);

						// Use the matching controller if the file exists
						if ( file_exists(APP_PATH.'/controllers/'.$controller.'.php') && !$this->routes['match'] ) {
							$this->routes['match'] = true;
							$this->controller($controller);

						} else {
							return false;
						}

					}

				// This is an anonymous function
				} elseif ( gettype($f) == 'object' ) {

					$f();

				} else {
					return false;
				}

			// Not an exact match, there are variables
			} else {
	           $f($map);
			}

		// Number of parameters in route don't match URI
		} else {

			if ( $route == '/' ) {
				return false;
			}

			$controller = $this->sanitizeUri($route);

			// Check if the route matches part of the URI
            if ( $this->inString( $controller, $this->sanitizeUri($this->uri) ) && !$f ) {

				// If the file exists, use the matching controller
				if ( file_exists(APP_PATH.'/controllers/'.$controller.'.php') ) {
					$this->controller($controller);
					return true;
				}

            } else {

	        	// Let's check if there's a wildcard
			    if ( in_array('*', $routes) && $f && !$this->routes['match'] ) {
				    $this->routes['match'] = true;
					$f();
					return true;
				}

				return false;

            }

		}

		exit();

    }

	/*
	|--------------------------------------------------------------------------
	| Remove slashes at the beginning and end of a URI/route
	|--------------------------------------------------------------------------
	*/

	public function sanitizeUri($uri) {

		// Remove initial slash
		if ( substr($uri, 0, 1) == '/' ) {
			$uri = substr($uri, 1);
		}

		// Remove ending slash
		if ( substr($uri, -1, 1) == '/' ) {
			$uri = substr($uri, 0, strlen($uri) - 1);
		}

		return $uri;

	}


	/*
	|--------------------------------------------------------------------------
	| Search for something in a string
	|--------------------------------------------------------------------------
	*/

    public function inString($needle, $haystack) {
        if ( strpos($haystack, $needle) !== false ) {
            return true;
        }
    }


	/*
	|--------------------------------------------------------------------------
	| Check to see if the number of endpoint paths deep provided match that
	| of the actual URI requested
	|--------------------------------------------------------------------------
	*/

	public function path($path) {

		if ( substr($path, 0, 1) == '/' ) {
			$path = substr($path, 1, strlen($path));
		}

		if ( substr($path, -1, 1) == '/' ) {
			$path = substr($path, 0, strlen($path)-1);
		}

		$exp_path = explode('/', $path);
		$n_path = count($exp_path);
		$n_params = count($this->params);

		if ( $n_params == $n_path ) {
			return true;
		}

	}


	/*
	|--------------------------------------------------------------------------
	| Internal redirect (relative URL, include initial backslash)
	|--------------------------------------------------------------------------
	*/

	public function redirect($to, $from=false, $params=false) {
		
		// Provided a full URL
		if ( $this->inString('http', $to) ) {
			$url = $to;
		// Provided a relative URL
		} else {
			// This is on a subdomain
			if ( $this->host_domain != APP_DOMAIN ) {
				$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://'.$this->host_domain.$to;
			// This on the main domain
			} else {
				$url = APP_URL.$to;
			}
		}

		// Check for query strings, append them
		if (! empty($params) ) {
			$query_string = http_build_query($params);
			$url = $url.'/?'.$query_string;
		}

		// This is an entry in redirects.php
		if ( $from && $to ) {
			if (  $this->uri == $from ) {
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: '.$url);
				exit();
			}
		// They only provided a "to" location
		} else {
			header('Location: '.$url);
			exit();
		}
	}


	/*
	|--------------------------------------------------------------------------
	| Send a header (before echoing the content)
	|--------------------------------------------------------------------------
	*/

	public function sendHeader($type) {
		$type = strtolower($type);
		// JSON content
		if ( $type == 'json' ) {
			$content = 'Content-Type: application/json';
		}
		return header($content);
	}


	/*
	|--------------------------------------------------------------------------
	| Load a file from the controllers directory
	|--------------------------------------------------------------------------
	*/

	public function controller($file, $vars=false) {
		global $app;
		if ($vars) {
			extract($vars);
		}
		$controller = APP_PATH.'/controllers/'.$file.'.php';
		if ( file_exists($controller) ) {
			require_once($controller);
		} else {
			return false;
		}
	}


	/*
	|--------------------------------------------------------------------------
	| Load a file from the views directory
	|--------------------------------------------------------------------------
	*/

	public function view($file, $vars=false) {
		global $app;
		if ($vars) {
			extract($vars);
		}
		require_once(APP_PATH.'/views/'.$file.'.php');
	}


	/*
	|--------------------------------------------------------------------------
	| Check if a specific $_GET variable is set
	|--------------------------------------------------------------------------
	*/
	public function query($string) {
		if ( isset($_GET[strtolower($string)]) ) {
			return true;
		}
	}


	/*
	|--------------------------------------------------------------------------
	| Test for the request method
	|--------------------------------------------------------------------------
	*/

	public function action($request_method) {
		if ( strtolower($request_method) == strtolower($_SERVER['REQUEST_METHOD']) ) {
			return true;
		}
	}


	/*
	|--------------------------------------------------------------------------
	| Return specific HTTP error status codes
	|--------------------------------------------------------------------------
	*/

	public function http($code) {
		if ( $code == 404 ) {
			header('HTTP/1.1 404 Not Found');
			$this->view('http_404');
		} elseif ( $code == 403 ) {
			header('HTTP/1.1 403 Forbidden');
			$this->view('http_403');
		} elseif ( $code == 401 ) {
			header("HTTP/1.1 401 Unauthorized");
			$this->view('http_401');
		}
	}

	public function dump($data, $desc=false) {
		if ( is_array($data) ) {
			echo '<hr/><pre>';
			print_r($data);
			echo '</pre><hr/>';
		} else {
			echo '<br/>';
			echo $data;
			echo '<br/>';
		}
	}


	/*
	|--------------------------------------------------------------------------
	| Decode JSON body request
	|--------------------------------------------------------------------------
	*/

	public function receiveJSON() {
		return json_decode(file_get_contents('php://input'), true);
	}


	/*
	|--------------------------------------------------------------------------
	| Validate Data
	|--------------------------------------------------------------------------
	*/

	public function validate($data, $type) {

		if ( $type == 'text' ) {

			if ( preg_match('/^[\w\-\,\.\!\;\:\/\"\?\'\%\^\{\}\[\]\*\#\&\$\@\(\)\r\n ]+$/', $data) ) {
				return true;
			} else {
				return false;
			}

		// Alpha, Num, Dash, Underscore, No Spaces
		} elseif ( $type == 'id' ) {

			if ( preg_match('/^[a-zA-Z]+[a-zA-Z0-9-_]+$/', $data) ) {
				return true;
			} else {
				return false;
			}

		// Email Address
		} elseif ( $type == 'email' ) {

			if ( filter_var($data, FILTER_VALIDATE_EMAIL) ) {
				return true;
			} else {
				return false;
			}

		// Phone Number
		} elseif ( $type == 'phone' ) {

			if ( preg_match('/^[0-9-+(). ]+$/', $data) ) {
				return true;
			} else {
				return false;
			}

		}

	}


	/*
	|--------------------------------------------------------------------------
	| Request via cURL
	|--------------------------------------------------------------------------
	*/

	public function curlRequest($url, $verb='get', $json=false, $headers=false, $body=false) {

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if ( is_array($headers) ) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		if ( strtolower($verb) == 'post' && $body ) {

			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

			if ( $json == true ) {
				//$headers[] = 'Content-Type: application/json; charset=utf-8';
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
			} else {
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
			}

		} elseif ( strtolower($verb) == 'get' ) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		}

		$response = curl_exec($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response = [
			'data' => $response,
			'status' => $http_status
		];
		curl_close($ch);
		return $response;

	}


	///////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////

	//  USER METHODS

	///////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////


	/*
	|--------------------------------------------------------------------------
	| Register
	|--------------------------------------------------------------------------
	*/

	public function confirmUser($selector, $token) {

		try {
		    $this->auth->confirmEmail($selector, $token);
		}
		catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
		    $this->errors[] = 'Invalid token';
		}
		catch (\Delight\Auth\TokenExpiredException $e) {
		    $this->errors[] = 'Token expired';
		}
		catch (\Delight\Auth\UserAlreadyExistsException $e) {
		    $this->errors[] = 'Email address already exists';
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
		    $this->errors[] ='Too many requests';
		}

		if ( empty($this->errors) ) {
			return true;
		} else {
			return false;
		}

	}


	/*
	|--------------------------------------------------------------------------
	| Remember the requested URL to forward to after login
	|--------------------------------------------------------------------------
	*/
		
	public function requireLogin() {
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://';
		$requested_url = $protocol.$this->host_domain.$this->uri;
		$_SESSION['login_intent'] = $requested_url;
		$this->redirect(APP_URL.'/login');
	}
	

	/*
	|--------------------------------------------------------------------------
	| Get the session variable to use after login to redirect user
	|--------------------------------------------------------------------------
	*/
		
	public function loginIntent() {
		
		$requested_url = $_SESSION['login_intent'];
		
		if ( isset($requested_url) ) {
			return $requested_url;
		} else {
			return false;
		}

	}


	/*
	|--------------------------------------------------------------------------
	| Login
	|--------------------------------------------------------------------------
	*/

	public function login($user, $password, $remember=false) {

		if ( $remember == true ) {
			$remember_duration = REMEMBER_DURATION;
		} else {
			$remember_duration = null;
		}

	    	try {

	    		// We are logging in with an email address
	    		if ( filter_var($user, FILTER_VALIDATE_EMAIL) ) {
	    			$this->auth->login($user, $password, $remember_duration);
	    		// We are logging in with a username
	    		} else {
	    			$this->auth->loginWithUsername($user, $password, $remember_duration);
	    		}

			return true;

	    	}

	    	catch (\Delight\Auth\InvalidEmailException $e) {
	    		$this->errors[] = 'Unable to login. Invalid email provided.';
	    	}

	    	catch (\Delight\Auth\UnknownUsernameException $e) {
	    	    $this->errors[] = 'Unable to login. Invalid user provided.';
	    	}

	    	catch (\Delight\Auth\InvalidPasswordException $e) {
	    	    $this->errors[] = 'Unable to login. Invalid password.';
	    	}

	    	catch (\Delight\Auth\EmailNotVerifiedException $e) {
	    	    $this->errors[] = 'Unable to login. User not verified.';
	    	}

	    	catch (\Delight\Auth\TooManyRequestsException $e) {
	    	    $this->errors[] = 'Unable to login. Too many requests.';
	    	}

	    	if (! empty($this->errors) ) {
		    	return false;
	    	}

	}


	/*
	|--------------------------------------------------------------------------
	| Generate a random string
	|--------------------------------------------------------------------------
	*/

	public function randomPassword() {
		return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),0,12);
	}


	/*
	|--------------------------------------------------------------------------
	| Check if a logged in user has admin privileges
	|--------------------------------------------------------------------------
	*/

	public function isAdmin() {
		if ($this->auth->hasRole(\Delight\Auth\Role::ADMIN)) {
			return true;
		}
	}


	/*
	|--------------------------------------------------------------------------
	| Regiseter a new user
	|--------------------------------------------------------------------------
	*/

	public function createAccount($email, $password, $password_repeat, $verified=false) {

		$this->errors = [];

		if (! $this->validate($email, 'email') ) {
			$this->errors[] = 'Invalid email address.';
		}

		if ( $password != $password_repeat ) {
			$this->errors[] = 'Your passwords do not match. Try again.';
		}

        	if (! empty($this->errors) ) {
	        	return false;
        	}

        // Register the user
        try {

	        // We need to send a verification email to user
			if (! $verified ) {

	            $user_id = $this->auth->register(
	            	$email,
	            	$password,
	            	null,
		        	function ($selector, $token) use ($email) {
						$this->sendEmail(
							false, // Name
							false, // From address.
							[
								'template' => 'account-confirmation', // template in view/email
								'subject' => 'Account Confirmation',
								'to' => [$email]
							],
							[ // Variables to pass into the template
								'selector' => $selector,
								'token' => $token,
							]
						);
		        	}
	            );

			// Set the user as verified automatically
			} else {

	            $user_id = $this->auth->register(
	            		$email,
	            		$$password,
	            		null,
					null
	            	);

			}

		    	// Now log in
		    	return true;

        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            $this->errors[] = 'Invalid email address.';
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            $this->errors[] = 'Invalid password.';
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            $this->errors[] = 'User already exists.';
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            $this->errors[] = 'Too many requests.';
        }

        	if (! empty($this->errors) ) {
	        	return false;
        	}


	}


	/*
	|--------------------------------------------------------------------------
	| Reset password request, send reset email
	|--------------------------------------------------------------------------
	*/

	public function sendRecoveryEmail($email) {
		try {

		    $this->auth->forgotPassword($email, function ($selector, $token) use ($email) {
				$this->sendEmail(
					false,
					false,
					[
						'template' => 'recover', // template in view/email
						'subject' => 'Recover Your Account',
						'to' => [$email]
					],
					[ // Variables to pass into the template
						'selector' => $selector,
						'token' => $token,
					]
				);
		    });

		}
		catch (\Delight\Auth\InvalidEmailException $e) {
		    $this->errors[] = 'Invalid email address';
		}
		catch (\Delight\Auth\EmailNotVerifiedException $e) {
		    $this->errors[] = 'Email not verified';
		}
		catch (\Delight\Auth\ResetDisabledException $e) {
		    $this->errors[] = 'Password reset is disabled';
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
		    $this->errors[] = 'Too many requests';
		}

		if ( empty($this->errors) ) {
			return true;
		} else {
			return false;
		}

	}


	/*
	|--------------------------------------------------------------------------
	| Check that this account can be reset
	|--------------------------------------------------------------------------
	*/

	public function verifyRecovery($selector, $token) {

		try {
		    if ( $this->auth->canResetPasswordOrThrow($selector, $token) ) {
			    return true;
		    }
		}
		catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
		    $this->errors[] = 'Invalid token';
		}
		catch (\Delight\Auth\TokenExpiredException $e) {
		    $this->errors[] = 'Token expired';
		}
		catch (\Delight\Auth\ResetDisabledException $e) {
		    $this->errors[] = 'Password reset is disabled';
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
		    $this->errors[] = 'Too many requests';
		}

		if (! empty($this->errors) ) {
			return false;
		}

	}


	/*
	|--------------------------------------------------------------------------
	| Do the password reset
	|--------------------------------------------------------------------------
	*/

	public function resetPassword($selector, $token, $password, $password_repeat) {

		if ( $password != $password_repeat ) {
			$this->errors[] = 'Your passwords do not match.';
			return false;
		}

		try {
		    if ( $this->auth->resetPassword($selector, $token, $password) ) {
			    return true;
		    }
		}
		catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
		    $this->errors[] = 'Invalid token';
		}
		catch (\Delight\Auth\TokenExpiredException $e) {
		    $this->errors[] = 'Token expired';
		}
		catch (\Delight\Auth\ResetDisabledException $e) {
		    $this->errors[] = 'Password reset is disabled';
		}
		catch (\Delight\Auth\InvalidPasswordException $e) {
		    $this->errors[] = 'Invalid password';
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
		    $this->errors[] = 'Too many requests';
		}

		if (! empty($this->errors) ) {
			return false;
		}

	}


	/*
	|--------------------------------------------------------------------------
	| Update account email address
	|--------------------------------------------------------------------------
	*/

	public function updateAccountEmail($new_email, $password) {
		try {
		    if ($auth->reconfirmPassword($_POST['password'])) {
		        $auth->changeEmail($_POST['newEmail'], function ($selector, $token) {
		            echo 'Send ' . $selector . ' and ' . $token . ' to the user (e.g. via email to the *new* address)';
		        });

		        echo 'The change will take effect as soon as the new email address has been confirmed';
		    }
		    else {
		        echo 'We can\'t say if the user is who they claim to be';
		    }
		}
		catch (\Delight\Auth\InvalidEmailException $e) {
		    die('Invalid email address');
		}
		catch (\Delight\Auth\UserAlreadyExistsException $e) {
		    die('Email address already exists');
		}
		catch (\Delight\Auth\EmailNotVerifiedException $e) {
		    die('Account not verified');
		}
		catch (\Delight\Auth\NotLoggedInException $e) {
		    die('Not logged in');
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
		    die('Too many requests');
		}
	}


	///////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////

	//  MAIL

	///////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////


	/*
	|--------------------------------------------------------------------------
	| Send an Email with SwiftMailer
	|--------------------------------------------------------------------------
	*/

	public function sendEmail($name=false, $from=false, $email=['template'=>'', 'subject'=>'', 'to'=>[]], $vars=false) {

        $transport = (new Swift_SmtpTransport(SMTP_SERVER, SMTP_PORT, SMTP_ENCRYPTION))
        ->setUsername(SMTP_USER)
        ->setPassword(SMTP_PASSWORD);

        $mailer = new Swift_Mailer($transport);

        $app_url = APP_URL;

		if ( $vars ) {
			extract($vars);
		}

        ob_start();
        require(APP_PATH.'/views/email/'.$email['template'].'.php');
        $body = ob_get_contents();
        ob_end_clean();

        	if (! $name ) {
	        	$name = SMTP_FROM_NAME;
        	}

        	if (! $this->validate($from, 'email') ) {
	        	$from = SMTP_FROM_EMAIL;
        	}

        $message = (new Swift_Message())
        ->setSubject($email['subject'])
        ->setFrom([$from => $name])
        ->setTo($email['to'])
        ->setBody($body, 'text/html');

        $result = $mailer->send($message);

	}


	///////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////

	//  FORMS

	///////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////


	/*
	|--------------------------------------------------------------------------
	| Render a form from the views/forms directory
	|--------------------------------------------------------------------------
	*/

	public function form($form, $attrs=false) {
		$form = APP_PATH.'/views/forms/'.$form.'.php';
		if ( file_exists($form) ) {
			require_once($form);
		}
	}

}
