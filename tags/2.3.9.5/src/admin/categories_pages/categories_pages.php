<?php

	function ml_admin_categories_pages_page() {
		echo "<div id='ml_categories_pages_page'>";	
		wp_register_style('mobiloud_categories_pages', MOBILOUD_PLUGIN_URL . '/admin/categories_pages/categories_pages.css');
		wp_enqueue_style("mobiloud_categories_pages");
		include(dirname( __FILE__ ).'/page.php');
		echo "</div>";
	}

?>