<?php $app->view('header');?>

<div class="container-fluid">
	
	<div class="row no-gutter">
	
		<div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>

		<div class="col-md-8 col-lg-6">

			<div class="login d-flex align-items-center py-5">

				<div class="container">

					<div class="row">

						<div class="col-md-9 col-lg-8 mx-auto">
						
							<?php /* 
							// ***********************************************************************
							// STEP 2:
							// LINK FROM THE EMAIL WAS CLICKED - SHOW / SUBMIT THE PASSWORD RESET FORM
							//
							*/ ?>
							<?php if ( isset($password_form) ):?>
							
								<?php if ( $app->action('post') ):?>
								
									<?php if (! empty($errors) ):?>
									
										<h3 class="login-heading mb-4">Choose a New Password</h3>
										
										<?php foreach($errors as $error):?>
										<div class="alert alert-danger" role="alert">
											<?=$error;?>
										</div>  
										<?php endforeach;?>
										
										<?php $app->form('password-reset');?>
									
									<?php else:?>
									
										<h3 class="login-heading mb-4">Login with Your New Password</h3>
										<?php $app->form('login', ['action' => APP_URL.'/login']); ?>
									
									<?php endif;?>
								
								<?php else:?>

									<?php if (! empty($errors) ):?>
									
										<h3 class="login-heading mb-4">Sorry...</h3>
										<?php foreach($errors as $error):?>
										<div class="alert alert-danger" role="alert">
											<?=$error;?>
										</div>  
										<?php endforeach;?>
										
									<?php else:?>
										
										<h3 class="login-heading mb-4">Choose a New Password</h3>
										<?php $app->form('password-reset');?>
								
									<?php endif;?>
								
								<?php endif;?>
							
							<?php /* 
							// ***********************************************************************
							// STEP 1:
							// SHOW / SUBMIT THE RECOVERY FORM
							// 
							*/ ?>
							<?php else:?>
							
								<h3 class="login-heading mb-4">Recover Your Account</h3>
								
								<?php // Recovery form was submitted and valid ?>
								<?php if ( isset($success) && $success ):?>
							
									<div class="alert alert-success" role="alert">
										Email sent. Click the recovery link to reset your password.
									</div>  
								
								<?php // Recovery form was submitted, but there are errors ?>
								<?php elseif(isset($errors) && !empty($errors) ):?>
								
									<?php foreach($errors as $error):?>
									<div class="alert alert-danger" role="alert">
										<?=$error;?>
									</div>  
									<?php endforeach;?>
									
								<?php endif;?>
							
								<?php $app->form('recover');?>
								
								<div class="text-center">
									<a class="small" href="<?=APP_URL;?>/login">Login</a> | 
									<?php if( REGISTRATION_ENABLED ):?><a class="small" href="<?=APP_URL;?>/create-account">Create Account</a><?php endif;?>
								</div>
							
							<?php endif;?>
						
						</div><!--col-->
					
					</div><!--row-->
					
				</div><!--container-->

			</div><!--login-->

		</div><!--col-->
			
	</div><!--row-->

</div><!--container-->

<?php $app->view('footer');?>