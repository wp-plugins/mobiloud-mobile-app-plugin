<?php
/**
 * @package Mobiloud
 * @version 1.0.1
 */
/*
Plugin Name: Mobiloud
Plugin URI: http://www.mobiloud.com
Description: Mobiloud  for Wordpress
Author: Fifty Pixels Ltd
Version: 1.0.1
Author URI: http://www.50pixels.com
*/



define('MOBILOUD_PLUGIN_URL', plugin_dir_url( __FILE__ ));


include_once dirname( __FILE__ ) . '/configuration.php';
include_once dirname( __FILE__ ) . '/push.php';
include_once dirname( __FILE__ ) . '/stats.php';

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
	

	if( !class_exists( 'WP_Http' ) )
	    include_once( ABSPATH . WPINC. '/class-http.php' );

	add_action('admin_menu','mobiloud_plugin_menu');
	add_action('publish_post','ml_post_published_notification');

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



//push notifications




?>