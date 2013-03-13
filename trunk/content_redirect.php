<?php
	ini_set('display_errors', 1);
	include("../../../wp-blog-header.php");
	include_once("libs/ml_content_redirect.php");

	$redirect = new MLContentRedirect();
	if($redirect->verify_secret_key($_GET["secret_key"]))
	{
			//enable disable it
			if(isset($_GET["active"]))
		 		$redirect->set_option("ml_content_redirect_enable",$_GET["active"] == "t");
			if(isset($_GET["url"]))
		 		$redirect->set_option("ml_content_redirect_url",$_GET["url"]);
			if(isset($_GET["slug"]))
		 		$redirect->set_option("ml_content_redirect_slug",$_GET["slug"]);
			if(isset($_GET["version"]))
		 		$redirect->set_option("ml_content_redirect_app_version",$_GET["version"]);


	 		echo json_encode(
	 					 ["active" => $redirect->ml_content_redirect_enable, 
	 						"url" => $redirect->ml_content_redirect_url,
	 						"slug" => $redirect->ml_content_redirect_slug,
	 						"version" => $redirect->ml_content_redirect_app_version]
	 		);
	}
	
	else echo json_encode(["error" => "Permission denied"]);
?>
