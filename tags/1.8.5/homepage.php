<?php

function mobiloud_home_page()
{
	if(isset($_POST['submitted'])) {
	
	$name = trim($_POST['contactName']);

	$email = trim($_POST['email']);
	
	$website = trim($_POST['website']);

	$emailTo = 'hello@mobiloud.com';
	$subject = 'From '.$name;
	$body = "New request from Mobiloud Plugin. \n\nName: $name \nEmail: $email \nWebsite: $website\n";
	$headers = 'From: '.$name.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
	
	mail($emailTo, $subject, $body, $headers);

	$emailSent = true;

}
 
	?>

	<div class="wrap">
		
		<p>&nbsp;</p>
		
		<div class="narrow">

			<!-- GENERAL -->
			<div class="ml_homepage_general">
				<div class='img_homepage'><img src="<?php echo MOBILOUD_PLUGIN_URL;?>/app_overview.png"></div>
				<div class="content center">
					<h1>Turn your app into a stunning app</h1>
					<h2>Enter your details below to get started</h2>
					
					<!-- FORM -->
					<form class="form-horizontal formContact" target="_blank" action="<?php echo MOBILOUD_PLUGIN_URL;?>form-mail.php" id="contactForm" method="post">
					  <div class="control-group inputGroup" >
						<div class="controls">
						  <label for="contactName">Your name</label>
						  <input type="text" id="contactName" name="contactName" placeholder="Enter your name" required>
						</div>
					  </div>
					  <div class="control-group inputGroup">
						<div class="controls">
						  <label for="email">Your email</label>
						  <input type="email" id="email" name="email" placeholder="Enter your email" required>
						</div>
					  </div>
					  <div class="control-group inputGroup">
						<div class="controls">
						  <label for="website">Your website</label>
						  <input type="text" id="website" name="website" placeholder="Enter your website">
						</div>
					  </div>
					  
		  			  <input type="checkbox" name="terms" value="agree" required>I agree to Mobiloud's <a href="http://mobiloud.com/terms.php">terms of service</a> and <a href="https://www.iubenda.com/privacy-policy/435863/legal">privacy policy</a>
		  			  <br /><br />
		  			  
					  <input type="submit" value="Get started now" id="submitted" name="submitted" class="btn-submit">
					</form>
					
				</div>
				
			</div>
		</div>

	</div>
	<?php
}

?>