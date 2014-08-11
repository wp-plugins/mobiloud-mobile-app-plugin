<?php
include("../../../wp-blog-header.php");
	$info = array("version" => "2.3.9");
	$callback = sanitize_text_field($_GET['callback']);
	if($callback) {
		echo $callback."(";
	}
	echo json_encode($info);
	if($callback) {
		echo ")";
	}
?>