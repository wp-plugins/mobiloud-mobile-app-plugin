<?php
include("../../../wp-blog-header.php");
ini_set('display_errors', 0);

include_once("ml_facebook.php");
include("comments/comment_iphone.php");

function ml_render_comment($comment, $platform="iphone"){
	if($platform == "iphone"){
		ml_render_iphone_comment($comment);
	}
}

function ml_render_comments($post_id, $platform="iphone", $offset=0){
	$parameters = array(
		'post_id' => $post_id,
		//'number' => 10,
		'offset' =>  $offset,
		'status' => "approve",
		'order' => 'ASC'
	);

	$comments = get_comments($parameters);
	foreach($comments as $comment){
		ml_render_comment($comment,$platform);
	}
}

?><!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1, user-scalable=no">
<link href="<?php echo plugins_url('mobiloud-mobile-app-plugin/comments/css/styles.css'); ?>" rel="stylesheet" media="all" /><link href="<?php echo plugins_url('mobiloud-mobile-app-plugin/comments/css/_typeplate.css'); ?>" rel="stylesheet" media="all" /></head><body>
<?php ml_render_comments($_GET['post_id']); ?>
</body></html>