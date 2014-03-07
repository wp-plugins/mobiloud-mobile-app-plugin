
<?php

if (strlen(get_option('ml_api_key'))>10 && strlen(get_option('ml_secret_key')) ) { ?>
	<iframe height="2000px" src="https://push.mobiloud.com/iframes/notifications/<?php echo get_option('ml_api_key'); ?>/<?php echo get_option('ml_secret_key'); ?>" width="100%">
	</iframe>
<?php } else {echo '
<div class="wrap">
	<div class="narrow">
		<div class="stuffbox" id="ml_admin_push" style="padding:50px">

		This page will be accessible once you have <a href="admin.php?page=mobiloud_menu_homepage">purchased your app</a> and enterd a valid license key.
			
			</div>
		</div>
</div>
			';}
	
?>
