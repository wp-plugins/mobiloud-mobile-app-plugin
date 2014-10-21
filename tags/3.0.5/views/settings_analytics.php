<div id="ml_settings_analytics" class="tabs-panel ml-compact">
    <form method="post" action="<?php echo admin_url('admin.php?page=mobiloud_settings&tab=analytics'); ?>">
        <?php wp_nonce_field('form-settings_analytics'); ?>
        <h3>Google Analytics</h3>
        <div class='ml-col-twothirds'>
		    <?php if( strlen(Mobiloud::get_option('ml_pb_app_id')) > 0 && Mobiloud::get_option('ml_pb_app_id') < "543e7b3f1d0ab16d148b4599"): ?>			
	        <div class='update-nag'>
	            <p> The settings below are only available for recently published apps.</p>
				<p> Your app was built wih a pre-configured Analytics ID, get in touch at <a href='mailto:support@mobiloud.com'>support@mobiloud.com</a> to know more.</p>
	        </div>
	        <?php endif; ?>
            <p>Configure your Google Analytics tracking code below to track page and article views and user activity on your app 
                within Google Analytics.</p>  
            <p>You'll need to <a target="_blank" href="https://support.google.com/analytics/answer/2614741?hl=en-GB">setup a new Google Analytics property</a> and select "App" as the type of property you 
                want to track. Make a note of the Tracking ID and enter it below (ignore any instructions relating to the SDK, we've done everything for you!).</p>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">Tracking ID</th>
                        <td>
                            <input size="36" type="text" id="ml_google_tracking_id" name="ml_google_tracking_id" placeholder="UA-XXXXXXXX-X" value='<?php echo Mobiloud::get_option('ml_google_tracking_id'); ?>'>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h3>Other Analytics tools</h3>
        <div class='ml-col-twothirds'>    
            Beyond Google Analytics, the Mobiloud developer team can setup other Analytics solutions for your app, for more information, 
            contact <a href="mailto:support@mobiloud.com">support@mobiloud.com</a>.
        </div>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
    </form>
</div>