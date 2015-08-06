<div id="wrap" class="mobiloud">
    <div id="ml-header">
        <a href="<?php echo admin_url('admin.php?page=mobiloud'); ?>" class="ml-logo">
            <img src="<?php echo MOBILOUD_PLUGIN_URL; ?>assets/img/mobiloud-logo-black.png"/>
        </a>
        
        <?php if(strlen(Mobiloud::get_option('ml_pb_app_id')) <= 0 && strlen(Mobiloud::get_option('ml_pb_secret_key')) <= 0): ?>
        <a href="http://www.mobiloud.com/publish/?utm_source=wp-plugin-admin&utm_medium=web&utm_campaign=plugin-admin-header
<?php echo get_option('affiliate_link', null); ?>" target="_blank" class="pricing-btn button-primary">
            See Pricing &amp; Publish Your App
        </a>        
        <p class='ml-trial-msg'>Design and test your app for free. Choose your plan when you're ready to publish it.</p>
        <?php else: ?>
        <!-- <a class="ml-intercom ml-contact-button button button-primary" href="mailto:h89uu5zu@incoming.intercom.io" data-intercom_original_html="Contact Us" style="visibility: visible;">Contact Us</a> -->
	<?php endif; ?>
   </div>