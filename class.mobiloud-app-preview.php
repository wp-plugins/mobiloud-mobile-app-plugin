<?php
class Mobiloud_App_Preview {
    private static $initiated = false;
    
    public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}
    
    /**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;
        
        add_action('wp_ajax_ml_preview_app_display', array('Mobiloud_App_Preview', 'render_preview'));
    }
    
    public static function render_preview() {
        self::process_data();
        $iconShade = 'ml-icon-dark';
        if(self::get_color_brightness(get_option('ml_preview_theme_color')) < 190) {
            $iconShade = 'ml-icon-white';
        }
        Mobiloud_Admin::render_part_view('app_preview', compact('iconShade'));
        exit;
    }
    
    public static function process_data() {
        if(isset($_POST['ml_preview_upload_image'])) {
            if($_POST['ml_preview_upload_image'] != get_option('ml_preview_upload_image')) {
                update_option("ml_preview_upload_image_time", time());
            }
            update_option("ml_preview_upload_image", $_POST['ml_preview_upload_image']);        
        }   
        if(isset($_POST['ml_preview_theme_color'])) {        
            update_option("ml_preview_theme_color", $_POST['ml_preview_theme_color']);        
        }
        if(isset($_POST['ml_preview_os'])) {
            update_option("ml_preview_os", $_POST['ml_preview_os']);
        }
        if(isset($_POST['ml_article_list_view_type'])) {
            Mobiloud::set_option('ml_article_list_view_type', sanitize_text_field($_POST['ml_article_list_view_type'])); 
        }
    }    
    
    public static function get_preview_posts() {
        $args = array(
            'posts_per_page'   => 5,
            'offset'           => 0,
            'category'         => '',
            'orderby'          => 'post_date',
            'order'            => 'DESC',
            'include'          => '',
            'exclude'          => '',
            'meta_key'         => '',
            'meta_value'       => '',
            'post_type'        => 'post',
            'post_mime_type'   => '',
            'post_parent'      => '',
            'post_status'      => 'publish',
            'suppress_filters' => true 
        );

        return get_posts($args);
    }
    
    public static function get_color_brightness($hex) {
        // returns brightness value from 0 to 255

        // strip off any leading #
        $hex = str_replace('#', '', $hex);

        $c_r = hexdec(substr($hex, 0, 2));
        $c_g = hexdec(substr($hex, 2, 2));
        $c_b = hexdec(substr($hex, 4, 2));

        return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
    }
    
    public static function how_long_ago($timestamp){
        $difference = time() - $timestamp;

        if($difference >= 60*60*24*365){        // if more than a year ago
            $int = intval($difference / (60*60*24*365));
            $s = ($int > 1) ? 's' : '';
            $r = $int . ' year' . $s . ' ago';
        } elseif($difference >= 60*60*24*7*5){  // if more than five weeks ago
            $int = intval($difference / (60*60*24*30));
            $s = ($int > 1) ? 's' : '';
            $r = $int . ' month' . $s . ' ago';
        } elseif($difference >= 60*60*24*7){        // if more than a week ago
            $int = intval($difference / (60*60*24*7));
            $s = ($int > 1) ? 's' : '';
            $r = $int . ' week' . $s . ' ago';
        } elseif($difference >= 60*60*24){      // if more than a day ago
            $int = intval($difference / (60*60*24));
            $s = ($int > 1) ? 's' : '';
            $r = $int . ' day' . $s . ' ago';
        } elseif($difference >= 60*60){         // if more than an hour ago
            $int = intval($difference / (60*60));
            $s = ($int > 1) ? 's' : '';
            $r = $int . ' hour' . $s . ' ago';
        } elseif($difference >= 60){            // if more than a minute ago
            $int = intval($difference / (60));
            $s = ($int > 1) ? 's' : '';
            $r = $int . ' minute' . $s . ' ago';
        } else {                                // if less than a minute ago
            $r = 'moments ago';
        }

        return $r;
    }
    
    public static function trim_post_title($title, $length=null) {
        if($length === null || strlen($title) <= $length) {
            return $title;
        }
        return substr($title, 0, $length) . '...';
    }
}