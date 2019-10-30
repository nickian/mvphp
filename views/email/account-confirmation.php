<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Verify Account</title>
</head>
<body>
	<h2>New Account Confirmation</h2>
	<p>Click here to verify your email address:</p>
	<p><a href="<?=APP_URL;?>/create-account/verify/<?=$selector;?>/<?=$token;?>" target="_blank"><?=APP_URL;?>/register/verify/<?=$selector;?>/<?=$token;?></a></p>
</body>
</html>