<?php

class Mobiloud_Admin {

    private static $initiated = false;
    private static $get_started_tasks = array(
        'design' => array('nav_text' => 'Design', 'task_text' => 'Design your app'),
        'menu_config' => array('nav_text' => 'Menu Configuration', 'task_text' => 'Configure the menu'),
        'test_app' => array('nav_text' => 'Test The App', 'task_text' => 'Test the app'),
        'publish' => array('nav_text' => 'Publish Your App', 'task_text' => 'Publish your app')
    );
    public static $settings_tabs = array(
        'general' => 'General',
        'posts' => 'Content',
        'advertising' => 'Advertising',
        'analytics' => 'Analytics',
        'editor' => 'Editor',
        'membership' => 'Membership',
        'license' => 'License'
    );
    public static $push_tabs = array(
        'notifications' => 'Notifications',
        'settings' => 'Settings',
    );
    public static $editor_sections = array(
        'ml_post_head' => 'PHP Inside HEAD tag',
        'ml_post_custom_js' => 'Custom JS',
        'ml_post_custom_css' => 'Custom CSS',
        'ml_post_start_body' => 'PHP at the start of body tag',
        'ml_html_post_start_body' => 'HTML at the start of body tag',
        'ml_post_before_details' => 'PHP before post details',
        'ml_html_post_before_details' => 'HTML before post details',
        'ml_post_right_of_date' => 'PHP right of date',
        'ml_post_after_details' => 'PHP after post details',
        'ml_html_post_after_details' => 'HTML after post details',
        'ml_post_before_content' => 'PHP before Content',
        'ml_html_post_before_content' => 'HTML before Content',
        'ml_post_after_content' => 'PHP after Content',
        'ml_html_post_after_content' => 'HTML after Content',
        'ml_post_after_body' => 'PHP at the end of body tag',
        'ml_html_post_after_body' => 'HTML at the end of body tag',
        'ml_post_footer' => 'PHP Footer'
    );
    public static $banner_positions = array(
        'ml_banner_above_content' => 'Above Content',
        'ml_banner_above_title' => 'Above Title',
        'ml_banner_below_content' => 'Below Content',
    );

    public static function init() {
        if (!self::$initiated) {
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

//        add_action('admin_head', 'ml_init_olark');
//        add_action('admin_head', 'ml_init_getvero');
        add_action('admin_head', 'ml_init_intercom');
        add_action('admin_head', 'ml_init_perfect_audience');
        add_action('admin_head', array('Mobiloud_Admin', 'check_mailing_list_alert'));

        add_action('wp_ajax_ml_save_initial_data', array('Mobiloud_Admin', 'save_initial_data'));
        add_action('wp_ajax_ml_save_editor', array('Mobiloud_Admin', 'save_editor'));
        add_action('wp_ajax_ml_save_banner', array('Mobiloud_Admin', 'save_banner'));
        add_action('wp_ajax_ml_tax_list', array('Mobiloud_Admin', 'get_tax_list'));

        add_action('save_post', array('Mobiloud_Admin', 'flush_cache_on_save') );
        add_action('transition_post_status',  array('Mobiloud_Admin','flush_cache_on_transition'), 10, 3 );
    }

    public static function flush_cache_on_save( $post_id ) {

        global $wpdb;

        $json_transients = $wpdb->get_results(
            "SELECT option_name AS name FROM $wpdb->options
              WHERE option_name LIKE '_transient_ml_json%'"
        );

        foreach ($json_transients as $transient) {
            delete_transient( trim($transient->name,'_transient_') );
        }

        $key = http_build_query(array('post_id'=>"$post_id", "type"=>"ml_post") );
        $hash = hash('crc32', $key);
        delete_transient( 'ml_post_'.$hash );
    }

    public static function flush_cache_on_transition( $new_status, $old_status, $post ) {

        global $wpdb;

        $json_transients = $wpdb->get_results(
            "SELECT option_name AS name FROM $wpdb->options
              WHERE option_name LIKE '_transient_ml_json%'"
        );

        foreach ($json_transients as $transient) {
            delete_transient( trim($transient->name,'_transient_') );
        }

        $key = http_build_query(array('post_id'=>"$post->ID", "type"=>"ml_post") );
        $hash = hash('crc32', $key);
        delete_transient( 'ml_post_'.$hash );
    }

    public static function admin_init() {
        self::set_default_options();
        self::admin_redirect();
        self::register_scripts();
    }

    public static function admin_menu() {
        add_submenu_page('mobiloud', 'Get Started', 'Get Started', "activate_plugins", 'mobiloud', array('Mobiloud_Admin', 'menu_get_started'));
        add_menu_page('Mobiloud', 'Mobiloud', 'activate_plugins', 'mobiloud', array('Mobiloud_Admin', 'menu_get_started'), "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PHN2ZyAgIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIiAgIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyIgICB4bWxuczpzdmc9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgICB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgICB4bWxuczpzb2RpcG9kaT0iaHR0cDovL3NvZGlwb2RpLnNvdXJjZWZvcmdlLm5ldC9EVEQvc29kaXBvZGktMC5kdGQiICAgeG1sbnM6aW5rc2NhcGU9Imh0dHA6Ly93d3cuaW5rc2NhcGUub3JnL25hbWVzcGFjZXMvaW5rc2NhcGUiICAgdmVyc2lvbj0iMS4wIiAgIGlkPSJMYXllcl8xIiAgIHg9IjBweCIgICB5PSIwcHgiICAgd2lkdGg9IjI0cHgiICAgaGVpZ2h0PSIyNHB4IiAgIHZpZXdCb3g9IjAgMCAyNCAyNCIgICBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCAyNCAyNCIgICB4bWw6c3BhY2U9InByZXNlcnZlIiAgIGlua3NjYXBlOnZlcnNpb249IjAuNDguNCByOTkzOSIgICBzb2RpcG9kaTpkb2NuYW1lPSJ3b3JkX3JpcXVhZHJvX29uZGFfYmx1XzI0LjEuc3ZnIj48bWV0YWRhdGEgICAgIGlkPSJtZXRhZGF0YTI5Ij48cmRmOlJERj48Y2M6V29yayAgICAgICAgIHJkZjphYm91dD0iIj48ZGM6Zm9ybWF0PmltYWdlL3N2Zyt4bWw8L2RjOmZvcm1hdD48ZGM6dHlwZSAgICAgICAgICAgcmRmOnJlc291cmNlPSJodHRwOi8vcHVybC5vcmcvZGMvZGNtaXR5cGUvU3RpbGxJbWFnZSIgLz48ZGM6dGl0bGU+PC9kYzp0aXRsZT48L2NjOldvcms+PC9yZGY6UkRGPjwvbWV0YWRhdGE+PGRlZnMgICAgIGlkPSJkZWZzMjciPjxjbGlwUGF0aCAgICAgICBpZD0iU1ZHSURfMl8tMiI+PHVzZSAgICAgICAgIGhlaWdodD0iMTA1Mi4zNjIyIiAgICAgICAgIHdpZHRoPSI3NDQuMDk0NDgiICAgICAgICAgeT0iMCIgICAgICAgICB4PSIwIiAgICAgICAgIHN0eWxlPSJvdmVyZmxvdzp2aXNpYmxlIiAgICAgICAgIHhsaW5rOmhyZWY9IiNTVkdJRF8xXy04IiAgICAgICAgIG92ZXJmbG93PSJ2aXNpYmxlIiAgICAgICAgIGlkPSJ1c2U5LTEiIC8+PC9jbGlwUGF0aD48Y2xpcFBhdGggICAgICAgaWQ9ImNsaXBQYXRoMzAxOCI+PHVzZSAgICAgICAgIGhlaWdodD0iMTA1Mi4zNjIyIiAgICAgICAgIHdpZHRoPSI3NDQuMDk0NDgiICAgICAgICAgeT0iMCIgICAgICAgICB4PSIwIiAgICAgICAgIHN0eWxlPSJvdmVyZmxvdzp2aXNpYmxlIiAgICAgICAgIHhsaW5rOmhyZWY9IiNTVkdJRF8xXy04IiAgICAgICAgIG92ZXJmbG93PSJ2aXNpYmxlIiAgICAgICAgIGlkPSJ1c2UzMDIwIiAvPjwvY2xpcFBhdGg+PGNsaXBQYXRoICAgICAgIGlkPSJjbGlwUGF0aDMwMjIiPjx1c2UgICAgICAgICBoZWlnaHQ9IjEwNTIuMzYyMiIgICAgICAgICB3aWR0aD0iNzQ0LjA5NDQ4IiAgICAgICAgIHk9IjAiICAgICAgICAgeD0iMCIgICAgICAgICBzdHlsZT0ib3ZlcmZsb3c6dmlzaWJsZSIgICAgICAgICB4bGluazpocmVmPSIjU1ZHSURfMV8tOCIgICAgICAgICBvdmVyZmxvdz0idmlzaWJsZSIgICAgICAgICBpZD0idXNlMzAyNCIgLz48L2NsaXBQYXRoPjxjbGlwUGF0aCAgICAgICBpZD0iY2xpcFBhdGgzMDI2Ij48dXNlICAgICAgICAgaGVpZ2h0PSIxMDUyLjM2MjIiICAgICAgICAgd2lkdGg9Ijc0NC4wOTQ0OCIgICAgICAgICB5PSIwIiAgICAgICAgIHg9IjAiICAgICAgICAgc3R5bGU9Im92ZXJmbG93OnZpc2libGUiICAgICAgICAgeGxpbms6aHJlZj0iI1NWR0lEXzFfLTgiICAgICAgICAgb3ZlcmZsb3c9InZpc2libGUiICAgICAgICAgaWQ9InVzZTMwMjgiIC8+PC9jbGlwUGF0aD48Y2xpcFBhdGggICAgICAgaWQ9ImNsaXBQYXRoMzAzMCI+PHVzZSAgICAgICAgIGhlaWdodD0iMTA1Mi4zNjIyIiAgICAgICAgIHdpZHRoPSI3NDQuMDk0NDgiICAgICAgICAgeT0iMCIgICAgICAgICB4PSIwIiAgICAgICAgIHN0eWxlPSJvdmVyZmxvdzp2aXNpYmxlIiAgICAgICAgIHhsaW5rOmhyZWY9IiNTVkdJRF8xXy04IiAgICAgICAgIG92ZXJmbG93PSJ2aXNpYmxlIiAgICAgICAgIGlkPSJ1c2UzMDMyIiAvPjwvY2xpcFBhdGg+PGNsaXBQYXRoICAgICAgIGlkPSJjbGlwUGF0aDMwMzQiPjx1c2UgICAgICAgICBoZWlnaHQ9IjEwNTIuMzYyMiIgICAgICAgICB3aWR0aD0iNzQ0LjA5NDQ4IiAgICAgICAgIHk9IjAiICAgICAgICAgeD0iMCIgICAgICAgICBzdHlsZT0ib3ZlcmZsb3c6dmlzaWJsZSIgICAgICAgICB4bGluazpocmVmPSIjU1ZHSURfMV8tOCIgICAgICAgICBvdmVyZmxvdz0idmlzaWJsZSIgICAgICAgICBpZD0idXNlMzAzNiIgLz48L2NsaXBQYXRoPjxjbGlwUGF0aCAgICAgICBpZD0iY2xpcFBhdGgzMDM4Ij48dXNlICAgICAgICAgaGVpZ2h0PSIxMDUyLjM2MjIiICAgICAgICAgd2lkdGg9Ijc0NC4wOTQ0OCIgICAgICAgICB5PSIwIiAgICAgICAgIHg9IjAiICAgICAgICAgc3R5bGU9Im92ZXJmbG93OnZpc2libGUiICAgICAgICAgeGxpbms6aHJlZj0iI1NWR0lEXzFfLTgiICAgICAgICAgb3ZlcmZsb3c9InZpc2libGUiICAgICAgICAgaWQ9InVzZTMwNDAiIC8+PC9jbGlwUGF0aD48ZGVmcyAgICAgICBpZD0iZGVmczUiPjxyZWN0ICAgICAgICAgaGVpZ2h0PSIyNCIgICAgICAgICB3aWR0aD0iMjQiICAgICAgICAgaWQ9IlNWR0lEXzFfIiAvPjwvZGVmcz48Y2xpcFBhdGggICAgICAgaWQ9IlNWR0lEXzJfIj48dXNlICAgICAgICAgaWQ9InVzZTkiICAgICAgICAgb3ZlcmZsb3c9InZpc2libGUiICAgICAgICAgeGxpbms6aHJlZj0iI1NWR0lEXzFfIiAvPjwvY2xpcFBhdGg+PGRlZnMgICAgICAgaWQ9ImRlZnM1LTIiPjxyZWN0ICAgICAgICAgaGVpZ2h0PSIyNCIgICAgICAgICB3aWR0aD0iMjQiICAgICAgICAgaWQ9IlNWR0lEXzFfLTgiICAgICAgICAgeD0iMCIgICAgICAgICB5PSIwIiAvPjwvZGVmcz48Y2xpcFBhdGggICAgICAgaWQ9ImNsaXBQYXRoMzA0NSI+PHVzZSAgICAgICAgIGlkPSJ1c2UzMDQ3IiAgICAgICAgIG92ZXJmbG93PSJ2aXNpYmxlIiAgICAgICAgIHhsaW5rOmhyZWY9IiNTVkdJRF8xXy04IiAgICAgICAgIHN0eWxlPSJvdmVyZmxvdzp2aXNpYmxlIiAgICAgICAgIHg9IjAiICAgICAgICAgeT0iMCIgICAgICAgICB3aWR0aD0iNzQ0LjA5NDQ4IiAgICAgICAgIGhlaWdodD0iMTA1Mi4zNjIyIiAvPjwvY2xpcFBhdGg+PC9kZWZzPjxzb2RpcG9kaTpuYW1lZHZpZXcgICAgIHBhZ2Vjb2xvcj0iI2ZmZmZmZiIgICAgIGJvcmRlcmNvbG9yPSIjNjY2NjY2IiAgICAgYm9yZGVyb3BhY2l0eT0iMSIgICAgIG9iamVjdHRvbGVyYW5jZT0iMTAiICAgICBncmlkdG9sZXJhbmNlPSIxMCIgICAgIGd1aWRldG9sZXJhbmNlPSIxMCIgICAgIGlua3NjYXBlOnBhZ2VvcGFjaXR5PSIwIiAgICAgaW5rc2NhcGU6cGFnZXNoYWRvdz0iMiIgICAgIGlua3NjYXBlOndpbmRvdy13aWR0aD0iNjQwIiAgICAgaW5rc2NhcGU6d2luZG93LWhlaWdodD0iNDgwIiAgICAgaWQ9Im5hbWVkdmlldzI1IiAgICAgc2hvd2dyaWQ9ImZhbHNlIiAgICAgaW5rc2NhcGU6em9vbT0iOS44MzMzMzMzIiAgICAgaW5rc2NhcGU6Y3g9IjEyIiAgICAgaW5rc2NhcGU6Y3k9IjEyIiAgICAgaW5rc2NhcGU6d2luZG93LXg9IjUyNSIgICAgIGlua3NjYXBlOndpbmRvdy15PSI2NiIgICAgIGlua3NjYXBlOndpbmRvdy1tYXhpbWl6ZWQ9IjAiICAgICBpbmtzY2FwZTpjdXJyZW50LWxheWVyPSJMYXllcl8xIiAvPjxwYXRoICAgICBzdHlsZT0iZmlsbDojMDAwMDAwIiAgICAgY2xpcC1wYXRoPSJ1cmwoI1NWR0lEXzJfKSIgICAgIGQ9Ik0gNCAwIEMgMS43OTEgMCAwIDEuNzkxIDAgNCBMIDAgMjAgQyAwIDIyLjIwOSAxLjc5MSAyNCA0IDI0IEwgMjAgMjQgQyAyMi4yMDkgMjQgMjQgMjIuMjA5IDI0IDIwIEwgMjQgNCBDIDI0IDEuNzkxIDIyLjIwOSAwIDIwIDAgTCA0IDAgeiBNIDEzLjUgMy41IEMgMTMuNjI2NDcgMy41IDEzLjc2MDA3NSAzLjUyNzgwNzUgMTMuODc1IDMuNTYyNSBDIDEzLjk2NDMyMiAzLjU4ODAxMDYgMTQuMDQ0NTY2IDMuNjEzNDE5NiAxNC4xMjUgMy42NTYyNSBDIDE0LjE0NjI2MyAzLjY2ODI4MjkgMTQuMTY2OTgzIDMuNjc0MzIyNSAxNC4xODc1IDMuNjg3NSBDIDE0LjI5ODM5NSAzLjc1NDUxMTcgMTQuMzgyNDM3IDMuODQxNzk4NiAxNC40Njg3NSAzLjkzNzUgQyAxNC41NDc5MzggNC4wMjQ0OTcgMTQuNjAxMjUzIDQuMTE0MTQwOSAxNC42NTYyNSA0LjIxODc1IEwgMTQuNjg3NSA0LjIxODc1IEMgMTQuNzAyNzE3IDQuMjQ4NDA3MyAxNC43MDM3MDIgNC4yODI3NDIgMTQuNzE4NzUgNC4zMTI1IEMgMTQuODUxNTQyIDQuNTc1MTA2MyAxNC45NzQzNjEgNC44MjM1NDY2IDE1LjA5Mzc1IDUuMDkzNzUgQyAxNS4xMDcwOTMgNS4xMjM4OTYyIDE1LjExMTgzMSA1LjE1NzI2ODggMTUuMTI1IDUuMTg3NSBDIDE1LjI0MzAzIDUuNDU4OTExMyAxNS4zNjQ2NDggNS43MjE0NTk1IDE1LjQ2ODc1IDYgQyAxNS41OTQyNzggNi4zMzQ4MTMyIDE1LjcwODE3NCA2LjY4NzE0ODMgMTUuODEyNSA3LjAzMTI1IEMgMTUuODk5MjYxIDcuMzE4NzI5NSAxNS45OTA3MjYgNy42MTI1ODQ2IDE2LjA2MjUgNy45MDYyNSBDIDE2LjA2NzM1IDcuOTI2MTM3NCAxNi4wNTc3MiA3Ljk0ODgzNSAxNi4wNjI1IDcuOTY4NzUgQyAxNi4xMzYyNjggOC4yNzUyNzQ3IDE2LjIyNDI4OSA4LjU5MzUyOSAxNi4yODEyNSA4LjkwNjI1IEMgMTYuMjgzMDU3IDguOTE2Mjc3MiAxNi4yNzk0NiA4LjkyNzQ2NjQgMTYuMjgxMjUgOC45Mzc1IEMgMTYuMzM5Mzg1IDkuMjYwMDI0MyAxNi4zNjY1MjcgOS41Nzc3MDM1IDE2LjQwNjI1IDkuOTA2MjUgQyAxNi40ODczNTggMTAuNTgzMDYyIDE2LjUzMTI1IDExLjI2NzAyNCAxNi41MzEyNSAxMS45Njg3NSBMIDE2LjUzMTI1IDEyIEwgMTYuNTYyNSAxMiBMIDE2LjU2MjUgMTIuMDMxMjUgQyAxNi41NjI1IDEyLjcxNjI1IDE2LjUxNzY5OSAxMy40MDEzNzIgMTYuNDM3NSAxNC4wNjI1IEMgMTYuNDM2Mjg2IDE0LjA3MjY5MyAxNi40Mzg3MzIgMTQuMDgzNTYyIDE2LjQzNzUgMTQuMDkzNzUgQyAxNi4zOTgwNzIgMTQuNDEzNzI5IDE2LjMzODQ4OSAxNC43MTczNDQgMTYuMjgxMjUgMTUuMDMxMjUgQyAxNi4yNzc2MjYgMTUuMDUxNTY0IDE2LjI4NDk0NiAxNS4wNzM0NjEgMTYuMjgxMjUgMTUuMDkzNzUgQyAxNi4yMjQ3NDIgMTUuMzk3MzA0IDE2LjE2NjMyOSAxNS43MDI0MzcgMTYuMDkzNzUgMTYgQyAxNi4wMjg1NDkgMTYuMjczNTIzIDE1Ljk1MzIwMSAxNi41NDQyNjYgMTUuODc1IDE2LjgxMjUgQyAxNS43NzAwMDIgMTcuMTY0OTM4IDE1LjY1Nzk4MyAxNy41MDA4NjUgMTUuNTMxMjUgMTcuODQzNzUgQyAxNS4zMTQ3NDkgMTguNDQwMDI1IDE1LjA2MDI0OCAxOC45OTkzODcgMTQuNzgxMjUgMTkuNTYyNSBDIDE0Ljc3MDkzNiAxOS41ODMzMDYgMTQuNzYwMzk0IDE5LjYwNDIzOCAxNC43NSAxOS42MjUgQyAxNC43NDMwMDMgMTkuNjQzOTI4IDE0Ljc1NzgzMyAxOS42Njg5OTMgMTQuNzUgMTkuNjg3NSBDIDE0LjczMzEyNyAxOS43MjA5MjcgMTQuNzA0NTg2IDE5Ljc0Nzk1IDE0LjY4NzUgMTkuNzgxMjUgQyAxNC42MzI0MjkgMTkuODg1Nzc1IDE0LjU3OTIwMiAxOS45NzU1MTkgMTQuNSAyMC4wNjI1IEMgMTQuNDQ1NCAyMC4xMjI5MzggMTQuMzc3Mzc2IDIwLjE2OTI4OSAxNC4zMTI1IDIwLjIxODc1IEMgMTQuMDk0NzEgMjAuMzg5MzY2IDEzLjgyODY3NCAyMC41IDEzLjUzMTI1IDIwLjUgQyAxMy40Mjk5NjMgMjAuNSAxMy4zNDQ2OTIgMjAuNDkwMzk1IDEzLjI1IDIwLjQ2ODc1IEMgMTMuMTc0NTQzIDIwLjQ1MzMxMSAxMy4xMDE0MjggMjAuNDM0MDI3IDEzLjAzMTI1IDIwLjQwNjI1IEMgMTMuMDIzMzEzIDIwLjQwMjkwMiAxMy4wMDc4NTYgMjAuNDA5NzQ5IDEzIDIwLjQwNjI1IEMgMTIuODQyNzM0IDIwLjMzOTc2NiAxMi43MTI4MTMgMjAuMjQzOTM4IDEyLjU5Mzc1IDIwLjEyNSBDIDEyLjM1NjI1IDE5Ljg4Nzc1IDEyLjIxODc1IDE5LjU1MSAxMi4yMTg3NSAxOS4xODc1IEMgMTIuMjE4NzUgMTkuMDg3ODg3IDEyLjIyNzgxOSAxOC45OTUzNjkgMTIuMjUgMTguOTA2MjUgQyAxMi4yNTIzMTQgMTguODk0OTM0IDEyLjI0NzM5NyAxOC44ODYyMDggMTIuMjUgMTguODc1IEMgMTIuMjU2MTMxIDE4Ljg1Mjc4OCAxMi4yNzM5MTYgMTguODM0MjU2IDEyLjI4MTI1IDE4LjgxMjUgQyAxMi4zMDU4MSAxOC43Mjk2ODIgMTIuMzM1NDcgMTguNjY4ODYgMTIuMzc1IDE4LjU5Mzc1IEwgMTIuMzQzNzUgMTguNTkzNzUgQyAxMy4zNTQ3NSAxNi42MjQ3NSAxMy45MDYyNSAxNC4zOTYyNSAxMy45MDYyNSAxMi4wMzEyNSBMIDEzLjkwNjI1IDEyIEwgMTMuOTA2MjUgMTEuOTY4NzUgQyAxMy45MDYyNSAxMS42NzMxMjUgMTMuODkyOTM1IDExLjM4NDg2OSAxMy44NzUgMTEuMDkzNzUgQyAxMy44Mzk4ODMgMTAuNTExMjY2IDEzLjc1ODU1NSA5LjkzNjg1OTQgMTMuNjU2MjUgOS4zNzUgQyAxMy41NTE0MDYgOC44MDg2NTc4IDEzLjQxOTA3OCA4LjI1OTg4MDkgMTMuMjUgNy43MTg3NSBDIDEzLjE2NzI4NSA3LjQ1MDE5NTMgMTMuMDk3NzM0IDcuMTk5MDkzOCAxMyA2LjkzNzUgQyAxMi44MDMyNDIgNi40MTQ3MzQ0IDEyLjU2NSA1Ljg5ODUgMTIuMzEyNSA1LjQwNjI1IEMgMTIuMzA4MjMzIDUuMzk3NTMzMiAxMi4zMTY1MzkgNS4zODM5MDA5IDEyLjMxMjUgNS4zNzUgQyAxMi4yODgyMTcgNS4zMjI3MzE2IDEyLjI2NzM0NyA1LjI3NDQ2ODggMTIuMjUgNS4yMTg3NSBDIDEyLjIzNzk5IDUuMTc2NjM0NiAxMi4yMjY3MzkgNS4xMzc2MzU4IDEyLjIxODc1IDUuMDkzNzUgQyAxMi4yMDExOTkgNS4wMDgwNjg0IDEyLjE4NzUgNC45MDMzNzUgMTIuMTg3NSA0LjgxMjUgQyAxMi4xODc1IDQuMDg1NSAxMi43NzMgMy41IDEzLjUgMy41IHogTSA4Ljc1IDUuOTM3NSBDIDkuMTI5NDExMyA1LjkzNzUgOS40ODExNDMzIDYuMTE0MDA4OSA5LjcxODc1IDYuMzc1IEMgOS43OTc5NTIyIDYuNDYxOTk3IDkuODUxMTc4OSA2LjU1MTY0MDkgOS45MDYyNSA2LjY1NjI1IEwgOS45Mzc1IDYuNjI1IEMgOS45NTY3MzIgNi42NjI1MjcgOS45NDk5MTEgNi43MTIyNDEgOS45Njg3NSA2Ljc1IEMgMTAuNTU3NzgzIDcuOTMwNjIxOSAxMC45NTc1MSA5LjIzMDY5NjQgMTEuMTI1IDEwLjU5Mzc1IEMgMTEuMTgwODMgMTEuMDQ4MTAxIDExLjIxODc1IDExLjQ5OTAxOCAxMS4yMTg3NSAxMS45Njg3NSBMIDExLjIxODc1IDEyIEwgMTEuMjE4NzUgMTIuMDMxMjUgQyAxMS4yMTg3NSAxMy45NTUyNSAxMC43NTk1IDE1Ljc3MiA5LjkzNzUgMTcuMzc1IEwgOS45MDYyNSAxNy4zNDM3NSBDIDkuNjg1OTY1NyAxNy43NjIyMjkgOS4yODcxMzE4IDE4LjA2MjUgOC43ODEyNSAxOC4wNjI1IEMgOC4wNTUyNSAxOC4wNjI1IDcuNDY4NzUgMTcuNDc4IDcuNDY4NzUgMTYuNzUgQyA3LjQ2ODc1IDE2LjU0NDI1NiA3LjUwOTMxNSAxNi4zNjA5NTEgNy41OTM3NSAxNi4xODc1IEwgNy41NjI1IDE2LjE1NjI1IEMgOC4xOTY1IDE0LjkxOTI1IDguNTYyNSAxMy41MTYyNSA4LjU2MjUgMTIuMDMxMjUgTCA4LjU2MjUgMTIgTCA4LjUzMTI1IDEyIEwgOC41MzEyNSAxMS45Njg3NSBDIDguNTMxMjUgMTAuNDgzNzUgOC4xOTY1IDkuMDc5NzUgNy41NjI1IDcuODQzNzUgTCA3LjU5Mzc1IDcuODQzNzUgQyA3LjU0NTU1NyA3Ljc1MjMwMTggNy40OTU3NjQyIDcuNjYwNDM1NyA3LjQ2ODc1IDcuNTYyNSBDIDcuNDQxNzM1NyA3LjQ2NDU2NDMgNy40Mzc1IDcuMzYwNTU5MSA3LjQzNzUgNy4yNSBDIDcuNDM3NSA2LjUyMyA4LjAyNCA1LjkzNzUgOC43NSA1LjkzNzUgeiAiICAgICBpZD0icGF0aDExIiAvPjwvc3ZnPg==", '25.90239843209');
        add_submenu_page('mobiloud', 'Settings', 'Settings', "activate_plugins", 'mobiloud_settings', array('Mobiloud_Admin', 'menu_settings'));
        add_submenu_page('mobiloud', 'Push Notification', 'Push Notifications', "activate_plugins", 'mobiloud_push', array('Mobiloud_Admin', 'menu_push'));
    }

    private static function set_default_options() {        
        if (is_null(get_option('ml_eager_loading_enable', null))) {
            add_option('ml_eager_loading_enable', true);
        }
        if (is_null(get_option('ml_popup_message_on_mobile_active', null))) {
            add_option("ml_popup_message_on_mobile_active", false);
        }
        if (is_null(get_option('ml_automatic_image_resize', null))) {
            add_option("ml_automatic_image_resize", false);
        }
    }

    private static function admin_redirect() {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            // if(!self::initial_details_saved()) {
            //     if(!isset($_GET['page']) || (isset($_GET['page']) && $_GET['page'] !== 'mobiloud')) {
            //         wp_redirect('admin.php?page=mobiloud');
            //     }
            // }
            if (get_option('mobiloud_do_activation_redirect', false)) {
                delete_option('mobiloud_do_activation_redirect');
                if (!isset($_GET['activate-multi'])) {
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

        wp_register_script('mobiloud-forms', MOBILOUD_PLUGIN_URL . 'assets/js/mobiloud-forms.js');
        wp_enqueue_script('mobiloud-forms');

        wp_register_script('mobiloud-push', MOBILOUD_PLUGIN_URL . 'assets/js/mobiloud-push.js');
        wp_enqueue_script('mobiloud-push');

        wp_register_script('mobiloud-editor', MOBILOUD_PLUGIN_URL . 'assets/js/mobiloud-editor.js');
        wp_enqueue_script('mobiloud-editor');

        wp_register_script('mobiloud-menu-config', MOBILOUD_PLUGIN_URL . 'assets/js/mobiloud-menu-config.js');
        wp_enqueue_script('mobiloud-menu-config');

        wp_register_script('mobiloud-app-simulator', MOBILOUD_PLUGIN_URL . 'assets/js/mobiloud-app-simulator.js');
        wp_enqueue_script('mobiloud-app-simulator');

        wp_enqueue_script('mobiloud', MOBILOUD_PLUGIN_URL . 'mobiloud.js', array('jquery', 'jquery-ui-core'), MOBILOUD_PLUGIN_VERSION);

        wp_register_style('mobiloud-iphone', MOBILOUD_PLUGIN_URL . "/css/iphone.css");
        wp_enqueue_style("mobiloud.css");
        wp_enqueue_style("mobiloud-iphone");

        wp_register_script('jquerychosen', MOBILOUD_PLUGIN_URL . '/libs/chosen/chosen.jquery.min.js', array('jquery'));
        wp_enqueue_script('jquerychosen');

        wp_register_script('iscroll', MOBILOUD_PLUGIN_URL . '/libs/iscroll/iscroll.js', array('jquery'));
        wp_enqueue_script('iscroll');

        wp_register_script('resizecrop', MOBILOUD_PLUGIN_URL . '/libs/jquery.resizecrop-1.0.3.min.js', array('jquery'));
        wp_enqueue_script('resizecrop');

        wp_register_script('imgliquid', MOBILOUD_PLUGIN_URL . '/libs/imgliquid/jquery.imgliquid.js', array('jquery'));
        wp_enqueue_script('imgliquid');

        wp_register_script('areyousure', MOBILOUD_PLUGIN_URL . 'libs/jquery.are-you-sure.js', array('jquery'));
        wp_enqueue_script('areyousure');

        wp_register_style('jquerychosen-css', MOBILOUD_PLUGIN_URL . "/libs/chosen/chosen.css");
        wp_enqueue_style("jquerychosen-css");

        wp_register_style('mobiloud-dashicons', MOBILOUD_PLUGIN_URL . "/libs/dashicons/css/dashicons.css");
        wp_enqueue_style("mobiloud-dashicons");

        wp_register_style('mobiloud-style', MOBILOUD_PLUGIN_URL . "/assets/css/mobiloud-style-32.css");
        wp_enqueue_style("mobiloud-style");

        wp_register_style('mobiloud_admin_post', MOBILOUD_PLUGIN_URL . '/admin/post/post.css');
        wp_enqueue_style("mobiloud_admin_post");
    }

    public static function render_view($view, $parent = null, $data = array()) {
        if ($parent === null) {
            $parent = $view;
        }
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                $$key = $val;
            }
        }
        include MOBILOUD_PLUGIN_DIR . 'views/header.php';

        if (file_exists(MOBILOUD_PLUGIN_DIR . 'views/header_' . $parent . '.php'))
            include MOBILOUD_PLUGIN_DIR . 'views/header_' . $parent . '.php';

        include MOBILOUD_PLUGIN_DIR . 'views/' . $view . '.php';

        include MOBILOUD_PLUGIN_DIR . 'views/footer.php';
    }

    public static function render_part_view($view, $data = array(), $static = false) {
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                $$key = $val;
            }
        }
        if ($static) {
            include MOBILOUD_PLUGIN_DIR . 'views/static/' . $view . '.php';
        } else {
            include MOBILOUD_PLUGIN_DIR . 'views/' . $view . '.php';
        }
    }

    public static function render_remote_view($view) {
        $resp = wp_remote_get(MOBILOUD_CONTENT_URL . '/' . $view . '.php', array(
            'timeout' => 20
        ));
        if (!is_wp_error($resp) && isset($resp['body'])) {
            echo $resp['body'];
        } else {
            self::render_part_view($view, array(), true);
        }
    }

    public static function check_mailing_list_alert() {
        //check if maillist not alerted and initial details saved
        if (Mobiloud::get_option('ml_maillist_alert', '') === '' && Mobiloud::get_option('ml_initial_details_saved', '') === true) {
            self::track_user_event('mailinglist_signup');
            Mobiloud::set_option('ml_maillist_alert', true);
        }
    }

    public static function menu_get_started() {
        
        $tab = sanitize_text_field($_GET['tab']);
        switch ($tab) {
            default:
            case 'design':
                wp_enqueue_script('wp-color-picker');
                wp_enqueue_media();
                wp_enqueue_style('wp-color-picker');

                wp_register_script('mobiloud-app-preview-js', MOBILOUD_PLUGIN_URL . '/assets/js/mobiloud-app-preview.js', array('jquery'));
                wp_enqueue_script('mobiloud-app-preview-js');

                wp_register_style('mobiloud-app-preview', MOBILOUD_PLUGIN_URL . "/assets/css/mobiloud-app-preview.css");
                wp_enqueue_style("mobiloud-app-preview");

                global $current_user;
                get_currentuserinfo();

                /**
                 * Process Form
                 */
                if (count($_POST) && check_admin_referer('form-get_started_design')) {
                    Mobiloud::set_option('ml_preview_upload_image', sanitize_text_field($_POST['ml_preview_upload_image']));
                    Mobiloud::set_option('ml_preview_theme_color', sanitize_text_field($_POST['ml_preview_theme_color']));
                    switch ($_POST['homepagetype']) {
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

                    Mobiloud::set_option('ml_datetype', sanitize_text_field($_POST['ml_datetype']));
                    Mobiloud::set_option('ml_dateformat', sanitize_text_field($_POST['ml_dateformat']));

                    Mobiloud::set_option('ml_home_page_id', sanitize_text_field($_POST['ml_home_page_id']));
                    Mobiloud::set_option('ml_home_url', sanitize_text_field($_POST['ml_home_url']));

                    Mobiloud::set_option('ml_show_article_list_menu_item', isset($_POST['ml_show_article_list_menu_item']));
                    Mobiloud::set_option('ml_article_list_menu_item_title', sanitize_text_field($_POST['ml_article_list_menu_item_title']));
                    self::set_task_status('design', 'complete');
                }

                if (strlen(trim(get_option('ml_preview_theme_color'))) <= 2) {
                    update_option("ml_preview_theme_color", '#1e73be');
                }

                $root_url = network_site_url('/');
                $plugins_url = plugins_url();
                $mobiloudPluginUrl = MOBILOUD_PLUGIN_URL;
                $mobiloudPluginVersion = MOBILOUD_PLUGIN_VERSION;
                $appname = get_bloginfo('name');

                if (!self::initial_details_saved()) {
                    self::render_part_view('initial_details', compact('current_user'));
                }
                self::render_view('get_started_design', 'get_started');
                self::track_user_event('view_get_started_design');
                break;
            case 'menu_config':


                /**
                 * Process Form
                 */
                if (count($_POST) && check_admin_referer('form-get_started_menu_config')) {
                    ml_remove_all_categories();
                    if (count($_POST['ml-menu-categories'])) {
                        foreach ($_POST['ml-menu-categories'] as $cat_ID) {
                            ml_add_category(sanitize_text_field($cat_ID));
                        }
                    }
                    
                    $menu_terms = array();
                    if (count($_POST['ml-menu-terms'])) {
                        foreach ($_POST['ml-menu-terms'] as $term) {
                            $menu_terms[] = $term;
                        }
                    }
                    Mobiloud::set_option('ml_menu_terms', $menu_terms);

                    $menu_tags = array();
                    if (count($_POST['ml-menu-tags'])) {
                        foreach ($_POST['ml-menu-tags'] as $tag) {
                            $menu_tags[] = $tag;
                        }
                    }
                    Mobiloud::set_option('ml_menu_tags', $menu_tags);
                    
                    ml_remove_all_pages();
                    if (count($_POST['ml-menu-pages'])) {
                        foreach ($_POST['ml-menu-pages'] as $page_ID) {
                            ml_add_page(sanitize_text_field($page_ID));
                        }
                    }

                    $menu_links = array();
                    if (count($_POST['ml-menu-links'])) {
                        foreach ($_POST['ml-menu-links'] as $menu_link) {
                            $menu_link_vals = explode(":=:", $menu_link);
                            $menu_links[] = array(
                                'urlTitle' => sanitize_text_field($menu_link_vals[0]),
                                'url' => sanitize_text_field($menu_link_vals[1]),
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
                $check_url = 'http://www.mobiloud.com/simulator/check.php?url=' . urlencode(MOBILOUD_PLUGIN_URL);
                $loadDemo = false;
                $check_content = @file_get_contents($check_url);
                $error_reason = '';
                if (self::isJson($check_content)) {
                    $check_result = json_decode($check_content, true);
                    if (isset($check_result['error'])) {
                        $loadDemo = true;
                        $error_reason = $check_result['error'];
                    }
                } else {
                    $loadDemo = true;
                    $error_reason = 'we are unable to reach your site';
                }
                $params_array = array('plugin_url' => urldecode($plugin_url));
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
        if(Mobiloud::get_option('ml_activation_tracked_pa') == 'activated') {
            ml_track('Plugin installed', array('perfect_audience'));
            Mobiloud::set_option('ml_activation_tracked_pa', true);
        }
        if (is_null(get_option('ml_license_tracked', null)) && strlen(Mobiloud::get_option('ml_pb_app_id')) >= 0 
                && strlen(Mobiloud::get_option('ml_pb_secret_key')) >= 0) {
            ml_track('License details saved', array('perfect_audience'));
            update_option('ml_license_tracked', true);
        }
    }

    public static function menu_settings() {
        $tab = sanitize_text_field($_GET['tab']);
        switch ($tab) {
            default:
            case 'general':
                wp_register_script('mobiloud-general', MOBILOUD_PLUGIN_URL . '/assets/js/mobiloud-general.js', array('jquery'));
                wp_enqueue_script('mobiloud-general');
                /**
                 * Process Form
                 */
                if (count($_POST) && check_admin_referer('form-settings_general')) {
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
                    if (isset($_POST['postypes']) && count($_POST['postypes'])) {
                        $include_post_types = implode(",", $_POST['postypes']);
                    }
                    Mobiloud::set_option('ml_article_list_include_post_types', sanitize_text_field($include_post_types));

                    $categories = get_categories();
                    $exclude_categories = array();
                    if (count($categories)) {
                        foreach ($categories as $category) {
                            if (!isset($_POST['categories']) || count($_POST['categories']) === 0 || (isset($_POST['categories']) && !in_array(wp_slash(html_entity_decode($category->cat_name)), $_POST['categories']))) {
                                $exclude_categories[] = $category->cat_name;
                            }
                        }
                    }

                    Mobiloud::set_option('ml_article_list_exclude_categories', implode(",", $exclude_categories));
                    
                    Mobiloud::set_option('ml_custom_field_enable', isset($_POST['ml_custom_field_enable']));
                    Mobiloud::set_option('ml_custom_field_name', sanitize_text_field($_POST['ml_custom_field_name']));
                }
                self::render_view('settings_general', 'settings');
                self::track_user_event('view_settings_general');
                break;
            case 'posts':
                wp_enqueue_media();
                wp_register_script('mobiloud-posts', MOBILOUD_PLUGIN_URL . '/assets/js/mobiloud-posts.js', array('jquery'));
                wp_enqueue_script('mobiloud-posts');
                /**
                 * Process Form
                 */
                if (count($_POST) && check_admin_referer('form-settings_posts')) {
                    Mobiloud::set_option('ml_eager_loading_enable', isset($_POST['ml_eager_loading_enable']));
                    Mobiloud::set_option('ml_hierarchical_pages_enabled', isset($_POST['ml_hierarchical_pages_enabled']));
                    Mobiloud::set_option('ml_rtl_text_enable', isset($_POST['ml_rtl_text_enable']));
                    Mobiloud::set_option('ml_show_article_featuredimage', isset($_POST['ml_show_article_featuredimage']));
                    Mobiloud::set_option('ml_post_author_enabled', isset($_POST['ml_post_author_enabled']));
                    Mobiloud::set_option('ml_page_author_enabled', isset($_POST['ml_page_author_enabled']));
                    Mobiloud::set_option('ml_post_date_enabled', isset($_POST['ml_post_date_enabled']));
                    Mobiloud::set_option('ml_page_date_enabled', isset($_POST['ml_page_date_enabled']));
                    Mobiloud::set_option('ml_post_title_enabled', isset($_POST['ml_post_title_enabled']));
                    Mobiloud::set_option('ml_page_title_enabled', isset($_POST['ml_page_title_enabled']));

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
                if (count($_POST) && check_admin_referer('form-settings_analytics')) {
                    Mobiloud::set_option('ml_google_tracking_id', sanitize_text_field($_POST['ml_google_tracking_id']));
                }
                self::render_view('settings_analytics', 'settings');
                self::track_user_event('view_settings_analytics');
                break;
            case 'advertising':
                /**
                 * Process Form
                 */
                if (count($_POST) && check_admin_referer('form-settings_advertising')) {
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
                if (count($_POST) && check_admin_referer('form-settings_license')) {
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
                if (count($_POST) && check_admin_referer('form-settings_membership')) {
                    Mobiloud::set_option('ml_subscriptions_enable', isset($_POST['ml_subscriptions_enable']));
                }
                self::render_view('settings_membership', 'settings');
                self::track_user_event('view_settings_membership');
                break;
        }
    }

    public static function menu_push() {
        $tab = sanitize_text_field($_GET['tab']);
        switch ($tab) {
            default:
            case 'notifications':
                self::render_view('push_notifications', 'push');
                self::track_user_event('view_push_notifications');
                break;
            case 'settings':
                /**
                 * Process Form
                 */
                if (count($_POST) && check_admin_referer('form-push_settings')) {
                    Mobiloud::set_option('ml_push_notification_enabled', isset($_POST['ml_push_notification_enabled']));
                    Mobiloud::set_option('ml_pb_use_ssl', isset($_POST['ml_pb_use_ssl']));

                    $include_post_types = '';
                    if (isset($_POST['postypes']) && count($_POST['postypes'])) {
                        $include_post_types = implode(",", $_POST['postypes']);
                    }
                    Mobiloud::set_option('ml_push_post_types', sanitize_text_field($include_post_types));

                    if (isset($_POST['ml_push_notification_categories'])) {
                        ml_push_notification_categories_clear();
                        if (is_array($_POST['ml_push_notification_categories'])) {
                            foreach ($_POST['ml_push_notification_categories'] as $categoryID) {
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
        if ($task == $tab || (!isset($_GET['tab']) && $task == 'design')) {
            $class = 'current';
        }

        $class .= ' ' . self::get_task_status($task);
        return $class;
    }

    public static function set_task_status($task, $status) {
        $task_statuses = Mobiloud::get_option('ml_get_start_tasks', false);
        if ($task_statuses === false) {
            $task_statuses = array(
                $task => $status
            );
        } else {
            $task_statuses[$task] = $status;
        }
        Mobiloud::set_option('ml_get_start_tasks', $task_statuses);
    }

    public static function get_task_status($task) {
        $task_statuses = Mobiloud::get_option('ml_get_start_tasks', false);
        if ($task_statuses !== false && isset($task_statuses[$task])) {
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
    }

    public static function save_editor() {
        if (isset(self::$editor_sections[$_POST['editor']])) {
            Mobiloud::set_option($_POST['editor'], $_POST['value']);
        }
    }

    public static function save_banner() {
        if (isset(self::$banner_positions[$_POST['position']])) {
            Mobiloud::set_option($_POST['position'], $_POST['value']);
        }
    }

    public static function track_user_event($event) {
        if (Mobiloud::get_option('ml_initial_details_saved')) {
            ml_track_mixpanel($event);
            ?>
            <script type='text/javascript'>
                _veroq.push(['track', "<?php echo esc_js($event); ?>"]);
                Intercom("trackUserEvent", "<?php echo esc_js($event); ?>");
            </script>

            <?php
        }
    }
    
    public static function get_tax_list() {
        $list = array();
        if(isset($_POST['group'])) {
            $group = sanitize_text_field($_POST['group']);
            $terms = get_terms($group, array('hide_empty'=>false));
            if(count($terms)) {
                
                foreach($terms as $term) {
                   $parent_name = '';
                   if($term->parent) {
                       $parent_term = get_term_by('id', $term->parent, $group);
                       if($parent_term) {
                           $parent_name = $parent_term->name . ' - ';
                       }
                   }
                   $list[$term->term_id] = array('id'=>$term->term_id, 'fullname'=>$parent_name.$term->name, 'title'=>$term->name);
                }
            }
        }
        header('Content-Type: application/json');
        wp_send_json(array('terms'=>$list));
    }

}
