 <html>

	<body>
		<?php
			$name = trim($_POST['contactName']);

			$email = trim($_POST['email']);
	
			$website = trim($_POST['website']);

			$emailTo = 'sales@mobiloud.com';
			$subject = 'New plugin installation from '.$website;
			$body = "New request from Mobiloud Plugin. \n\nName: $name \nEmail: $email \nWebsite: $website\n";
			$headers = 'From: '.$name.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
	
			mail($emailTo, $subject, $body, $headers);

		?>

		<!-- FORM -->
		<form  action="http://app.mobiloud.com/registration" id="configurator-form" method="post">
			<input type='hidden' value="<?=$name?>" name='fullname'/>
			<input type='hidden' value="<?=$email?>" name='email'/>
			<input type='hidden' value="<?=$website?>" name='site'/>
		</form>

		<script type="text/javascript">
			document.getElementById("configurator-form").submit();
		</script>

	</body>
</html>