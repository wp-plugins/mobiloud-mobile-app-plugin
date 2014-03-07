<?php
	add_action('wp_ajax_ml_admin_post_save_code', 'ml_admin_post_save_code_callback');
	add_action('wp_ajax_ml_admin_post_save_options', 'ml_admin_post_save_options_callback');

	function ml_admin_post_save_code_callback() {
		//save api key
		if(isset($_POST['customization_name']) && isset($_POST['code']) && $_POST['customization_name'] != "ml_null")
		{
			ml_set_generic_option($_POST['customization_name'],$_POST['code']);
		}

		die();
	}

	function ml_admin_post_save_options_callback()
	{
		if(isset($_POST['ml_post_author_enabled'])) {
			ml_set_generic_option('ml_post_author_enabled',$_POST['ml_post_author_enabled']);
		}
		if(isset($_POST['ml_post_date_enabled'])) {
			ml_set_generic_option('ml_post_date_enabled',$_POST['ml_post_date_enabled']);
		}
		if(isset($_POST['ml_page_author_enabled'])) {
			ml_set_generic_option('ml_page_author_enabled',$_POST['ml_page_author_enabled']);
		}
		if(isset($_POST['ml_page_date_enabled'])) {
			ml_set_generic_option('ml_page_date_enabled',$_POST['ml_page_date_enabled']);
		}
		if(isset($_POST['ml_eager_loading_enable'])) {
			ml_set_generic_option('ml_eager_loading_enable',$_POST['ml_eager_loading_enable']);
		}

		die();
	}


	function ml_admin_post_page() {
		echo "<div id='ml_admin_post_page'>";	
		wp_register_style('mobiloud_admin_post', MOBILOUD_PLUGIN_URL . '/admin/post/post.css');
		wp_enqueue_style("mobiloud_admin_post");
		include(dirname( __FILE__ ).'/page.php');
		echo "</div>";

		//page preview
		echo "<div id='ml_admin_post_page_preview'>";	
		include(dirname( __FILE__ ).'/preview.php');
		echo "</div>";
	}

?>