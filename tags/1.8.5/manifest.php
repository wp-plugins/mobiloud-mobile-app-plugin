<?php
	include("../../../wp-blog-header.php");

	header('Content-Type: text/cache-manifest');
	echo "CACHE MANIFEST\n";
	echo plugin_dir_url(__FILE__)."post_html/css/iphone_portrait.css\n";
	echo plugin_dir_url(__FILE__)."post_html/css/iphone.css\n";

	echo plugin_dir_url(__FILE__)."post_html/css/ipad.css\n";
	echo plugin_dir_url(__FILE__)."post_html/css/ipad_portrait.css\n";

	echo plugin_dir_url(__FILE__)."post_html/js/mobiloud.js\n";
	echo plugin_dir_url(__FILE__)."post_html/js/jquery.min.js\n";
?>