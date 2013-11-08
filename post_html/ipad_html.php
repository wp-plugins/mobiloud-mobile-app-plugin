<?php
function ipad_html($post)
{
	global $ml_html_banners_enable;
	$ml_html_banners_enable = get_option("ml_html_banners_enable");

	$prefiltered_html = ml_filters_get_filtered($post->post_content);

	$prefiltered_html = str_replace("\n","<p></p>",$prefiltered_html);
 	
	$html = str_get_html($prefiltered_html);	
	
	$img_tags = $html->find('img');
	$iframe_tags = $html->find('iframe');
	$object_tags = $html->find('object');
	$embed_tags = $html->find('embed');
	
	$tags = array_merge($img_tags,$iframe_tags,$object_tags,$embed_tags);
	$scripts = $html->find('script');
	//on center, with specific width and no height
	foreach($tags as $e)
	{
		//no width or height
		if(isset($e->width)) $e->width = null;
		if(isset($e->height)) $e->height = null;

		$e->style = "max-width:520px;margin-top:20px;margin-bottom:20px;";
		if($e->tag == "iframe" || $e->tag == "object" || $e->tag == "embed")
		{
			//should be a video
				$e->width = 500;
				$e->height = 300;
		}
		//center
		$e->outertext = "<center><div class=\"mobiloud_media\">" . $e->outertext . "</div></center><p></p>";
	}
		
	foreach($scripts as $s)
	{
		$s->outertext = ""; 
	}
	
	//JAVASCRIPT INCLUDES
	$header_js = "<script type=\"text/javascript\" src=\"".plugin_dir_url(__FILE__)."js/jquery.min.js\"></script>";

	$header_js .= "<script type=\"text/javascript\" src=\"".plugin_dir_url(__FILE__)."js/mobiloud.js\"></script>";
	//HEAD
	$header = "<head>".$header_js;
	
	$header .= "<meta name=\"viewport\" content=\"width=device-width; minimum-scale=1.0; maximum-scale=1.0;\" />";
	$header .= "<link rel=\"StyleSheet\" href=\"".plugin_dir_url(__FILE__)."css/ipad.css\" type=\"text/css\"  media=\"screen\">";

	$header .= "<link rel=\"StyleSheet\" href=\"".plugin_dir_url(__FILE__)."css/ipad_portrait.css\" type=\"text/css\"  media=\"screen\" id=\"orient_css\">";

	$header .= ml_filters_header($post->postID);

	$header .= "</head>";

	
	$init_html = "<html manifest=\"".plugin_dir_url(__FILE__+"../")."manifest.php\">".$header;
	
	$title = "<h1 class='title' align='left'>".$post->post_title."</h1>";
	
	$author = get_author_name($post->post_author);
	
	$text_author = "";
	if(strcmp($author, "admin") != 0){
		if(strcmp($author, "") != 0){
			$text_author = " &bull; ".get_author_name($post->post_author);
		}
	}
	
	if ( get_post_type($post->ID) != "page") {
		$title .= "<p class='details'>".mysql2date('F j Y',$post->post_date)."".$text_author."</p><p>&nbsp;</p>";
	}
		
	$final_html = $init_html;
	$final_html .= "<body><div id=\"content\">";
	$final_html .= $spaces;
	$final_html .= $title.$html->save().$spaces."<br/><br/><br/><br/><br/><br/><br/><br/></div></body></html>";

	return $final_html;
}
?>