<?php
include("../../../wp-blog-header.php");
include_once("libs/img_resize.php");

include("post_html.php");


//ini_set('display_errors', 1);

/*** POSTS LIST ***/

$user_offset = $_POST["offset"];
$user_post_count = $_POST["postcount"];
$user_category = $_POST["category"];

$user_search = $_POST["search"];
$platform = $_POST["platform"];


$user_limit = 15;

if(isset($_POST["limit"]))
{
    $user_limit = $_POST["limit"];
	if($user_limit > 30) $user_limit = 30;
}

$raw_content = false;
if(isset($_GET["rawcontent"]))
{
	$raw_content = true;
}

if(!isset($platform) || $platform == NULL)
	$platform = "iphone";


$published_post_count = wp_count_posts()->publish;


if($user_category) {
	$category = get_category_by_slug($user_category);
	if($category) $published_post_count = get_post_count(array($category->cat_ID));
} 


if($user_offset == NULL) $user_offset = 0;
if($user_post_count == NULL) $user_post_count = $published_post_count;

$new_posts_count = $published_post_count - $user_post_count;
$real_offset = $user_offset + $new_posts_count;

$posts = query_posts(
	array('showposts' => $user_limit,
		  'orderby' => 'post_date',
		  'order' => 'DESC',
		  'post_type' => 'post',
		  'post_status' => 'publish',
		  'offset' => $real_offset,
		  'category_name' => $user_category,
		  's' => $user_search
		)
);

$posts_options = array("raw_content" => $raw_content);

print_posts($posts,$published_post_count,$user_offset,$platform,$posts_options);

function print_posts($posts,$tot_count,$offset,$platform,$options)
{
	global $ml_automatic_image_resize;
	$ml_automatic_image_resize = get_option("ml_automatic_image_resize");
	
	/** JSON OUTPUT **/
	$final_posts = array("posts" => array(), "post-count" => $tot_count);
	
	foreach($posts as $post)
	{

		$post_id = $post->ID;
		if($offset > 0 && is_sticky($post_id)) continue;
		/** comments **/



		$images = get_children( array(
			'post_parent'    => $post_id,
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
		) );
		
		$final_post = array();
		$final_post["post_id"] = "$post_id";
		$final_post["comments-count"] = get_post_comment_count($post_id);
		
		$final_post["permalink"] = get_permalink($post_id);
		
		$final_post["author"] = array();
		$final_post["author"]["name"] = get_author_name($post->post_author);
		$final_post["author"]["author_id"] = $post->post_author;
		
		$final_post["categories"] = array();
				
		$categories = get_the_category($post_id);
		foreach($categories as $category)
		{
			$final_post["categories"][] = array(
				"cat_id" => "$category->cat_ID",
				"name" => $category->cat_name,
				"slug" => $category->category_nicename);
		}

		$final_post["title"] = $post->post_title;
		$final_post["date"] = $post->post_date;
		
		$video_id = get_the_first_youtube_id($post);
		$main_image_url = get_the_first_image($post);
		$post_thumb_url = get_first_attachment_url($post_id);

		if($post_thumb_url)
		{
			$main_image_url = $post_thumb_url;
		}

		//resizing
		$main_image_thumb_url = NULL;
		$main_image_medium_thumb_url = NULL;
		
		if($main_image_url != NULL)
		{
			if($ml_automatic_image_resize)
			{
				$main_image_thumb_url = ml_image_resize($main_image_url,100,65,true);
				$main_image_big_thumb_url = ml_image_resize($main_image_url,320,220,true);										
			}
		}

		$final_post["videos"] = array();
		$final_post["images"] = array();

		if($video_id != NULL)
		{
			$final_post["videos"][] = $video_id;
		}
		
		
		if($main_image_url != NULL)
		{
			$image = array( "full" => $main_image_url, 
							"thumb" => $main_image_thumb_url,
							"big-thumb" => $main_image_big_thumb_url); 
			$final_post["images"][] = $image;
		}
		
		
		foreach ( (array) $images as $image ) {
			$image = array();
			$image["full"] = wp_get_attachment_thumb_url($image->ID,false);
			$image["thumb"] =   wp_get_attachment_url( $image->ID,'thumbnail');
			$final_post["images"][] = $image;
		}	

		
		$post_desc = strip_tags($post->post_content);
		$post_desc = preg_replace("'\s+'", ' ', $post_desc);
		
		$final_post["post_description"] = substr($post_desc,0,200);
		$final_post["excerpt"] = strip_tags($post->post_excerpt);

		//raw content, for debugging
		if($options["raw_content"])
			$final_post["content"] = $post->post_content;


		else if($platform == "ipad")
			$final_post["content"] = ipad_html($post);
		else
			$final_post["content"] = iphone_html($post);
				
		$final_posts["posts"][] = $final_post;
	}

	echo json_encode($final_posts);
}



function get_the_first_image($post) {
	$html = str_get_html($post->post_content);	
	$img_tags = $html->find('img');
	foreach($img_tags as $img)
	{
		if($img && isset($img->src))
		{
			return $img->src;
		}		
	}
	return NULL;
}

function get_first_attachment_url($post_id)
{
	$args = array(
		'post_type' => 'attachment',
		'numberposts' => null,
		'post_status' => null,
		'post_parent' => $post_id
	); 
	$attachments = get_posts($args);
	if ($attachments && count($attachments) > 0) {
		$att = $attachments[0];
		$image = wp_get_attachment_image_src($att->ID, "full");
		if($image && count($image)>0){
			$url = $image[0];
			return $url;
		}
	}
	return NULL;
}

function get_the_first_youtube_id($post) {
	$html = str_get_html($post->post_content);	
	$video_tags = $html->find('iframe');

	foreach($video_tags as $v)
	{
		$yid = youtubeID_from_link($v->src);
		if($yid != NULL) return $yid;
	}
	return NULL;
}

function get_post_comment_count($post_id)
{
	global $wpdb;
	$request = "SELECT * FROM $wpdb->comments WHERE comment_post_ID=".$post_id;
	
	$comments = $wpdb->get_results($request);
	return count($comments);	
}



function get_post_count($categories) {
	global $wpdb;
	$post_count = 0;

		foreach($categories as $cat) :
			$querystr = "
				SELECT count
				FROM $wpdb->term_taxonomy, $wpdb->posts, $wpdb->term_relationships
				WHERE $wpdb->posts.ID = $wpdb->term_relationships.object_id
				AND $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
				AND $wpdb->term_taxonomy.term_id = $cat
				AND $wpdb->posts.post_status = 'publish'
			";
			$result = $wpdb->get_var($querystr);
  		$post_count += $result;
   endforeach; 

   return $post_count;
}


function youtubeID_from_link($link) {
	$matches = array();
    if(preg_match('~
        # Match non-linked youtube URL in the wild. (Rev:20111012)
        https?://         # Required scheme. Either http or https.
        (?:[0-9A-Z-]+\.)? # Optional subdomain.
        (?:               # Group host alternatives.
          youtu\.be/      # Either youtu.be,
        | youtube\.com    # or youtube.com followed by
          \S*             # Allow anything up to VIDEO_ID,
          [^\w\-\s]       # but char before ID is non-ID char.
        )                 # End host alternatives.
        ([\w\-]{11})      # $1: VIDEO_ID is exactly 11 chars.
        (?=[^\w\-]|$)     # Assert next char is non-ID or EOS.
        (?!               # Assert URL is not pre-linked.
          [?=&+%\w]*      # Allow URL (query) remainder.
          (?:             # Group pre-linked alternatives.
            [\'"][^<>]*>  # Either inside a start tag,
          | </a>          # or inside <a> element text contents.
          )               # End recognized pre-linked alts.
        )                 # End negative lookahead assertion.
        [?=&+%\w-]*        # Consume any URL (query) remainder.
        ~ix',
        $link,$matches)) {
	
		if(count($matches) >= 2)
		{
			return $matches[1];
		}
	}
	else return NULL;
}


?>





