<?php
	function ml_pages()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";
		$pages = $wpdb->get_results("SELECT id, page_ID, ml_render FROM $table_name");
		$final_pages = array();
		foreach($pages as $page) {
			$p = get_page($page->page_ID);
			array_push($final_pages, $p);
		}		
		return $final_pages;
	}

	function ml_page_change_render($page_ID,$on) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";
		$wpdb->update($table_name,array('ml_render' => $on),array('%d'));
	}

	function ml_page_render_off($page_ID) {
		ml_page_change_render($page_ID,0);
	}

	function ml_page_render_on($page_ID) {
		ml_page_change_render($page_ID,1);
	}

	function ml_page_get_render($page_ID) {
		$page = ml_get_page($page_ID);
		return ($page->ml_render == 1 || $page->ml_render == "1");
	}

	function ml_get_page($page_ID) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";
		return $wpdb->get_row("SELECT * FROM $table_name WHERE page_ID = $page_ID");
	}

	function ml_add_page($page_ID) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";
		$wpdb->insert($table_name, array("page_ID" => $page_ID),array("%d"));
	}

	function ml_remove_page($page_ID) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";

		$wpdb->query( 
			$wpdb->prepare("DELETE FROM $table_name WHERE page_ID = %d",$page_ID)
		);
	}

    function ml_remove_all_pages() {
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";

		$wpdb->query( "DELETE FROM $table_name");
	}
?>
