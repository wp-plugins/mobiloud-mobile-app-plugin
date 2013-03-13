<?php
	include("../../../wp-blog-header.php");

	$info = array("version" => "1.8.2",
				 "name" => get_bloginfo("name"),
				 "plugin_dir_url" => plugin_dir_url(__FILE__));
	echo json_encode($info);
?>