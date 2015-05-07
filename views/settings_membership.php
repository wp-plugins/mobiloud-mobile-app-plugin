<div id="ml_settings_membership" class="tabs-panel ml-compact">
    <form method="post" action="<?php echo admin_url('admin.php?page=mobiloud_settings&tab=membership'); ?>">
        <?php wp_nonce_field('form-settings_membership'); ?>
        <h3>Membership</h3>
        <div class='ml-col-twothirds'>
            <p>Mobiloud can integrate with a number of WordPress membership plugins and require your users to authenticate to access the contents of your app.</p>
			<p>Don't see your membership plugin here? <a class="ml-intercom" href="mailto:h89uu5zu@incoming.intercom.io">Contact our developers</a> for help.</p>
            <div class="ml-form-row ml-checkbox-wrap">
                <input type="checkbox" id="ml_subscriptions_enable" name="ml_subscriptions_enable" value="true" <?php echo Mobiloud::get_option('ml_subscriptions_enable') ? 'checked' : ''; ?>/>
                <label for="ml_subscriptions_enable">Enable <a target="_blank" href="https://wordpress.org/plugins/groups/">WP-Groups</a> subscriptions</label>
            </div>
        </div>        
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
    </form>
</div>