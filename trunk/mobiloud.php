<?php
/**
 * @package Mobiloud
 * @version 2.1.2
 */
/*
Plugin Name: Mobiloud
Plugin URI: http://www.mobiloud.com
Description: Turn your WordPress site into beautiful native mobile apps. No coding needed.
Author: 50pixels
Version: 2.1.2
Author URI: http://www.mobiloud.com
*/

if(get_option('ml_debug') == 'true')
	ini_set('display_errors', 1);	

define('MOBILOUD_PLUGIN_URL', plugins_url()."/mobiloud-mobile-app-plugin");
define('MOBILOUD_PLUGIN_RELATIVE_URL',"/wp-content/plugins/mobiloud-mobile-app-plugin");


define('MOBILOUD_PLUGIN_VERSION', "2.0");
define('MOBILOUD_PUSH_API_PUBLISH_URL', "https://push.mobiloud.com/api/notifications/publish");

//define('MOBILOUD_POST_ASSETS_URL', "http://www.mobiloud.com/api/post");
define('MOBILOUD_POST_ASSETS_URL', MOBILOUD_PLUGIN_URL."/post");



//define('MOBILOUD_HOME_MENU_URL', MOBILOUD_PLUGIN_URL."/configuration/home_menu");


include_once dirname( __FILE__ ) . '/push.php';
//include_once dirname( __FILE__ ) . '/libs/cache.php';

include_once dirname( __FILE__ ) . '/stats.php';
include_once dirname( __FILE__ ) . '/ml_facebook.php';

include_once dirname( __FILE__ ) . '/subscriptions/functions.php';

include_once dirname( __FILE__ ) . '/admin/categories_pages/categories_pages.php';
include_once dirname( __FILE__ ) . '/admin/banners/banners.php';
include_once dirname( __FILE__ ) . '/admin/post/post.php';
include_once dirname( __FILE__ ) . '/admin/license/license.php';
include_once dirname( __FILE__ ) . '/admin/subscriptions/subscriptions.php';

include_once dirname( __FILE__ ) . '/admin/configuration/configuration.php';
include_once dirname( __FILE__ ) . '/push_notifications/menu.php';

//include_once dirname( __FILE__ ) . '/configuration/home_menu/home_menu.php';
//include_once dirname( __FILE__ ) . '/home_menu/functions.php';

include_once dirname( __FILE__ ) . '/homepage.php';
include_once dirname( __FILE__ ) . '/intercom.php';

include_once dirname( __FILE__ ) . '/admin_pointer.php';


register_activation_hook(__FILE__,'mobiloud_install');
add_action('init', 'mobiloud_plugin_init');
//INSTALLATION
//tables creation
function mobiloud_install()
{
	ml_notifications_install();

	ml_categories_install();
	//ml_home_menu_install();
	ml_pages_install();

	ml_facebook_install();

	ml_init_ios_app_redirect();
	ml_init_automatic_image_resize();
	ml_set_eager_loading("true");
	
}



register_activation_hook(__FILE__, 'mobiloud_activate');
add_action('admin_init', 'mobiloud_redirect');

function mobiloud_activate() {
    add_option('mobiloud_do_activation_redirect', true);
}

function mobiloud_redirect() {
    if (get_option('mobiloud_do_activation_redirect', false)) {
        delete_option('mobiloud_do_activation_redirect');
        if(!isset($_GET['activate-multi']))
        {
            wp_redirect("admin.php?page=mobiloud_menu_homepage");
        }
    }
}


function ml_notifications_install()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_notifications";
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		//install della tabella
		$sql = "CREATE TABLE " . $table_name . " (
			  id bigint(11) NOT NULL AUTO_INCREMENT,
			  time bigint(11) DEFAULT '0' NOT NULL,
			  post_id bigint(11) NOT NULL,
			  UNIQUE KEY id (id)
			);";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

}

function ml_categories_install()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_categories";
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		//install della tabella
		$sql = "CREATE TABLE " . $table_name . " (
			  id bigint(11) NOT NULL AUTO_INCREMENT,
			  time bigint(11) DEFAULT '0' NOT NULL,
			  cat_ID bigint(11) NOT NULL,
			  UNIQUE KEY id (id)
			);";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

}

function ml_pages_install()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_pages";
	

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		//install della tabella
		$sql = "CREATE TABLE " . $table_name . " (
			  id bigint(11) NOT NULL AUTO_INCREMENT,
			  time bigint(11) DEFAULT '0' NOT NULL,
			  page_ID bigint(11) NOT NULL,
			  UNIQUE KEY id (id)
			);";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	//check if there is the column 'ml_render'
	$results = $wpdb->get_results( "SHOW FULL COLUMNS FROM `" . $table_name."` LIKE 'ml_render'", ARRAY_A );
	if($results == NULL || count($results) == 0) {
		//update the table
		$sql = "ALTER TABLE $table_name ADD ml_render TINYINT(1) NOT NULL DEFAULT 1;"; 
		$wpdb->query($sql);
	}
}

function ml_facebook_install()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_fb_users";

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		//install della tabella
		$sql = "CREATE TABLE " . $table_name . " (
			  id bigint(11) NOT NULL AUTO_INCREMENT,
			  fb_id varchar(255) NOT NULL,
			  email varchar(255) NOT NULL,
			  name varchar(255) NOT NULL,
			  UNIQUE KEY id (id)
			);";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		$sql = "CREATE INDEX idx_fb_users ON $table_name(fb_id,email);";
		dbDelta($sql);
	}
	
}


function mobiloud_plugin_menu() 
{	
	add_object_page("Mobiloud", "Mobiloud",NULL, "mobiloud_menu","activate_plugins",MOBILOUD_PLUGIN_URL."/menu_logo.png",25);
	add_submenu_page( 'mobiloud_menu', 'Mobiloud Homepage', 'Design your app', "activate_plugins", 'mobiloud_menu_homepage', 'mobiloud_home_page');	
	add_submenu_page( 'mobiloud_menu', 'Mobiloud Configuration', 'Configuration', "activate_plugins", 'mobiloud_menu_configuration', 'mobiloud_configuration_page');
	add_submenu_page( 'mobiloud_menu', 'Mobiloud License', 'License', "activate_plugins", 'mobiloud_menu_license', 'ml_admin_license_page');
	
	add_submenu_page( 'mobiloud_menu', 'Mobiloud Post Customization', 'Post Customization', "activate_plugins", 'mobiloud_menu_post', 'ml_admin_post_page');
	add_submenu_page( 'mobiloud_menu', 'Mobiloud Push Notifications', 'Push Notifications', "activate_plugins", 'mobiloud_menu_push_notifications', 'mobiloud_push_notifications_page');
	add_submenu_page( 'mobiloud_menu', 'Mobiloud Subscriptions', 'Subscriptions', "activate_plugins", 'mobiloud_menu_subscriptions', 'ml_admin_subscriptions_page');		
	//add_submenu_page('mobiloud_menu', 'Mobiloud Analytics',"Analytics", "activate_plugins",'mobiloud_charts' , "mobiloud_charts"); 	
	//add_submenu_page( 'mobiloud_menu', 'Mobiloud Banners', 'Banners', "activate_plugins", 'mobiloud_menu_banners', 'ml_admin_banners_page');
	//add_submenu_page( 'mobiloud_menu', 'Mobiloud Categories and Pages', 'Categories & Pages', "activate_plugins", 'mobiloud_menu_categories_pages', 'ml_admin_categories_pages_page');
	//add_submenu_page( 'mobiloud_menu', 'Mobiloud Home Menu', 'Home Menu', "activate_plugins", 'mobiloud_menu_home_menu', 'ml_home_menu_page');
}




//INIT

function mobiloud_plugin_init()
{
	ml_categories_install();

	global $ml_api_key, $ml_secret_key, $ml_server_host, $ml_server_port;
	global $ml_last_post_id;
	
	global $ml_push_url;
	
	//variabili che servono a verificare quando un certificato e` stato inviato correttamente
	global $ml_has_prod_cert, $ml_has_dev_cert;
	
	global $mobiloud_charts_url;
	
	//facebook
	global $ml_fb_app_id, $ml_fb_secret_key;
	
	//mobile promotional message
	global $ml_popup_message_on_mobile_active, $ml_popup_message_on_mobile_url;
	
	//general configuration
	global $ml_automatic_image_resize;
	global $ml_push_notification_enabled;
	global $ml_html_banners_enable;
	
	global $ml_article_list_enable_dates;
	global $ml_article_list_enable_featured_images;
	
	global $ml_home_article_list_enabled;
	global $ml_home_page_enabled;
	global $ml_home_url_enabled;

	global $ml_home_page_id;
	global $ml_home_url;
	
	$ml_home_article_list_enabled = get_option("ml_home_article_list_enabled",true);
	$ml_home_page_enabled = get_option("ml_home_page_enabled",false);
	$ml_home_url_enabled = get_option("ml_home_url_enabled",false);

	$ml_home_page_id = get_option("ml_home_page_id");
	$ml_home_url = get_option("ml_home_url");
	
	
	global $ml_hierarchical_pages_enabled;
	$ml_hierarchical_pages_enabled = get_option("ml_hierarchical_pages_enabled",true);
	
	//content redirect
	global $ml_content_redirect_enable;
	global $ml_content_redirect_url;
	global $ml_content_redirect_category;

	$ml_html_banners_enable = get_option("ml_html_banners_enable");
	$ml_article_list_enable_dates = get_option("ml_article_list_enable_dates",true);
	$ml_article_list_enable_featured_images = get_option("ml_article_list_enable_featured_images",true);
	
	
	
	
	$ml_cert_type = "development";
	$ml_server_host = "https://api.mobiloud.com";
	#$ml_server_host = "https://localhost:3000";
	
	$ml_server_port = 80;	
	
	$ml_push_url = $ml_server_host + "/notifications/send";
	
	$ml_has_prod_cert = get_option('ml_has_prod_cert');
	$ml_has_dev_cert  = get_option('ml_has_dev_cert');
	
	$ml_api_key = get_option('ml_api_key');
	$ml_secret_key = get_option('ml_secret_key');
	
	$ml_last_post_id = get_option('ml_last_post_id');
	
	$ml_fb_app_id = get_option("ml_fb_app_id");
	$ml_fb_secret_key = get_option("ml_fb_secret_key");
	
	$ml_eager_loading = get_option('ml_eager_loading_enable');
	
	$ml_popup_message_on_mobile_active = get_option("ml_popup_message_on_mobile_active");
	$ml_popup_message_on_mobile_appid = get_option("ml_popup_message_on_mobile_appid");
	
	//enqueue js and css
	wp_enqueue_script('mobiloud',MOBILOUD_PLUGIN_URL.'mobiloud.js',array('jquery','jquery-ui'),MOBILOUD_PLUGIN_VERSION);
	
	wp_register_style('mobiloud', MOBILOUD_PLUGIN_URL . 'mobiloud.css');
	wp_register_style('mobiloud-iphone', MOBILOUD_PLUGIN_URL . "/css/iphone.css");
	wp_enqueue_style("mobiloud.css");
	wp_enqueue_style("mobiloud-iphone");


	if( !class_exists( 'WP_Http' ) )
	    include_once( ABSPATH . WPINC. '/class-http.php' );

	add_action('admin_menu','mobiloud_plugin_menu');
	

	//push notifications
	$ml_push_notification_enabled = get_option("ml_push_notification_enabled");
	if($ml_push_notification_enabled)
	{
		add_action('publish_post','ml_post_published_notification');
	}

	//cache
	//add_action('publish_post','ml_flush_posts_cache');
	//add_action('delete_post  ','ml_flush_posts_cache');
	//add_action('edit_post ','ml_flush_posts_cache');


	//content redirect
  	$ml_content_redirect_enable = get_option("ml_content_redirect_enable");
	$ml_content_redirect_url = get_option("ml_content_redirect_url");
	$ml_content_redirect_slug = get_option("ml_content_redirect_slug");

	add_action('wp_head', 'ml_add_ios_app_redirect');
	add_action('admin_footer','ml_init_intercom');

	add_filter('get_avatar', 'ml_get_avatar',10,2);
	
}

function ml_set_eager_loading($ml_eager_loading)
{
	$ml_eager_loading = $ml_eager_loading;
	ml_set_generic_option('ml_eager_loading_enable',$ml_eager_loading);
}

function ml_set_generic_option($a_option,$a_value)
{
	if(!update_option($a_option,$a_value))
		add_option($a_option,$a_value);
}

function ml_set_api_key($new_api_key)
{
	$ml_api_key = $new_api_key;
	ml_set_generic_option('ml_api_key',$ml_api_key);
}

function ml_set_secret_key($new_secret_key)
{
	$ml_secret_key= $new_secret_key;
	ml_set_generic_option('ml_secret_key',$ml_secret_key);	
}

function ml_set_server_host($new_server_host)
{
	$ml_server_host = $ml_server_host;
	ml_set_generic_option('ml_server_host',$ml_server_host);		
}

//facebook
function ml_set_fb_app_id($new_fb_app_id)
{
	$ml_fb_app_id = $new_fb_app_id;
	ml_set_generic_option('ml_fb_app_id',$ml_fb_app_id);
}
function ml_set_fb_secret_key($new_fb_secret_key)
{
	$ml_fb_secret_key = $new_fb_secret_key;
	ml_set_generic_option('ml_fb_secret_key',$ml_fb_secret_key);	
}


function ml_get_avatar($avatar,$comment)
{
	$id_or_email = $comment->comment_author_email != NULL ? $comment->comment_author_email : $comment->user_id ;

	$link = ml_facebook_get_picture_url($id_or_email);
	if($link)
	{
		//using fb
		$avatar = "<img src='$link' class='avatar avatar-50 photo' height=50 width=50>";
	}
	return $avatar;
}

//iphone redirect to app
function ml_add_ios_app_redirect()
{
	//mobile promotional message
	global $ml_popup_message_on_mobile_active, $ml_popup_message_on_mobile_appid;

	if(!isset($_GET["mobiloud"]) && $ml_popup_message_on_mobile_active)
	{
		$ml_popup_message_on_mobile_appid = get_option("ml_popup_message_on_mobile_appid");
		echo "<meta name='apple-itunes-app' content=\"app-id=$ml_popup_message_on_mobile_appid\">";
	}
}

function ml_init_ios_app_redirect() 
{
	global $ml_popup_message_on_mobile_active;
	
	$ml_popup_message_on_mobile_active = false;
	
	
	ml_set_generic_option("ml_popup_message_on_mobile_active",$ml_popup_message_on_mobile_active);
}

function ml_init_automatic_image_resize() 
{
	global $ml_automatic_image_resize;
	
	$ml_automatic_image_resize = false;
	ml_set_generic_option("ml_automatic_image_resize",$ml_automatic_image_resize);
}


?>