<?php
include("../../../wp-blog-header.php");
include_once("libs/img_resize.php");
include_once("libs/ml_content_redirect.php");

include_once("filters.php");
include("post_html.php");

$platform = $_GET["platform"];
$page_ID = $_GET["page_ID"];

if(!isset($platform) || $platform == NULL)
	$platform = "iphone";

$page = get_page($page_ID);

echo "<p>&nbsp;</p>";


if($platform == "ipad")
	echo ipad_html($page);
else
	echo iphone_html($page);

echo "<p>&nbsp;</p>";
echo "<p>&nbsp;</p>";
echo "<p>&nbsp;</p>";
echo "<p>&nbsp;</p>";

?>