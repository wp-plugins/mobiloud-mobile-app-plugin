<?php

	setup_postdata($post); // enable author and other data

	if(!isset($custom_css)){
		$custom_css = stripslashes(get_option('ml_post_custom_css'));
		echo $custom_css ? '<style type="text/css" media="screen">' . $custom_css . '</style>' : '';
	}
	if(!isset($custom_js)){
		$custom_js = stripslashes(get_option('ml_post_custom_js'));
		echo $custom_js ? '<script>' . $custom_js . '</script>' : '';
	}

	eval(stripslashes(get_option('ml_post_start_body')));
	echo stripslashes(get_option('ml_html_post_start_body'));

	eval(stripslashes(get_option('ml_post_before_top_banner')));

	// featured image banner
	/*if(has_post_thumbnail()){
		the_post_thumbnail(array(1024, 768), array( // device width largest width is a landscape ipad
			'class' => 'post-featured-image',
		));
	}*/

	eval(stripslashes(get_option('ml_post_after_top_banner')));

?>
<article>
<?php

	eval(stripslashes(get_option('ml_post_before_details')));
	echo stripslashes(get_option('ml_html_post_before_details'));

if(!isset($_GET["page_ID"])){
	// title, date, author, meta
	echo get_option('ml_post_date_enabled')=='true' ? '<div class="post_meta"><time title="' . $post->post_date . '">' . human_time_diff(strtotime($post->post_date), time()) . ' ago</time></div>' : '';
	
} else {
	echo get_option('ml_page_date_enabled')=='true' ? '<div class="post_meta"><time title="' . $post->post_date . '">' . human_time_diff(strtotime($post->post_date), time()) . ' ago</time></div>' : '';
}
	
	echo '<div class="post_meta right">'; eval(stripslashes(get_option('ml_post_right_of_date'))); echo '</div>';
?>
	<div class="clear"></div>
	<h1 class="gamma post_title"><?php echo $post->post_title; ?></h1>
<?php

	if( !isset($_POST['allow_lazy']) || isset($_GET['fullcontent']) || get_option('ml_eager_loading_enable')=='true' || isset($_GET["page_ID"]) ){
		
	if(!isset($_GET["page_ID"])){
	echo get_option('ml_post_author_enabled')=='true' ? '<p class="post_meta">' . get_the_author_link() . '</p><div class="clear"></div>' : ''; // clear because .post_meta is floated
	} else {
		echo get_option('ml_page_author_enabled')=='true' ? '<p class="post_meta">' . get_the_author_link() . '</p><div class="clear"></div>' : ''; // clear because .post_meta is floated
	}

	eval(stripslashes(get_option('ml_post_after_details')));
	echo stripslashes(get_option('ml_html_post_after_details'));

	eval(stripslashes(get_option('ml_post_before_content')));
	echo stripslashes(get_option('ml_html_post_before_content'));

	// content
	the_content();

	eval(stripslashes(get_option('ml_post_after_content')));
	echo stripslashes(get_option('ml_html_post_after_content'));

	eval(stripslashes(get_option('ml_post_before_footer_banner')));

	eval(stripslashes(get_option('ml_post_after_footer_banner')));

	}
?>
</article>
<?php

	eval(stripslashes(get_option('ml_post_after_body')));
	echo stripslashes(get_option('ml_html_post_after_body'));
?>