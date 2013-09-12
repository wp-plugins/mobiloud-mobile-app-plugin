<?php
	function ml_pages()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";
		$pages = $wpdb->get_results("SELECT id, page_ID, ml_render,title,url,position FROM $table_name ORDER BY position");
		$final_pages = array();
		foreach($pages as $page) {
			$p = get_page($page->page_ID);
			$dict = array();
			
			if($p != NULL) {
				$dict['id'] = $p->ID;
				$dict['post_title'] = $p->post_title;
			}
			$dict['row_id'] = $page->id;
			$dict['ml_page_title'] = $page->title; 
			$dict['ml_page_url'] = $page->url;

			if($dict['id'] && has_post_thumbnail( $dict['id'] )) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $dict['id'] ), 'single-post-thumbnail' );
				if($image && count($image) > 0) 
					$dict['ml_image_url'] = $image[0];
			}

			array_push($final_pages, $dict);
		}		
		return $final_pages;
	}

	function ml_page_change_render($id,$on) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";
		$wpdb->update($table_name,array('ml_render' => $on),array('id' => $id),array('%d'),array('%d'));
	}

	function ml_page_render_off($id) {
		ml_page_change_render($id,0);
	}

	function ml_page_render_on($id) {
		ml_page_change_render($id,1);
	}

	function ml_page_get_render($id) {
		$page = ml_get_page($id);
		return ($page->ml_render == 1 || $page->ml_render == "1");
	}

	function ml_page_set_title($page_ID,$title) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";
		$wpdb->update($table_name,array("page_ID" => $page_ID), 
					  array( 'title' => $title ), 
					  array('%d'), array( '%s' ) );
	}

	function ml_get_page($id) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";
		return $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");
	}

	function ml_add_page($page_ID,$title,$url) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";
		
		if($url == '') $url = NULL;
		if($title == '') $title = NULL;

		$wpdb->insert($table_name, array("page_ID" => $page_ID,"title" => $title, "url" => $url),array("%d","%s","%s"));
		$last_id = $wpdb->insert_id;
		ml_update_page_position($last_id,$last_id);
	}

	function ml_remove_page($id) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";

		$wpdb->query( 
			$wpdb->prepare("DELETE FROM $table_name WHERE id = %d",$id)
		);
	}

	function ml_update_page_ID($id, $page_ID)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";

		$wpdb->update($table_name,array("page_ID" => $page_ID), 
					  array( 'ID' => $id ), 
					array('%d'), array( '%d' ) );

	}

	function ml_update_page_position($id, $position)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";

		$wpdb->update(
			$table_name,
			array('position' => $position ),
			array("id" => $id), 
			array('%d'), array( '%d' ) 
		);
	}
	
	//a.cat_ID <-> b.page_ID
	function ml_switch_pages($a_id,$b_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "mobiloud_pages";

		//getting rows id
		ml_update_page_position($a_id,$b_id);
		ml_update_page_position($b_id,$a_id);
	}


?>
