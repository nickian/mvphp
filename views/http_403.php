<?php $app->view('header', array(
	'title' => 'Forbidden'
));?>

<style>
    body {
		background: #000;
		background: url('/images/backgrounds/401-403.jpg') no-repeat center center fixed; 
		-webkit-background-size: cover;
		-moz-background-size: cover;
		-o-background-size: cover;
		background-size: cover;
    }
    
    h1 {
        display: block;
        width: 100%;
        margin-top: 20%;
        font-family: helvetica, arial, sans-serif;
        font-size: 45px;
        font-weight: 300;
        color: white;
        text-align: center;
        line-height: 1;
    }
    
    h1 span {
        display: block;
        font-size: 155px;
        color: red;
    }
</style>

<h1 class="animated zoomIn slower"><span>403</span>Forbidden</h1>

<?php $app->view('footer');?>
