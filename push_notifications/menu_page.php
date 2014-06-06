<?php
if(strlen(get_option('ml_pb_app_id'))>10 && strlen(get_option('ml_pb_secret_key'))) {
?>
<link rel='stylesheet' href="<?php echo MOBILOUD_PLUGIN_URL.'/notifications.css';?>" type='text/css' media='all' />
<div class="wrap">
    <div class="ml_header">
        <img src="<?php echo MOBILOUD_PLUGIN_URL;?>/mobiloud_36.png" style="float:left;margin-top:5px;">
        <h1 style="float:left;margin-left:20px;color:#555">Mobiloud Push Notifications</h1>
        <?php $registeredDevicesCount = ml_registered_devices_count(); ?>
        <h3 style="float:right;">Registered Devices: <?php echo $registeredDevicesCount ? $registeredDevicesCount : 'Unknown'; ?></h3>
        <div style="clear:both;"></div>
    </div>
    <p>&nbsp;</p>
    <?php if(!ml_check_pb_keys()): ?>
    <div class="wrap">
            <div class="stuffbox" id="ml_admin_push" style="padding:50px">

            The license details which you have entered seem to be invalid. Please <a href="admin.php?page=mobiloud_menu_license">click here</a> to enter them again or contact Mobiloud support.

            </div>
    </div>
    <?php else: ?>
        <div id="success-message" class="updated" style="display: none;">Your message has been sent!</div>

        <!-- SEND MANUAL NOTIFICATION -->
        <div id="ml_push_notification_manual_send" class="stuffbox">            
            <?php ml_push_notification_manual_send_ajax_load(); ?>
        </div> 
        <!-- NOTIFICATIONS LIST -->
        <div id="ml_push_notification_history">
            <?php ml_push_notification_history_ajax_load(); ?>
        </div>
    <?php endif; ?>
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
