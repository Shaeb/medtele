<?php
add_required_class( 'Application.Controller.php', CONTROLLER );

$application = ApplicationController::getInstance();
$user = $application->getUser();

if($application->session->isSessionAuthenticated()){
?>
<div id="toppanel">
	<div id="panel">
		<div class="content clearfix">
			<div class="left">
				<h1>Welcome to MedTeleNusring.com</h1>
				<h3><?php echo $user->username; ?>'s Profile</h3>
				<p class="grey">
					Username: <?php echo $user->username; ?>
				</p>
				<p class="grey">Last Login Date: <?php echo $user->lastLogin; ?></p>
			</div>
			<div class="left">
				<!-- Login Form -->
				<form action="<?php echo CONTROLLER_PATH; ?>User.Controller.php" method="post">
					<input type="hidden" name="redirect" value="IndexPage" />
					<input type="hidden" name="message" value="registration" />
					<h1>Got something to say?</h1>				
					<label class="grey" for="username_registration">Tweet a quick message</label>
					<textarea class="field" name="tweet_message" id="tweet_message">shoutout!</textarea><br/>
					<input type="submit" name="submit" value="Register" class="bt_register" />
				</form>
			</div>
			<div class="left right">			
				<!-- Register Form -->
				<form action="<?php echo CONTROLLER_PATH; ?>User.Controller.php" method="post">
					<input type="hidden" name="redirect" value="IndexPage" />
					<input type="hidden" name="message" value="registration" />
					<h1>Want to enable Facebook Connect?</h1>				
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
	        <li>Hello <?php echo $user->username; ?>!</li>
			<li class="sep">|</li>
			<li id="toggle">
				<a id="open" class="open" href="#"><?php echo $user->username; ?>'s Profile</a>
				<a id="close" style="display: none;" class="close" href="#">Close Panel</a>			
			</li>
	    	<li class="right"></li>
		</ul> 
	</div> <!-- / top -->
	
</div>
<?php 
}
?>