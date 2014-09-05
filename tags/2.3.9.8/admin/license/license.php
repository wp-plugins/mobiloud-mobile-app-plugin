<?php
	add_action('wp_ajax_ml_admin_license_keys', 'ml_admin_license_callback');
	add_action('wp_enqueue_scripts', 'ml_admin_license_enqueue_script');

	function ml_admin_license_callback()
	{
		global $ml_pb_app_id, $ml_pb_secret_key;
		
		//save app id
		if(isset($_POST['ml_pb_app_id']))
		{
			ml_set_pb_app_id($_POST['ml_pb_app_id']);
			$ml_pb_app_id = get_option('ml_pb_app_id');
		}

		//save api secret
		if(isset($_POST['ml_pb_secret_key']))
		{
			ml_set_pb_secret_key($_POST['ml_pb_secret_key']);
			$ml_pb_secret_key = get_option('ml_pb_secret_key');
		}
		
		ml_admin_license_page();
		die();
	}

	function ml_admin_license_enqueue_script() {
		wp_enqueue_script('mobiloud_admin_license',MOBILOUD_PLUGIN_URL.'admin/license/license.js',array('jquery','jquery-ui'),MOBILOUD_PLUGIN_VERSION);
	}

	function ml_admin_license_page() {
		global $ml_pb_app_id, $ml_pb_secret_key;

		echo "<div id='ml_admin_license_page'>";	
		wp_register_style('mobiloud_admin_license', MOBILOUD_PLUGIN_URL . '/admin/license/license.css');
		wp_enqueue_style("mobiloud_admin_license");
		
		include(dirname( __FILE__ ).'/page.php');
		echo "</div>";
	}
?>
