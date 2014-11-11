<?php
/*
Plugin Name: Mobiloud
Plugin URI: http://www.mobiloud.com
Description: Beautiful mobile apps for your WordPress blog or news site. No coding required.
Author: 50pixels
Version: 3.0.7
Author URI: http://www.mobiloud.com
*/

define('MOBILOUD_PLUGIN_VERSION', "3.0.7");
define('MOBILOUD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define('MOBILOUD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define('MOBILOUD_PLUGIN_RELATIVE_URL',"/wp-content/plugins/mobiloud-mobile-app-plugin");
define('MOBILOUD_POST_ASSETS_URL', MOBILOUD_PLUGIN_URL."/post");
define('MOBILOUD_PB_NOSSL_URL', 'http://api.pushbots.com');
define('MOBILOUD_PB_SSL_URL', 'https://api.pushbots.com');
define('MOBILOUD_CONTENT_URL', 'http://www.mobiloud.com/plugin_content/');

include_once MOBILOUD_PLUGIN_DIR . 'push.php';
include_once MOBILOUD_PLUGIN_DIR . 'stats.php';
include_once MOBILOUD_PLUGIN_DIR . 'subscriptions/functions.php';

include_once MOBILOUD_PLUGIN_DIR . 'admin/categories_pages/categories_pages.php';
include_once MOBILOUD_PLUGIN_DIR . 'admin/banners/banners.php';
include_once MOBILOUD_PLUGIN_DIR . 'admin/post/post.php';
include_once MOBILOUD_PLUGIN_DIR . 'admin/license/license.php';
include_once MOBILOUD_PLUGIN_DIR . 'admin/subscriptions/subscriptions.php';

include_once MOBILOUD_PLUGIN_DIR . 'admin/configuration/configuration.php';
include_once MOBILOUD_PLUGIN_DIR . 'admin/menu/menu.php';
include_once MOBILOUD_PLUGIN_DIR . 'push_notifications/menu.php';

include_once MOBILOUD_PLUGIN_DIR . 'homepage.php';
include_once MOBILOUD_PLUGIN_DIR . 'intercom.php';

include_once MOBILOUD_PLUGIN_DIR . 'admin_pointer.php';

require_once(MOBILOUD_PLUGIN_DIR . 'class.mobiloud-app-preview.php');

require_once(MOBILOUD_PLUGIN_DIR . 'class.mobiloud.php');
register_activation_hook(__FILE__, array('Mobiloud', 'mobiloud_activate'));
add_action( 'init', array( 'Mobiloud', 'init' ) );

if ( is_admin() ) {
	require_once( MOBILOUD_PLUGIN_DIR . 'class.mobiloud-admin.php' );
	add_action( 'init', array( 'Mobiloud_Admin', 'init' ) );
}


add_action('init', 'mobiloud_plugin_init');

//INIT

function mobiloud_plugin_init()
{
	global $ml_api_key, $ml_secret_key, $ml_server_host, $ml_server_port;
    global $ml_pb_app_id, $ml_pb_secret_key;
	global $ml_last_post_id;

	global $ml_push_url;

	//variabili che servono a verificare quando un certificato e` stato inviato correttamente
	global $ml_has_prod_cert, $ml_has_dev_cert;

	global $mobiloud_charts_url;

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

	global $ml_home_page_full;
	global $ml_home_page_id;
	global $ml_home_url;

	global $ml_show_article_list_menu_item;
	global $ml_article_list_menu_item_title;

	$ml_home_article_list_enabled = get_option("ml_home_article_list_enabled",true);
	$ml_home_page_enabled = get_option("ml_home_page_enabled",false);
	$ml_home_url_enabled = get_option("ml_home_url_enabled",false);

	$ml_home_page_full = get_option("ml_home_page_full",false);
	$ml_home_page_id = get_option("ml_home_page_id");
	$ml_home_url = get_option("ml_home_url");

	$ml_show_article_list_menu_item = get_option("ml_show_article_list_menu_item",true);
	$ml_article_list_menu_item_title = get_option("ml_article_list_menu_item_title","Articles");

	global $ml_hierarchical_pages_enabled;
	$ml_hierarchical_pages_enabled = get_option("ml_hierarchical_pages_enabled",true);

    global $ml_pb_use_ssl;
    $ml_pb_use_ssl = get_option('ml_pb_use_ssl', false);
    if($ml_pb_use_ssl) {
        define('MOBILOUD_PB_URL', MOBILOUD_PB_SSL_URL);
    } else {
        define('MOBILOUD_PB_URL', MOBILOUD_PB_NOSSL_URL);
    }

	global $ml_article_list_include_post_types;
	$ml_article_list_include_post_types = get_option("ml_article_list_include_post_types","post");

	global $ml_article_list_exclude_categories;
	$ml_article_list_exclude_categories = get_option("ml_article_list_exclude_categories","");

	global $ml_include_pages_in_search;
	$ml_include_pages_in_search = get_option("ml_include_pages_in_search",false);

	global $ml_menu_show_favorites;
	$ml_menu_show_favorites = get_option("ml_menu_show_favorites",true);

	global $ml_menu_urls;
	$ml_menu_urls = get_option("ml_menu_urls",array());

	//content redirect
	global $ml_content_redirect_enable;
	global $ml_content_redirect_url;
	global $ml_content_redirect_category;

	$ml_html_banners_enable = get_option("ml_html_banners_enable");
	$ml_article_list_enable_dates = get_option("ml_article_list_enable_dates",true);
	$ml_article_list_enable_featured_images = get_option("ml_article_list_enable_featured_images",true);


    $ml_pb_app_id = get_option('ml_pb_app_id');
	$ml_pb_secret_key = get_option('ml_pb_secret_key');

	$ml_last_post_id = get_option('ml_last_post_id');

	$ml_fb_app_id = get_option("ml_fb_app_id");
	$ml_fb_secret_key = get_option("ml_fb_secret_key");

	$ml_eager_loading = get_option('ml_eager_loading_enable');

	$ml_popup_message_on_mobile_active = get_option("ml_popup_message_on_mobile_active");
	$ml_popup_message_on_mobile_appid = get_option("ml_popup_message_on_mobile_appid");
    
	if( !class_exists( 'WP_Http' ) )
	    include_once( ABSPATH . WPINC. '/class-http.php' );

	//push notifications
	$ml_push_notification_enabled = get_option("ml_push_notification_enabled");

	//content redirect
  	$ml_content_redirect_enable = get_option("ml_content_redirect_enable");
	$ml_content_redirect_url = get_option("ml_content_redirect_url");
	$ml_content_redirect_slug = get_option("ml_content_redirect_slug");
}