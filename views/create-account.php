<?php $app->view('header', array(
	'title' => 'Create Your Account'
));?>

<div class="container-fluid">
	
	<div class="row no-gutter">
		
		<div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
		
		<div class="col-md-8 col-lg-6">
	
			<div class="login d-flex align-items-center py-5">
			
				<div class="container">
	
					<div class="row">
						
						<div class="col-md-9 col-lg-8 mx-auto">
							
							<h3 class="login-heading mb-4">Create an Account</h3>
              
			              	<?php if ( isset($success) && $success ):?>
							<div class="alert alert-success" role="alert">
								We sent a verification email to you. Please click on the confirmation link to verify your email address.
							</div>         		
			              	<?php else:?>
              	
			              	<?php if ( isset($errors) && !empty($errors) ):?>
			              	<?php foreach($errors as $error):?>
							<div class="alert alert-danger" role="alert">
								<?=$error;?>
							</div>
			              	<?php endforeach;?>
			              	<?php endif;?>
              	
							<form method="post">
							
								<div class="form-label-group">
									<input type="text" id="email" class="form-control" placeholder="Email" name="email" required autofocus>
									<label for="email">Email Address</label>
								</div>
								
								<div class="form-label-group">
									<input type="password" id="password" class="form-control" placeholder="Password" name="password" required>
									<label for="password">Password</label>
								</div>
								
								<div class="form-label-group">
									<input type="password" id="repeat_password" class="form-control" placeholder="Password" name="repeat_password" required>
									<label for="repeat_password">Repeat Password</label>
								</div>
								
								<button class="btn btn-lg btn-success btn-block btn-login text-uppercase font-weight-bold mb-2 mt-3" type="submit">Create My Account</button>
								
								<a href="<?=APP_URL;?>/login" class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2 mt-3">Log In</a>
							
								<div class="text-center">
									<a class="small" href="<?=APP_URL;?>/login/recover">Forgot password?</a>
								</div>
							
							</form>
              	
							<?php endif;?>

						</div>
						
					</div>
					
				</div>
				
			</div>
			
		</div>
		
	</div>
	
</div>

<?php $app->view('footer');?>