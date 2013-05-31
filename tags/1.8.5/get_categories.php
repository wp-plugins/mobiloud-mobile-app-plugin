<?php
include("../../../wp-blog-header.php");
include_once("categories.php");
include_once("pages.php");


//ini_set('display_errors', 1);

$categories = ml_categories();
$final_categories = array();

$pages = ml_pages();
$final_pages = array();

//categories
foreach($categories as $c)
{
	$cat = array();
	$cat["name"] = $c->cat_name;
	$cat["slug"] = $c->slug;
	$cat["id"] = $c->cat_ID;
	array_push($final_categories,$cat);
}

//pages
foreach($pages as $p)
{
	$page = array();
	$page["title"] = $p->post_title;
	$page["link"] = get_permalink($p->ID);
	$page["ml_link"] = plugins_url("get_page.php?page_ID=".$p->ID,__FILE__);
	$page["ml_render"] = ml_page_get_render($p->ID);
	$page["id"] = $p->ID;
	array_push($final_pages,$page);
}


echo json_encode(array("categories" => $final_categories, "pages" => $final_pages));
?>