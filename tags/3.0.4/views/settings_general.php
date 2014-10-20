<div id="ml_settings_general" class="tabs-panel ml-compact">
    <form method="post" action="<?php echo admin_url('admin.php?page=mobiloud_settings'); ?>">
        <?php wp_nonce_field('form-settings_general'); ?>
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
                    <input type="checkbox" id="ml_article_list_enable_featured_images" name="ml_article_list_enable_featured_images" value="true" <?php echo Mobiloud::get_option('ml_article_list_enable_featured_images') ? 'checked' : ''; ?>/>
                    <label for="ml_article_list_enable_featured_images">Show featured images in the list</label>
                </div>
                <div class="ml-form-row ml-checkbox-wrap no-margin">
                    <input type="checkbox" id="ml_automatic_image_resize_active" name="ml_automatic_image_resize_active" value="true" <?php echo Mobiloud::get_option('ml_automatic_image_resize_active') ? 'checked' : ''; ?>/>
                    <label for="ml_automatic_image_resize_active">Automatically resize image thumbnails</label>
                </div>
                <div class="ml-form-row ml-checkbox-wrap no-margin">
                    <input type="checkbox" id="ml_article_list_show_excerpt" name="ml_article_list_show_excerpt" value="true" <?php echo Mobiloud::get_option('ml_article_list_show_excerpt') ? 'checked' : ''; ?>/>
                    <label for="ml_article_list_show_excerpt">Show excerpt in article list</label>
                </div>
                <div class="ml-form-row ml-checkbox-wrap no-margin">
                    <input type="checkbox" id="ml_article_list_show_comment_count" name="ml_article_list_show_comment_count" value="true" <?php echo Mobiloud::get_option('ml_article_list_show_comment_count') ? 'checked' : ''; ?>/>
                    <label for="ml_article_list_show_comment_count">Show comments count in article list</label>
                </div>
            </div>
        </div>
        <h4>Custom Post Field</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>Your app post list has the ability to show data from a Custom Field defined in your post. Enter the name of the
                    Custom Field to so the value is shown in the list. (If not required then leave blank)</p>            
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
        <h4>Custom Post Types</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>Select which post types should be included in the articles list.</p>
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
                <p>Select which categories should be included in the articles list.</p>
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
        <h3>Post and Page screen settings</h3>
        <h4>Content loading settings</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>You can increase loading speed by disabling the pre-loading of posts when your app starts. Content will load on request.</p> 
                <p><em>Note: if you'd like recently loaded posts to be accessible offline when no connection is available, then make sure to 
                        pre-load post contents.</em></p>
            </div>
            <div class='ml-col-half'>
                <div class="ml-form-row ml-checkbox-wrap">
                    <input type="checkbox" id="ml_eager_loading_enable" name="ml_eager_loading_enable" value="true" <?php echo Mobiloud::get_option('ml_eager_loading_enable') ? 'checked' : ''; ?>/>
                    <label for="ml_eager_loading_enable">Preload post contents when the app starts</label>
                </div>
            </div>
        </div>
        <h4>Children page navigation</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>The page hierarchy navigation feature allows users to see a list of children pages at the bottom of every page within your app. This is useful for websites with a complex hierarchy of pages.</p>
            </div>
            <div class='ml-col-half'>
                <div class="ml-form-row ml-checkbox-wrap">
                    <input type="checkbox" id="ml_hierarchical_pages_enabled" name="ml_hierarchical_pages_enabled" value="true" <?php echo Mobiloud::get_option('ml_hierarchical_pages_enabled') ? 'checked' : ''; ?>/>
                    <label for="ml_hierarchical_pages_enabled">Enable page hierarchy navigation</label>
                </div>
            </div>
        </div>
        <h4>Post and page meta information</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>Change which meta elements of your posts and pages should be displayed in the post and page screens.</p>
            </div>
            <div class='ml-col-half'>
                <div class="ml-form-row ml-checkbox-wrap">
                    <input type="checkbox" id="ml_post_author_enabled" name="ml_post_author_enabled" value="true" <?php echo Mobiloud::get_option('ml_post_author_enabled') ? 'checked' : ''; ?>/>
                    <label for="ml_post_author_enabled">Show author in posts</label>
                </div>
                <div class="ml-form-row ml-checkbox-wrap no-margin">
                    <input type="checkbox" id="ml_page_author_enabled" name="ml_page_author_enabled" value="true" <?php echo Mobiloud::get_option('ml_page_author_enabled') ? 'checked' : ''; ?>/>
                    <label for="ml_page_author_enabled">Show author in pages</label>
                </div>
                <div class="ml-form-row ml-checkbox-wrap no-margin">
                    <input type="checkbox" id="ml_post_date_enabled" name="ml_post_date_enabled" value="true" <?php echo Mobiloud::get_option('ml_post_date_enabled') ? 'checked' : ''; ?>/>
                    <label for="ml_post_date_enabled">Show date in posts</label>
                </div>
                <div class="ml-form-row ml-checkbox-wrap no-margin">
                    <input type="checkbox" id="ml_page_date_enabled" name="ml_page_date_enabled" value="true" <?php echo Mobiloud::get_option('ml_page_date_enabled') ? 'checked' : ''; ?>/>
                    <label for="ml_page_date_enabled">Show date in pages</label>
                </div>
                <div class="ml-form-row ml-checkbox-wrap no-margin">
                    <input type="checkbox" id="ml_post_title_enabled" name="ml_post_title_enabled" value="true" <?php echo Mobiloud::get_option('ml_post_title_enabled') ? 'checked' : ''; ?>/>
                    <label for="ml_post_title_enabled">Show title in posts</label>
                </div>
                <div class="ml-form-row ml-checkbox-wrap no-margin">
                    <input type="checkbox" id="ml_page_title_enabled" name="ml_page_title_enabled" value="true" <?php echo Mobiloud::get_option('ml_page_title_enabled') ? 'checked' : ''; ?>/>
                    <label for="ml_page_title_enabled">Show title in pages</label>
                </div>
            </div>
        </div>
        <h4>Override Article/Page URL with a custom field</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>When sharing your content, users will normally share the article's URL. For curation-based publications,
                    though, you might want users to share the source for that story.</p>
                <p>Enter a custom field name to the right which you can fill for every post with the URL you want users to share.</p>
            </div>
            <div class='ml-col-half'>
                <div class="ml-form-row ml-left-align clearfix">
                    <label class='ml-width-120' for="ml_custom_field_url">URL Field Name</label>
                    <input type="text" placeholder="Custom Field Name" id="ml_custom_field_url" name="ml_custom_field_url" value="<?php echo esc_attr(Mobiloud::get_option('ml_custom_field_url')); ?>"/>
                </div>
            </div>
        </div>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
    </form>
</div>