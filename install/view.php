<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Install MVPHP</title>
	<link rel="stylesheet" href="/css/app.min.css">
	<link href="/css/all.min.css" rel="stylesheet">
	<script src="/js/jquery.min.js" charset="utf-8"></script>
	<script src="/js/bootstrap.min.js" charset="utf-8"></script>
	<style>
		body {
			background: #000;
			background: url('/images/backgrounds/code.jpg') no-repeat center center fixed;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
		}
		.main-container {
			max-width: 600px;
			margin: auto;
			background: #fff;
			padding-bottom: 40px;
		}

		.container-padding {
			padding: 30px;
		}

		input {
			font-size: 16px;
		}
		h2 {
			border-bottom: 1px solid #dbdbdb;
			margin-bottom: 30px;
		}
		label {
			font-weight: bold;
		}
		small {
			display: block;
		}
        ::selection {
          background: #593196;
          color: #fff;
        }
        ::-moz-selection {
          background: #593196;
          color: #fff;
        }
	</style>
</head>
<body>

	<nav class="navbar navbar-expand-sm bg-dark navbar-dark justify-content-end">
	    <a class="navbar-brand" href="#"><img src="/images/mvphp.png" style="width:150px;" /></a>
	    <a href="https://github.com/nickian/mvphp" target="_blank" class="btn btn-primary ml-auto mr-2">Github</a>
	    <div class="collapse navbar-collapse flex-grow-0" id="navbarSupportedContent">

	    </div>
	</nav>


	<div class="container main-container">

		<div id="install" class="container-padding">

			<h2>Install MVPHP</h2>

            <?php if ( isset($errors) && !empty($errors) ):?>

            <?php foreach( $errors as $error ):?>

            <div class="alert alert-danger text-left" role="alert">
				<?=$error;?>
			</div>

            <?php endforeach;?>

            <?php endif;?>

			<div class="alert alert-primary text-left" role="alert">
				This installer will create a few required tables in your database, create your first admin user, and create a config.php file in the root directory. You can always change or add to the config.php file later.
			</div>

            <form method="post">

    			<div id="database" class="section mt-4">

    				<h2>Database</h2>

    					<div class="form-row">
    						<div class="form-group col-md-6">
    							<label for="database-host">Database Host</label>
    							<input type="text" class="form-control" name="database_host" id="database-host"<?php if ( isset($_POST['database_host']) ):?> value="<?=$_POST['database_host'];?>"<?php endif;?>>
    						</div>
    						<div class="form-group col-md-6">
    							<label for="database-name">Database Name</label>
    							<input type="text" class="form-control" name="database_name" id="database-name"<?php if ( isset($_POST['database_name']) ):?> value="<?=$_POST['database_name'];?>"<?php endif;?>>
    						</div>
    					</div>

    					<div class="form-row">
    						<div class="form-group col-md-6">
    							<label for="database-user">Database User</label>
    							<input type="text" class="form-control" name="database_user" id="database-user"<?php if ( isset($_POST['database_user']) ):?> value="<?=$_POST['database_user'];?>"<?php endif;?>>
    						</div>
    						<div class="form-group col-md-6">
    							<label for="database-password">Database Password</label>
    							<input type="password" class="form-control" name="database_password" id="database-password"<?php if ( isset($_POST['database_password']) ):?> value="<?=$_POST['database_password'];?>"<?php endif;?>>
    						</div>
    					</div>

    			</div><!--database-->

    			<div id="create-admin-user" class="section mt-4">

    				<h2>Create Your Admin User</h2>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<label for="email">Email</label>
    						<input type="email" class="form-control" name="email" id="email"<?php if ( isset($_POST['email']) ):?> value="<?=$_POST['email'];?>"<?php endif;?>>
    					</div>
    				</div>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<label for="username">Username</label>
    						<input type="text" class="form-control" name="username" id="username"<?php if ( isset($_POST['username']) ):?> value="<?=$_POST['username'];?>"<?php endif;?>>
    						<small>This is optional. You can log in with either a username or an email address.</small>
    					</div>
    				</div>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<label for="password">Password</label>
    						<input type="password" class="form-control" name="password" id="password"<?php if ( isset($_POST['password']) ):?> value="<?=$_POST['password'];?>"<?php endif;?>>
    					</div>
    				</div>

    			</div><!--config settings-->


    			<div id="app-settings" class="section mt-4">

    				<h2>App Settings</h2>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<label for="admin-email">App Name</label>
    						<input type="text" class="form-control" name="app_name" id="app-name" <?php if ( isset($_POST['app_name']) ):?> value="<?=$_POST['app_name'];?>"<?php endif;?>>
    						<small>What do you want to call website?</small>
    					</div>
    				</div>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<label for="company">Company Name</label>
    						<input type="text" class="form-control" name="company" id="company"<?php if ( isset($_POST['company']) ):?> value="<?=$_POST['company'];?>"<?php endif;?>>
    						<small>This is optional</small>
    					</div>
    				</div>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<label for="app-path">Absolute Path to MVPHP</label>
    						<input type="text" class="form-control" name="app_path" id="app-path"<?php if ( isset($_POST['app_path']) ):?> value="<?=$_POST['app_path'];?>"<?php else:?>value="<?=$app_path;?>"<?php endif;?>>
    					</div>
    				</div>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<label for="app-url">URL to MVPHP</label>
    						<input type="text" class="form-control" name="app_url" id="app-url"<?php if ( isset($_POST['app_url']) ):?> value="<?=$_POST['app_url'];?>"<?php else:?>value="<?=$app_url;?>"<?php endif;?>>
    					</div>
    				</div>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<div class="custom-control custom-switch">

                                <input type="checkbox" class="custom-control-input" name="registration" id="registration" value="1"
                                <?php if($_SERVER['REQUEST_METHOD'] == 'POST'):?>
                                <?php if(isset($_POST['registration']) && $_POST['registration'] == 1):?> checked<?php else:?><?php endif;?>
                                <?php else:?> checked<?php endif;?>>

                                <label class="custom-control-label" for="registration">Enable User Registration</label>

    						</div>
    					</div>
    				</div>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<div class="custom-control custom-switch">

                              <input type="checkbox" class="custom-control-input" name="usernames" id="usernames" value="1"
                              <?php if($_SERVER['REQUEST_METHOD'] == 'POST'):?>
                              <?php if(isset($_POST['usernames']) && $_POST['usernames'] == 1):?> checked<?php else:?><?php endif;?>
                              <?php else:?> checked<?php endif;?>>

    						  <label class="custom-control-label" for="usernames">Allow Usernames</label>
    						  <small>You can always log in with an email address regardless. If enabled, users can choose a username in their profile settings.</small>
    						</div>
    					</div>
    				</div>

                    <div class="form-row">
    					<div class="form-group col-md-12">
    						<div class="custom-control custom-switch">

                                <input type="checkbox" class="custom-control-input" name="debug" id="debug" value="1"
                                <?php if($_SERVER['REQUEST_METHOD'] == 'POST'):?>
                                <?php if(isset($_POST['debug']) && $_POST['debug'] == 1):?> checked<?php else:?><?php endif;?>
                                <?php else:?> checked<?php endif;?>>

    						  <label class="custom-control-label" for="debug">Debug Mode</label>
    						  <small>Display all PHP errors.</small>
    						</div>
    					</div>
    				</div>

    			</div><!--SMTP settings-->

    			<div id="app-settings" class="section mt-4">

    				<h2>SMTP Settings</h2>

    				<p>These settings are optional and can be configured later in the config.php file.</p>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<label for="smtp-server">Server</label>
    						<input type="text" class="form-control" name="smtp_server" id="smtp-server"<?php if ( isset($_POST['smtp_server']) ):?> value="<?=$_POST['smtp_server'];?>"<?php endif;?>>
    					</div>
    				</div>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<label for="smtp-user">User</label>
    						<input type="text" class="form-control" name="smtp_user" id="smtp-user"<?php if ( isset($_POST['smtp_user']) ):?> value="<?=$_POST['smtp_user'];?>"<?php endif;?>>
    					</div>
    				</div>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<label for="smtp-password">Password</label>
    						<input type="password" class="form-control" name="smtp_password" id="smtp-password"<?php if ( isset($_POST['smtp_password']) ):?> value="<?=$_POST['smtp_password'];?>"<?php endif;?>>
    					</div>
    				</div>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<label for="smtp-port">Port</label>
    						<input type="text" class="form-control" name="smtp_port" id="smtp-port"<?php if ( isset($_POST['smtp_port']) ):?> value="<?=$_POST['smtp_port'];?>"<?php endif;?>>
    					</div>
    				</div>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<label for="smtp-encryption">Encryption</label>
    						<select class="custom-select" name="smtp_encryption" id="smtp-encryption">
    							<option value="none"<?php if ( isset($_POST['smtp_encryption']) && $_POST['smtp_encryption'] == 'none' ):?> selected<?php endif;?>>None</option>
    							<option value="ssl"<?php if ( isset($_POST['smtp_encryption']) && $_POST['smtp_encryption'] == 'ssl' ):?> selected<?php endif;?>>SSL</option>
    							<option value="tls"<?php if ( isset($_POST['smtp_encryption']) && $_POST['smtp_encryption'] == 'tls' ):?> selected<?php endif;?>>TLS</option>
    						</select>
    					</div>
    				</div>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<label for="smtp-from-email">Default From Email Address</label>
    						<input type="email" class="form-control" name="smtp_from_email" id="smtp-from-email"<?php if ( isset($_POST['smtp_from_email']) ):?> value="<?=$_POST['smtp_from_email'];?>"<?php endif;?>>
    					</div>
    				</div>

    				<div class="form-row">
    					<div class="form-group col-md-12">
    						<label for="smtp-from-name">From From Name</label>
    						<input type="text" class="form-control" name="smtp_from_name" id="smtp-from-name"<?php if ( isset($_POST['smtp_from_name']) ):?> value="<?=$_POST['smtp_from_name'];?>"<?php endif;?>>
    					</div>
    				</div>

    			</div><!--SMTP settings-->

        		<div class="text-center section-compact mt-3">
        			<button type="submit" class="btn btn-primary">Install MVPHP</button>
        		</div>

            </form>

		</div><!--install-->

	</div>

<script type="text/javascript" src="lib/install.js"></script>

</body>
</html>
