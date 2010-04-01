<?php
add_required_class( 'Application.Controller.php', CONTROLLER );

$application = ApplicationController::getInstance();

if(!$application->session->isSessionAuthenticated()){
?>

<a href="login.php" target="login_module" class="trigger">Login</a>

<div class="lightbox" id="login_module">
	<form action="<?php echo CONTROLLER_PATH; ?>User.Controller.php" method="GET" class="form">
		<h3>Login</h3>
		<p>
			<input type="hidden" name="redirect" value="IndexPage" />
			<input type="hidden" name="message" value="login" />
			<label for="username_login">Username:</label><input type="text" name="username_login" id="username_login" value="username" validate="username"/>
		</p>
		<p>
			<label for="password_login">Password:</label><input type="password" name="password_login" id="password_login" value="password" validate="password"/>
		</p>
		<p>
			<input type="submit" name="submit" id="submit_login" value="Login &gt;&gt;" />
		</p>
	</form>
	<h3>Dont't have an account?</h3>
	<p>
		<a href="login.php?action=register">Register</a> for an account with Techweaver Contractor Services now!
	</p>
</div>

<div class="registration" id="registration">
	<form action="<?php echo CONTROLLER_PATH; ?>User.Controller.php" method="post" class="form">
		<h3>Registration</h3>
		<p>
			<input type="hidden" name="redirect" value="IndexPage" />
			<input type="hidden" name="message" value="registration" />
			<label for="username">Email Address:</label><input type="text" name="username_registration" id="username_registration" value="username" validate="username"/>
		</p>
		<p>
			<label for="password">Password:</label><input type="password" name="password_registration" id="password_registration" value="password" validate="password"/>
		</p>
		<p>
			<input type="submit" name="submit" id="submit_login" value="Register &gt;&gt;" />
		</p>
	</form>
</div>
<?php
} 
?>