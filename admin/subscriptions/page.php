<script src="<?php echo MOBILOUD_PLUGIN_URL; ?>/admin/subscriptions/subscriptions.js" type="text/javascript">
</script><div class="wrap">
<div class="narrow">
<div id="ml_admin_subscriptions">
<h1>
Subscriptions
</h1><?php if(ml_has_groups_library() == false) { ?>

<div class="error">
You need to install or activate
<a href="http://www.itthinx.com/plugins/groups/">
<b>
Groups
</b></a>plugin
</div><?php } else { ?>

<div class="stuffbox" id="ml_admin_subscriptions_options">
<h3>
Options
<h2 style="text-decoration:underline">
Options
</h2><h2>
<input data-checked="<?php echo get_option('ml_subscriptions_enable',false); ?>" name="ml_subscriptions_enable" type="checkbox" />Enable Subscriptions System
</h2><input class="button button-primary button-large" data-label="Save" data-saving-label="Saving..." type="submit" value="Save" /></h3></div><?php } ?>
</div></div></div>