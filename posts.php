<?php

// prevent newrelic injected JavaScript breaking JSON
if(extension_loaded('newrelic')){
    newrelic_disable_autorum();
}

include("../../../wp-load.php");
//include_once("libs/errors.php");
//ini_set('display_errors', 1);

if(!function_exists("file_get_html")) {
        require_once("libs/simple_html_dom.php");       
}

include_once("libs/ml_content_redirect.php");

include_once("categories.php");
include_once("filters.php");

include_once dirname( __FILE__ ) . '/subscriptions/functions.php';

$ml_content_redirect = new MLContentRedirect();

$permalinkIsTaxonomy = false;
$permalinkIsCustomTaxonomy = false;

/*** POSTS LIST ***/
$user_offset = $_POST["offset"];
$user_post_count = $_POST["postcount"];
$user_category = $_POST["category"];
$user_category_id = $_POST["category_id"];

/* If we didn't get a category ID, but got a category permalink, set the category ID, if we can get it */
if (empty($user_category_id) && isset($_POST['permalink'])) { 
    $user_category_permalink = $_POST["permalink"];

    $c = get_category_by_path($user_category_permalink, false);


    //If getting category fails, trying to get custom taxonomy
    if ($c==NULL){
        $c = get_taxonomy_by_path($user_category_permalink);
    if ($c) {
            $permalinkIsCustomTaxonomy = true;
        }
    }

    // So is it a category/taxonomy? Let's also tell the permalink parser we already figured it out
    if ($c) {
        $user_category_id = $c->term_id;
        $permalinkIsTaxonomy = true;
        $taxonomy = $c->taxonomy;
    }
}

$user_category_filter = $_POST["categories"];

$user_search = $_POST["search"];

$app_version = $_POST['app_version'];

$user_limit = 15;

if(isset($_POST["limit"]))
{
    $user_limit = $_POST["limit"];
    if($user_limit > 30) $user_limit = 30;
}

// Process the permalink parameter, unless we already did as a taxonomy
if(isset($_POST["permalink"]) && !$permalinkIsTaxonomy) {
    $postIDfromURL = url_to_postid( $_POST["permalink"] );
    if($postIDfromURL){
        $_POST["post_id"] = $postIDfromURL;
    } else {
        return;
    }
}

$includedPostTypes = explode(",",get_option("ml_article_list_include_post_types","post"));

$published_post_count = 0;
foreach($includedPostTypes as $incPostType) {
    $published_post_count += wp_count_posts($incPostType)->publish;
}

$term_arr = array();
if($user_category_id){
    $term_arr = ml_get_term_by('id', $user_category_id, $taxonomy);
    $category = $term_arr['term'];
} else if($user_category) {
    $term_arr = ml_get_term_by('slug', $user_category, $taxonomy);
    $category = $term_arr['term'];
} 

if($category) {
    $published_post_count = get_post_count(array($category->term_id));
    $published_post_count += ml_get_category_child_post_count($category->term_id, $term_arr['tax']);
}

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
    $includedPostTypes = explode(",",get_option("ml_article_list_include_post_types"));
    
    $categoryNames = array();
    $excludeCategories = array();
    $includedCategories  =array();
    $categoryName = "";
    
    if($user_category){
        array_push($categoryNames,$user_category);
        $catObj = $category;
        $categoryName = $catObj->term_id;
    } else if($user_category_id){
        $catObj = $category;
        array_push($categoryNames,$catObj->slug);
        $categoryName = $catObj->term_id;
    } else {
        $all_cats = get_categories('orderby=name');  
        if(!empty($all_cats)) {
            $excluded_cats = explode(",",get_option("ml_article_list_exclude_categories",""));
            foreach($all_cats as $cat) {
                if(array_search($cat->cat_name, $excluded_cats) === false) {
                    $includedCategories[$cat->cat_ID] = $cat->cat_ID;
                }
            }
        }
    }
    
    if(strlen($user_search)>0 && !in_array("page",$includedPostTypes) && (get_option("ml_include_pages_in_search","false")=="true"||get_option("ml_include_pages_in_search","false")==true)){
        array_push($includedPostTypes,"page");
    }
    //echo('post types ' . json_encode($includedPostTypes) . ' ..');
    
    if((empty($includedPostTypes) || (isset($includedPostTypes[0]) && $includedPostTypes[0] == '')) && !(count($term_arr) && $term_arr['term'])) {
        return;
    }
    $query_array = array('posts_per_page' => $user_limit,
              'orderby' => 'post_date',
              'order' => 'DESC',
              'post_type' => $includedPostTypes,
              'post_status' => 'publish',
              'offset' => $real_offset,
              
              's' => $user_search
            );
    
        if(count($term_arr) && $term_arr['term']) {
            unset($query_array['post_type']);
        }
        
    if(!empty($includedCategories)) {
        $query_array['cat'] = implode(",",$includedCategories);
    }
    
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
    } else if(count($term_arr) && $term_arr['term']){
        $query_array['tax_query'] = array(
                    array(
                        'taxonomy' => $term_arr['tax'], 
                        'field' => 'term_id', 
                        'terms' => array($term_arr['term']->term_id)
                    )            
                );
        
    }
            
            //echo json_encode($query_array);
    $posts_options = array();
    if(!isset($_POST["post_id"])){

        wp_reset_postdata();
        $query = new WP_Query($query_array);
        $posts = $query->get_posts();

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
            $query_array['posts_per_page'] = get_option('ml_sticky_category_2_posts', 3);
            $query_array['category_name'] = null;
            $query_array['category'] = null;
            $query_array['cat'] = $cat->cat_ID;
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
            $query_array['posts_per_page'] = get_option('ml_sticky_category_1_posts', 3);;
            $query_array['category_name'] = null;
            $query_array['category'] = null;
            $query_array['cat'] = $cat->cat_ID;
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
            
            $posts = ml_subscriptions_filter_posts($posts,null);
        }
    }

    print_posts($posts,$published_post_count,$user_offset,$posts_options,$taxonomy,$permalinkIsTaxonomy);
}

function print_posts($posts,$tot_count,$offset,$options,$taxonomy,$permalinkIsTaxonomy)
{
    /** JSON OUTPUT **/
    $final_posts = array("posts" => array(), "post-count" => $tot_count);
    $eager_loading = isset($_POST['allow_lazy']) ? $_POST['allow_lazy'] : false;
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
        if(strlen(trim(get_option('ml_custom_field_url', ''))) > 0 ) {
            $custom_url_value = get_post_meta($post->ID, get_option('ml_custom_field_url'), true);
            if(strlen(trim($custom_url_value)) > 0) {
                $final_post["permalink"] = $custom_url_value;
            }
        }
        $final_post["author"] = array();
        $final_post["author"]["name"] = html_entity_decode(get_author_name($post->post_author));
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

        if ($taxonomy!=='category' && !empty($taxonomy)) {
            $terms = wp_get_post_terms( $post_id, $taxonomy);

            foreach($terms as $term)
            {
                $final_post["categories"][] = array(
                    "cat_id" => "$term->term_id",
                    "name" => $term->name,
                    "slug" => $term->slug);
            }
        }

        $final_post["title"] = strip_tags($post->post_title);
        $final_post["title"] = html_entity_decode($final_post["title"]);

        $final_post["date"] = $post->post_date;
        
        if (get_option('ml_datetype', 'prettydate') == 'datetime'){
            $final_post["date_display"] = date_i18n( get_option('ml_dateformat', 'F j, Y') , strtotime($post->post_date), get_option('gmt_offset'));
        }

        if(get_option('ml_eager_loading_enable') == true || $eager_loading == "true" || $post_type == 'page' || isset($_POST['post_id'])){
            
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
        
        /* if(strlen(get_option('ml_custom_featured_image', '')) > 0) {
            $custom_featured_image_url = get_post_meta($post->ID, get_option('ml_custom_featured_image', ''), true);
            $final_post["images"][0] = array(
                "full" => $custom_featured_image_url, 
                "thumb" => array("url" => $custom_featured_image_url),
                "big-thumb" => array("url" => $custom_featured_image_url)
            );
        } */
        
        if (strlen(get_option('ml_custom_featured_image')) > 0 && class_exists('MultiPostThumbnails')) {
            $customImageUrl = MultiPostThumbnails::get_post_thumbnail_url(
                    get_post_type($post->ID), Mobiloud::get_option('ml_custom_featured_image'), $post->ID, 'large'
            );
            if($customImageUrl !== false) {
                $final_post["images"][0] = array(
                    "full" => $customImageUrl, 
                    "thumb" => array("url" => $customImageUrl),
                    "big-thumb" => array("url" => $customImageUrl)
                );
            }
        }
        
        foreach ( (array) $images as $image ) {
            $imageToAdd = array();
            $imageToAdd["full"] = wp_get_attachment_image_src($image->ID,'full');
            $imageToAdd["thumb"] =  wp_get_attachment_image_src( $image->ID,'thumbnail');
            $imageToAdd["imageId"] = $image->ID;
            $final_post["images"][] = $imageToAdd;
        }   
        
        if($eager_loading) {
            if(get_option('ml_eager_loading_enable') == true) {
                $final_post["lazy"] = "true";
            } else {
                $final_post["lazy"] = "false";
            }
        } else {
            $final_post["lazy"] = "false";
        }
        
        // if($final_post["lazy"] == 'true' || isset($_POST["post_id"])) {
            //capturing the html output generated
            ob_start();
            include("post/post.php");
            $html_content = ob_get_clean();        

            //replace relative URLs with absolute
            $html_content = preg_replace("#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http|/)([^\"'>]+)([\"'>]+)#", '$1'.$final_post["permalink"].'/$2$3', $html_content);
            $final_post["content"] = $html_content;
        // } else {
        //     $final_post['content'] = '';
        // }
        
        //sticky ?
        $final_post["sticky"] = is_sticky($post->ID) || $post->sticky;

        //custom field?
        if(strlen(get_option('ml_custom_field_name', '')) > 0) {
            
            if (get_option('ml_custom_field_name', '') == "excerpt") {
//              setup_postdata( $post->ID );
                $custom_field_val = html_entity_decode(urldecode(strip_tags(get_post_excerpt($post->ID))));
//              $custom_field_val = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $custom_field_val); 

                $final_post['custom1'] = $custom_field_val;         
            } else {
                $custom_field_val = get_post_meta($post->ID, get_option('ml_custom_field_name', ''), true);
                $final_post['custom1'] = $custom_field_val;             
            }
            
        }
        
        //excerpt
        $final_post['excerpt'] = html_entity_decode(urldecode(strip_tags(get_post_excerpt($post->ID))));
        
        $final_posts["posts"][] = $final_post;
    }

    // Add a top-level attribute for the taxonomy if we had a taxonomy permalink request
    if ($permalinkIsTaxonomy) {
        $final_posts['taxonomy'] = $taxonomy;
    }

    $current_user = wp_get_current_user();
    $final_posts = apply_filters('ml_posts',$final_posts,$current_user);

    //Preprocessing text for avoid "Invalid UTF-8" errors
    $charset = 'UTF-8';
    array_walk_recursive($final_posts,function(&$data) use ($charset){
            $data = iconv($charset, 'UTF-8//IGNORE', $data);
        });

    $json_string = json_encode($final_posts);
    echo $json_string;

    //caching json string if is main posts
    if($offset == 0) {
        //ml_cache_set('main_posts',$json_string);
    }
}

function get_post_excerpt($post_id) {
    global $post;  
    $save_post = $post;
    $post = get_post($post_id);
    $output = get_the_excerpt();
    $post = $save_post;
    return $output;    
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

function ml_get_term_by($by, $term_ref, $taxonomy_used) {
    $taxes = ml_get_used_taxonomies($taxonomy_used);

    foreach($taxes as $tax) {
        $term = get_term_by($by, $term_ref, $tax);
        if($term) {
            return array('term'=>$term, 'tax'=>$tax);
        }
    }
        
    return array('term'=>false, 'tax'=>false);
}

function ml_get_used_taxonomies($taxonomy_used) {
    $taxes = array('category', 'post_tag', $taxonomy_used);
    $menu_terms = get_option('ml_menu_terms', array());
    foreach($menu_terms as $term) {
        $term_data = explode("=", $term);
        $taxes[] = $term_data[0];
    }
    return $taxes;
}

function ml_get_category_child_post_count($parent_id, $taxonomy='category') {
    $count = 0;
    $tax_terms = get_terms($taxonomy,array('child_of'=>$parent_id));
    foreach ($tax_terms as $tax_term) {
        $count +=$tax_term->count;
    }
    return $count;
}

/**
 * Retrieve taxonomy based on URL containing the taxonomy slug.
 *
 * Breaks the $taxonomy_path parameter up to get the taxonomy slug.
 *
 * @since 3.2.3
 *
 * @param string $taxonomy_path URL containing taxonomy slugs.
 * @param string $output Optional. Constant OBJECT, ARRAY_A, or ARRAY_N
 * @return null|object|array Null on failure. Type is based on $output value.
 */
function get_taxonomy_by_path( $taxonomy_path, $output = OBJECT ) {
    $taxonomy_path = rawurlencode( urldecode( $taxonomy_path ) );
    $taxonomy_path = str_replace( '%2F', '/', $taxonomy_path );
    $taxonomy_path = str_replace( '%20', ' ', $taxonomy_path );
    $taxonomy_paths = '/' . trim( $taxonomy_path, '/' );
    $leaf_path  = sanitize_title( basename( $taxonomy_paths ) );
    $taxonomies = get_taxonomies();

    $taxonomies = get_terms( $taxonomies, array('get' => 'all', 'slug' => $leaf_path ) );

    if ( empty( $taxonomies ) )
        return null;

    $taxonomy = get_term( reset( $taxonomies )->term_id, reset( $taxonomies )->taxonomy, $output );

    return $taxonomy;
}

?>