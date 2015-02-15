<div id="ml_settings_editor" class="tabs-panel ml-compact">
    <form method="post" action="<?php echo admin_url('admin.php?page=mobiloud_settings&tab=editor'); ?>">
        <?php wp_nonce_field('form-settings_editor'); ?>
        <h3>Code Editor</h3>
        <div class='ml-col-twothirds'>
            <p>You can use the editor to inject HTML, PHP, CSS and Javascript code in a number of positions within the post and page 
                screens. You can reference the current post id using $post->id.</p>  
            <p><em>Note: this is for developers and advanced users only.</em></p>

            <div class="ml-editor-controls">
                <select id="ml_admin_post_customization_select" name="ml_admin_post_customization_select">
                    <option value="">
                        Select a customization...
                    </option>
                    <?php foreach(Mobiloud_Admin::$editor_sections as $editor_key=>$editor_name): ?>
                    <option value='<?php echo esc_attr($editor_key); ?>'?>
                        <?php echo esc_html($editor_name); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <a href="#" class='button-primary ml-save-editor-btn'>Save</a>
            </div>
            <textarea class='ml-editor-area ml-show'></textarea>
            <?php foreach(Mobiloud_Admin::$editor_sections as $editor_key=>$editor_name): ?>
            <textarea class='ml-editor-area' name='<?php echo esc_attr($editor_key); ?>'><?php echo stripslashes(htmlspecialchars(Mobiloud::get_option($editor_key, ''))); ?></textarea>
            <?php endforeach; ?>
            
            <h4>Preview the results</h4>
            <p>Select a post or page to preview the results of your edits.</p>
            <select id="preview_popup_post_select">
                <?php 
                $posts_query = array(
                    'posts_per_page' => 10,'orderby' => 'post_date','order' => 'DESC','post_type'
                );
                $included_post_types = explode(",", Mobiloud::get_option('ml_article_list_include_post_types', array()));
                foreach($included_post_types as $post_type) {
                    $posts_query['post_type'] = $post_type;
                    $posts = get_posts($posts_query); 
                    if(count($posts) > 0) {
                        ?>                    
                        <optgroup label="<?php echo ucfirst($post_type); ?>">
                        <?php foreach($posts as $post) { ?>

                        <option value="<?php echo MOBILOUD_PLUGIN_URL; ?>post/post.php?post_id=<?php echo $post->ID; ?>">
                        <?php if(strlen($post->post_title) > 40) { ?>

                        <?php echo substr($post->post_title,0,40); ?>

                        ..
                        <?php } else { ?>

                        <?php echo $post->post_title; ?>

                        <?php } ?>
                        </option><?php } ?>
                        </optgroup>
                        <?php
                    }
                }
                
                
                ?>
                <?php $pages = get_pages(array('sort_order' => 'ASC', 'sort_column' => 'post_title', 'post_type' => 'page','post_status' => 'publish')); ?>
                <optgroup label="Pages">
                <?php foreach($pages as $page) { ?>

                <option value="<?php echo MOBILOUD_PLUGIN_URL; ?>post/post.php?post_id=<?php echo $page->ID; ?>">
                <?php if(strlen($page->post_title) > 40) { ?>

                <?php echo substr($page->post_title,0,40); ?>

                ..
                <?php } else { ?>

                <?php echo $page->post_title; ?>

                <?php } ?>
                </option><?php } ?>
                </optgroup>
            </select>
            <a href='#' class='ml_open_preview_btn button-secondary ml-preview-phone-btn'>Preview on phone</a>
            <a href='#' class='ml_open_preview_btn button-secondary ml-preview-tablet-btn'>Preview on tablet</a>
        </div>
        <h3>Need help from a pro?</h3>
        <div class='ml-col-twothirds'>
            <p>The Mobiloud developer team can help you integrate custom fields, add video/audio embeds and 
                much more to your app, for more information, contact <a href='mailto:support@mobiloud.com'>support@mobiloud.com</a>.</p>
        </div>
    </form>
</div>
<div id="preview_popup_content">
<div class="iphone5s_device">
<iframe id="preview_popup_iframe">
</iframe></div><div class="ipadmini_device">
<iframe id="preview_popup_iframe">
</iframe></div></div>