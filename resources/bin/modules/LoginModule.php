<a href="login.php" target="login_module" class="trigger">Login</a>

<div class="lightbox" id="login_module">
	<form action="resources/bin/processors/loginUser.php" method="GET" class="form">
		<h3>Login</h3>
		<p>
			<input type="hidden" name="redirect" value="IndexPage" />
			<input type="hidden" name="action" value="login" />
			<label for="username">Username:</label><input type="text" name="username_login" id="username_login" value="username" validate="username"/>
		</p>
		<p>
			<label for="password">Password:</label><input type="password" name="password_login" id="password_login" value="password" validate="password"/>
		</p>
		<p>
			<input type="submit" name="submit_login" id="submit_login" value="Login &gt;&gt;" />
		</p>
	</form>
	<h3>Dont't have an account?</h3>
	<p>
		<a href="login.php?action=register">Register</a> for an account with Techweaver Contractor Services now!
	</p>
</div>