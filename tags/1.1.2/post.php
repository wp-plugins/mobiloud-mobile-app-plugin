<?php
include("../../../wp-blog-header.php");
/*** SINGLE POST FROM PERMALINK ***/

$url = $_GET["permalink"];
if($url == NULL)
{
	$url = $_POST["permalink"];
}


$post_id = url_to_postid($url);
$post = get_post($post_id);

if($post)
{
	print_post($post);
}

function print_post($post)
{
	/** Genero l' output xml **/
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	$post_id = $post->ID;

	$images = get_children( array(
		'post_parent'    => $post_id,
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
	) );

	echo "<post>\n";
	echo "<comments-count>".get_post_comment_count($post_id)."</comments-count>\n";

	echo "\t<post_id>".$post_id."</post_id>\n";
		
	//Author
	echo "\t<author>\n";
	echo "\t\t<name>".get_author_name($post->post_author)."</name>\n";
	echo "\t</author>\n";
		
	$categories = get_the_category($post_id);
	foreach($categories as $category)
	{
		echo "\t<category>".$category->cat_name."</category>\n";
	}

	echo "\t<title><![CDATA[".$post->post_title."]]></title>\n";
	echo "\t<date>".$post->post_date."</date>\n";
		
	$video_url = get_the_first_video($post);
	$main_image_url = get_the_first_image($post);
		
	if($video_url != "")
	{
		echo "\t<video>\n";
		echo "\t\t<video_url>".$video_url."</video_url>\n";
		echo "\t</video>\n";
	}
		
	if($main_image_url != "")
	{
		echo "\t<image>\n";
		echo "\t\t<image_thumb>".$main_image_url."</image_thumb>\n";
		echo "\t\t<image_full>".$main_image_url."</image_full>\n";
		echo "\t</image>\n";
	}
		
	foreach ( (array) $images as $image ) {
		echo "\t<image>\n";
		echo "\t\t<image_thumb>".wp_get_attachment_thumb_url($image->ID,false)."</image_thumb>\n";
		echo "\t\t<image_full>". wp_get_attachment_url( $image->ID,'thumbnail' )."</image_full>\n";
		echo "\t</image>\n";
	}	

	$post_desc = strip_tags($post->post_content);
	$post_desc = preg_replace("'\s+'", ' ', $post_desc);
	echo "\t<post_description><![CDATA[".substr($post_desc,0,200)."]]></post_description>\n";
	echo "\t<excerpt><![CDATA[".strip_tags($post->post_excerpt)."]]></excerpt>\n";
	echo "\t<content><![CDATA[".$post->post_content."]]></content>\n";
	echo "\t<comment-count>".$post->comment_count."</comment-count>\n";
	echo "</post>\n\n";
}



function get_the_first_image($post) {
  $first_img = '';
  ob_start();
  ob_end_clean();
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  $first_img = $matches [1] [0];

  if(empty($first_img)){ //Defines a default image
    $first_img = "";
  }
  return $first_img;
}

function get_the_first_video($post) {
  $first_img = '';
  ob_start();
  ob_end_clean();
  $output = preg_match_all('/<embed.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  $first_video = $matches [1] [0];

  if(empty($first_video)){ //Defines a default video
    $first_video = "";
  }
  return $first_video;
}

function get_post_comment_count($post_id)
{
	global $wpdb;
	$request = "SELECT * FROM $wpdb->comments WHERE comment_post_ID=".$post_id;
	
	$comments = $wpdb->get_results($request);
	return count($comments);	
}

?>





