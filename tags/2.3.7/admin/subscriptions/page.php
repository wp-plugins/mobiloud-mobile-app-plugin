<script src="<?php echo MOBILOUD_PLUGIN_URL; ?>/admin/subscriptions/subscriptions.js" type="text/javascript">
</script><div class="wrap">
    <h1 style="float:left;">Membership Options</h1>
    <a id="intercom" style="float: right;" class="ml-contact-button button button-primary" href="mailto:h89uu5zu@incoming.intercom.io">Contact Us</a>       
     <div style="clear:both;"></div>
<div class="narrow">
<div id="ml_admin_subscriptions">
    
    <?php if(ml_has_groups_library() == false) { ?>

<div class="update-nag">
You need to install and activate the
<a href="http://www.itthinx.com/plugins/groups/" target="_blank">
<b>
WP Groups
</b></a>plugin to enable password protected user logins to your app.
</div><?php } else { ?>

<div class="stuffbox" id="ml_admin_subscriptions_options">
<h2 style="text-decoration:underline">
Options
</h2><h2>
<input data-checked="<?php echo get_option('ml_subscriptions_enable',false); ?>" name="ml_subscriptions_enable" type="checkbox" />Require users to login to access the app
</h2><input class="button button-primary button-large" data-label="Save" data-saving-label="Saving..." type="submit" value="Save" /></h3></div><?php } ?>
</div></div></div>