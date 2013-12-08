<script src="<?php echo MOBILOUD_PLUGIN_URL; ?>/admin/post/stripslashes.js" type="text/javascript">
</script><script src="<?php echo MOBILOUD_PLUGIN_URL; ?>/admin/post/post.js" type="text/javascript">
</script><div id="ml_admin_post">
<h1>
Mobiloud Post Customization
</h1><input class="button button-primary button-large" data-label="Save" data-saving-label="Saving..." id="ml_admin_post_save_btn" type="submit" value="Save" /><select id="ml_admin_post_customization_select">
<option value="ml_null">
Select a customization...
</option><option value="ml_post_head">
PHP Inside HEAD tag
</option><option value="ml_post_custom_js">
Custom JS
</option><option value="ml_post_custom_css">
Custom CSS
</option><option value="ml_post_start_body">
PHP at the beginning of BODY tag
</option><option value="ml_post_before_top_banner">
PHP before top Banner
</option><option value="ml_post_after_top_banner">
PHP after top Banner
</option><option value="ml_post_before_details">
PHP before post details
</option><option value="ml_post_after_details">
PHP after post details
</option><option value="ml_post_before_content">
PHP before Content
</option><option value="ml_post_after_content">
PHP after Content
</option><option value="ml_post_before_footer_banner">
PHP before footer Banner
</option><option value="ml_post_after_footer_banner">
PHP after footer Banner
</option><option value="ml_post_after_body">
PHP at the end of BODY tag
</option><option value="ml_post_footer">
PHP Footer
</option></select><div class="clearfix">
</div><textarea id="ml_admin_post_textarea">
</textarea><textarea class="hidden" name="ml_post_head">
<?php echo htmlspecialchars(get_option('ml_post_head')); ?>

</textarea><textarea class="hidden" name="ml_post_custom_js">
<?php echo htmlspecialchars(get_option('ml_post_custom_js')); ?>

</textarea><textarea class="hidden" name="ml_post_custom_css">
<?php echo htmlspecialchars(get_option('ml_post_custom_css')); ?>

</textarea><textarea class="hidden" name="ml_post_start_body">
<?php echo htmlspecialchars(get_option('ml_post_start_body')); ?>

</textarea><textarea class="hidden" name="ml_post_before_top_banner">
<?php echo htmlspecialchars(get_option('ml_post_before_top_banner')); ?>

</textarea><textarea class="hidden" name="ml_post_after_top_banner">
<?php echo htmlspecialchars(get_option('ml_post_after_top_banner')); ?>

</textarea><textarea class="hidden" name="ml_post_before_details">
<?php echo htmlspecialchars(get_option('ml_post_before_details')); ?>

</textarea><textarea class="hidden" name="ml_post_after_details">
<?php echo htmlspecialchars(get_option('ml_post_after_details')); ?>

</textarea><textarea class="hidden" name="ml_post_before_content">
<?php echo htmlspecialchars(get_option('ml_post_before_content')); ?>

</textarea><textarea class="hidden" name="ml_post_after_content">
<?php echo htmlspecialchars(get_option('ml_post_after_content')); ?>

</textarea><textarea class="hidden" name="ml_post_before_footer_banner">
<?php echo htmlspecialchars(get_option('ml_post_before_footer_banner')); ?>

</textarea><textarea class="hidden" name="ml_post_after_footer_banner">
<?php echo htmlspecialchars(get_option('ml_post_after_footer_banner')); ?>

</textarea><textarea class="hidden" name="ml_post_after_body">
<?php echo htmlspecialchars(get_option('ml_post_after_body')); ?>

</textarea><textarea class="hidden" name="ml_post_footer">
<?php echo htmlspecialchars(get_option('ml_post_footer')); ?>

</textarea></div><div class="wrap">
<div class="narrow">
<div class="stuffbox" id="ml_admin_post_options">
<h3>
Options
<h2 style="text-decoration:underline">
Posts
</h2><h2>
<input data-checked="<?php echo get_option('ml_eager_loading_enable',false); ?>" name="ml_eager_loading_enable" type="checkbox" />Preload the content
</h2><h2>
<input data-checked="<?php echo get_option('ml_post_author_enabled',false); ?>" name="ml_post_author_enabled" type="checkbox" />Show author name
</h2><h2>
<input data-checked="<?php echo get_option('ml_post_date_enabled',false); ?>" name="ml_post_date_enabled" type="checkbox" />Show date
</h2><h2 style="text-decoration:underline">
Pages
</h2><h2>
<input data-checked="<?php echo get_option('ml_page_author_enabled',false); ?>" name="ml_page_author_enabled" type="checkbox" />Show author name
</h2><h2>
<input data-checked="<?php echo get_option('ml_page_date_enabled',false); ?>" name="ml_page_date_enabled" type="checkbox" />Show date
</h2></h3><input class="button button-primary button-large" data-label="Save" data-saving-label="Saving..." type="submit" value="Save" /></div></div></div>