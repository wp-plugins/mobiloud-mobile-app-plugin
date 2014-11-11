<div id="get_started_design" class="tabs-panel">
    <div class="get_started_options">
        <form method="post" action="<?php echo admin_url('admin.php?page=mobiloud'); ?>">
            <?php wp_nonce_field( 'form-get_started_design' ); ?>            
            <div class="ml-form-row">
                <label>Upload Your Logo</label>
                <input id="ml_preview_upload_image" type="text" size="36" name="ml_preview_upload_image" value="<?php echo get_option("ml_preview_upload_image"); ?>" />
                <input id="ml_preview_upload_image_button" type="button" value="Upload Image" />
            </div>
            <?php $logoPath = Mobiloud::get_option("ml_preview_upload_image"); ?>
            <div class="ml-form-row ml-preview-upload-image-row" <?php echo (strlen($logoPath) === 0) ? 'style="display:none;"' : ''; ?>>               
                <div class='ml-preview-image-holder'>
                    <img src='<?php echo $logoPath; ?>'/>
                </div>         
                <a href='#' class='ml-preview-image-remove-btn'>Remove logo</a>
            </div>
            <div class="ml-form-row">
                <label>Navigation Bar Color</label>
                <input name="ml_preview_theme_color" id="ml_preview_theme_color" type="text" value="<?php echo get_option("ml_preview_theme_color"); ?>" />
            </div>
            <div class="ml-form-row">
                <label>Home Screen Settings</label>
                <p>Choose what to show on your app's home screen.</p>
                <div class="ml-radio-wrap">
                    <input type="radio" id="ml_home_article_list_enabled" name="homepagetype" value="ml_home_article_list_enabled" <?php echo get_option('ml_home_article_list_enabled', true) ? 'checked' : ''; ?>/>
                    <label for="ml_home_article_list_enabled">Article List</label>
                </div>
                <div class="ml-radio-wrap">
                    <input type="radio" id="ml_home_page_enabled" name="homepagetype" value="ml_home_page_enabled" <?php echo get_option('ml_home_page_enabled') ? 'checked' : ''; ?>/>
                    <label for="ml_home_page_enabled">Page</label>
                    <select name="ml_home_page_id">
                        <option value="">Select a page</option>
                        <?php $pages = get_pages();?>
                        <?php 
                            foreach($pages as $p) {
                                $selected = '';
                                if(Mobiloud::get_option("ml_home_page_id") == $p->ID) {
                                    $selected = 'selected="selected"';
                                }
                                ?>
                                <option value="<?php echo $p->ID; ?>" <?php echo $selected; ?>>
                                    <?php echo $p->post_title; ?>
                                </option>
                                <?php
                            }
                        ?>
                    </select>                
                </div>
                <div class="ml-radio-wrap">
                    <input type="radio" id="ml_home_url_enabled" name="homepagetype" value="ml_home_url_enabled" <?php echo get_option('ml_home_url_enabled') ? 'checked' : ''; ?>/>
                    <label for="ml_home_url_enabled">URL</label>
                    <input id="ml_home_url" placeholder="Type the URL here" name="ml_home_url" type="text" value="<?php echo get_option('ml_home_url_enabled') ? get_option('ml_home_url'): ''; ?>">

                </div>
            </div>
            <div class="ml-form-row ml-home-screen-label">
                <label>Articles Menu Item</label>
                <p>Enter the label you'd like to use for the 'Articles' menu item, letting users list your articles.</p>
                <div class="ml-form-row ml-checkbox-wrap">
                    <input type="checkbox" id="ml_show_article_list_menu_item" name="ml_show_article_list_menu_item" value="true" <?php echo Mobiloud::get_option('ml_show_article_list_menu_item') ? 'checked' : ''; ?>/>
                    <label for="ml_show_article_list_menu_item">Show 'Article' list menu item</label>
                </div>
                <input type='text' id='ml_article_list_menu_item_title' name='ml_article_list_menu_item_title' value='<?php echo Mobiloud::get_option('ml_article_list_menu_item_title', 'Articles'); ?>'/>
            </div>
            <div class="ml-form-row">
                <label>Article List Style</label>
                <div class="ml-radio-wrap">
                    <input type="radio" id="ml_article_list_view_type_extended" name="ml_article_list_view_type" value="extended" <?php echo get_option('ml_article_list_view_type', 'extended') == 'extended' ? 'checked' : ''; ?>/>
                    <label for="ml_article_list_view_type_extended">Extended (large thumbnails)</label>
                </div>
                <div class="ml-radio-wrap">
                    <input type="radio" id="ml_article_list_view_type_compact" name="ml_article_list_view_type" value="compact" <?php echo get_option('ml_article_list_view_type') == 'compact' ? 'checked' : ''; ?>/>
                    <label for="ml_article_list_view_type_compact">Compact (square thumbnails)</label>
                </div>                
            </div>
            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
        </form>
	    <?php if( strlen(Mobiloud::get_option('ml_pb_app_id')) > 0 && Mobiloud::get_option('ml_pb_app_id') < "543e7b3f1d0ab16d148b4599"): ?>			
        <div class='update-nag'>
            <p>Some of the functionality above applies only to new apps. Your app might require to be updated for these settings to take effect.</p>
			<p>Should you have any questions or to request an update, get in touch at <a href='mailto:support@mobiloud.com'>support@mobiloud.com</a>.</p>
        </div>
        <?php endif; ?>
    </div>
    <div class="get_started_preview">
        <div class="ml-form-row">
            <label>Quick preview of the app design</label>
            <p>This is only a mockup. <a href="./admin.php?page=mobiloud&tab=test_app">Click here</a> to test your app on your own device.</p>
            <div class="os-selection">
                <div class="radio-wrap">
                    <input type="radio" id='ml_preview_os_ios' name="ml_preview_os" value='ios' checked/>
                    <label for='ml_preview_os_ios'>iOS</label>
                </div>
                <div class="radio-wrap">
                    <input type="radio" id='ml_preview_os_android' name="ml_preview_os" value='android'/>
                    <label for='ml_preview_os_android'>Android</label>
                </div>
                <div style='clear:both;'></div>
            </div>
        </div>
        <div class="ml-preview-app"></div>
        <div id="ml_preview_loading"><img src="<?php echo MOBILOUD_PLUGIN_URL; ?>/assets/img/spinner.gif" alt="Loading..."/><br/>Loading Preview...</div>
    </div>    
    <div style='clear:both;'></div>
</div>
<img src="http://ads.perfectaudience.com/seg?add=2099049&t=2" width="1" height="1" border="0" />
