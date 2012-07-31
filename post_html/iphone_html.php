<?php
function iphone_html($post)
{
	$prefiltered_html = str_replace("\n","<p></p>",$post->post_content);
 	$prefiltered_html = preg_replace("/\[caption.*\[\/caption\]/", '', $prefiltered_html);

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

		$e->style = "max-width:280px;";
		
		//center
		$e->outertext = "<center><div class=\"mobiloud_media\">" . $e->outertext . "</div></center><p></p>";
	}
		
	foreach($scripts as $s)
	{
		$s->outertext = ""; 
	}
	
	//JAVASCRIPT INCLUDES
	$header_js = "<script type=\"text/javascript\" src=\"/wp-content/plugins/mobiloud-mobile-app-plugin/post_html/js/jquery.min.js\"></script>";

	$header_js .= "<script type=\"text/javascript\" src=\"/wp-content/plugins/mobiloud-mobile-app-plugin/post_html/js/mobiloud.js\"></script>";
	//HEAD
	$header = "<head>".$header_js;
	
	$header .= "<meta name=\"viewport\" content=\"width=device-width; minimum-scale=1.0; maximum-scale=1.0;\" />";
	$header .= "<link rel=\"StyleSheet\" href=\"/wp-content/plugins/mobiloud-mobile-app-plugin/post_html/css/iphone.css\" type=\"text/css\"  media=\"screen\">";

	$header .= "<link rel=\"StyleSheet\" href=\"/wp-content/plugins/mobiloud-mobile-app-plugin/post_html/css/iphone_portrait.css\" type=\"text/css\"  media=\"screen\" id=\"orient_css\">";
	$header .= "</head>";

	
	$init_html = "<html>".$header;
	
	$spaces = "<p>&nbsp;</p>";
	
	$title = "<h4 align='left'>".$post->post_title."</h4>";
	$title .= "<table width='100%'><tr><td class='article_date' align=left>".mysql2date('l, F j, Y',$post->post_date)."</td><td class='author'  align=right>".get_author_name($post->post_author)."</td></tr></table><p>&nbsp;</p>";
	
	
	return $init_html."<body onorientationchange=\"mobiloud_orient();\"><div id=\"content\"><p>&nbsp;</p>".$title.$html->save().$spaces."</div></body></html>";
}
?>