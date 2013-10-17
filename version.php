<?php
	$info = array("version" => "1.8.16")
	$callback = $_GET['callback'];
	if($callback) {
		echo $callback."(";
	}
	echo json_encode($info);
	if($callback) {
		echo ")";
	}
?>