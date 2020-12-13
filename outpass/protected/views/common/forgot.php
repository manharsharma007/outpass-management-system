<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Productive</title>
	<?php Loader::addStyle('style-login.css'); ?>
	<?php Loader::addStyle('css/font-awesome.min.css'); ?>
</head>
<body>
	<div class="login-container container">

	<div class="login-box">
	<form action="#" method="post">

		<div class="login-window">
			<div class="row heading">
				<h4><i class="fa fa-sign-in" aria-hidden="true"></i> Reset Password</h4>
			</div>

	<div class="row">
		<?php
			if(isset($error['error_code']) && isset($error['message'])) {

				if($error['error_code'] == SUCCESS)
					echo '<div class="msg success"><p>'.$error["message"].'</p>';
				elseif($error['error_code'] == WARNING)
					echo '<div class="msg warning"><p>'.$error["message"].'</p>';
				elseif($error['error_code'] == ERROR)
					echo '<div class="msg fail"><p>'.$error["message"].'</p>';

				echo'</div>';
			}
	?>
	</div>
	<div class="row">
	<div class="msg warning"><p>This will reset your password.</p>
	</div>
			<div class="input-element clearfix">
				<input type="text" name="email"  required placeholder="Enter email"  value="">
				<i class="fa fa-envelope" aria-hidden="true"></i>
			</div>

				<input type="submit" name="submit" value="Reset" class="login_btn btn-red btn-green" />

			<div class="row clearfix">
				<p class="forgot"><a href="<?= SITE_URL ?>"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Login</a></p>
			</div>

		</div>
		
	</form>
</div>
	</div>
</body>
</html>