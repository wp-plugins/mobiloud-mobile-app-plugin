<?php if(isset($post_id) == false && isset($page) == false) { ?>
<?php $post_id = $_GET['post_id']; ?>
<?php $post = get_post($post_id); ?>
<?php } ?>
<?php if(isset($post) == false) { ?>

<?php $post = get_post($post_id); ?>
<?php } ?>
<?php $post_type = get_post_type($post->ID); ?>
<?php $post_content = $post->post_content; ?>
<?php $eager_loading = $_GET['eager']; ?>
<head>
<meta content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" name="viewport" /><script src="<?php echo MOBILOUD_POST_ASSETS_URL; ?>/js/jquery.min.js" type="text/javascript">
</script><link href="<?php echo MOBILOUD_POST_ASSETS_URL; ?>/css/mobile.css" media="all" rel="StyleSheet" type="text/css" /><script src="<?php echo MOBILOUD_POST_ASSETS_URL; ?>/js/spinner.js" type="text/javascript">
</script><script src="<?php echo MOBILOUD_POST_ASSETS_URL; ?>/js/jquery.spin.js" type="text/javascript">
</script><script src="<?php echo MOBILOUD_POST_ASSETS_URL; ?>/js/mobile.js" type="text/javascript">
</script><?php echo get_option('ml_post_head'); ?>

</head><body>
<?php if(get_option('ml_eager_loading_enable') == 'true' || $eager_loading == "true" || $post_type == 'page' || isset($_POST['post_id']) || isset($_GET['fullcontent'])) { ?>

<?php include(dirname(__FILE__)."/body_content.php"); ?>
<?php } else { ?>
<?php if(!isset($_POST['allow_lazy'])){ ?>
<div id="lazy_body">
<div class="post-content" id="post_content">
<div id="post_header">
<h1 class="post-title">
<?php echo $post->post_title; ?>

</h1></div>
</div>
<div id="lazy_content_spinner">
</div></div><div data-post_id="<?php echo $post->ID; ?>" data-url="<?php echo MOBILOUD_PLUGIN_URL; ?>/post/body_content.php" id="mobiloud_lazy_load">
</div><script src="<?php echo MOBILOUD_POST_ASSETS_URL; ?>/js/lazy_load.js" type="text/javascript">
</script>

<?php } else { ?>
 <div class="post-content" id="post_content">
 <div id="post_header">
<h1 class="post-title">
<?php echo $post->post_title; ?>

</h1></div>
 </div>
 <?php } ?>
 <?php } ?>
</body><?php eval(stripslashes(get_option('ml_post_footer'))); ?>
