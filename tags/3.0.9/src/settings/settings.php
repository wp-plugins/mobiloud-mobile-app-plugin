<?php
include("../../../wp-blog-header.php");
ini_set('display_errors', 1);

$true_value = 'true';
$settings = array();

//BANNERS
if(get_option('ml_banners_enabled') == $true_value) {
	$banners = array();

	if(get_option('ml_banners_admob_enabled') == $true_value) {
		$banners['admob'] = array();

		$banners['admob']['phone_top'] = get_option('ml_banners_admob_phone',NULL);
		$banners['admob']['pad_top'] = get_option('ml_banners_admob_pad',NULL);
	} 
	
	if(get_option('ml_banners_html_enabled') == $true_value) {
		$banners['html'] = array();
		//phone
		$banners['html']['phone_top']  = get_option('ml_banners_html_phone_top',NULL);
		$banners['html']['phone_article_bottom']  = get_option('ml_banners_html_phone_article_bottom',NULL);	

		//pad
		$banners['html']['pad_top']  = get_option('ml_banners_html_pad_top',NULL);
		$banners['html']['pad_article_bottom']  = get_option('ml_banners_html_pad_article_bottom',NULL);	
	}

	$settings['banners'] = $banners;
}

//ANALYTICS
$settings['google_analytics'] =  get_option('ml_google_analytics_id',NULL);

//CATEGORY LIST

//PAGE LIST

//UI
$UI = array();
$UI['toolbar_color'] = get_option('ml_ui_toolbar_color',NULL);

$settings['UI'] = $UI;

$json = json_encode($settings);
echo $json;
?>