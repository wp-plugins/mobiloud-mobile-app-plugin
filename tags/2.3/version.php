<?php
	$info = array("version" => "2.3");
	$callback = $_GET['callback'];
	if($callback) {
		echo $callback."(";
	}
	echo json_encode($info);
	if($callback) {
		echo ")";
	}
?>