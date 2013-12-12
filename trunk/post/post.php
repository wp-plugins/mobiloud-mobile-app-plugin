<?php if($post_id == NULL && $page_id == NULL) { ?>

<?php include_once(dirname(__FILE__)."/../../../../wp-blog-header.php"); ?>
<?php $post_id = $_GET['post_id']; ?>
<?php $post = get_post($post_id); ?>
<?php } ?>
<?php if(get_option('ml_debug') == 'true') { ?>

<?php ini_set('display_errors', 1);; ?>
<?php } ?>
<?php $post_type = get_post_type($post->ID); ?>
<?php $post_content = $post->post_content; ?>
<?php $post_text_direction = (get_option('ml_rtl_text_enable') == 'true') ? 'RTL' : 'LTR'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?php echo $post_text_direction; ?>">
<?php include('html_content.php'); ?>
</html>