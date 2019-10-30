<form method="post"
<?php if( is_array($attrs) ):?>
	<?php foreach($attrs as $key => $value):?>
		 <?=$key;?>="<?=$value;?>"
	<?php endforeach;?>
<?php endif;?>
>

	<div class="form-label-group">
		<input type="text" id="user" class="form-control" placeholder="User / Email" name="user" required autofocus>
		<label for="user">User / Email</label>
	</div>
	
	<div class="form-label-group">
		<input type="password" id="password" class="form-control" placeholder="Password" name="password" required>
		<label for="password">Password</label>
	</div>
	
	<div class="custom-control custom-switch">
		<input type="checkbox" class="custom-control-input" id="remember" name="remember" value="1">
		<label class="custom-control-label" for="remember">Remember Me</label>
	</div>
	
	<button class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2 mt-3" type="submit">Log In</button>
	
</form>