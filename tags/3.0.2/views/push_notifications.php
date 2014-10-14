<div id="ml_push_notifications" class="tabs-panel ml-compact">
    <?php if(strlen(Mobiloud::get_option('ml_pb_app_id')) <= 0 && strlen(Mobiloud::get_option('ml_pb_secret_key')) <= 0): ?>
    <div id="ml_admin_push" style="padding-top:30px">

		<p>This page will be accessible once you have <a target='_blank' href="http://www.mobiloud.com/pricing/">purchased your app</a> and <a href="./admin.php?page=mobiloud_settings&tab=license">entered a valid license key</a>.
			
		<br/><br/>
		
		 You'll be able to send manual push notification messages to your users, attaching posts and pages to every message. You'll also find here a convenient log of all messages previously sent.</p>
			
    </div>
    <?php else: ?>
    <form method="post" action="<?php echo admin_url('admin.php?page=mobiloud_push&tab=notifications'); ?>">
        <?php wp_nonce_field('form-push_notifications'); ?>
        <h3>Send manual message</h3>
        <div id="success-message" class="updated" style="display: none;">Your message has been sent!</div>
        <?php ml_push_notification_manual_send(); ?>
        
        <h3>Notification history</h3>
        <!-- NOTIFICATIONS LIST -->
        <div id="ml_push_notification_history">
            <?php ml_push_notification_history_ajax_load(); ?>
        </div>
    </form>
    <?php endif; ?>
</div>