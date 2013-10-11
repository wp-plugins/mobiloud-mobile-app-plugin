<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
	include("../../../wp-blog-header.php");
	global $wp_version, $required_php_version;
	$info = array("version" => "1.8.16",
				 "php_version" => phpversion(),
				 "wp_version" => $wp_version,
				 "required_php_version" => $required_php_version,
				 "name" => get_bloginfo("name"),
				 "plugin_dir_url" => plugin_dir_url(__FILE__));
	$callback = $_GET['callback'];
	if($callback) {
		echo $callback."(";
	}
	echo json_encode($info);
	if($callback) {
		echo ")";
	}

?>