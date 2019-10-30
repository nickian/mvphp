<?php
	
if (! is_dir( dirname(__DIR__, 1).'/vendor' ) ) {
	echo '<p>Before running this installer, make sure you have <a href="https://getcomposer.org" target="_blank">Composer</a> installed. Then run the "composer install" command in the root directory of this project to install all of the required dependencies.</p>';
	exit();
}

if (! is_dir( dirname(__DIR__, 1).'/node_modules' ) ) {
	echo '<p>Before running this installer, make sure you have Node.js installed. Then run "npm install" in the root directory of this project.</p>';
	exit();
}

if ( !is_dir( dirname(__DIR__, 1).'/public/css') || !is_dir( dirname(__DIR__, 1).'/public/js') || !is_dir( dirname(__DIR__, 1).'/public/webfonts') ) {
	echo '<p>Make sure you build the frontend first by running the "gulp" command in the project root.</p>';
	exit();
}

$app_domain = $_SERVER['HTTP_HOST'];
$app_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST'];
$app_path = dirname(__DIR__, 1);

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

	$errors = [];

	if (! $_POST['database_host'] ) {
			$errors[] = 'Database host is required.';
	}

	if (! $_POST['database_name'] ) {
			$errors[] = 'Database name is required.';
	}

	if (! $_POST['database_user'] ) {
			$errors[] = 'Database user is required.';
	}

	if (! $_POST['database_password'] ) {
			$errors[] = 'Database password is required.';
	}

	if (! $_POST['email'] ) {
			$errors[] = 'Admin email is required.';
	}

	if (! $_POST['password'] ) {
			$errors[] = 'Admin password is required.';
	}

	if (! $_POST['app_name'] ) {
			$errors[] = 'App name is required.';
	}

	if (! $_POST['app_path'] ) {
			$errors[] = 'App path is required.';
	}

	if (! $_POST['app_url'] ) {
			$errors[] = 'App URL is required.';
	}

	// No errors, try installing
	if ( empty($errors) ) {

		try {
			$db = new \PDO('mysql:dbname='.$_POST['database_name'].';host='.$_POST['database_host'].';charset=utf8mb4', $_POST['database_user'], $_POST['database_password']);
			$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$sql = file_get_contents(dirname(__DIR__, 1).'/install/install.sql');
			$db->exec($sql);
		} catch (PDOException $e) {
			$errors[] = 'Unable to connect to the database. ' . $e;
		}

	}

	// Create the first user
	if ( empty($errors) ) {

		require_once('../vendor/autoload.php');
		$auth = new \Delight\Auth\Auth($db);

		if ( $_POST['username'] ) {
			$username = $_POST['username'];
		} else {
			$username = null;
		}

		try {
		    $user_id = $auth->register($_POST['email'], $_POST['password'], $username, null);
		}
		catch (\Delight\Auth\InvalidEmailException $e) {
		    $errors[] = 'Invalid email address.';
		}
		catch (\Delight\Auth\InvalidPasswordException $e) {
		    $errors[] = 'Invalid password.';
		}
		catch (\Delight\Auth\UserAlreadyExistsException $e) {
		    $errors[] = 'User already exists.';
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
		    $errors[] = 'Too many requests.';
		}

	}

	// Give the new user admin privileges
	if ( empty($errors) ) {

		try {
		    $auth->admin()->addRoleForUserById($user_id, \Delight\Auth\Role::ADMIN);
		}
		catch (\Delight\Auth\UnknownIdException $e) {
		    $errors[] = 'Unable to assign admin priviliges. Unknown user ID.';
		}

	}

	// Create config file
	if ( empty($errors) ) {

		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$characters_length = strlen($characters);
		$key = '';
		for ($i = 0; $i < 60; $i++) {
			$key .= $characters[rand(0,$characters_length-1)];
		}

		if ( $_POST['company'] ) {
			$company_name = $_POST['company'];
		} else {
			$company_name = '';
		}

		if ( isset($_POST['debug']) ) {
			$debug = 1;
		} else {
			$debug = 0;
		}

		if ( isset($_POST['registration']) ) {
			$registration = 1;
		} else {
			$registration = 0;
		}

		if ( isset($_POST['usernames']) ) {
			$usernames = 1;
		} else {
			$usernames = 0;
		}

		if ( $_POST['smtp_server'] ) {
			$smtp_server = $_POST['smtp_server'];
		} else {
			$smtp_server = '';
		}

		if ( $_POST['smtp_user'] ) {
			$smtp_user = $_POST['smtp_user'];
		} else {
			$smtp_user = '';
		}

		if ( $_POST['smtp_password'] ) {
			$smtp_password = $_POST['smtp_password'];
		} else {
			$smtp_password = '';
		}
		
		if ( $_POST['smtp_port'] ) {
			$smtp_port = $_POST['smtp_port'];
		} else {
			$smtp_port = '';
		}
		
		if ( $_POST['smtp_encryption'] && $_POST['smtp_encryption'] != 'none' ) {
			$smtp_encryption = $_POST['smtp_encryption'];
		} else {
			$smtp_encryption = '';
		}
		
		if ( $_POST['smtp_from_email'] ) {
			$smtp_from_email = $_POST['smtp_from_email'];
		} else {
			$smtp_from_email = '';
		}

		if ( $_POST['smtp_from_name'] ) {
			$smtp_from_name = $_POST['smtp_from_name'];
		} else {
			$smtp_from_name = '';
		}

		$config_file_template = file_get_contents($app_path.'/install/config_template.php');

		$config_file_new = str_replace(
			array(
				'{{APP_NAME}}',
				'{{APP_PATH}}',
				'{{APP_URL}}',
				'{{APP_DOMAIN}}',
				'{{COMPANY_NAME}}',
				'{{DEBUG}}',
				'{{APP_KEY}}',
				'{{REGISTRATION_ENABLED}}',
				'{{ALLOW_USERNAMES}}',
				'{{DB_HOST}}',
				'{{DB_NAME}}',
				'{{DB_USER}}',
				'{{DB_PASSWORD}}',
				'{{SMTP_SERVER}}',
				'{{SMTP_USER}}',
				'{{SMTP_PASSWORD}}',
				'{{SMTP_PORT}}',
				'{{SMTP_ENCRYPTION}}',
				'{{SMTP_FROM_EMAIL}}',
				'{{SMTP_FROM_NAME}}'
			),
			array(
				addslashes($_POST['app_name']),
				$app_path,
				$app_url,
				$app_domain,
				addslashes($company_name),
				$debug,
				$key,
				$registration,
				$usernames,
				$_POST['database_host'],
				$_POST['database_name'],
				$_POST['database_user'],
				$_POST['database_password'],
				$smtp_server,
				$smtp_user,
				$smtp_password,
				$smtp_port,
				$smtp_encryption,
				$smtp_from_email,
				addslashes($smtp_from_name)
			),
			$config_file_template
		);

		$file = fopen(dirname(__DIR__, 1).'/config.php', "w");
		fwrite($file, $config_file_new);
		fclose($file);

	}

	// No errors, forward to homepage
	if ( empty($errors) ) {
		header('Location: '.$app_url.'/?installed=1');
		exit();
	}

}

require_once('../install/view.php');
