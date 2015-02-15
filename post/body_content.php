<?php

    if(!function_exists('setup_postdata_custom')) {
        /**
        * Set up global post data.
        *
        * @since 1.5.0
        *
        * @param object $post Post data.
        * @uses do_action_ref_array() Calls 'the_post'
        * @return bool True when finished.
        */
       function setup_postdata_custom( $post ) {
           global $id, $authordata, $currentday, $currentmonth, $page, $pages, $multipage, $more, $numpages;

           $id = (int) $post->ID;

           $authordata = get_userdata($post->post_author);

           $currentday = mysql2date('d.m.y', $post->post_date, false);
           $currentmonth = mysql2date('m', $post->post_date, false);
           $numpages = 1;
           $multipage = 0;
           $page = get_query_var('page');
           if ( ! $page )
               $page = 1;
           if ( is_single() || is_page() || is_feed() )
               $more = 1;
           $content = $post->post_content;
           $pages = array( $post->post_content );


           /**
            * Fires once the post data has been setup.
            *
            * @since 2.8.0
            *
            * @param WP_Post &$post The Post object (passed by reference).
            */
           do_action_ref_array( 'the_post', array( &$post ) );

           return true;
       }
    }
    $current_permalink = get_permalink($post->ID);
    if(!function_exists('ml_convert_relative_links')) {
        function ml_convert_relative_links($content) {
            global $current_permalink;
            $content = preg_replace("#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http|/)([^\"'>]+)([\"'>]+)#", '$1'.$current_permalink.'$2$3', $content);
            return $content;
        }
        add_filter( 'the_content', 'ml_convert_relative_links', 20 );
    }

	setup_postdata_custom($post); // enable author and other data

	if(!isset($custom_css)){
		$custom_css = stripslashes(get_option('ml_post_custom_css'));
		echo $custom_css ? '<style type="text/css" media="screen">' . $custom_css . '</style>' : '';
	}
	if(!isset($custom_js)){
		$custom_js = stripslashes(get_option('ml_post_custom_js'));
		echo $custom_js ? '<script>' . $custom_js . '</script>' : '';
	}

    echo stripslashes(get_option('ml_banner_above_content', ''));
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
<article class="mb_article">
<?php

	eval(stripslashes(get_option('ml_post_before_details')));
	echo stripslashes(get_option('ml_html_post_before_details'));

	if(!isset($_GET["page_ID"])){
		// title, date, author, meta
		echo get_option('ml_post_date_enabled') ? '<p class="mb_post_meta"><time title="' . $post->post_date . '">' . date_i18n(get_option('date_format'), strtotime($post->post_date)) . '</time></p>' : '';

	} else {
		echo get_option('ml_page_date_enabled') ? '<p class="mb_post_meta"><time title="' . $post->post_date . '">' . date_i18n(get_option('date_format'), strtotime($post->post_date)) . '</time></p>' : '';
	}
	
	echo '<div class="mb_post_meta right">'; eval(stripslashes(get_option('ml_post_right_of_date'))); echo '</div>';
?>
	<div class="mb_clear"></div>
    <?php echo stripslashes(get_option('ml_banner_above_title', '')); ?>
    <?php if(!isset($_GET["page_ID"])): ?>
        <?php if(get_option('ml_post_title_enabled')): ?>
            <h1 class="gamma mb_post_title"><?php echo $post->post_title; ?></h1>
        <?php endif; ?>
    <?php else: ?>
        <?php if(get_option('ml_page_title_enabled')): ?>
            <h1 class="gamma mb_post_title"><?php echo $post->post_title; ?></h1>
        <?php endif; ?>
    <?php endif; ?>
<?php
    $show_full_body = false;
    if(get_option('ml_eager_loading_enable') || isset($_GET['fullcontent']) || isset($_GET["page_ID"])) {
        //Preload content option is enabled so always load the full body 
        //OR fullcontent $_GET is set
        $show_full_body = true;
    } elseif(isset($_POST['allow_lazy']) && !get_option('ml_eager_loading_enable')) {
        $show_full_body = true;
    }
    
    if($show_full_body) {
	//if( !isset($_POST['allow_lazy']) || isset($_GET['fullcontent']) || get_option('ml_eager_loading_enable')=='true' || isset($_GET["page_ID"]) || isset($_POST['post_id'])){

		if(!isset($_GET["page_ID"])){
			echo get_option('ml_post_author_enabled') ? '<p class="mb_post_meta">' . get_the_author() . '</p><div class="mb_clear"></div>' : ''; // clear because .post_meta is floated
		} else {
			echo get_option('ml_page_author_enabled') ? '<p class="mb_post_meta">' . get_the_author() . '</p><div class="mb_clear"></div>' : ''; // clear because .post_meta is floated
		}

	eval(stripslashes(get_option('ml_post_after_details')));
	echo stripslashes(get_option('ml_html_post_after_details'));

	eval(stripslashes(get_option('ml_post_before_content')));
	echo stripslashes(get_option('ml_html_post_before_content'));

	// content
	global $more;
    $more = 1;
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
    echo stripslashes(get_option('ml_banner_below_content', ''));
?>
<script>
    var iframes = document.getElementsByTagName('iframe')
        , frameRatios = []
        , container = document.getElementsByTagName('article')[0]
        , imgs = document.getElementsByTagName('img');
    for(var i = 0; i < iframes.length; i++){
        var frame = iframes[i];
        frameRatios[i] = frame.offsetHeight / frame.offsetWidth;
        frame.removeAttribute('width');
        frame.removeAttribute('height');
        frame.style.width = '100%';
    }
    for(var i = 0; i < imgs.length; i++){
        var img = imgs[i];
        img.removeAttribute('width');
        img.removeAttribute('height');
        while(img = img.parentNode){
            if(/^attachment_[0-9]+$/.test(img.id)){
                img.removeAttribute('style');
            }
        }
    }
    window.onresize = function(){
        var containerWidth = container.offsetWidth;
        for(var i = 0; i < iframes.length; i++){
            var frame = iframes[i];
            frame.style.height = (containerWidth * frameRatios[i]) + 'px';
        }
    };
    window.onresize();
</script>