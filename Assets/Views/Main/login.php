<?php
	global $addedMetas;
	$addedMetas = '<link href="'.SELF_DIR.'Assets/Resources/signin.css" rel="stylesheet">';
	WEB::load_view("Part","head");
?>
<form class="container-fluid form-signin">
	<input type="hidden" name="key" value="login/submit">
	<div class="container signin-header">
		<!-- <img class="" src="<?= web::getImageUrl(LOGO) ?>" alt="" width="80%" height="auto"> -->
		<h1>Login</h1>
	</div>
	<div class="row form-group">
		<div class="col-sm-2">
			<label for="inputEmail"> <i class="fa fa-user"></i> </label>
		</div>
		<div class="col-sm-10">
			<input name="email" type="email" id="inputEmail" class="form-control" placeholder="Email address" required
				autofocus>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-sm-2">
			<label for="inputPassword"> <i class="fa fa-lock"></i> </label>
		</div>
		<div class="col-sm-10">
			<input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password"
				required>
		</div>
	</div>
	<button to="CT<?= time() ?>" class="btn btn-lg btn-primary btn-block submitForm" type="button"
		onclick="SYS.XHRForm(this);">Sign in</button>
	<div id="CT<?= time() ?>" class="CT1"></div>
	<div class="mt-2 already-have-account">
		<a href="<?= SELF_DIR ?>register">Register</a>
		&middot;
		<a href="<?= SELF_DIR ?>home">Home</a>
	</div>
	<p class="mt-2 mb-3 text-muted"> PDemia &copy; <?= date("Y",time()) ?></p>
</form>
<script>
	$(document).on('keydown', 'input', function (e) {
		if ((e.keyCode == 13 && e.ctrlKey) || e.keyCode == 13) {
			e.preventDefault();
			$(this).parents("form").find(".submitForm").click();
		}
	});
</script>
</body>

</html>