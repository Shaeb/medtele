<?php
add_required_class( 'Application.Controller.php', CONTROLLER );

$application = ApplicationController::getInstance();

if(!$application->session->isSessionAuthenticated()){
?>
<div id="toppanel">
	<div id="panel">
		<div class="content clearfix">
			<div class="left">
				<h1>Welcome to MedTeleNusring.com</h1>
				<h2>Sliding login panel Demo with jQuery</h2>		
				<p class="grey">You can put anything you want in this sliding panel: videos, audio, images, forms... The only limit is your imagination!</p>
				<h2>Download</h2>
				<p class="grey">To download this script go back to <a href="http://web-kreation.com/index.php/tutorials/nice-clean-sliding-login-panel-built-with-jquery" title="Download">article</a></p>
			</div>
			<div class="left">
				<!-- Login Form -->
				<form class="clearfix" action="<?php echo CONTROLLER_PATH; ?>User.Controller.php" method="post">
					<input type="hidden" name="redirect" value="IndexPage" />
					<input type="hidden" name="message" value="login" />
					<h1>Member Login</h1>
					<label class="grey" for="username_login">Username:</label>
					<input class="field" type="text" name="username_login" id="username_login" value="username" size="23" />
					<label class="grey" for="password_login">Password:</label>
					<input class="field" type="password" name="password_login" id="password_login" size="23" />
	            	<label><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> Remember me</label>
        			<div class="clear"></div>
					<input type="submit" name="submit" value="Login" class="bt_login" />
					<a class="lost-pwd" href="#">Lost your password?</a>
				</form>
			</div>
			<div class="left right">			
				<!-- Register Form -->
				<form action="<?php echo CONTROLLER_PATH; ?>User.Controller.php" method="post">
					<input type="hidden" name="redirect" value="IndexPage" />
					<input type="hidden" name="message" value="registration" />
					<h1>Not a member yet? Sign Up!</h1>				
					<label class="grey" for="username_registration">Email:</label>
					<input class="field" type="text" name="username_registration" id="username_registration" value="username" size="23" />
					<label class="grey" for="password_registration">Password:</label>
					<input class="field" type="password" name="password_registration" id="password_registration" size="23" />
					<label>Please pick a good password you will remember.</label>
					<input type="submit" name="submit" value="Register" class="bt_register" />
				</form>
			</div>
		</div>
	</div> <!-- /login -->	

    <!-- The tab on top -->	
	<div class="tab">
		<ul class="login">
	    	<li class="left"></li>
	        <li>Hello Guest!</li>
			<li class="sep">|</li>
			<li id="toggle">
				<a id="open" class="open" href="#">Log In | Register</a>
				<a id="close" style="display: none;" class="close" href="#">Close Panel</a>			
			</li>
	    	<li class="right"></li>
		</ul> 
	</div> <!-- / top -->
	
</div>
<?php 
}
?>