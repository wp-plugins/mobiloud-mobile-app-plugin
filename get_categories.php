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
	if($c->cat_name != NULL && $c->slug != NULL && $c->cat_ID != NULL) {
		$cat["name"] = $c->cat_name;
		$cat["slug"] = $c->slug;
		$cat["id"] = "$c->cat_ID";
		array_push($final_categories,$cat);
	}
}

$my_wp_query = new WP_Query();
$all_wp_pages = $my_wp_query->query(array('post_type' => 'page'));
				
				
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
				
				
				
				$children = get_page_children($p->ID,$all_wp_pages);
			
				$childarray = array();
			
				foreach($children as $child){
						
						if($child->post_title!=NULL&&$child->ID!=NULL){
							$childobject = array();
							$childobject["title"] = $child->post_title;
							$childobject["link"] = get_permalink($child->ID);
							$childobject["ml_link"] = plugins_url("get_page.php?page_ID=".$child->ID,__FILE__);
							$childobject["ml_render"] = ml_page_get_render($child->ID);
							$childobject["id"] = "$child->ID";
						
							array_push($childarray,$childobject);
						}
						
				}
				
				$page["children"] = $childarray;
				
		}
		
		array_push($final_pages,$page);
	}
}


echo json_encode(array("categories" => $final_categories, "pages" => $final_pages));
?>