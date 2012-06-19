<?php
include("../../../wp-blog-header.php");
ini_set('display_errors', 1);

include("comments/comment_iphone.php");
//include("ml_facebook.php");



function ml_render_header($platform="iphone")
{
	?>
	
	<head>
		
		<meta name="viewport" content="width=device-width; minimum-scale=1.0; maximum-scale=1.0;" />
		<link rel="StyleSheet" href="/wp-content/plugins/mobiloud/comments/css/<?php echo $platform;?>.css" type="text/css"  media="screen">
		
	</head>
	
	<?php
}



function ml_render_comment($comment,$platform="iphone")
{
	if($platform == "iphone") 
		ml_render_iphone_comment($comment);	
}


function ml_render_comments($post_id,$platform="iphone",$offset=0)
{
	$parameters = array(
						'post_id' => $post_id,
						//'number' => 10,
						'offset' =>  $offset,
						'status' => "approve",
						'order' => 'ASC'
					   );
					

	$comments = get_comments($parameters);
	foreach($comments as $comment)
	{
		ml_render_comment($comment,$platform);
	}

}


function ml_get_avatar_from_email($email)
{
	$link = ml_facebook_get_picture_url($email);
	if($link) return "<img src='$link'>";
	else return NULL;
}


?>


<HTML>

<?php
ml_render_header();
ml_render_comments($_GET["post_id"]);
//wp_login_form(array('redirect' => $_SERVER['REQUEST_URI']));
?>

</HTML>