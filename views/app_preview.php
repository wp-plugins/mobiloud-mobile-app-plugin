<div class='ml-preview <?php echo strlen(get_option('ml_preview_os')) ? get_option('ml_preview_os') : 'ios'; ?>'>
    <div class='ml-preview-body'>
        <div class="ml-preview-top-bar <?php echo $iconShade; ?>" style='background-color: <?php echo get_option('ml_preview_theme_color'); ?>;'></div>
        <div class='ml-preview-menu-bar' style='background-color: <?php echo get_option('ml_preview_theme_color'); ?>;'>
            <a href='javascript:void(0);' class='ml-icon ml-icon-menu <?php echo $iconShade; ?>'></a>
            <a href='javascript:void(0);' class='ml-preview-logo-holder'>
                <?php
                if (strlen(trim(get_option("ml_preview_upload_image"))) > 0) {
                    $logoPath = get_option("ml_preview_upload_image");
                } else {
                    $logoPath = MOBILOUD_PLUGIN_URL . '/assets/img/ml_preview_nologo.png';
                }
                ?>  
                <img class='ml-preview-logo' src='<?php echo $logoPath; ?>'/>
            </a>
            <a href='javascript:void(0);' class='ml-icon ml-icon-search <?php echo $iconShade; ?>'></a>
        </div>
        <div class='ml-preview-article-list <?php echo get_option('ml_article_list_view_type', 'extended') == 'extended' ? 'ml-view-extended' : 'ml-view-compact'; ?>'>
            <div class='scroller'>
                <?php
                $posts = Mobiloud_App_Preview::get_preview_posts();
                if (count($posts) > 0) {

                    foreach ($posts as $post) {
                        $imageUrl = null;
                        if (has_post_thumbnail($post->ID)) {
                            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');
                            $imageUrl = $image[0];
                            if (strlen(Mobiloud::get_option('ml_custom_featured_image')) > 0 && class_exists('MultiPostThumbnails')) {
                                $customImageUrl = MultiPostThumbnails::get_post_thumbnail_url(
                                        get_post_type($post->ID), Mobiloud::get_option('ml_custom_featured_image'), $post->ID
                                );
                                if($customImageUrl !== false) {
                                    $imageUrl = $customImageUrl;
                                }
                            }
                        }
                        ?>
                        <div class='ml-preview-article'>
                            <?php if ($imageUrl): ?>
                                <div class="ml-post-img-wrapper">
                                    <img class='ml-preview-img' src='<?php echo $imageUrl; ?>'/>   
                                </div>
                            <?php endif; ?>
                            <div class='ml-preview-article-body' <?php echo!$imageUrl ? 'style="width:100%;"' : ''; ?>>
                                <h3><?php echo Mobiloud_App_Preview::trim_post_title($post->post_title, get_option('ml_article_list_view_type', 'extended') == 'extended' ? null : 45); ?></h3>
                                <span class='ml-article-date'><?php echo (get_option('ml_datetype', 'prettydate') == 'prettydate' ? Mobiloud_App_Preview::how_long_ago(strtotime($post->post_date)) : date_i18n( get_option('ml_dateformat', 'F j, Y') , strtotime($post->post_date), get_option('gmt_offset'))); ?></span>
                            </div>
                            <div class="clear"></div>
                        </div>
        <?php
    }
} else {
    ?>
                    <div class='ml-preview-article'>
                        <img class='ml-preview-img' src='<?php echo MOBILOUD_PLUGIN_URL; ?>/assets/img/articles/1.jpg'/>          
                        <div class='ml-preview-article-body'>
                            <h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit</h3>
                            <span class='ml-article-date'>42 days ago</span>
                        </div>
                    </div>
                    <div class='ml-preview-article'>
                        <img class='ml-preview-img' src='<?php echo MOBILOUD_PLUGIN_URL; ?>/assets/img/articles/2.jpg'/>          
                        <div class='ml-preview-article-body'>
                            <h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit</h3>
                            <span class='ml-article-date'>42 days ago</span>
                        </div>
                    </div>
                    <div class='ml-preview-article'>
                        <img class='ml-preview-img' src='<?php echo MOBILOUD_PLUGIN_URL; ?>/assets/img/articles/3.jpg'/>          
                        <div class='ml-preview-article-body'>
                            <h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit</h3>
                            <span class='ml-article-date'>56 days ago</span>
                        </div>
                    </div>
                    <div class='ml-preview-article'>
                        <img class='ml-preview-img' src='<?php echo MOBILOUD_PLUGIN_URL; ?>/assets/img/articles/4.jpg'/>          
                        <div class='ml-preview-article-body'>
                            <h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit</h3>
                            <span class='ml-article-date'>56 days ago</span>
                        </div>
                    </div>
<?php } //has posts check  ?>
            </div>
        </div>
    </div>
</div>