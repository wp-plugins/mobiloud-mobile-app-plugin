<script src="<?php echo MOBILOUD_PLUGIN_URL; ?>/admin/banners/banners.js" type="text/javascript">
</script><div class="wrap">
<div class="narrow">
<div id="ml_admin_banners">
<h1>
Mobiloud Banners
</h1><select data-selected="<?php echo ml_banners_service(); ?>" id="ml_admin_banners_service_select" name="banners_service">
<option value="off">
OFF
</option><option value="admob">
Admob
</option><option value="html">
HTML
</option></select><div class="stuffbox hidden" id="ml_admin_banners_admob">
<h3>
Admob
</h3><h2>
Phone Admob ID
</h2><p>
<input id="admob_phone_id" placeholder="Phone Admob id" size="" type="text" value="<?php echo get_option('ml_banners_admob_phone_id'); ?>" /></p><h2>
Tablet Admob ID
</h2><p>
<input id="admob_tablet_id" placeholder="Tablet Admob id" size="" type="text" value="<?php echo get_option('ml_banners_admob_tablet_id'); ?>" /></p></div><div class="stuffbox hidden" id="ml_admin_banners_html">
<h3>
HTML
</h3><h2>
Phone - Main screen - Top
</h2><textarea id="html_phone_top">
<?php echo htmlspecialchars(get_option('ml_banners_html_phone_top')); ?>

</textarea><h2>
Phone - Inside Article - Top
</h2><textarea id="html_phone_article_top">
<?php echo htmlspecialchars(get_option('ml_banners_html_phone_article_top')); ?>

</textarea><h2>
Phone - Inside Article - Bottom
</h2><textarea id="html_phone_article_bottom">
<?php echo htmlspecialchars(get_option('ml_banners_html_phone_article_bottom')); ?>

</textarea><h2>
Tablet - Main screen - Top
</h2><textarea id="html_tablet_top">
<?php echo htmlspecialchars(get_option('ml_banners_html_tablet_top')); ?>

</textarea><h2>
Tablet - Inside Article - Top
</h2><textarea id="html_tablet_article_top">
<?php echo htmlspecialchars(get_option('ml_banners_html_tablet_article_top')); ?>

</textarea><h2>
Tablet - Inside Article - Bottom
</h2><textarea id="html_tablet_article_bottom">
<?php echo htmlspecialchars(get_option('ml_banners_html_tablet_article_bottom')); ?>

</textarea></div><p>
</p><input class="button button-primary button-large" data-label="Save" data-saving-label="Saving..." id="ml_admin_banners_save_btn" type="submit" value="Save" /></div></div></div>