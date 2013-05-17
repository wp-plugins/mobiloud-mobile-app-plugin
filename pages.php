<?php
	function ml_pages()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";
		$pages = $wpdb->get_results("SELECT id, page_ID FROM $table_name");
		$final_pages = array();
		foreach($pages as $page) {
			$p = get_page($page->page_ID);
			array_push($final_pages, $p);
		}		
		return $final_pages;
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

?>
