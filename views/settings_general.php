<div id="ml_settings_general" class="tabs-panel ml-compact">
    <form method="post" action="<?php echo admin_url('admin.php?page=mobiloud_settings'); ?>">
        <?php wp_nonce_field('form-settings_general'); ?>
	
	<p>The options on this page let you define exactly what content is presented in the app's main article list, including adding custom post types, filtering content by category and adding a custom field to the list.</p>
	<p>Any questions or need some help? <a class="ml-intercom" href="mailto:h89uu5zu@incoming.intercom.io">Send us a message</a></p>
	
	
       <h3>Application details</h3>

        <!-- <h4>App Name</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>Enter the name of your App.</p>
            </div>
            <div class='ml-col-half'>
                <div class="ml-form-row">
                    <input id="ml_app_name" type="text" size="36" name="ml_app_name" value="<?php echo esc_attr(Mobiloud::get_option("ml_app_name", $appname)); ?>" />
                </div>
            </div>
        </div> -->
        <h4>Email Contact</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>Setup email contact details.</p>
            </div>
            <div class='ml-col-half'>
                <div class="ml-form-row ml-checkbox-wrap">
                    <input type="checkbox" id="ml_show_email_contact_link" name="ml_show_email_contact_link" value="true" <?php echo Mobiloud::get_option('ml_show_email_contact_link') ? 'checked' : ''; ?>/>
                    <label for="ml_show_email_contact_link">Show email contact link?</label>
                </div>
                <div class="ml-email-contact-row ml-form-row" <?php
                    echo !Mobiloud::get_option('ml_show_email_contact_link') ? 'style="display:none;"' : ''; ?>>
                    <label for="ml_contact_link_email">Enter public email address</label>
                    <input id="ml_contact_link_email" type="text" size="36" name="ml_contact_link_email" value="<?php echo esc_attr(Mobiloud::get_option("ml_contact_link_email", '')); ?>" />
                </div>
            </div>
        </div>
        <h4>Copyright Notice</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>Enter the copyright notice which will be displayed in your app's settings screen.</p>
            </div>
            <div class='ml-col-half'>
                <div class="ml-form-row">
                    <textarea id="ml_copyright_string" name="ml_copyright_string" rows="4" style="width:100%"><?php echo esc_attr(Mobiloud::get_option("ml_copyright_string", '')); ?></textarea>
                </div>
            </div>
        </div>


	    <?php if( strlen(Mobiloud::get_option('ml_pb_app_id')) > 0 && Mobiloud::get_option('ml_pb_app_id') < "543e7b3f1d0ab16d148b4599"): ?>
        <div class='update-nag'>
            <p>The functionality above is new. Your app might require to be updated for these settings to take effect.</p>
			<p>Should you have any questions or to request an update, get in touch at <a href='mailto:support@mobiloud.com'>support@mobiloud.com</a>.</p>
        </div>
        <?php endif; ?>
		
        <h3>Article List settings</h3>
        <h4>List preferences</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>Adjust how your content will show in article lists, affecting your app's main list as well as category lists.</p>            
            </div>
            <div class='ml-col-half'>
                <div class="ml-form-row ml-checkbox-wrap">
                    <input type="checkbox" id="ml_article_list_enable_dates" name="ml_article_list_enable_dates" value="true" <?php echo Mobiloud::get_option('ml_article_list_enable_dates') ? 'checked' : ''; ?>/>
                    <label for="ml_article_list_enable_dates">Show post dates in the list</label>
                </div>
                <div class="ml-form-row ml-checkbox-wrap no-margin">
                    <input type="checkbox" id="ml_automatic_image_resize_active" name="ml_automatic_image_resize_active" value="true" <?php echo Mobiloud::get_option('ml_automatic_image_resize_active') ? 'checked' : ''; ?>/>
                    <label for="ml_automatic_image_resize_active">Compress image thumbnails</label>
                </div>
                <div class="ml-form-row ml-checkbox-wrap no-margin">
                    <input type="checkbox" id="ml_article_list_show_excerpt" name="ml_article_list_show_excerpt" value="true" <?php echo Mobiloud::get_option('ml_article_list_show_excerpt') ? 'checked' : ''; ?>/>
                    <label for="ml_article_list_show_excerpt">Show excerpts in article list</label>
                </div>
                <div class="ml-form-row ml-checkbox-wrap no-margin">
                    <input type="checkbox" id="ml_article_list_show_comment_count" name="ml_article_list_show_comment_count" value="true" <?php echo Mobiloud::get_option('ml_article_list_show_comment_count') ? 'checked' : ''; ?>/>
                    <label for="ml_article_list_show_comment_count">Show comments count in article list</label>
                </div>
            </div>
        </div>
       
    
        <h4>Custom Post Types</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>Select which post types should be included in the article list.</p>
                <?php
                $posttypes = get_post_types('','names'); 
                $includedPostTypes = explode(",",Mobiloud::get_option("ml_article_list_include_post_types","post"));
                foreach( $posttypes as $v ) {
                    if($v!="attachment" && $v!="revision" && $v!="nav_menu_item"){
                        $checked = '';
                        if(in_array($v,$includedPostTypes)){
                            $checked = "checked";
                        }                         
                        ?>
                        <div class="ml-form-row ml-checkbox-wrap no-margin">
                            <input type="checkbox" id='postypes_<?php echo esc_attr($v); ?>' name="postypes[]" value="<?php echo esc_attr($v); ?>" <?php echo $checked; ?>/>
                            <label for="postypes_<?php echo esc_attr($v); ?>"><?php echo esc_html($v); ?></label>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
		
        <h4>Categories</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>Select which categories should be included in the article list.</p>
                <?php
                $categories = get_categories('orderby=name');  
                $wp_cats = array();  

                $excludedCategories = explode(",",get_option("ml_article_list_exclude_categories",""));

                foreach( $categories as $category_list ) {  
                    $wp_cats[$category_list->cat_ID] = $category_list->cat_name;  
                }
                foreach( $wp_cats as $v ) {
                    $checked = '';
                    if(!in_array($v,$excludedCategories)){
                        $checked = "checked";
                    }                         
                    ?>
                    <div class="ml-form-row ml-checkbox-wrap no-margin">
                        <input type="checkbox" id='categories_<?php echo esc_attr($v); ?>' name="categories[]" value="<?php echo esc_attr($v); ?>" <?php echo $checked; ?>/>
                        <label for="categories_<?php echo esc_attr($v); ?>"><?php echo esc_html($v); ?></label>
                    </div>
                    <?php                    
                }
                ?>
            </div>
        </div>        
		
		
        <h4>Sticky categories</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>The first posts from each sticky category are displayed before all others in the app's article list.</p>            
            </div>
            <div class='ml-col-half'>
                <div class='ml-form-row ml-left-align clearfix'>
                    <label class='ml-width-120'>First category</label>                            
                    <select name="sticky_category_1">
                        <option value="">Select a category</option>
                        <?php 
                        $categories = get_categories();                        
                        foreach ($categories as $c) {
                            $selected = '';
                            if(Mobiloud::get_option('sticky_category_1') === $c->cat_ID) {
                                $selected = 'selected="selected"';
                            }
                            echo "<option value='".esc_attr($c->cat_ID)."' ".$selected.">".esc_html($c->cat_name)."</option>";
                        }
                        ?>
                    </select>
                    <label>No. of Posts</label>   
                    <input type='text' size='2' id='ml_sticky_category_1_posts' name='ml_sticky_category_1_posts' value='<?php echo esc_attr(Mobiloud::get_option('ml_sticky_category_1_posts', 3)); ?>'/>
                    
                </div>
                <div class='ml-form-row ml-left-align clearfix'>
                    <label class='ml-width-120'>Second category</label>                            
                    <select name="sticky_category_2">
                        <option value="">Select a category</option>
                        <?php $categories = get_categories(); ?>
                        <?php
                        foreach ($categories as $c) {
                            $selected = '';
                            if(Mobiloud::get_option('sticky_category_2') === $c->cat_ID) {
                                $selected = 'selected="selected"';
                            }
                            echo "<option value='".esc_attr($c->cat_ID)."' ".$selected.">".esc_html($c->cat_name)."</option>";
                        }
                        ?>
                    </select>
                    <label>No. of Posts</label>   
                    <input type='text' size='2' id='ml_sticky_category_2_posts' name='ml_sticky_category_2_posts' value='<?php echo esc_attr(Mobiloud::get_option('ml_sticky_category_2_posts', 3)); ?>'/>
                    
                </div>
            </div>
        </div>
		
        <h4>Custom field in article list</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>Your app's article list can show data from a Custom Field (e.g. author, price, source) defined in your posts.</p>            
            </div>
            <div class='ml-col-half'>
                <div class="ml-form-row ml-checkbox-wrap">
                    <input type="checkbox" id="ml_custom_field_enable" name="ml_custom_field_enable" value="true" <?php echo Mobiloud::get_option('ml_custom_field_enable') ? 'checked' : ''; ?>/>
                    <label for="ml_custom_field_enable">Show custom field in article list</label>
                </div>
                <div class="ml-form-row ml-left-align clearfix">
                    <label class='ml-width-120' for="ml_custom_field_name">Field Name</label>
                    <input type="text" placeholder="Custom Field Name" id="ml_custom_field_name" name="ml_custom_field_name" value="<?php echo esc_attr(Mobiloud::get_option('ml_custom_field_name')); ?>"/>
                </div>
            </div>
        </div>
		
		
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
    </form>
</div>