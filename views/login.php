<?php $app->view('header');?>

<div class="container-fluid">
	<div class="row no-gutter">
		
		<div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
		
		<div class="col-md-8 col-lg-6">
				
			<div class="login d-flex align-items-center py-5">
					
				<div class="container">
						
					<div class="row">
							
						<div class="col-md-9 col-lg-8 mx-auto">
								
							<h3 class="login-heading mb-4">Welcome back!</h3>
              
							<?php if ( isset($_GET['confirmed']) && $_GET['confirmed'] ):?>
              	
							<div class="alert alert-success" role="alert">
								You email address has been confirmed! Now you can log in.
							</div>   
              
							<?php elseif (isset($errors) && !empty($errors)):?>
              	
							<?php foreach($errors as $error):?>
							<div class="alert alert-danger" role="alert">
								<?=$error;?>
							</div>
							<?php endforeach;?>
              	
							<?php endif;?>
							
							<?php $app->form('login');?>
			              
							<?php if( REGISTRATION_ENABLED ):?>
							<?php if (! isset($confirmed) || !$confirmed ):?>
							<a href="<?=APP_URL;?>/create-account" class="btn btn-lg btn-success btn-block btn-login text-uppercase font-weight-bold mb-2 mt-3">Create an Account</a>
							<?php endif;?>
							<?php endif;?>
				
							<div class="text-center">
								<a class="small" href="<?=APP_URL;?>/login/recover">Forgot password?</a>
							</div>
              
            			</div><!--col-->
            			
					</div><!--row-->

				</div><!--container-->

			</div><!--login-->
    </div>
  </div>
</div>

<?php $app->view('footer');?>