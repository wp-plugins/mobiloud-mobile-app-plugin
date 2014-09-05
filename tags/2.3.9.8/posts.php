<?php
include("../../../wp-load.php");
//include_once("libs/errors.php");
ini_set('display_errors', 1);

if(!function_exists("file_get_html")) {
        require_once("libs/simple_html_dom.php");
}

include_once("libs/ml_content_redirect.php");

include_once("categories.php");
include_once("filters.php");

include_once dirname( __FILE__ ) . '/subscriptions/functions.php';

$ml_content_redirect = new MLContentRedirect();

/*** POSTS LIST ***/

$user_offset = $_POST["offset"];
$user_post_count = $_POST["postcount"];
$user_category = $_POST["category"];
$user_category_id = $_POST["category_id"];
$user_category_filter = $_POST["categories"];

$user_search = $_POST["search"];

$app_version = $_POST['app_version'];

$user_limit = 15;

if(isset($_POST["limit"]))
{
    $user_limit = $_POST["limit"];
	if($user_limit > 30) $user_limit = 30;
}

if(isset($_POST["permalink"])){

	$postIDfromURL = url_to_postid( $_POST["permalink"] );
	if($postIDfromURL){
		$_POST["post_id"] = $postIDfromURL;
	} else {
		return;
	}

}

$published_post_count = wp_count_posts()->publish;

if($user_category_id){
	$category = get_category($user_category_id);
} else if($user_category) {
	$category = get_category_by_slug($user_category);
}


if($category) $published_post_count = get_post_count(array($category->cat_ID));

if($user_category_filter){
	$arrayFilter = array();
	$arrayFilterItems = explode(",",$user_category_filter);
	foreach($arrayFilterItems as $afi){
		$tcat = get_category_by_slug($afi);
		if(!$tcat){
			$tcat = get_category($afi);
		}
		if($tcat){
			array_push($arrayFilter,$tcat->cat_ID);
		}
	}
	$published_post_count = get_post_count($arrayFilter);
}

if($user_offset == NULL) $user_offset = 0;
if($user_post_count == NULL) $user_post_count = $published_post_count;

$new_posts_count = $published_post_count - $user_post_count;
$real_offset = $user_offset + $new_posts_count;



if($ml_content_redirect->ml_content_redirect_enable == "1" &&
	 $ml_content_redirect->is_valid_version($app_version) )
{
	$options = $_POST;
	echo $ml_content_redirect->load_content($options);
	return;
}



else {
	//not cached
	$includedPostTypes = explode(",",get_option("ml_article_list_include_post_types","post"));

	$categoryNames = array();
	$excludeCategories = array();
	$categoryName = "";

	if($user_category){
		array_push($categoryNames,$user_category);
		$catObj = get_category_by_slug($user_category);
  		$categoryName = $catObj->cat_ID;
	} else if($user_category_id){
		$catObj = get_category($user_category_id);
		array_push($categoryNames,$catObj->slug);
		$categoryName = $catObj->cat_ID;
	} else {
		foreach(explode(",",get_option("ml_article_list_exclude_categories","")) as $cname){
			array_push($excludeCategories,get_cat_ID($cname));
		}
	}

	if(strlen($user_search)>0 && !in_array("page",$includedPostTypes) && (get_option("ml_include_pages_in_search","false")=="true"||get_option("ml_include_pages_in_search","false")==true)){
		array_push($includedPostTypes,"page");
	}
	//echo('post types ' . json_encode($includedPostTypes) . ' ..');


	$query_array = array('posts_per_page' => $user_limit,
			  'orderby' => 'post_date',
			  'order' => 'DESC',
			  'post_type' => $includedPostTypes,
			  'post_status' => 'publish',
			  'offset' => $real_offset,
			  'category__not_in' => $excludeCategories,
			  's' => $user_search
			);

	$arrayFilter = array();

	if(isset($_POST["categories"])){
		$arrayFilter = array();
		$arrayFilterItems = explode(",",$user_category_filter);
		foreach($arrayFilterItems as $afi){
			$tcat = get_category_by_slug($afi);
			if(!$tcat){
				$tcat = get_category($afi);
			}
			if($tcat){
				array_push($arrayFilter,$tcat->slug);
			}
		}
		$arrayFilterList = implode(',',$arrayFilter);
		$query_array['category_name'] = $arrayFilterList;
	} else if(isset($_POST["category_id"])){
		$query_array['cat'] = $categoryName;
	} else if($categoryName){
		$query_array['category'] = $categoryName;
	}


			//echo json_encode($query_array);

	$posts_options = array();
	if(!isset($_POST["post_id"])){
		$posts = get_posts($query_array);
		$posts_options = array();


		if($user_category == NULL)
	{
		$sticky_category_1 = get_option('sticky_category_1');
		$sticky_category_2 = get_option('sticky_category_2');
	}
//wp_reset_postdata();
	//must be the second, first because the first will be prepended
	if($sticky_category_2 && ($real_offset == NULL || $real_offset == 0))
	{
		//loading second 3 posts of the sticky category
		$cat = ml_get_category($sticky_category_2);
		if($cat)
		{
			$query_array['posts_per_page'] = 3;
			//$query_array['category_name'] = $cat->slug;
			$query_array['category'] = get_cat_ID($cat->slug);
			$cat_2_posts = get_posts($query_array);
			foreach($cat_2_posts as $p)
			{
				$p->sticky = true;
				foreach ( $posts as $i => $v )
				{
   	    	if ($v->ID == $p->ID)
		      	array_splice($posts, $i,1);
  			}

			}
			$posts = array_merge($cat_2_posts,$posts);
			//wp_reset_postdata();
		}
	}

	if($sticky_category_1 && ($real_offset == NULL || $real_offset == 0))
	{
		//loading first 3 posts of the sticky category
		$cat = get_category($sticky_category_1);
		if($cat)
		{
			$query_array['posts_per_page'] = 3;
			//$query_array['category_name'] = $cat->slug;
			$query_array['category'] = get_cat_ID($cat->slug);
			$cat_1_posts = get_posts($query_array);
			foreach($cat_1_posts as $p)
			{
				$p->sticky = true;
				foreach ( $posts as $i => $v )
				{
   	    	if ($v->ID == $p->ID)
		      	array_splice($posts, $i,1);
  			}

			}
			$posts = array_merge($cat_1_posts,$posts);
			//wp_reset_postdata();
		}
	}

	} else {
		$single_post_id = $_POST['post_id'];
		$posts = array();
		$posts[0] = get_post($single_post_id);
	}



	//subscriptions system enabled?
	if(ml_subscriptions_enable()) {
		//user login using
		//$_POST['username']
		//$_POST['password']
		$user = ml_login_wordpress($_POST['username'],$_POST['password']);
		if(get_class($user) == "WP_User") {
			//loggedin
			//subscriptions: filter posts using capabilities
			$posts = ml_subscriptions_filter_posts($posts,$user->ID);

		} else {
			$posts = array();
		}
	}

	print_posts($posts,$published_post_count,$user_offset,$posts_options);
}

function print_posts($posts,$tot_count,$offset,$options)
{
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
		$comments_count = wp_count_comments($post_id);

		$final_post["comments-count"] = 0;
		if($comments_count) {
			$final_post["comments-count"] = intval($comments_count->approved);
		}

		$final_post["permalink"] = get_permalink($post_id);

		$final_post["author"] = array();
		$final_post["author"]["name"] = get_author_name($post->post_author);
		$final_post["author"]["author_id"] = $post->post_author;

		$final_post["post_type"] = $post->post_type;

		$final_post["categories"] = array();

		$categories = get_the_category($post_id);
		foreach($categories as $category)
		{
			$final_post["categories"][] = array(
				"cat_id" => "$category->cat_ID",
				"name" => $category->cat_name,
				"slug" => $category->category_nicename);
		}

		$final_post["title"] = preg_replace('#<!--(.*?)-->#', '', strip_tags(html_entity_decode($post->post_title)));
		$final_post["date"] = $post->post_date;

		if(get_option('ml_eager_loading_enable') == 'true' || $eager_loading == "true" || $post_type == 'page' || isset($_POST['post_id'])){

		} else {
			$final_post["lazy"] = "true";
		}

		try {
			$video_id = get_the_first_youtube_id($post);
		}
		catch (Exception $e) {}

		try {

			//featured image
			$main_image_url = get_the_first_image($post);

		}
		catch (Exception $e) {
			//error getting or resizing the images
		}

		$final_post["videos"] = array();
		$final_post["images"] = array();

		if($video_id != NULL)
		{
			$final_post["videos"][] = $video_id;
		}


		if($main_image_url != NULL)
		{

			$image = array(
											"full" => $main_image_url,
											"thumb" => array("url" => $main_image_url),
											"big-thumb" => array("url" => $main_image_url)
										);

			$final_post["images"][0] = $image;
		}

		foreach ( (array) $images as $image ) {
			$imageToAdd = array();
			$imageToAdd["full"] = wp_get_attachment_image_src($image->ID,'full');
			$imageToAdd["thumb"] =  wp_get_attachment_image_src( $image->ID,'thumbnail');
			$imageToAdd["imageId"] = $image->ID;
			$final_post["images"][] = $imageToAdd;
		}


		//capturing the html output generated
		ob_start();
		include("post/post.php");
		$html_content = ob_get_clean();

        //replace relative URLs with absolute
        $html_content = preg_replace("#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http|/)([^\"'>]+)([\"'>]+)#", '$1'.$final_post["permalink"].'/$2$3', $html_content);
		$final_post["content"] = $html_content;

		//sticky ?
		$final_post["sticky"] = is_sticky($post->ID) || $post->sticky;


		$final_posts["posts"][] = $final_post;
	}

	$json_string = json_encode($final_posts);
	echo $json_string;

	//caching json string if is main posts
	if($offset == 0) {
		//ml_cache_set('main_posts',$json_string);
	}
}



function get_the_first_image($post) {
	//try to get the featured image
	if (has_post_thumbnail( $post->ID ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
		if($image != NULL && count($image) > 0) {
			return $image[0];
		}
	}

	//if there is no featured image, check what's the first image
	//inside the html.

	$html = str_get_html($post->post_content);
	if($html == NULL)
		return NULL;

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
	if($html == NULL)
		return NULL;

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
