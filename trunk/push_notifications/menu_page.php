<?php
if(strlen(get_option('ml_pb_app_id'))>10 && strlen(get_option('ml_pb_secret_key'))) {
    
    wp_register_script('google_chart', 'https://www.google.com/jsapi');
    wp_enqueue_script('google_chart');
?>
<link rel='stylesheet' href="<?php echo MOBILOUD_PLUGIN_URL.'/notifications.css';?>" type='text/css' media='all' />
<div class="wrap">
    <div class="ml_header">
        <h1 style="float: left;">Mobiloud Push Notifications</h1>
        <a id="intercom" style="float: right;" class="ml-contact-button button button-primary" href="mailto:h89uu5zu@incoming.intercom.io">Contact Us</a>       
        <div style="clear:both;"></div>
        
        <?php $registeredDevicesCount = ml_registered_devices_count(); ?>
        <div style="float:right;"> 
            <h3 style="float:right;"><u>Registered Devices</u><br/>
            Android: <?php echo $registeredDevicesCount['android'] !== null ? ($registeredDevicesCount['android'] == 0 ? 'No Devices Registered' : $registeredDevicesCount['android']) : 'Unknown'; ?><br/>
            iOS: <?php echo $registeredDevicesCount['ios'] !== null ? ($registeredDevicesCount['ios'] == 0 ? 'No Devices Registered' : $registeredDevicesCount['ios']) : 'Unknown'; ?>
            </h3>
        </div>
        <div style="clear:both;"></div>
    </div>
    <p>&nbsp;</p>
    <div id="success-message" class="updated" style="display: none;">Your message has been sent!</div>

    <!-- SEND MANUAL NOTIFICATION -->
    <div id="ml_push_notification_manual_send" class="stuffbox">            
        <?php ml_push_notification_manual_send_ajax_load(); ?>
    </div> 
    <!-- NOTIFICATIONS LIST -->
    <div id="ml_push_notification_history">
        <?php ml_push_notification_history_ajax_load(); ?>
    </div>
</div>
<?php
} else if (strlen(get_option('ml_api_key'))>10 && strlen(get_option('ml_secret_key')) ) { ?>
	<iframe height="2000px" src="https://push.mobiloud.com/iframes/notifications/<?php echo get_option('ml_api_key'); ?>/<?php echo get_option('ml_secret_key'); ?>" width="100%">
	</iframe>
<?php } else {echo '
<div class="wrap">
	<div class="narrow">
		<div class="stuffbox" id="ml_admin_push" style="padding:50px">

		This page will be accessible once you have <a href="admin.php?page=mobiloud_menu_homepage">purchased your app</a> and entered a valid license key.
			
			</div>
		</div>
</div>
			';}
	
?>
