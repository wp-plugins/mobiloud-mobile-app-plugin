<?php
/**
 * @package Mobiloud
 * @version 1.5.3
 */
/*
Plugin Name: Mobiloud
Plugin URI: http://www.mobiloud.com
Description: Mobiloud  for Wordpress
Author: Fifty Pixels Ltd
Version: 1.5.3
Author URI: http://www.50pixels.com
*/

ini_set('display_errors', 1);

define('MOBILOUD_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('MOBILOUD_PLUGIN_VERSION', "1.5.3");


include_once dirname( __FILE__ ) . '/push.php';
include_once dirname( __FILE__ ) . '/stats.php';
include_once dirname( __FILE__ ) . '/ml_facebook.php';

include_once dirname( __FILE__ ) . '/configuration.php';
include_once dirname( __FILE__ ) . '/intercom.php';

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
	
	ml_init_ios_app_redirect();
	ml_init_automatic_image_resize();
	
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
	
	//mobile promotional message
	global $ml_popup_message_on_mobile_active, $ml_popup_message_on_mobile_url;
	
	global $ml_automatic_image_resize;
	
	
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
	
	$ml_popup_message_on_mobile_active = get_option("ml_popup_message_on_mobile_active");
	$ml_popup_message_on_mobile_appid = get_option("ml_popup_message_on_mobile_appid");
	
	if( !class_exists( 'WP_Http' ) )
	    include_once( ABSPATH . WPINC. '/class-http.php' );

	add_action('admin_menu','mobiloud_plugin_menu');
	
	//MOBILOUD AD
	//delete_option("ml_mobiloud_ad_notice_disabled");
	$ml_mobiloud_ad_notice_disabled = get_option("ml_mobiloud_ad_notice_disabled");
	if(!$ml_mobiloud_ad_notice_disabled)
	{
		add_action('admin_notices','ml_mobiloud_ad_notice');
		add_action('wp_ajax_ml_disable_mobiloud_ad_notice', 'ml_disable_mobiloud_ad_notice_callback');
		
	}

	add_action('publish_post','ml_post_published_notification');
	add_action('wp_head', 'ml_add_ios_app_redirect');
	add_action('admin_footer','ml_init_intercom');

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
	
	$ml_automatic_image_resize = true;
	ml_set_generic_option("ml_automatic_image_resize",$ml_automatic_image_resize);
}

function ml_disable_mobiloud_ad_notice_callback()
{
	add_option("ml_mobiloud_ad_notice_disabled",true);
	die();
}

function ml_mobiloud_ad_notice()
{
		
	?>
	<div class="updated" style="height:55px;padding:10px;" id="ml_mobiloud_ad_notice">
		<p style="font-size:15px;" align="center">Congratulations, the Mobiloud plugin is now installed. Contact <a href="mailto:support@mobiloud.com">support@mobiloud.com</a> for help configuring it.
		</p>
		<p align="center" style="margin-top:8px;">
			<a href="#" class="button-primary" id="ml_mobiloud_ad_notice_button">
				Hide this message and visit Mobiloud.com to get an app designed for your site!
			</a>
		</p>
	</div>
	
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		
		jQuery("#ml_mobiloud_ad_notice_button").click(function(){
			var data = {
				action: 'ml_disable_mobiloud_ad_notice'
			};

			$.post(ajaxurl, data, function(response) {
				eval(response);
				//saving the result and reloading the div
				jQuery("#ml_mobiloud_ad_notice").remove();
				window.location = "http://www.mobiloud.com";
			});			
			
		});

			
	});
	</script>	
	<?php
}
?>