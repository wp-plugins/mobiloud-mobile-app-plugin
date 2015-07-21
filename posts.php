<?php
// prevent newrelic injected JavaScript breaking JSON
if(extension_loaded('newrelic')){
    newrelic_disable_autorum();
}

global $benchmarking_enabled;
if (!$benchmarking_enabled) {
    include("../../../wp-load.php");
}

set_snapshot('Loaded posts.php', 'general');

//Cache processing
$cache = get_cache('ml_json', http_build_query($_POST));
if (!empty($cache)) {
    echo_content($cache);
    set_snapshot('Loaded from cache.', 'general'); stop_snapshots('general');
    return;
};

//Redirect processing
if(!function_exists("file_get_html")) require_once("libs/simple_html_dom.php");
include_once("libs/ml_content_redirect.php");

$ml_content_redirect = new MLContentRedirect();
if($ml_content_redirect->ml_content_redirect_enable == "1" && $ml_content_redirect->is_valid_version($_POST['app_version']) )
{
    $options = $_POST;
    echo $ml_content_redirect->load_content($options);
    return;
}

include_once("categories.php");
include_once("filters.php");
include_once dirname( __FILE__ ) . '/subscriptions/functions.php';

set_snapshot('Include files', 'general');

//error_reporting(1);

init_api();

function init_api()
{
    $excluded_cats_ids = array();
    $term_arr = array();
    $currentTerm = false;
    $category = false;
    $taxonomy = false;
    $permalinkIsTaxonomy = false;

    $user_offset = (isset($_POST["offset"]) ? $_POST["offset"] : 0);
    $user_post_count = (isset($_POST["postcount"]) ? $_POST["postcount"] : false);
    $user_category = (isset($_POST["category"]) ? $_POST["category"] : false);
    $user_category_id = (isset($_POST["category_id"]) ? $_POST["category_id"] : false);
    $user_category_filter = (isset($_POST["categories"]) ? $_POST["categories"] : false);
    $user_search = (isset($_POST["search"]) ? $_POST["search"] : false);
    $user_category_permalink = (isset($_POST["permalink"]) ? $_POST["permalink"] : false);
    $user_limit = (isset($_POST["limit"]) ? $_POST["limit"] : 15);

    if($user_limit > 30) $user_limit = 30;

    $includedPostTypes = explode(",",get_option("ml_article_list_include_post_types","post"));

    //Define taxonomies/categories variables
    if (empty($user_category_id) && !empty($user_category_permalink)) {
        list($user_category_id, $permalinkIsTaxonomy, $taxonomy) = set_taxonomy_by_permalink($user_category_permalink, $user_category_id);
    }

    // Process the permalink parameter, unless we already did as a taxonomy
    if (isset($_POST["permalink"]) && !$permalinkIsTaxonomy) {
        $postIDfromURL = url_to_postid($_POST["permalink"]);
        if ($postIDfromURL) {
            $_POST["post_id"] = $postIDfromURL;
        } else {
            return;
        }
    }

    //Get terms and categories
    if($user_category_id){
        $term_arr = ml_get_term_by('id', $user_category_id, $taxonomy);
        $category = $term_arr['term'];
    } else if($user_category) {
        $term_arr = ml_get_term_by('slug', $user_category, $taxonomy);
        $category = $term_arr['term'];
    }

    $published_post_count = count_posts_by_filter($includedPostTypes, $category, $term_arr, $user_category_filter);
    set_snapshot('Count posts by filter', 'general');

    //Set offset
    if($user_post_count == NULL) $user_post_count = $published_post_count;
    $new_posts_count = $published_post_count - $user_post_count;
    $real_offset = $user_offset + $new_posts_count;

    //Check post types
    $includedPostTypes = explode(",",get_option("ml_article_list_include_post_types"));
    if (strlen($user_search) > 0 && !in_array("page", $includedPostTypes)
        && (get_option(
                "ml_include_pages_in_search", "false"
            ) == "true"
            || get_option("ml_include_pages_in_search", "false") == true)
    ) {
        array_push($includedPostTypes, "page");
    }

    if ((empty($includedPostTypes) || (isset($includedPostTypes[0]) && $includedPostTypes[0] == ''))
        && !(count($term_arr)
            && $term_arr['term'])
    ) {
        return;
    }

    //Processing by Term / by excluded categories
    if ($user_category || $user_category_id) {
        $currentTerm = $category;
    } else {
        $excluded_cats_ids = get_excluded_cats();
    }

    $query_array = get_query_array($user_limit, $excluded_cats_ids, $includedPostTypes,
        $real_offset, $user_search, $term_arr, $user_category_filter);
    set_snapshot('Get query_array', 'general');

    $posts = ml_get_posts($query_array, $user_category, $real_offset);
    set_snapshot('Get posts', 'general');

    if(!empty($excluded_cats_ids)) {
        $published_post_count = count_posts_by_query($excluded_cats_ids, $includedPostTypes, $user_search);
        set_snapshot('Count posts', 'general');
    }

    print_posts($posts, $published_post_count, $user_offset, $taxonomy, $permalinkIsTaxonomy, $_POST, $currentTerm);

}


/**
 * Get array of excluded categories
 * @return Array
 */
function get_excluded_cats()
{
    $excluded_cats_ids = array();
    $all_cats = get_categories('orderby=name');
    if (!empty($all_cats)) {
        $excluded_cats = explode(",", get_option("ml_article_list_exclude_categories", ""));

        foreach ($excluded_cats as $cat) {
            $cat = get_term_by('name', $cat, 'category');
            if (!empty($cat)) {
                array_push($excluded_cats_ids, $cat->term_taxonomy_id);
            }
        }
    }
    return $excluded_cats_ids;
}

/**
 * @param $includedPostTypes
 * @param $category
 * @param $term_arr
 * @param $user_category_filter
 *
 * @return int
 */
function count_posts_by_filter($includedPostTypes, $category, $term_arr, $user_category_filter)
{
    $published_post_count = 0;
    foreach ($includedPostTypes as $incPostType) {
        $published_post_count += wp_count_posts($incPostType)->publish;
    }

    if ($category) {
        $published_post_count = get_post_count(array($category->term_id));
        $published_post_count += ml_get_category_child_post_count($category->term_id, $term_arr['tax']);
    }

    if ($user_category_filter) {
        $arrayFilter = array();
        $arrayFilterItems = explode(",", $user_category_filter);
        foreach ($arrayFilterItems as $afi) {
            $tcat = get_category_by_slug($afi);
            if (!$tcat) {
                $tcat = get_category($afi);
            }
            if ($tcat) {
                array_push($arrayFilter, $tcat->cat_ID);
            }
        }
        $published_post_count = get_post_count($arrayFilter);
        return $published_post_count;
    }
    return $published_post_count;
}


/**
 * @param $user_limit
 * @param $excluded_cats_ids
 * @param $includedPostTypes
 * @param $real_offset
 * @param $user_search
 * @param $term_arr
 * @param $user_category_filter
 *
 * @return array
 */
function get_query_array($user_limit, $excluded_cats_ids, $includedPostTypes, $real_offset,
    $user_search, $term_arr, $user_category_filter) {

    $arrayFilter = array();

    $query_array = array('posts_per_page' => $user_limit,
                         'tax_query'      => array(
                             array(
                                 'taxonomy' => 'category',
                                 'field' => 'term_id',
                                 'terms' => $excluded_cats_ids,
                                 'operator' => 'NOT IN',
                                 'include_children' => false
                             ),
                         ),
                         'orderby'        => 'post_date',
                         'order'          => 'DESC',
                         'post_type'      => $includedPostTypes,
                         'post_status'    => 'publish',
                         'offset'         => $real_offset,
                         's'              => $user_search
    );

    if (count($term_arr) && $term_arr['term']) {
        unset($query_array['post_type']);
    }


    if (isset($_POST["categories"])) {
        $arrayFilterItems = explode(",", $user_category_filter);
        foreach ($arrayFilterItems as $afi) {
            $tcat = get_category_by_slug($afi);
            if (!$tcat) {
                $tcat = get_category($afi);
            }
            if ($tcat) {
                array_push($arrayFilter, $tcat->slug);
            }
        }
        $arrayFilterList = implode(',', $arrayFilter);
        $query_array['category_name'] = $arrayFilterList;

    } else if (count($term_arr) && $term_arr['term']) {
        $query_array['tax_query'] = array(
            array(
                'taxonomy' => $term_arr['tax'],
                'field'    => 'term_id',
                'terms'    => array($term_arr['term']->term_id)
            )
        );

    }

    return $query_array;
}


/**
 * @param $excluded_cats_ids
 * @param $includedPostTypes
 * @param $user_search
 *
 * @return mixed
 */
function count_posts_by_query($excluded_cats_ids, $includedPostTypes, $user_search)
{
    $count_query_array = array(
        'posts_per_page' => 5000,
        'tax_query'      => array(
            array(
                'taxonomy'         => 'category',
                'field'            => 'term_id',
                'terms'            => $excluded_cats_ids,
                'operator'         => 'NOT IN',
                'include_children' => false
            ),
        ),
        'post_type'      => $includedPostTypes,
        'post_status'    => 'publish',
        's'              => $user_search
    );
    wp_reset_postdata();
    $query = new WP_Query($count_query_array);
    $published_post_count = $query->found_posts;
    wp_reset_postdata();
    return $published_post_count;
}



/**
 * Get posts from database
 *
 * @param $query_array
 * @param $user_category
 * @param $real_offset
 *
 * @return array
 */
function ml_get_posts($query_array, $user_category, $real_offset)
{
    if (!isset($_POST["post_id"])) {
        wp_reset_postdata();
        $query = new WP_Query($query_array);
        $posts = $query->get_posts();
        wp_reset_postdata();

        if ($user_category == null) {
            $sticky_category_1 = get_option('sticky_category_1');
            $sticky_category_2 = get_option('sticky_category_2');
        }

        //must be the second, first because the first will be prepended
        if ($sticky_category_2 && ($real_offset == null || $real_offset == 0)) {
            //loading second 3 posts of the sticky category
            $cat = ml_get_category($sticky_category_2);
            if ($cat) {
                $query_array['posts_per_page'] = get_option('ml_sticky_category_2_posts', 3);
                $query_array['category_name'] = null;
                $query_array['category'] = null;
                $query_array['cat'] = $cat->cat_ID;
                $cat_2_posts = get_posts($query_array);
                foreach ($cat_2_posts as $p) {
                    $p->sticky = true;
                    foreach ($posts as $i => $v) {
                        if ($v->ID == $p->ID)
                            array_splice($posts, $i, 1);
                    }

                }
                $posts = array_merge($cat_2_posts, $posts);
            }
        }

        if ($sticky_category_1 && ($real_offset == null || $real_offset == 0)) {
            //loading first 3 posts of the sticky category
            $cat = get_category($sticky_category_1);
            if ($cat) {
                $query_array['posts_per_page'] = get_option('ml_sticky_category_1_posts', 3);;
                $query_array['category_name'] = null;
                $query_array['category'] = null;
                $query_array['cat'] = $cat->cat_ID;
                $cat_1_posts = get_posts($query_array);
                foreach ($cat_1_posts as $p) {
                    $p->sticky = true;
                    foreach ($posts as $i => $v) {
                        if ($v->ID == $p->ID)
                            array_splice($posts, $i, 1);
                    }

                }
                $posts = array_merge($cat_1_posts, $posts);
            }
        }

    } else {
        $single_post_id = $_POST['post_id'];
        $posts = array();
        $posts[0] = get_post($single_post_id);
    }

    //subscriptions system enabled?
    if (ml_subscriptions_enable()) {
        //user login using $_POST['username'] and $_POST['password']
        $user = ml_login_wordpress($_POST['username'], $_POST['password']);
        if (get_class($user) == "WP_User") {
            //loggedin
            //subscriptions: filter posts using capabilities
            $posts = ml_subscriptions_filter_posts($posts, $user->ID);
        } else {
            $posts = ml_subscriptions_filter_posts($posts, null);
        }
    }
    return $posts;
}

function print_posts($posts, $tot_count, $offset, $taxonomy, $permalinkIsTaxonomy, $query, $currentTerm)
{
    /** JSON OUTPUT **/
    $final_posts = array("posts" => array(), "post-count" => $tot_count, "request_parameters"=>$query);

    if (!empty($currentTerm) ){
        if (!empty($currentTerm->slug) ) $final_posts["request_parameters"]["term_slug"] = $currentTerm->slug;
        if (!empty($currentTerm->name) ) $final_posts["request_parameters"]["term_name"] = $currentTerm->name;
        $final_posts["request_parameters"]["term_taxonomy"] = $taxonomy;
        if ($taxonomy!=='category') $final_posts["request_parameters"]["is_custom_taxonomy"] = True;
    }

    $eager_loading = isset($_POST['allow_lazy']) ? $_POST['allow_lazy'] : false;

    $final_posts = get_final_posts($posts, $offset, $taxonomy, $eager_loading, $final_posts);
    set_snapshot('Get final posts loop', 'general');

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
    set_snapshot('Final json', 'general');

    $key = http_build_query($_POST);
    set_cache('ml_json', $key, $json_string);

    echo_content($json_string);

    stop_snapshots('general');
}

/**
 * Generate posts array
 *
 * @param $posts
 * @param $offset
 * @param $taxonomy
 * @param $eager_loading
 * @param $post_type
 * @param $final_posts
 *
 * @return mixed
 */
function get_final_posts($posts, $offset, $taxonomy, $eager_loading, $final_posts)
{
    foreach ($posts as $post) {

        set_snapshot('Start processing post', 'posts_loop', True);

        $final_post = array();
        $post_id = $post->ID;

        $key = http_build_query(array('post_id'=>"$post_id", "type"=>"ml_post") );
        $cache = get_cache('ml_post', $key);

        if (!empty($cache)) {
            $final_posts["posts"][] = unserialize($cache);

            set_snapshot('Post loaded from cache.', 'posts_loop');
            stop_snapshots('posts_loop');
            continue;
        };

        $final_post["post_id"] = "$post_id";
        $final_post["post_type"] = $post->post_type;

        $final_post["author"] = array();
        $final_post["categories"] = array();

        $final_post["author"]["name"] = html_entity_decode(get_author_name($post->post_author));
        $final_post["author"]["author_id"] = $post->post_author;

        $final_post["title"] = strip_tags($post->post_title);
        $final_post["title"] = html_entity_decode($final_post["title"]);

        $final_post["videos"] = array();
        $final_post["images"] = array();


        if ($offset > 0 && is_sticky($post_id)) continue;

        /** comments
        ========================================================================
         **/

        $images = get_children(
            array(
                 'post_parent'    => $post_id,
                 'post_type'      => 'attachment',
                 'post_mime_type' => 'image',
            )
        );

        $comments_count = wp_count_comments($post_id);

        $final_post["comments-count"] = 0;
        if ($comments_count) {
            $final_post["comments-count"] = intval($comments_count->approved);
        }

        set_snapshot('Define comments', 'posts_loop', True);

        /** permalink
        ========================================================================
         **/

        $final_post["permalink"] = get_permalink($post_id);

        if (strlen(trim(get_option('ml_custom_field_url', ''))) > 0) {
            $custom_url_value = get_post_meta($post->ID, get_option('ml_custom_field_url'), true);
            if (strlen(trim($custom_url_value)) > 0) {
                $final_post["permalink"] = $custom_url_value;
            }
        }

        set_snapshot('Define permalink', 'posts_loop', True);

        /** categories
        ========================================================================
         **/

        $categories = get_the_category($post_id);
        foreach ($categories as $category) {
            $final_post["categories"][] = array(
                "cat_id" => "$category->cat_ID",
                "name"   => $category->cat_name,
                "slug"   => $category->category_nicename);
        }

        if ($taxonomy !== 'category' && !empty($taxonomy)) {
            $terms = wp_get_post_terms($post_id, $taxonomy);

            foreach ($terms as $term) {
                $final_post["categories"][] = array(
                    "cat_id" => "$term->term_id",
                    "name"   => $term->name,
                    "slug"   => $term->slug);
            }
        }

        set_snapshot('Define categories', 'posts_loop', True);


        /** date
        ========================================================================
         **/


        $final_post["date"] = $post->post_date;

        if (get_option('ml_datetype', 'prettydate') == 'datetime') {
            $final_post["date_display"] = date_i18n(
                get_option('ml_dateformat', 'F j, Y'), strtotime($post->post_date), get_option('gmt_offset')
            );
        }

        set_snapshot('Define date', 'posts_loop', True);

        /** media
        ========================================================================
         **/

        try {
            $video_id = get_the_first_youtube_id($post);
        } catch (Exception $e) {}

        try {
            //featured image
            $main_image_url = get_the_first_image($post);

        } catch (Exception $e) {
            //error getting or resizing the images
        }


        if ($video_id != null) {
            $final_post["videos"][] = $video_id;
        }

        if ($main_image_url != null) {

            $image = array(
                "full"      => $main_image_url,
                "thumb"     => array("url" => $main_image_url),
                "big-thumb" => array("url" => $main_image_url)
            );

            $final_post["images"][0] = $image;
        }

        if (strlen(get_option('ml_custom_featured_image')) > 0 && class_exists('MultiPostThumbnails')) {
            $customImageUrl = MultiPostThumbnails::get_post_thumbnail_url(
                get_post_type($post->ID), Mobiloud::get_option('ml_custom_featured_image'), $post->ID, 'large'
            );
            if ($customImageUrl !== false) {
                $final_post["images"][0] = array(
                    "full"      => $customImageUrl,
                    "thumb"     => array("url" => $customImageUrl),
                    "big-thumb" => array("url" => $customImageUrl)
                );
            }
        }

        foreach ((array)$images as $image) {
            $imageToAdd = array();
            $imageToAdd["full"] = wp_get_attachment_image_src($image->ID, 'full');
            $imageToAdd["thumb"] = wp_get_attachment_image_src($image->ID, 'thumbnail');
            $imageToAdd["imageId"] = $image->ID;
            $final_post["images"][] = $imageToAdd;
        }

        set_snapshot('Define media', 'posts_loop', True);

        /** Eager loading
        ========================================================================
         **/

        if (get_option('ml_eager_loading_enable') == true || $eager_loading == "true" || $final_post["post_type"] == 'page'
            || isset($_POST['post_id'])) {} else { $final_post["lazy"] = "true"; }

        if ($eager_loading) {
            if (get_option('ml_eager_loading_enable') == true) {
                $final_post["lazy"] = "true";
            } else {
                $final_post["lazy"] = "false";
            }
        } else {
            $final_post["lazy"] = "false";
        }

        set_snapshot('Define lazy load', 'posts_loop', True);

        /** Content
        ========================================================================
         **/

        //capturing the html output generated
        ob_start();
        include("post/post.php");
        $html_content = ob_get_clean();

        //replace relative URLs with absolute
        $html_content = preg_replace(
            "#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http|/)([^\"'>]+)([\"'>]+)#", '$1' . $final_post["permalink"] . '/$2$3',
            $html_content
        );
        $final_post["content"] = $html_content;

        set_snapshot('Define content', 'posts_loop', True);

        /** Sticky
        ========================================================================
         **/

        //sticky ?
        $final_post["sticky"] = is_sticky($post->ID) || $post->sticky;


        /** Custom field
        ========================================================================
         **/

        if (strlen(get_option('ml_custom_field_name', '')) > 0) {

            if (get_option('ml_custom_field_name', '') == "excerpt") {
                $custom_field_val = html_entity_decode(urldecode(strip_tags(get_post_excerpt($post->ID))));

                $final_post['custom1'] = $custom_field_val;
            } else {
                $custom_field_val = get_post_meta($post->ID, get_option('ml_custom_field_name', ''), true);
                $final_post['custom1'] = $custom_field_val;
            }
        }
        set_snapshot('Define custom', 'posts_loop', True);

        if (get_post_format($post) == 'status') {
            $final_post["title"] = $post->post_content;
            $final_post["content"] = "";
            $final_post['custom1'] = "";
        }

        //excerpt
        $final_post['excerpt'] = html_entity_decode(urldecode(strip_tags(get_post_excerpt($post->ID))));

        set_snapshot('Define excerpt', 'posts_loop', True);

        stop_snapshots('posts_loop');

        set_cache('ml_post', $key, serialize($final_post));

        $final_posts["posts"][] = $final_post;
    }

    return $final_posts;
}


/* If we didn't get a category ID, but got a category permalink, set the category ID, if we can get it */
function set_taxonomy_by_permalink($user_category_permalink, $user_category_id){
    $permalinkIsTaxonomy = false;
    $taxonomy = false;

    $c = get_category_by_path($user_category_permalink, false);

    //If getting category fails, trying to get custom taxonomy
    if ($c==NULL){
        $c = get_taxonomy_by_path($user_category_permalink);
    }

    // So is it a category/taxonomy? Let's also tell the permalink parser we already figured it out
    if ($c) {
        $user_category_id = $c->term_id;
        $permalinkIsTaxonomy = true;
        $taxonomy = $c->taxonomy;
    }

    return array($user_category_id, $permalinkIsTaxonomy, $taxonomy);
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


/**
 * Print content using Gzip compression
 * @param $data
 */
function echo_content($data)
{
    global $benchmarking_enabled;
    if (!isset($benchmarking_enabled) || $benchmarking_enabled == false) {
        ob_start("ob_gzhandler");
        echo $data;
        ob_end_flush();
    }
}


/**
 * Set cache item as wp transient record
 *
 * @since 3.2.4
 * @param $type String - type of the record (for the flush cache by type)
 * @param $key String - unique key for the data
 * @param $data String - cached data
 */
function set_cache($type, $key, $data) {
    global $cache_disabled;
    if ($cache_disabled==True) return;

    $hash = hash('crc32', $key);
    set_transient( $type.'_'.$hash, $data, 8 * HOUR_IN_SECONDS );
}

/**
 * Get cache from wp transient database
 *
 * @since 3.2.4
 * @param $type String
 * @param $key String
 *
 * @return String | null
 */
function get_cache($type, $key) {
    global $cache_disabled;
    if ($cache_disabled==True) return Null;

    $hash = hash('crc32', $key);
    $cached = get_transient( $type.'_'.$hash );
    return (!empty($cached) ? $cached : Null);
}

/**
 * Stop benchmarking (works only with Mobiloud QA plugin)
 *
 * @since 3.2.4
 * @param $collection String
 */
function stop_snapshots($collection) {
    global $benchmarking_enabled;

    if ($benchmarking_enabled==True) {
        $user_collection = sanitize_text_field($_GET['collection']);

        if ($user_collection == $collection) {
            \PHPBenchmark\Monitor::instance()->shutdown();
        }
    }
}

/**
 * Set snapshot point for the benchmarking (works only with Mobiloud QA plugin)
 *
 * @since 3.2.4
 * @param $name String - checkpoint name
 * @param $collection String - checkpoints collection name
 * @param $once Bool - avoid multiple checkpoints in loop
 */
function set_snapshot($name, $collection, $once = False) {
    global $benchmarking_enabled;

    if ($benchmarking_enabled==True) {
        if ($once == True)
        {
            if (in_array($name, \PHPBenchmark\Monitor::instance()->once_snapshots)) {return;}
            else {\PHPBenchmark\Monitor::instance()->once_snapshots[] = $name;
            }
        }
        $user_collection = sanitize_text_field($_GET['collection']);
        if ($user_collection == $collection) {
            \PHPBenchmark\Monitor::instance()->snapshot($name);
        }
    }
}
?>