<?php
include("../../../wp-blog-header.php");
include_once("pages.php");


//ini_set('display_errors', 1);
header('Content-type: application/json');

$final_pages = array();

if(ml_is_app_menu_enable())
{
	$pages = ml_pages();

	foreach($pages as $p)
	{
		$page = array();
		if($p['post_title'])
			$page["title"] = $p['post_title'];			
		else $page['title'] = $p['ml_page_title'];

		$page["title"] = $p['post_title'];
		if($p['id'])
			$page["link"] = get_permalink($p['id']);
		else if($p['ml_page_url']) 
			$page["link"] = $p['ml_page_url'];
		$page["ml_image_url"] = $p['ml_image_url'];
		$page["ml_link"] = plugins_url("get_page.php?page_ID=".$p['id'],__FILE__);
		$page["ml_render"] = ml_page_get_render($p['id']);
		$page["id"] = $p['id'];
		array_push($final_pages,$page);

	}
}


echo json_encode(array("app_menu" => ml_is_app_menu_enable(), "pages" => $final_pages));
?>