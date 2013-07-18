<?php
	include("../../../wp-blog-header.php");

	$info = array("version" => "1.8.7",
				 "name" => get_bloginfo("name"),
				 "plugin_dir_url" => plugin_dir_url(__FILE__));
	print_r($info);
?>