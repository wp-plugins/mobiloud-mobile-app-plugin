<?php
	add_action('wp_ajax_ml_admin_subscriptions_save_options', 'ml_admin_subscriptions_save_options_callback');

	function ml_admin_subscriptions_save_options_callback()
	{
		if(isset($_POST['ml_subscriptions_enable'])) {
			ml_set_generic_option('ml_subscriptions_enable',$_POST['ml_subscriptions_enable']);
		}
		die();
	}

	function ml_admin_subscriptions_page() {

		echo "<div id='ml_admin_subscriptions_page'>";	
		wp_register_style('mobiloud_admin_subscriptions', MOBILOUD_PLUGIN_URL . '/admin/subscriptions/subscriptions.css');
		wp_enqueue_style("mobiloud_admin_subscriptions");
		
		include(dirname( __FILE__ ).'/page.php');
		echo "</div>";
	}
?>