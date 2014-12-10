<?php
include("../../../wp-load.php");
	$info = array("version" => "3.0.8");
	$callback = sanitize_text_field($_GET['callback']);
	if($callback) {
		echo $callback."(";
	}
	echo json_encode($info);
	if($callback) {
		echo ")";
	}
?>