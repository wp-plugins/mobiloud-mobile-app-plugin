<?php
/**
 * @package Mobiloud
 * @version 1.2.2
 */
/*
Plugin Name: Mobiloud
Plugin URI: http://www.mobiloud.com
Description: Mobiloud  for Wordpress
Author: Fifty Pixels Ltd
Version: 1.2.2
Author URI: http://www.50pixels.com
*/


define('MOBILOUD_PLUGIN_URL', plugin_dir_url( __FILE__ ));


include_once dirname( __FILE__ ) . '/configuration.php';
include_once dirname( __FILE__ ) . '/push.php';
include_once dirname( __FILE__ ) . '/stats.php';
include_once dirname( __FILE__ ) . '/ml_facebook.php';

register_activation_hook(__FILE__,'mobiloud_install');
add_action('init', 'mobiloud_plugin_init');


//INSTALLATION
//tables creation
function mobiloud_install()
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
	
	ml_facebook_install();
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


function mobiloud_plugin_menu() {
	
	add_object_page("Mobiloud", "Mobiloud",NULL, "mobiloud_menu","activate_plugins",plugin_dir_url(__FILE__)."/menu_logo.png",25);
	
	add_submenu_page('mobiloud_menu', 'Mobiloud Analytics',"Analytics", "activate_plugins",'mobiloud_charts' , "mobiloud_charts"); 	
	add_submenu_page( 'mobiloud_menu', 'Mobiloud Configuration', 'Configuration', "activate_plugins", 'mobiloud_menu_configuration', 'mobiloud_configuration_page');
}




//INIT

function mobiloud_plugin_init()
{
	global $ml_api_key, $ml_secret_key, $ml_server_host, $ml_server_port;
	global $ml_last_post_id;
	
	global $ml_push_url;
	
	//variabili che servono a verificare quando un certificato e` stato inviato correttamente
	global $ml_has_prod_cert, $ml_has_dev_cert;
	
	global $mobiloud_charts_url;
	
	//facebook
	global $ml_fb_app_id, $ml_fb_secret_key;
	
	
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
	
	if( !class_exists( 'WP_Http' ) )
	    include_once( ABSPATH . WPINC. '/class-http.php' );

	add_action('admin_menu','mobiloud_plugin_menu');
	add_action('publish_post','ml_post_published_notification');

	add_filter( 'get_avatar', 'ml_get_avatar',10,2);
	
	wp_register_style('mobiloud.css', MOBILOUD_PLUGIN_URL . 'mobiloud.css');
	wp_enqueue_style("mobiloud.css");
	
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
?>