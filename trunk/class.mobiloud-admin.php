<?php
class Mobiloud_Admin {
    private static $initiated = false;
    private static $get_started_tasks = array(
        'design'=>array('nav_text'=>'Design', 'task_text'=>'Design your app'),
        'menu_config'=>array('nav_text'=>'Menu Configuration', 'task_text'=>'Configure the menu'),
        'test_app'=>array('nav_text'=>'Test The App', 'task_text'=>'Test the app'),
        'publish'=>array('nav_text'=>'Publish Your App', 'task_text'=>'Publish your app')
    );
    public static $settings_tabs = array(
        'general'=>'General',
        'posts'=>'Content',
        'advertising'=>'Advertising',
        'analytics'=>'Analytics',
        'editor'=>'Editor',
        'membership'=>'Membership',
        'license'=>'License'
    );    
    public static $push_tabs = array(
        'notifications'=>'Notifications',
        'settings'=>'Settings',
    );
    
    public static $editor_sections = array(
        'ml_post_head'=>'PHP Inside HEAD tag',
        'ml_post_custom_js'=>'Custom JS',
        'ml_post_custom_css'=>'Custom CSS',
        'ml_post_start_body'=>'PHP at the start of body tag',
        'ml_html_post_start_body'=>'HTML at the start of body tag',
        'ml_post_before_details'=>'PHP before post details',
        'ml_html_post_before_details'=>'HTML before post details',
        'ml_post_right_of_date'=>'PHP right of date',
        'ml_post_after_details'=>'PHP after post details',
        'ml_html_post_after_details'=>'HTML after post details',
        'ml_post_before_content'=>'PHP before Content',
        'ml_html_post_before_content'=>'HTML before Content',
        'ml_post_after_content'=>'PHP after Content',
        'ml_html_post_after_content'=>'HTML after Content',
        'ml_post_after_body'=>'PHP at the end of body tag',
        'ml_html_post_after_body'=>'HTML at the end of body tag',
        'ml_post_footer'=>'PHP Footer'
    );
    
    public static $banner_positions = array(
        'ml_banner_above_content'=>'Above Content',
        'ml_banner_above_title'=>'Above Title',
        'ml_banner_below_content'=>'Below Content',        
    );
    
    public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
        
        Mobiloud_App_Preview::init();
	}
    
    /**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;
        
        add_action('admin_init', array('Mobiloud_Admin', 'admin_init'));
        add_action('admin_menu', array('Mobiloud_Admin', 'admin_menu'));
        
        add_action('admin_head','ml_init_intercom');
        add_action('admin_head', 'ml_init_getvero');
        add_action('admin_head', array('Mobiloud_Admin', 'check_mailing_list_alert'));
        
        add_action('wp_ajax_ml_save_initial_data', array('Mobiloud_Admin', 'save_initial_data'));        
        add_action('wp_ajax_ml_save_editor', array('Mobiloud_Admin', 'save_editor'));
        add_action('wp_ajax_ml_save_banner', array('Mobiloud_Admin', 'save_banner'));        
    }
    
    public static function admin_init() {
        self::set_default_options();
        self::admin_redirect();
        self::register_scripts();
    }
    
    public static function admin_menu() {
        add_submenu_page( 'mobiloud', 'Get Started', 'Get Started', "activate_plugins", 'mobiloud', array('Mobiloud_Admin', 'menu_get_started'));
        add_menu_page('Mobiloud', 'Mobiloud', 'activate_plugins', 'mobiloud', array('Mobiloud_Admin', 'menu_get_started'), MOBILOUD_PLUGIN_URL."assets/img/ml-menu-icon.png", '25.90239843209'); 
        add_submenu_page( 'mobiloud', 'Settings', 'Settings', "activate_plugins", 'mobiloud_settings', array('Mobiloud_Admin', 'menu_settings'));
        add_submenu_page( 'mobiloud', 'Push Notification', 'Push Notifications', "activate_plugins", 'mobiloud_push', array('Mobiloud_Admin', 'menu_push'));
    }
    
    private static function set_default_options() {
        if(is_null(get_option('ml_eager_loading_enable', null))) {
            add_option('ml_eager_loading_enable',true);
        }
        if(is_null(get_option('ml_popup_message_on_mobile_active', null))) {
            add_option("ml_popup_message_on_mobile_active",false);
        }
        if(is_null(get_option('ml_automatic_image_resize', null))) {
            add_option("ml_automatic_image_resize",false);
        }        
    }
    
    private static function admin_redirect() {
        if(!defined('DOING_AJAX') || !DOING_AJAX) {
            // if(!self::initial_details_saved()) {
            //     if(!isset($_GET['page']) || (isset($_GET['page']) && $_GET['page'] !== 'mobiloud')) {
            //         wp_redirect('admin.php?page=mobiloud');
            //     }
            // }
            if (get_option('mobiloud_do_activation_redirect', false)) {
                delete_option('mobiloud_do_activation_redirect');
                if(!isset($_GET['activate-multi'])) { 
                    wp_redirect("admin.php?page=mobiloud");
                }
            }
        }
    }
    
    private static function register_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('wp-jquery-ui-dialog');
        
        wp_register_script('google_chart', 'https://www.google.com/jsapi');
        wp_enqueue_script('google_chart');
        
        wp_register_script('mobiloud-forms', MOBILOUD_PLUGIN_URL.'assets/js/mobiloud-forms.js');
        wp_enqueue_script('mobiloud-forms');
        
        wp_register_script('mobiloud-push', MOBILOUD_PLUGIN_URL.'assets/js/mobiloud-push.js');
        wp_enqueue_script('mobiloud-push');
        
        wp_register_script('mobiloud-editor', MOBILOUD_PLUGIN_URL.'assets/js/mobiloud-editor.js');
        wp_enqueue_script('mobiloud-editor');
        
        wp_register_script('mobiloud-menu-config', MOBILOUD_PLUGIN_URL.'assets/js/mobiloud-menu-config.js');
        wp_enqueue_script('mobiloud-menu-config');
        
        wp_register_script('mobiloud-app-simulator', MOBILOUD_PLUGIN_URL.'assets/js/mobiloud-app-simulator.js');
        wp_enqueue_script('mobiloud-app-simulator');
        
        wp_enqueue_script('mobiloud',MOBILOUD_PLUGIN_URL.'mobiloud.js',array('jquery','jquery-ui'),MOBILOUD_PLUGIN_VERSION);

        wp_register_style('mobiloud-iphone', MOBILOUD_PLUGIN_URL . "/css/iphone.css");
        wp_enqueue_style("mobiloud.css");
        wp_enqueue_style("mobiloud-iphone");

        wp_register_script('jquerychosen', MOBILOUD_PLUGIN_URL.'/libs/chosen/chosen.jquery.min.js', array('jquery'));
        wp_enqueue_script('jquerychosen');

        wp_register_script('iscroll', MOBILOUD_PLUGIN_URL.'/libs/iscroll/iscroll.js', array('jquery'));
        wp_enqueue_script('iscroll');

        wp_register_script('resizecrop', MOBILOUD_PLUGIN_URL.'/libs/jquery.resizecrop-1.0.3.min.js', array('jquery'));
        wp_enqueue_script('resizecrop');
        
        wp_register_script('imgliquid', MOBILOUD_PLUGIN_URL.'/libs/imgliquid/jquery.imgliquid.js', array('jquery'));
        wp_enqueue_script('imgliquid');
        
        wp_register_script('areyousure', MOBILOUD_PLUGIN_URL.'libs/jquery.are-you-sure.js', array('jquery'));
        wp_enqueue_script('areyousure');
        
        wp_register_style('jquerychosen-css', MOBILOUD_PLUGIN_URL . "/libs/chosen/chosen.css");
        wp_enqueue_style("jquerychosen-css");
        
        wp_register_style('mobiloud-dashicons', MOBILOUD_PLUGIN_URL . "/libs/dashicons/css/dashicons.css");
        wp_enqueue_style("mobiloud-dashicons");
        
        wp_register_style('mobiloud-style', MOBILOUD_PLUGIN_URL . "/assets/css/mobiloud-style.css");
        wp_enqueue_style("mobiloud-style");
        
        wp_register_style('mobiloud_admin_post', MOBILOUD_PLUGIN_URL . '/admin/post/post.css');
		wp_enqueue_style("mobiloud_admin_post");
    }
    
    public static function render_view($view, $parent=null, $data=array()) {
        if($parent === null) {
            $parent = $view;
        }
        if(!empty($data)) {
            foreach($data as $key=>$val) {
                $$key = $val;
            }
        }
        include MOBILOUD_PLUGIN_DIR . 'views/header.php';
        
        if(file_exists(MOBILOUD_PLUGIN_DIR . 'views/header_' . $parent . '.php'))
           include MOBILOUD_PLUGIN_DIR . 'views/header_' . $parent . '.php';
        
        include MOBILOUD_PLUGIN_DIR . 'views/'.$view.'.php';
        
        include MOBILOUD_PLUGIN_DIR . 'views/footer.php';
    }
    
    public static function render_part_view($view, $data=array(), $static=false) {                
        if(!empty($data)) {
            foreach($data as $key=>$val) {
                $$key = $val;
            }
        }
        if($static) {
            include MOBILOUD_PLUGIN_DIR . 'views/static/'.$view.'.php';
        } else {
            include MOBILOUD_PLUGIN_DIR . 'views/'.$view.'.php';           
        }
        
    }
    
    public static function render_remote_view($view) {
        $resp = wp_remote_get(MOBILOUD_CONTENT_URL . '/' . $view . '.php', array(
            'timeout'=>20
        ));
        if (!is_wp_error($resp) && isset($resp['body'])) {
            echo $resp['body'];
        } else {
            self::render_part_view($view, array(), true);
        }
    }
    
    public static function check_mailing_list_alert() {
        //check if maillist not alerted and initial details saved
        if(Mobiloud::get_option('ml_maillist_alert', '') === '' && Mobiloud::get_option('ml_initial_details_saved', '') === true) {
            self::track_user_event('mailinglist_signup');
            Mobiloud::set_option('ml_maillist_alert', true);
        }
    }
    
    public static function menu_get_started() {
        $tab = sanitize_text_field($_GET['tab']);
        switch($tab) {
            default:
            case 'design':
                wp_enqueue_script('wp-color-picker');
                wp_enqueue_media();
                wp_enqueue_style( 'wp-color-picker' );
                
                wp_register_script('mobiloud-app-preview-js', MOBILOUD_PLUGIN_URL.'/assets/js/mobiloud-app-preview.js', array('jquery'));
                wp_enqueue_script('mobiloud-app-preview-js');
        
                wp_register_style('mobiloud-app-preview', MOBILOUD_PLUGIN_URL . "/assets/css/mobiloud-app-preview.css");
                wp_enqueue_style("mobiloud-app-preview");
                
                global $current_user;
                get_currentuserinfo();

                /**
                 * Process Form
                 */
                if(count($_POST) && check_admin_referer('form-get_started_design')) {                    
                    Mobiloud::set_option('ml_preview_upload_image', sanitize_text_field($_POST['ml_preview_upload_image']));
                    Mobiloud::set_option('ml_preview_theme_color', sanitize_text_field($_POST['ml_preview_theme_color']));        
                    switch($_POST['homepagetype']) {
                        case 'ml_home_article_list_enabled':
                            Mobiloud::set_option('ml_home_article_list_enabled', true);    
                            Mobiloud::set_option('ml_home_page_enabled', false);    
                            Mobiloud::set_option('ml_home_url_enabled', false);    
                            break;
                        case 'ml_home_page_enabled':
                            Mobiloud::set_option('ml_home_article_list_enabled', false);    
                            Mobiloud::set_option('ml_home_page_enabled', true);    
                            Mobiloud::set_option('ml_home_url_enabled', false);    
                            break;
                        case 'ml_home_url_enabled':
                            Mobiloud::set_option('ml_home_article_list_enabled', false);    
                            Mobiloud::set_option('ml_home_page_enabled', false);    
                            Mobiloud::set_option('ml_home_url_enabled', true);    
                            break;
                    }
                    Mobiloud::set_option('ml_article_list_view_type', sanitize_text_field($_POST['ml_article_list_view_type'])); 
                    
                    Mobiloud::set_option('ml_home_page_id', sanitize_text_field($_POST['ml_home_page_id']));
                    Mobiloud::set_option('ml_home_url', sanitize_text_field($_POST['ml_home_url']));
                    
                    Mobiloud::set_option('ml_show_article_list_menu_item', isset($_POST['ml_show_article_list_menu_item']));
                    Mobiloud::set_option('ml_article_list_menu_item_title', sanitize_text_field($_POST['ml_article_list_menu_item_title']));
                    self::set_task_status('design', 'complete');
                }
                
                if(strlen(trim(get_option('ml_preview_theme_color'))) <=2) {
                    update_option("ml_preview_theme_color", '#1e73be');
                }

                $root_url = network_site_url('/');
                $plugins_url = plugins_url();
                $mobiloudPluginUrl = MOBILOUD_PLUGIN_URL;
                $mobiloudPluginVersion = MOBILOUD_PLUGIN_VERSION;
                $appname = get_bloginfo('name');
                
                if(!self::initial_details_saved()) {
                    self::render_part_view('initial_details', compact('current_user'));
                }
                self::render_view('get_started_design', 'get_started');
                self::track_user_event('view_get_started_design');
                break;
            case 'menu_config':
                
                
                /**
                 * Process Form
                 */
                if(count($_POST) && check_admin_referer('form-get_started_menu_config')) {
                    ml_remove_all_categories();
                    if(count($_POST['ml-menu-categories'])) {
                        foreach($_POST['ml-menu-categories'] as $cat_ID) {
                            ml_add_category(sanitize_text_field($cat_ID));
                        }
                    }
                    
                    ml_remove_all_pages();
                    if(count($_POST['ml-menu-pages'])) {
                        foreach($_POST['ml-menu-pages'] as $page_ID) {
                            ml_add_page(sanitize_text_field($page_ID));
                        }
                    }
                    
                    $menu_links = array();
                    if(count($_POST['ml-menu-links'])) {
                        foreach($_POST['ml-menu-links'] as $menu_link) {
                            $menu_link_vals = explode(":=:", $menu_link);
                            $menu_links[] = array(
                                'urlTitle'=>  sanitize_text_field($menu_link_vals[0]),
                                'url'=>sanitize_text_field($menu_link_vals[1]),
                            );
                        }
                    }
                    Mobiloud::set_option('ml_menu_urls', $menu_links);
                    
                    Mobiloud::set_option('ml_menu_show_favorites', $_POST['ml_menu_show_favorites'] == 'true');
                    Mobiloud::set_option('ml_show_android_cat_tabs', $_POST['ml_show_android_cat_tabs'] == 'true');     
                    
                    self::set_task_status('menu_config', 'complete');
                    self::track_user_event('menu_config_saved');
                }
                self::render_view('get_started_menu_config', 'get_started');
                self::track_user_event('view_get_started_menu_config');
                break;
            case 'test_app':                
                $plugin_url = str_replace("mobiloud-mobile-app-plugin", "", MOBILOUD_PLUGIN_URL);
                $check_url = 'http://www.mobiloud.com/simulator/check.php?url='.urlencode(MOBILOUD_PLUGIN_URL);
                $loadDemo = false;
                $check_content = @file_get_contents($check_url);
                $error_reason ='';
                if(self::isJson($check_content)) {   
                    $check_result = json_decode($check_content, true);
                    if(isset($check_result['error'])) {
                        $loadDemo = true;
                        $error_reason = $check_result['error'];
                    } 
                } else {
                    $loadDemo = true;
                    $error_reason = 'we are unable to reach your site';
                }
                $params_array = array('plugin_url'=> urldecode($plugin_url));
                $params = urlencode(json_encode($params_array));
                
                self::render_view('get_started_test_app', 'get_started', compact('loadDemo', 'params', 'error_reason'));
                self::track_user_event('view_get_started_test_app');
                self::set_task_status('test_app', 'complete');
                break;
            case 'publish':                       
                self::render_view('get_started_publish', 'get_started');
                self::track_user_event('view_get_started_publish');
                break;
        }
        
    }
    
    public static function menu_settings() {
        $tab = sanitize_text_field($_GET['tab']);
        switch($tab) {
            default:
            case 'general':
                wp_register_script('mobiloud-general', MOBILOUD_PLUGIN_URL.'/assets/js/mobiloud-general.js', array('jquery'));
                wp_enqueue_script('mobiloud-general');
                /**
                 * Process Form
                 */
                if(count($_POST) && check_admin_referer('form-settings_general')) {
                    Mobiloud::set_option('ml_app_name', sanitize_text_field($_POST['ml_app_name']));
                    Mobiloud::set_option('ml_show_email_contact_link', isset($_POST['ml_show_email_contact_link']));
                    Mobiloud::set_option('ml_contact_link_email', sanitize_text_field($_POST['ml_contact_link_email']));
                    Mobiloud::set_option('ml_copyright_string', sanitize_text_field($_POST['ml_copyright_string']));
                    Mobiloud::set_option('ml_article_list_enable_dates', isset($_POST['ml_article_list_enable_dates']));
                    Mobiloud::set_option('ml_automatic_image_resize_active', isset($_POST['ml_automatic_image_resize_active']));
                    Mobiloud::set_option('ml_article_list_show_excerpt', isset($_POST['ml_article_list_show_excerpt']));
                    Mobiloud::set_option('ml_article_list_show_comment_count', isset($_POST['ml_article_list_show_comment_count']));                    
                    
                    Mobiloud::set_option('sticky_category_1', sanitize_text_field($_POST['sticky_category_1']));
                    Mobiloud::set_option('ml_sticky_category_1_posts', sanitize_text_field($_POST['ml_sticky_category_1_posts']));
                    Mobiloud::set_option('sticky_category_2', sanitize_text_field($_POST['sticky_category_2']));
                    Mobiloud::set_option('ml_sticky_category_2_posts', sanitize_text_field($_POST['ml_sticky_category_2_posts']));
                    
                    $include_post_types = '';
                    if(isset($_POST['postypes']) && count($_POST['postypes'])) {
                        $include_post_types = implode(",", $_POST['postypes']);
                    }
                    Mobiloud::set_option('ml_article_list_include_post_types', sanitize_text_field($include_post_types));

                    $categories = get_categories();  
                    $exclude_categories = array();
                    if(count($categories)) {
                        foreach($categories as $category) {
                            if(!isset($_POST['categories']) || count($_POST['categories']) === 0 
                                    || (isset($_POST['categories']) && !in_array(html_entity_decode($category->cat_name), $_POST['categories']))) {
                                $exclude_categories[] = $category->cat_name;
                            }
                        }
                    }
                    
                    Mobiloud::set_option('ml_article_list_exclude_categories', implode(",", $exclude_categories));
                }
                self::render_view('settings_general', 'settings');
                self::track_user_event('view_settings_general');
                break;
            case 'posts':
                wp_enqueue_media();
                wp_register_script('mobiloud-posts', MOBILOUD_PLUGIN_URL.'/assets/js/mobiloud-posts.js', array('jquery'));
                wp_enqueue_script('mobiloud-posts');
                /**
                 * Process Form
                 */
                if(count($_POST) && check_admin_referer('form-settings_posts')) {
                    Mobiloud::set_option('ml_eager_loading_enable', isset($_POST['ml_eager_loading_enable']));
                    Mobiloud::set_option('ml_hierarchical_pages_enabled', isset($_POST['ml_hierarchical_pages_enabled']));
                    Mobiloud::set_option('ml_show_article_featuredimage', isset($_POST['ml_show_article_featuredimage']));
                    Mobiloud::set_option('ml_post_author_enabled', isset($_POST['ml_post_author_enabled']));
                    Mobiloud::set_option('ml_page_author_enabled', isset($_POST['ml_page_author_enabled']));
                    Mobiloud::set_option('ml_post_date_enabled', isset($_POST['ml_post_date_enabled']));
                    Mobiloud::set_option('ml_page_date_enabled', isset($_POST['ml_page_date_enabled']));
                    Mobiloud::set_option('ml_post_title_enabled', isset($_POST['ml_post_title_enabled']));
                    Mobiloud::set_option('ml_page_title_enabled', isset($_POST['ml_page_title_enabled']));
                    
                    Mobiloud::set_option('ml_custom_field_enable', isset($_POST['ml_custom_field_enable']));
                    Mobiloud::set_option('ml_custom_field_name', sanitize_text_field($_POST['ml_custom_field_name']));
                    
                    Mobiloud::set_option('ml_custom_field_url', sanitize_text_field($_POST['ml_custom_field_url']));
                    Mobiloud::set_option('ml_custom_featured_image', sanitize_text_field($_POST['ml_custom_featured_image']));
                    
                    Mobiloud::set_option('ml_comments_system', sanitize_text_field($_POST['ml_comments_system']));
                    Mobiloud::set_option('ml_disqus_shortname', sanitize_text_field($_POST['ml_disqus_shortname']));
                }
                self::render_view('settings_posts', 'settings');
                self::track_user_event('view_settings_posts');
                break;
            case 'analytics':
                /**
                 * Process Form
                 */
                if(count($_POST) && check_admin_referer('form-settings_analytics')) {
                    Mobiloud::set_option('ml_google_tracking_id', sanitize_text_field($_POST['ml_google_tracking_id']));
                }
                self::render_view('settings_analytics', 'settings');
                self::track_user_event('view_settings_analytics');
                break;
            case 'advertising':
                /**
                 * Process Form
                 */
                if(count($_POST) && check_admin_referer('form-settings_advertising')) {
                    Mobiloud::set_option('ml_advertising_platform', sanitize_text_field($_POST['ml_advertising_platform']));
                    
                    //iOS                    
                    Mobiloud::set_option('ml_ios_phone_banner_unit_id', sanitize_text_field($_POST['ml_ios_phone_banner_unit_id']));
                    Mobiloud::set_option('ml_ios_tablet_banner_unit_id', sanitize_text_field($_POST['ml_ios_tablet_banner_unit_id']));
                    Mobiloud::set_option('ml_ios_banner_position', sanitize_text_field($_POST['ml_ios_banner_position']));
                    Mobiloud::set_option('ml_ios_interstitial_unit_id', sanitize_text_field($_POST['ml_ios_interstitial_unit_id']));
                    Mobiloud::set_option('ml_ios_interstitial_interval', (int) sanitize_text_field($_POST['ml_ios_interstitial_interval']));
                    Mobiloud::set_option('ml_ios_native_ad_unit_id', sanitize_text_field($_POST['ml_ios_native_ad_unit_id']));
                    Mobiloud::set_option('ml_ios_native_ad_interval', (int) sanitize_text_field($_POST['ml_ios_native_ad_interval']));
                    
                    //Android
                    Mobiloud::set_option('ml_android_phone_banner_unit_id', sanitize_text_field($_POST['ml_android_phone_banner_unit_id']));
                    Mobiloud::set_option('ml_android_tablet_banner_unit_id', sanitize_text_field($_POST['ml_android_tablet_banner_unit_id']));
                    Mobiloud::set_option('ml_android_banner_position', sanitize_text_field($_POST['ml_android_banner_position']));
                    Mobiloud::set_option('ml_android_interstitial_unit_id', sanitize_text_field($_POST['ml_android_interstitial_unit_id']));
                    Mobiloud::set_option('ml_android_interstitial_interval', (int) sanitize_text_field($_POST['ml_android_interstitial_interval']));
                    Mobiloud::set_option('ml_android_native_ad_unit_id', sanitize_text_field($_POST['ml_android_native_ad_unit_id']));
                    Mobiloud::set_option('ml_android_native_ad_interval', (int) sanitize_text_field($_POST['ml_android_native_ad_interval']));
                }
                self::render_view('settings_advertising', 'settings');
                self::track_user_event('view_settings_advertising');
                break;
            case 'editor':
                self::render_view('settings_editor', 'settings');
                self::track_user_event('view_settings_editor');
                break;
            
            case 'license':
                /**
                 * Process Form
                 */
                if(count($_POST) && check_admin_referer('form-settings_license')) {
                    Mobiloud::set_option('ml_pb_app_id', sanitize_text_field($_POST['ml_pb_app_id']));
                    Mobiloud::set_option('ml_pb_secret_key', sanitize_text_field($_POST['ml_pb_secret_key']));
                }
                self::render_view('settings_license', 'settings');
                self::track_user_event('view_settings_license');
                break;
            
            case 'membership':
                /**
                 * Process Form
                 */
                if(count($_POST) && check_admin_referer('form-settings_membership')) {
                    Mobiloud::set_option('ml_subscriptions_enable', isset($_POST['ml_subscriptions_enable']));
                }
                self::render_view('settings_membership', 'settings');
                self::track_user_event('view_settings_membership');
                break;
        }
    }
    
    public static function menu_push() {
        $tab = sanitize_text_field($_GET['tab']);
        switch($tab) {
            default:
            case 'notifications':
                self::render_view('push_notifications', 'push');
                self::track_user_event('view_push_notifications');
                break;
            case 'settings':
                /**
                 * Process Form
                 */
                if(count($_POST) && check_admin_referer('form-push_settings')) {
                    Mobiloud::set_option('ml_push_notification_enabled', isset($_POST['ml_push_notification_enabled']));
                    Mobiloud::set_option('ml_pb_use_ssl', isset($_POST['ml_pb_use_ssl']));
                    
                    if(isset($_POST['ml_push_notification_categories'])) {
                        ml_push_notification_categories_clear();
                        if(is_array($_POST['ml_push_notification_categories'])) {
                            foreach($_POST['ml_push_notification_categories'] as $categoryID) {
                                ml_push_notification_categories_add($categoryID);
                            }
                        }
                    } else {
                         ml_push_notification_categories_clear();
                    }
                }
                self::render_view('push_settings', 'push');
                self::track_user_event('view_push_settings');
                break;
        }
    }
    
    /**
     * Get list of tasks for "Get Started" page
     * @return array
     */
    public static function get_started_tasks() {
        return self::$get_started_tasks;
    }
    
    /**
     * Get task CSS class (default, act ve, complete)
     * @param string $task
     */
    public static function get_task_class($task) {
        $class = '';
        $tab = sanitize_text_field($_GET['tab']);
        if($task == $tab || (!isset($_GET['tab']) && $task == 'design')) {
            $class = 'current';
        }
        
        $class .= ' ' . self::get_task_status($task);
        return $class;
    }
    
    public static function set_task_status($task, $status) {
        $task_statuses = Mobiloud::get_option('ml_get_start_tasks', false);
        if($task_statuses === false) {
            $task_statuses = array(
                $task=>$status
            );
        } else {
            $task_statuses[$task] = $status;
        }
        Mobiloud::set_option('ml_get_start_tasks', $task_statuses);
    }
    
    public static function get_task_status($task) {
        $task_statuses = Mobiloud::get_option('ml_get_start_tasks', false);
        if($task_statuses !== false && isset($task_statuses[$task])) {
            return $task_statuses[$task];
        }
        return 'incomplete';
    }
    
    private static function isJson($string) { 
        json_decode($string);
        return strlen($string) > 0;
    }
    
    public static function initial_details_saved() {
        return Mobiloud::get_option('ml_initial_details_saved', false);
    }
    
    public static function save_initial_data() {
        Mobiloud::set_option('ml_initial_details_saved', true);
        Mobiloud::set_option('ml_user_name', sanitize_text_field($_POST['ml_name']));
        Mobiloud::set_option('ml_user_email', sanitize_text_field($_POST['ml_email']));
        Mobiloud::set_option('ml_user_site', sanitize_text_field($_POST['ml_site']));
        Mobiloud::set_option('ml_join_mailinglist', sanitize_text_field($_POST['ml_maillist']));
    }
    
    public static function save_editor() {
        if(isset(self::$editor_sections[$_POST['editor']])) {
            Mobiloud::set_option($_POST['editor'], $_POST['value']);
        }
    }
    
    public static function save_banner() {
        if(isset(self::$banner_positions[$_POST['position']])) {
            Mobiloud::set_option($_POST['position'], $_POST['value']);
        }
    }
    
    public static function track_user_event($event) {
        if(Mobiloud::get_option('ml_initial_details_saved')) {
            ?>
            <script type='text/javascript'>
                _veroq.push(['track', "<?php echo esc_js($event); ?>"]);
                Intercom("trackUserEvent", "<?php echo esc_js($event); ?>");
            </script>

            <?php
        }
    }
}