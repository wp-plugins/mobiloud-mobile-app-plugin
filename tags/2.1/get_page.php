<?php
include("../../../wp-blog-header.php");


$page_ID = $_GET["page_ID"];


$page = get_page($page_ID);

if($page->post_content == NULL || strlen($page->post_content) == 0)
{
	//redirect
	$link = get_permalink($page_ID);
	header("Location: $link");
	exit;
}

$post = $page;

include("post/post.php");

?>