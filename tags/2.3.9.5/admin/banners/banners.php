<?php
	add_action('wp_ajax_ml_admin_banners_save', 'ml_admin_banners_save_callback');

	function ml_admin_banners_page() {
		echo "<div id='ml_admin_banners_page'>";	
		wp_register_style('mobiloud_admin_banners', MOBILOUD_PLUGIN_URL . '/admin/banners/banners.css');
		wp_enqueue_style("mobiloud_admin_banners");
		include(dirname( __FILE__ ).'/page.php');
		echo "</div>";
	}

	function ml_admin_banners_set_option($key,$value) {
		if($_POST[$key]) {				
			ml_set_generic_option($key,$_POST[$key]);
		}
		else {
			delete_option($key);
		}
	}
	function ml_banners_admob_enable() {
		return get_option('ml_banners_admob_enable') == "true";
	}
	function ml_banners_html_enable() {
		return get_option('ml_banners_html_enable') == "true";
	}
	function ml_banners_service() {
		if(ml_banners_admob_enable()) return 'admob';
		else if(ml_banners_html_enable()) return 'html';
		else return 'off';		
	}
	function ml_admin_banners_save_callback() {
		if($_POST['banners_service'] == 'off') {
			delete_option('ml_banners_admob_enable');
			delete_option('ml_banners_html_enable');
		}
		else if($_POST['banners_service'] == 'admob') {
			ml_set_generic_option('ml_banners_admob_enable','true');
			ml_admin_banners_set_option('ml_banners_admob_phone_id');
			ml_admin_banners_set_option('ml_banners_admob_tablet_id');
		}

		else if($_POST['banners_service'] == 'html') {
			ml_set_generic_option('ml_banners_html_enable','true');
			ml_admin_banners_set_option('ml_banners_html_phone_article_top');
			ml_admin_banners_set_option('ml_banners_html_phone_article_bottom');
			ml_admin_banners_set_option('ml_banners_html_phone_top');

			ml_admin_banners_set_option('ml_banners_html_tablet_top');
			ml_admin_banners_set_option('ml_banners_html_tablet_article_top');
			ml_admin_banners_set_option('ml_banners_html_tablet_article_bottom');
		}

		die();
	}
?>