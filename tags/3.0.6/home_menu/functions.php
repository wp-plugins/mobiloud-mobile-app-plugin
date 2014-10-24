<?php
function ml_hone_menu_install()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_menu";
	

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		//install della tabella
		$sql = "CREATE TABLE " . $table_name . " (
			  id bigint(11) NOT NULL AUTO_INCREMENT,
			  time bigint(11) DEFAULT '0' NOT NULL,
			  
			  page_ID bigint(11),
			  cat_ID bigint(11),
			  url VARCHAR(255),

			  title VARCHAR(255),
			  menu_type VARCHAR(255) NOT NULL,
				position BIGINT(20) unsigned,

			  UNIQUE KEY id (id)
			);";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

function ml_home_menu_create_item($title,$menu_type,$page_ID,$cat_ID,$url) {
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_menu";
	$data = array();
	$value_types = array();
	
	if($title != NULL && strlen(trim($title)) > 0) {
		$data['title'] = $title;
		array_push($value_types,'%s');
	}

	if($menu_type != NULL && $menu_type != '0') {
		$data['menu_type'] = $menu_type;
		array_push($value_types,'%s');
	}

	$wpdb->insert($table_name, $data,$value_types);

	$last_id = $wpdb->insert_id;

	if($last_id == 0) return 0;


	//position
	$wpdb->update($table_name,array("position" => $last_id), 
				  array( 'id' => $last_id ), 
				  array('%d'), array( '%d' ));
	return $last_id;
}

function ml_home_menu_items() 
{
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_menu";
	$items = $wpdb->get_results("SELECT * FROM $table_name ORDER BY position");
	return $items;
}

function ml_home_menu_get_item($item_ID) {
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_menu";
	return $wpdb->get_row("SELECT * FROM $table_name WHERE id = $item_ID");
}
?>
