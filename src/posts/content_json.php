<?php
	//ini_set('display_errors', 1);
	include_once("../../../../wp-blog-header.php");

	if(array_key_exists('callback', $_GET)) {
		$callback = $_GET['callback'];		
	}

	if(isset($callback)) {
		echo $callback."(";
	}

	ob_start();
	include("body_content.php");
	$body_content = ob_get_clean();

	$data = array('body_content' => $body_content);
	echo json_encode($data);
	if(isset($callback)) {
		echo ")";
	}

?>