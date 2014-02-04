 <html>

	<body>
		<?php
			$name = trim($_POST['contactName']);

			$email = trim($_POST['email']);
	
			$website = trim($_POST['website']);

			$root_url = $_POST['root_url'];
			$plugins_url = $_POST['plugins_url'];
			$appname = $_POST['appname'];
/*		
			$emailTo = 'sales@mobiloud.com';
			$subject = 'New plugin installation from '.$website;
			$body = "New request from Mobiloud Plugin. \n\nName: $name \nEmail: $email \nWebsite: $website\n";
			$headers = 'From: '.$name.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
	
			mail($emailTo, $subject, $body, $headers);
*/
		?>

		<!-- FORM -->
		<form  action="https://app.mobiloud.com" id="configurator-form" method="post">
			<input type='hidden' value="<?php echo $name?>" name='fullname'/>
			<input type='hidden' value="<?php echo $email?>" name='email'/>
			<input type='hidden' value="<?php echo $website?>" name='site'/>

			<input type='hidden' value="<?php echo $root_url?>" name='root_url'/>
			<input type='hidden' value="<?php echo $plugins_url?>" name='plugins_url'/>
			<input type='hidden' value="<?php echo $appname?>" name='app_name'/>

		</form>

		<script>window.profilesIoKey="99a7cd8f-8db3-11e3-8d44-0634f3e75b43";</script>
		<script src="https://profiles.io/record"></script>

		<script type="text/javascript">
			document.getElementById("configurator-form").submit();
		</script>

	</body>
</html>