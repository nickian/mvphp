<?php $app->view('header', array(
	'title' => 'MVPHP'
));?>

<style>
	body {
		margin-top: 15%;
		background: #000;
		background: url('/images/backgrounds/code.jpg') #000 no-repeat center center fixed;
		-webkit-background-size: cover;
		-moz-background-size: cover;
		-o-background-size: cover;
		background-size: cover;
	}
	img {
		width: 230px;
	}
	p {
		margin: 20px;
		font-size: 16px;
		color: #fff;
	}
	a {
		color: #20b573;
		font-weight: bold;
	}
	a:hover {
		color: #662f90;
		text-decoration: none;
	}
	.container {
		max-width: 600px;
		text-align: center;
		margin-bottom: 50px;
	}
</style>

<div class="container">

	<?php if ( isset($_GET['installed']) ):?>
		<div class="alert alert-success" role="alert">
			You've successfully installed MVPHP. Now go build something awesome!
		</div>
	<?php endif;?>

</div>

<div id="main-container" class="text-center">

	<img src="<?=APP_URL;?>/images/mvphp.png" alt="MVPHP" />
	<p>A simple, easily hackable framework for developing Minimally Viable PHP Web Applications.</p>
	<p>
		<a href="https://github.com/nickian/mvphp" target="_blank"><i class="fab fa-github"></i> Github</a>
	</p>

</div><!--main-container-->

<?php $app->view('footer');?>
