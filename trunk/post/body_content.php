<?php include_once(dirname(__FILE__)."/../../../../wp-blog-header.php"); ?>
<?php if(isset($post_id) == false) { ?>

<?php $post_id = $_GET['post_id']; ?>
<?php $post = get_post($post_id); ?>
<?php } ?>
<?php if(isset($post) == false) { ?>

<?php $post = get_post($post_id); ?>
<?php } ?>
<?php $post_type = get_post_type($post->ID); ?>
<?php $post_content = $post->post_content; ?>
<script type="text/javascript">
<?php echo stripslashes(get_option('ml_post_custom_js')); ?>

</script><style type="text/css">
<?php echo stripslashes(get_option('ml_post_custom_css')); ?>

</style><?php eval(stripslashes(get_option('ml_post_start_body'))); ?>
<div class="post-content" id="post_content">
<div id="loading_spinner">
</div><div id="post_header">
<?php eval(stripslashes(get_option('ml_post_before_details'))); ?>
<h1 class="post-title">
<?php echo $post->post_title; ?>

</h1><div class="second-line">
<?php if(($post_type == 'post' && get_option('ml_post_date_enabled') == "true") || ($post_type == 'page' && get_option('ml_page_date_enabled') == "true")) { ?>

<div class="date">
<?php echo mysql2date('F j Y',$post->post_date); ?>

</div><?php } ?>
<?php if(($post_type == 'post' && get_option('ml_post_author_enabled') == "true") || ($post_type == 'page' && get_option('ml_page_author_enabled') == "true")) { ?>

<div class="author-name">
<?php echo the_author_meta('display_name',$post->post_author); ?>

</div><?php } ?>
<div class="clearfix">
</div></div><div class="clearfix">
</div><?php eval(stripslashes(get_option('ml_post_after_details'))); ?>
</div><?php eval(stripslashes(get_option('ml_post_before_content'))); ?>
<div id="main_content">
<?php echo do_shortcode($post_content); ?>

</div><?php eval(stripslashes(get_option('ml_post_after_content'))); ?>
</div><?php eval(stripslashes(get_option('ml_post_end_body'))); ?>
