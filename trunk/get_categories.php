<?php
include("../../../wp-blog-header.php");
include_once("categories.php");
include_once("pages.php");

function build_page_object($dic){
	
	$childobject = array();
	$childobject["title"] = $dic->post_title;
	$childobject["link"] = get_permalink($dic->ID);
	$childobject["ml_link"] = plugins_url("get_page.php?page_ID=".$dic->ID,__FILE__);
	$childobject["ml_render"] = ml_page_get_render($dic->ID);
	$childobject["id"] = "$dic->ID";
	
	//$my_wp_query = new WP_Query();
	//$all_wp_pages = $my_wp_query->query(array('post_type' => 'page'));

	//$children = get_page_children($dic->ID,$all_wp_pages);
	$children = get_pages(array('parent' => $dic->ID));
	
	$childarray = array();
			
	foreach($children as $child){
						
		if($child->post_title!=NULL&&$child->ID!=NULL&&$child->post_parent==$dic->ID){
							
			array_push($childarray,build_page_object($child));
							
		}
						
	}
	
	//wp_reset_postdata();
	
	$childobject["children"] = $childarray;
						
	return $childobject;
	
}

//ini_set('display_errors', 1);

$categories = ml_categories();
$final_categories = array();

$pages = ml_pages();
$final_pages = array();

$final_urls = array();

$final_options = array();

//categories
foreach($categories as $c)
{
	$cat = array();
	if($c->cat_name != NULL && $c->slug != NULL && $c->cat_ID != NULL) {
		$cat["name"] = $c->cat_name;
		$cat["slug"] = $c->slug;
		$cat["id"] = "$c->cat_ID";
		array_push($final_categories,$cat);
	}
}

//$my_wp_query = new WP_Query();
//$all_wp_pages = $my_wp_query->query(array('post_type' => 'page'));
//		wp_reset_postdata();
		
		
//pages
foreach($pages as $p)
{
	$page = array();
	if($p->post_title != NULL && $p->ID != NULL) {
		$page["title"] = $p->post_title;
		$page["link"] = get_permalink($p->ID);
		$page["ml_link"] = plugins_url("get_page.php?page_ID=".$p->ID,__FILE__);
		$page["ml_render"] = ml_page_get_render($p->ID);
		$page["id"] = "$p->ID";
		
		if(get_option("ml_hierarchical_pages_enabled",true)==true){
				
				
				
				//$children = get_page_children($p->ID,$all_wp_pages);
				
				//
				
				//$children = get_posts(array('posts_per_page'   => -1));
			
			//echo json_encode($children);
				$children = get_pages(array('parent' => $p->ID));
				$childarray = array();
			
				foreach($children as $child){
						
						if($child->post_title!=NULL&&$child->ID!=NULL&&$child->post_parent==$p->ID){
							
							array_push($childarray,build_page_object($child));
							
						}
						
				}
				
				$page["children"] = $childarray;
				
				//wp_reset_postdata();
		}
		
		array_push($final_pages,$page);
	}
}

$urls = get_option("ml_menu_urls",array());
foreach($urls as $url){
	$urlObject = array();
	$urlObject["url"] = $url["url"];
	$urlObject["title"] = $url["urlTitle"];	
	array_push($final_urls,$urlObject);
}

$final_options = array();
$final_options["showFavorites"] = get_option('ml_menu_show_favorites',true);

echo json_encode(array("categories" => $final_categories, "pages" => $final_pages, "urls" => $final_urls, "options" => $final_options));


?>