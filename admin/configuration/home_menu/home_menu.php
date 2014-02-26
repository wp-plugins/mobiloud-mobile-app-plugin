<?php

	add_action('wp_ajax_ml_menu_add_item', 'ml_menu_add_item_callback');

	function ml_home_menu_page(){

		wp_enqueue_script('mobiloud-home-menu',MOBILOUD_HOME_MENU_URL."/home_menu.js",array('jquery-ui-draggable','jquery-ui-droppable','jquery-ui-sortable'),MOBILOUD_PLUGIN_VERSION);
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_style('mobiloud-home-menu',MOBILOUD_HOME_MENU_URL."/home_menu.css");
		wp_enqueue_style('mobiloud-home-menu-tabs',MOBILOUD_HOME_MENU_URL."/tabs.css");
		wp_enqueue_style('mobiloud-home-menu-bgs',MOBILOUD_HOME_MENU_URL."/backgrounds.css");

		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');

		wp_enqueue_script('jquery-ui-core'); 
		wp_enqueue_script('jquery-ui-tabs');


		?>

		<h1>Home Menu</h1>
		<div id='menu-tabs'>
			<ul>
				<li><a href='#menu-tabs-items'>Menu Items</a></li>
				<li><a href='#menu-tabs-backgrounds'>Backgrounds</a></li>		
			</ul>	

			<div class='tabs-content'>
				<div id="menu-tabs-items">
					<?php include 'items.php'; ?>
				</div>

				<div id="menu-tabs-backgrounds">
					<?php include 'backgrounds.php'; ?>
				</div>
			</div>

		</div>


		<?php
	}


	function ml_menu_add_item_callback() {
		$title = $_POST['title'];
		$menu_type = $_POST['menu_type'];
		$cat_ID = $_POST['cat_ID'];
		$page_ID = $_POST['page_ID'];
		$url = $_POST['url'];

		if($cat_ID == NULL || trim($cat_ID) == '0') $cat_ID = NULL;
		if($page_ID == NULL || trim($page_ID) == '0') $page_ID = NULL;

		$item_ID = ml_home_menu_create_item($title,$menu_type,$page_ID,$cat_ID,$url);
		$item = ml_home_menu_get_item($item_ID);
		ml_home_menu_print_item($item);
		die();
	}

	function ml_home_menu_print_item($item)
	{
		echo "<li class='menu-item' id='menu-item-$item->id'>$item->title</li>";
	}

	//check if there is the column 'ml_render'
	/*
	$results = $wpdb->get_results( "SHOW FULL COLUMNS FROM `" . $table_name."` LIKE 'ml_render'", ARRAY_A );
	if($results == NULL || count($results) == 0) {
		//update the table
		$sql = "ALTER TABLE $table_name ADD ml_render TINYINT(1) NOT NULL DEFAULT 1;"; 
		$wpdb->query($sql);
	}
  */
?>