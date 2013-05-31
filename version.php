<?php
	include("../../../wp-blog-header.php");

	$info = array("version" => "1.8.5",
				 "name" => get_bloginfo("name"),
				 "plugin_dir_url" => plugin_dir_url(__FILE__));

	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_pages";
	
	print_r($wpdb->get_col( "DESC " . $table_name, 0 ));
?>