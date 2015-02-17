<?php

class Mobiloud {
    
    private static $option_key = 'ml_options';
    
    private static $initiated = false;
    
    public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
            self::set_default_options();
		}
	}
    
    /**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;
                
        add_filter('get_avatar', array('Mobiloud', 'get_avatar'),10,2);
        
        if(get_option('ml_push_notification_enabled'))
        {
            add_action('transition_post_status', 'ml_pb_post_published_notification', 10, 3);
            //add_action('transition_post_status','ml_pb_post_published_notification');    
            //add_action('publish_future_post','ml_pb_post_published_notification_future');   
        }
    }
 
    public static function mobiloud_activate() {
        add_option('mobiloud_do_activation_redirect', true);
        if(!self::get_option('ml_activation_tracked', false)) {
            self::set_option('ml_activation_tracked', true);
            ml_track('plugin activated', array('mixpanel'));
        }
        if(!self::get_option('ml_activation_tracked_pa', false)) {
            self::set_option('ml_activation_tracked_pa', 'activated');
        }
        self::run_db_install();
    }
    
    public static function set_default_options() {        
        if(Mobiloud::get_option('ml_article_list_include_post_types', 'none') == 'none') {
            Mobiloud::set_option('ml_article_list_include_post_types', 'post');
        }
        if(Mobiloud::get_option('ml_custom_featured_image', 'none') == 'none') {
            Mobiloud::set_option('ml_custom_featured_image', '');
        }
        if(Mobiloud::get_option('ml_menu_show_favorites', 'none') == 'none') {
            Mobiloud::set_option('ml_menu_show_favorites', true);
        }
        if(Mobiloud::get_option('ml_show_android_cat_tabs', 'none') == 'none') {
            Mobiloud::set_option('ml_show_android_cat_tabs', true);
        }
        if(Mobiloud::get_option('ml_article_list_enable_dates', 'none') == 'none') {
            Mobiloud::set_option('ml_article_list_enable_dates', true);
        }
        if(Mobiloud::get_option('ml_show_article_featuredimage', 'none') == 'none') {
            Mobiloud::set_option('ml_show_article_featuredimage', true);
        }
        if(Mobiloud::get_option('ml_eager_loading_enable', 'none') == 'none') {
            Mobiloud::set_option('ml_eager_loading_enable', true);
        }
        if(Mobiloud::get_option('ml_post_author_enabled', 'none') == 'none') {
            Mobiloud::set_option('ml_post_author_enabled', true);
        }
        if(Mobiloud::get_option('ml_page_author_enabled', 'none') == 'none') {
            Mobiloud::set_option('ml_page_author_enabled', true);
        }
        if(Mobiloud::get_option('ml_post_date_enabled', 'none') == 'none') {
            Mobiloud::set_option('ml_post_date_enabled', true);
        }
        if(Mobiloud::get_option('ml_page_date_enabled', 'none') == 'none') {
            Mobiloud::set_option('ml_page_date_enabled', true);
        }
        if(Mobiloud::get_option('ml_post_title_enabled', 'none') == 'none') {
            Mobiloud::set_option('ml_post_title_enabled', true);
        }
        if(Mobiloud::get_option('ml_page_title_enabled', 'none') == 'none') {
            Mobiloud::set_option('ml_page_title_enabled', true);
        }
        
        if(Mobiloud::get_option('ml_article_list_view_type', 'none') == 'none') {
            Mobiloud::set_option('ml_article_list_view_type', 'extended');
        }
        if(Mobiloud::get_option('ml_show_email_contact_link', 'none') == 'none') {
            Mobiloud::set_option('ml_show_email_contact_link', true);
        }
        if(Mobiloud::get_option('ml_contact_link_email', 'none') == 'none') {
            Mobiloud::set_option('ml_contact_link_email', get_bloginfo('admin_email'));
        }
        if(Mobiloud::get_option('ml_copyright_string', 'none') == 'none') {
            Mobiloud::set_option('ml_copyright_string', '&copy; ' . date('Y') . ' ' . get_bloginfo('name'));
        }
        if(Mobiloud::get_option('ml_comments_system', 'none') == 'none' || Mobiloud::get_option('ml_comments_system', 'none') == '') {
            Mobiloud::set_option('ml_comments_system', 'wordpress');
        }        
    }
    
    private static function run_db_install() {        
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        $table_name = $wpdb->prefix . "mobiloud_notifications";
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE " . $table_name . " (
                  id bigint(11) NOT NULL AUTO_INCREMENT,
                  time bigint(11) DEFAULT '0' NOT NULL,
                  post_id bigint(11),
                  msg blob,
                  android varchar(1) NOT NULL,
                  ios varchar(1) NOT NULL,
                  tags blob,
                  UNIQUE KEY id (id)
                );";

            dbDelta($sql);
        }
        
        $table_name = $wpdb->prefix . "mobiloud_notification_categories";

        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE " . $table_name . " (
                id bigint(11) NOT NULL AUTO_INCREMENT,
                cat_ID bigint(11) NOT NULL,
                UNIQUE KEY id (id)
            );";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
        
        $table_name = $wpdb->prefix . "mobiloud_categories";

        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            //install della tabella
            $sql = "CREATE TABLE " . $table_name . " (
                  id bigint(11) NOT NULL AUTO_INCREMENT,
                  time bigint(11) DEFAULT '0' NOT NULL,
                  cat_ID bigint(11) NOT NULL,
                  UNIQUE KEY id (id)
                );";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
        
        $table_name = $wpdb->prefix . "mobiloud_pages";

        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            //install della tabella
            $sql = "CREATE TABLE " . $table_name . " (
                  id bigint(11) NOT NULL AUTO_INCREMENT,
                  time bigint(11) DEFAULT '0' NOT NULL,
                  page_ID bigint(11) NOT NULL,
                  UNIQUE KEY id (id)
                );";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

        //check if there is the column 'ml_render'
        $results = $wpdb->get_results( "SHOW FULL COLUMNS FROM `" . $table_name."` LIKE 'ml_render'", ARRAY_A );
        if($results == NULL || count($results) == 0) {
            //update the table
            $sql = "ALTER TABLE $table_name ADD ml_render TINYINT(1) NOT NULL DEFAULT 1;";
            $wpdb->query($sql);
        }
    }
    
    public static function set_generic_option($name, $value) {
        if(!update_option($name,$value))
            add_option($name,$value);
    }
    
    public static function get_avatar($avatar,$comment) {
        $id_or_email = $comment->comment_author_email != NULL ? $comment->comment_author_email : $comment->user_id ;
        return $avatar;
    }
    
    /**
     * Get ML option value
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get_option($name, $default=null) {
        /*$options = get_option(self::$option_key, array());
        
        if(isset($options[$name])) {
            return $options[$name];
        } else {
            return $default;
        }*/
        return get_option($name, $default);
    }
    
    /**
     * Set ML option value
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    public static function set_option($name, $value) {
        /*$options = get_option(self::$option_key, array());
        $options[$name] = $value;
        return update_option(self::$option_key, $options);*/
        return update_option($name, $value);
    } 
    
    public static function trim_string($string, $length=30) {
        if(strlen($string) <= $length) {
            return $string;
        } else {
            return substr($string, 0, $length) . '...';
        }
    }
    
    public static function get_plugin_url() {
        return MOBILOUD_PLUGIN_URL;
    }
}