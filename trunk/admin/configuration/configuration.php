<?php
include_once dirname( __FILE__ ) . '/../../categories.php';
include_once dirname( __FILE__ ) . '/../../pages.php';
include_once dirname( __FILE__ ) . '/app.php';
include_once dirname( __FILE__ ) . '/general/general.php';
include_once dirname( __FILE__ ) . '/categories.php';
include_once dirname( __FILE__ ) . '/pages.php';
include_once dirname( __FILE__ ) . '/homepage.php';
include_once dirname( __FILE__ ) . '/sticky_categories.php';
// include_once dirname( __FILE__ ) . '/facebook.php';



function mobiloud_configuration_page()
{
	?>
	
	<div></div>


	<div class="wrap">
		<div id="mobiloud_analytics_title" style="margin-top:20px;">
			<img src="<?php echo MOBILOUD_PLUGIN_URL;?>/mobiloud_36.png" style="float:left;margin-top:5px;">
			<h1 style="float:left;margin-left:20px;color:#555">Mobiloud Configuration</h3>
			<div style="clear:both;">
		</div>

		<p>&nbsp;</p>
		
		<div class="narrow">

			<!-- GENERAL -->
			<div id="ml_configuration_general" class="stuffbox" style="padding:20px;">
				<?php ml_configuration_general_ajax_load(); ?>
			</div>
            
            <!-- HOME PAGE -->
            <div id="ml_configuration_home" class="stuffbox" style="padding:20px;">
            	<?php ml_configuration_home_ajax_load(); ?>
            </div>

			<!-- CATEGORIES -->
			<div id="ml_configuration_categories" class="stuffbox" style="padding:20px;">
				<?php ml_configuration_categories_ajax_load(); ?>
			</div>

			<!-- PAGES -->
			<div id="ml_configuration_pages" class="stuffbox" style="padding:20px;">
				<?php ml_configuration_pages_ajax_load(); ?>
			</div>
		
			<!-- STICKY CATEGORIES -->
			<div id="ml_configuration_sticky_categories" class="stuffbox" style="padding:20px;">
				<?php ml_configuration_sticky_categories_ajax_load(); ?>
			</div>
			
			<!-- APP REDIRECT -->
			<div id="ml_configuration_app_redirect" class="stuffbox" style="padding:20px;">
				<?php ml_configuration_app_redirect_ajax_load(); ?>
			</div>
			
			<!-- FACEBOOK -->
			<!-- <div id="ml_facebook_keys" class="stuffbox">
				<?php // ml_configuration_facebook_ajax_load(); ?>
			</div> -->
		</div>

	</div>
	<?php
}




function ml_update_configuration($cert_content,$ml_cert_type)
{
	global $ml_api_key, $ml_secret_key, $ml_server_host, $ml_server_port;
	
	$parameters = array('api_key' => $ml_api_key,'secret_key' => $ml_secret_key);
	if($ml_cert_type != NULL) $parameters['apn_env'] = $ml_cert_type;
	if($cert_content != NULL) $parameters['apn_cert'] = $cert_content;
	
	$request = new WP_Http;
	$url = "$ml_server_host/notifications/update";
	$result = $request->request( $url, array('method' => 'POST', 
											 'timeout' => 20,
											 'body' => $parameters) );

	if($result != NULL && !isset($result->errors))
	{
		$dict = json_decode($result['body']);
		$service = NULL;
		if($dict)
		{
			echo "<script>alert('Saved');</script>";
		}
	}
}

function ml_send_notification_test()
{
	ml_send_notification("This is a test notification from Mobiloud");
	echo "<script>alert('Sent');</script>";
}


function ml_start_service()
{
	global $ml_api_key, $ml_secret_key, $ml_server_host, $ml_server_port;
	
	$parameters = array('api_key' => $ml_api_key,'secret_key' => $ml_secret_key);
	
	$request = new WP_Http;
	$url = "$ml_server_host/notifications/admin/start_service";
	$result = $request->request( $url, array('method' => 'POST', 
											 'timeout' => 20,
											 'body' => $parameters) );
	
}


function ml_stop_service()
{
	global $ml_api_key, $ml_secret_key, $ml_server_host, $ml_server_port;
	
	$parameters = array('api_key' => $ml_api_key,'secret_key' => $ml_secret_key);
	
	$request = new WP_Http;
	$url = "$ml_server_host/notifications/admin/stop_service";
	$result = $request->request( $url, array('method' => 'POST', 
											 'timeout' => 20,
											 'body' => $parameters) );
	
}

?>