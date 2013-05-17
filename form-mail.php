 <html>
	 <head>
		 <script type="text/javascript">
			var _veroq = _veroq || [];

			_veroq.push(['init', {
			  api_key: '36bd54bf9afde30628102337cf6dc4306a6a212a',
			  development_mode: true 
			  // Turn this off when you decide to 'go live'.
			} ]);

			(function() {var ve = document.createElement('script'); ve.type = 'text/javascript'; ve.async = true; ve.src = '//getvero.com/assets/m.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ve, s);})();
		</script>

	</head>

	<body>
		<?php
			$name = trim($_POST['contactName']);

			$email = trim($_POST['email']);
	
			$website = trim($_POST['website']);

			//$emailTo = 'sales@mobiloud.com';
			$emailTo = 'manon@50pixels.com';
			$subject = 'From '.$name;
			$body = "New request from Mobiloud Plugin. \n\nName: $name \nEmail: $email \nWebsite: $website\n";
			$headers = 'From: '.$name.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
	
			mail($emailTo, $subject, $body, $headers);

		?>

		<script type="text/javascript">
			_veroq.push(['user', {
			  id: '1', // This ID must be unique per customer
			  email: 'test@50pixels.com' // Replace this with the logged in customer's email
			}]);
	
			_veroq.push(['track', 'new_app_init']); 
	
			window.location.href='http://www.mobiloud.com/pricing.php?ref=plugin';
		</script>

	</body>
</html>