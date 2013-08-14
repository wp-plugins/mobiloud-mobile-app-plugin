<?php
	include("../../../wp-blog-header.php");
	global $wp_version, $required_php_version;
	$info = array("version" => "1.8.11",
				 "php_version" => phpversion(),
				 "wp_version" => $wp_version,
				 "required_php_version" => $required_php_version,
				 "name" => get_bloginfo("name"),
				 "plugin_dir_url" => plugin_dir_url(__FILE__));
	echo json_encode($info);
?>