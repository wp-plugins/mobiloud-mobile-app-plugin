<?php error_reporting(E_ALL); ?>
<?php ini_set('display_errors', '1'); ?>
<?php include("../../../wp-blog-header.php"); ?>
<?php require_once('post_funcs.php'); ?>
<?php $posts = ml_get_post_list($_GET); ?>
<?php echo json_encode($posts); ?>
