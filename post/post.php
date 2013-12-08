<?php if($post_id == NULL && $page_id == NULL) { ?>

<?php include_once(dirname(__FILE__)."/../../../../wp-blog-header.php"); ?>
<?php $post_id = $_GET['post_id']; ?>
<?php $post = get_post($post_id); ?>
<?php } ?>
<?php $post_type = get_post_type($post->ID); ?>
<?php $post_content = $post->post_content; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php include('html_content.php'); ?>
</html>