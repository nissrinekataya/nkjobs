<?php
	global $addedMetas;
	$addedMetas = '<link href="'.SELF_DIR.'Assets/Resources/register.css" rel="stylesheet">';
	WEB::load_view("Part","head");
?>
<form class="container-fluid form-register">
	<input type="hidden" name="key" value="register/submit">
	<div class="container register-header">
		<h1>Register New Account</h1>
	</div>
	<div class="row form-group">
		<div class="col-sm-2">
			<label> Name </label>
		</div>
		<div class="col-sm-10">
			<input name="name" type="text" class="form-control" placeholder="enter your name" required autofocus>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-sm-2">
			<label> Email </label>
		</div>
		<div class="col-sm-10">
			<input name="email" type="email" class="form-control" placeholder="Email address" required autofocus>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-sm-2">
			<label> Password </label>
		</div>
		<div class="col-sm-10">
			<input name="password" type="password" class="form-control" placeholder="password" required autofocus>
		</div>
	</div>

</form>
</body>
</html>