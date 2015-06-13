<?php
	add_action('wp_ajax_ml_admin_license_keys', 'ml_admin_license_callback');
	add_action('wp_enqueue_scripts', 'ml_admin_license_enqueue_script');

	function ml_admin_license_callback()
	{
		global $ml_api_key, $ml_secret_key;
		
		//save api key
		if(isset($_POST['ml_api_key']))
		{
			ml_set_api_key($_POST['ml_api_key']);
			$ml_api_key = get_option('ml_api_key');
		}

		//save api secret
		if(isset($_POST['ml_secret_key']))
		{
			ml_set_secret_key($_POST['ml_secret_key']);
			$ml_secret_key = get_option('ml_secret_key');
		}
		
		ml_admin_license_page();
		die();
	}

	function ml_admin_license_enqueue_script() {
		wp_enqueue_script('mobiloud_admin_license',MOBILOUD_PLUGIN_URL.'admin/license/license.js',array('jquery','jquery-ui-core'),MOBILOUD_PLUGIN_VERSION);
	}

	function ml_admin_license_page() {
		global $ml_api_key, $ml_secret_key;	

		echo "<div id='ml_admin_license_page'>";	
		wp_register_style('mobiloud_admin_license', MOBILOUD_PLUGIN_URL . '/admin/license/license.css');
		wp_enqueue_style("mobiloud_admin_license");
		
		include(dirname( __FILE__ ).'/page.php');
		echo "</div>";
	}
?>
