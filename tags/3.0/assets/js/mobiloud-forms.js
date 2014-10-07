jQuery(function() {
    jQuery('.get_started_options form').areYouSure({
        'fieldSelector': ":input:not(input[type=submit]):not(input[type=button]):not(#ml_preview_upload_image)"
    });
    
    jQuery("#get_started_menu_config form").areYouSure({
        'fieldSelector': ":input:not(input[type=submit]):not(input[type=button]):not(select):not(input[type=text])"
    });
    
    jQuery("#ml_settings_general form").areYouSure();    
    
    jQuery("#ml_settings_analytics form").areYouSure();    
    
    jQuery("#ml_settings_editor form").areYouSure({
        'fieldSelector': ":input:not(input[type=submit]):not(input[type=button]):not(select)"
    });   
    
    jQuery("#ml_settings_membership form").areYouSure();    
    
    jQuery("#ml_settings_license form").areYouSure();    
    
    jQuery("#ml_push_settings form").areYouSure();  
    
    jQuery("#ml_settings_advertising form").areYouSure({
        'fieldSelector': ":input:not(input[type=submit]):not(input[type=button]):not(#ml_ad_banner_position_select):not(#preview_popup_post_select):not(.ml-editor-area)"
    });  
});

