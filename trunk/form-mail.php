<?php
	$name = trim($_POST['contactName']);

	$email = trim($_POST['email']);
	
	$website = trim($_POST['website']);

	$emailTo = 'hello@mobiloud.com';
	$subject = 'From '.$name;
	$body = "New request from Mobiloud Plugin. \n\nName: $name \nEmail: $email \nWebsite: $website\n";
	$headers = 'From: '.$name.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
	
	mail($emailTo, $subject, $body, $headers);
	
	header( 'Location: http://www.mobiloud.com/pricing.php' ) ;
?>