<script type="text/javascript">
	var _veroq=_veroq||[];_veroq.push(["init",{api_key:"36bd54bf9afde30628102337cf6dc4306a6a212a",development_mode:true}]);(function(){var e=document.createElement("script");e.type="text/javascript";e.async=true;e.src="//getvero.com/assets/m.js";var t=document.getElementsByTagName("script")[0];t.parentNode.insertBefore(e,t)})()
</script>

<?php
	$name = trim($_POST['contactName']);

	$email = trim($_POST['email']);
	
	$website = trim($_POST['website']);

	$emailTo = 'sales@mobiloud.com';
	$subject = 'From '.$name;
	$body = "New request from Mobiloud Plugin. \n\nName: $name \nEmail: $email \nWebsite: $website\n";
	$headers = 'From: '.$name.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
	
	mail($emailTo, $subject, $body, $headers);

?>

<script type="text/javascript">
	_veroq.push(['user', {
	  id: '1', // This ID must be unique per customer
	  email: 'test@50pixels.com', // Replace this with the logged in customer's email
	  name: 'tt',
	  website: 'test'
	}]);
	
	_veroq.push(['track', 'new_app_init']); 
	
	window.location.href='http://www.mobiloud.com/pricing.php?ref=plugin';
</script>