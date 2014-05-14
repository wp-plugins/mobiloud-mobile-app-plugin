<?php

include("../../../wp-blog-header.php");
include_once("categories.php");
include_once("filters.php");

include_once dirname( __FILE__ ) . '/subscriptions/functions.php';


$app_version = $_POST['app_version'];

//		$sticky_category_1 = get_option('sticky_category_1');
//		$sticky_category_2 = get_option('sticky_category_2');		
	
	
$return_config = array();
$return_config["enable_featured_images"] = get_option('ml_article_list_enable_featured_images',true);
$return_config["enable_dates"] = get_option('ml_article_list_enable_dates',true);

if(get_option("ml_home_article_list_enabled",false)==true){
	$return_config["home_page_type"] = "article_list";
} else if(get_option("ml_home_page_enabled",false)==true){
	
	$return_config["home_page_type"] = "page";
	$return_config["home_page_id"] = get_option("ml_home_page_id");
	$return_config["home_page_full"] = get_option("ml_home_page_full");
	
	$return_config["show_article_list_menu_item"] = get_option("ml_show_article_list_menu_item",true);
	$return_config["article_list_menu_item_title"] = get_option("ml_article_list_menu_item_title","Articles");
	
	
} else if(get_option("ml_home_url_enabled",false)==true){
	$return_config["home_page_type"] = "url";
	$return_config["home_page_url"] = get_option("ml_home_url");
	
	$return_config["show_article_list_menu_item"] = get_option("ml_show_article_list_menu_item",true);
	$return_config["article_list_menu_item_title"] = get_option("ml_article_list_menu_item_title","Articles");
	
}

$return_config["enable_hierarchical_pages"] = get_option('ml_hierarchical_pages_enabled',true);

$json_string = json_encode($return_config);
echo $json_string;


?>
