<div id="get_started_menu_config" class="tabs-panel">
    <h3>Test your app in the simulator</h3>
    <p>
	   Test-drive your app in the simulator before signing up and publishing it.
    </p>
	
    <?php
    $user_email = Mobiloud::get_option('ml_user_email');
    $user_name = Mobiloud::get_option('ml_user_name');
    $user_site = Mobiloud::get_option('ml_user_site');
    $plugin_url = Mobiloud::get_plugin_url();
    $plugin_version = MOBILOUD_PLUGIN_VERSION;
    
    $http_prefix = 'http';
    if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) {
        $http_prefix = 'https';
    }
    ?>
    <?php add_thickbox(); ?>
    <a href="<?php echo $http_prefix; ?>://www.mobiloud.com/simulator/?name=<?php echo urlencode(esc_attr($user_name)); ?>&email=<?php echo urlencode(esc_attr($user_email)); ?>&site=<?php echo urlencode(esc_url($user_site)); ?>&p=<?php echo urlencode(esc_url($plugin_url)); ?>&v=<?php echo urlencode(esc_attr($plugin_version)); ?>" target="_blank" class="sim-btn thickbox button button-hero button-primary">
            See Live Preview
        </a>
		
		<p>Need help with the preview? <a class="ml-intercom" href="mailto:h89uu5zu@incoming.intercom.io">Get in touch</a>.</p>
		
    <h3>Test it on your own device</h3>
    <div class="">
        <img src="<?php echo MOBILOUD_PLUGIN_URL . '/assets/img/demo_app.png'; ?>" width="250" height="528" alt="Preview your own app" class="test_img"><br>
		<div>
        <p>To get a preview of your app for iOS or Android, follow the steps below.</p>

			<ol style="text-align:left;">
				<li> <strong>Download the <a href="https://itunes.apple.com/us/app/mobiloud/id903667370?mt=8">Mobiloud app from the App Store</a></strong> or the <a href="https://play.google.com/store/apps/details?id=com.mobiloud.android"><strong>Android app from Google Play</strong></a> on your own device.</li>
				<li>Open the Mobiloud app. You'll see the contents and design of the Mobiloud blog.</li>
				<li>Now <strong>shake your device</strong>. </li>
				<li>A screen named <strong>Preview your app</strong> will become accessible. </li>
				<li>Now <strong>enter your own site's URL</strong>, the app will reload so you can see your own design and content on your device!</li>
				<input name="siteurl" type="url" id="siteurl" value="<?php echo get_site_url(); ?>" class="regular-text code" />
			</ol>
		<p>Any questions or need some help? <a class="ml-intercom" href="mailto:h89uu5zu@incoming.intercom.io">Get in touch</a>.</p>
		
			<i>Hint: we've sent you an email with links to the apps, open it from your phone to make things easier.</i>
	        <?php if($loadDemo): ?>
	       
		    <div class='update-nag'>
	            <p>
	                You might experience some issues in getting a preview of your own app from your device (<?php echo $error_reason; ?>), 
	                but give it a try and should you have any problems, contact us at <a href="mailto:support@mobiloud.com">support@mobiloud.com</a>
	            </p>
	        </div>
	        <?php endif; ?>
			
		</div>
    </div>
</div>