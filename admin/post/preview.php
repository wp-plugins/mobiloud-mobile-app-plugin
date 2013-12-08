<h2 align="right">
Preview
</h2><div class="preview-selection">
<select id="preview_popup_post_select">
<?php $posts = get_posts(array('posts_per_page' => 10,'orderby' => 'post_date','order' => 'DESC','post_type' => 'post')); ?>
<?php $pages = get_pages(array('sort_order' => 'ASC', 'sort_column' => 'post_title', 'post_type' => 'page','post_status' => 'publish')); ?>
<optgroup label="Posts">
<?php foreach($posts as $post) { ?>

<option value="<?php echo MOBILOUD_PLUGIN_URL; ?>/post/post.php?post_id=<?php echo $post->ID; ?>">
<?php if(strlen($post->post_title) > 40) { ?>

<?php echo substr($post->post_title,0,40); ?>

..
<?php } else { ?>

<?php echo $post->post_title; ?>

<?php } ?>
</option><?php } ?>
</optgroup><optgroup label="Pages">
<?php foreach($pages as $page) { ?>

<option value="<?php echo MOBILOUD_PLUGIN_URL; ?>/post/post.php?post_id=<?php echo $page->ID; ?>">
<?php if(strlen($page->post_title) > 40) { ?>

<?php echo substr($page->post_title,0,40); ?>

..
<?php } else { ?>

<?php echo $page->post_title; ?>

<?php } ?>
</option><?php } ?>
</optgroup></select><div class="devices">
<div class="ipadmini-device-btn">
<img class="open_preview_btn" src="<?php echo MOBILOUD_PLUGIN_URL; ?>/images/ipadmini_120.png" />iPad
</div><div class="iphone5s-device-btn">
<img class="open_preview_btn" src="<?php echo MOBILOUD_PLUGIN_URL; ?>/images/iphone5s_100.png" />iPhone
</div><div class="clearfix">
</div></div><div class="description">
<p>
You can customize CSS, JavaScript and PHP code.
</p><p>
In PHP customizations you have the object
<b>
$post
</b>available.
<br />To see if $post is representing a page, just use the wordpress code
</p><b>
is_page($post->ID)
</b></div></div><div id="preview_popup_content">
<div class="iphone5s_device">
<iframe id="preview_popup_iframe">
</iframe></div><div class="ipadmini_device">
<iframe id="preview_popup_iframe">
</iframe></div></div>