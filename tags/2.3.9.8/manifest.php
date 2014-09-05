<?php
	include("../../../wp-blog-header.php");

	header('Content-Type: text/cache-manifest');
	echo "CACHE MANIFEST\n";
	echo plugin_dir_url(__FILE__)."post/css/mobile.css\n";
	echo plugin_dir_url(__FILE__)."post/js/mobile.js\n";
	echo plugin_dir_url(__FILE__)."post/js/jquery.min.js\n";
?>