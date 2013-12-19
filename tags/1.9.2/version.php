<?php
	$info = array("version" => "1.9.2");
	$callback = $_GET['callback'];
	if($callback) {
		echo $callback."(";
	}
	echo json_encode($info);
	if($callback) {
		echo ")";
	}
?>