 <html>

	<body>
		<?php
			$name = trim($_POST['contactName']);

			$email = trim($_POST['email']);
	
			$website = trim($_POST['website']);

			$root_url = $_POST['root_url'];
			$plugins_url = $_POST['plugins_url'];
			$appname = $_POST['appname'];
		?>

		<!-- FORM -->
		<form  action="http://www.mobiloud.com/service/" id="configurator-form" method="get">
			<input type='hidden' value="<?php echo $name?>" name='fullname'/>
			<input type='hidden' value="<?php echo $email?>" name='email'/>
			<input type='hidden' value="<?php echo $website?>" name='site'/>

			<input type='hidden' value="<?php echo $root_url?>" name='root_url'/>
			<input type='hidden' value="<?php echo $plugins_url?>" name='plugins_url'/>
			<input type='hidden' value="<?php echo $appname?>" name='app_name'/>

		</form>

		<script type="text/javascript">
			document.getElementById("configurator-form").submit();
		</script>

	</body>
</html>