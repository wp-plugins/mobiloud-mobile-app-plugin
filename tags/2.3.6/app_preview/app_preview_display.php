<div class='ml-preview <?php echo strlen(get_option('ml_preview_os')) ? get_option('ml_preview_os') : 'ios'; ?>'>
    <div class='ml-preview-body'>
        <div class="ml-preview-top-bar <?php echo $iconShade; ?>" style='background-color: <?php echo get_option('ml_preview_theme_color'); ?>;'></div>
        <div class='ml-preview-menu-bar' style='background-color: <?php echo get_option('ml_preview_theme_color'); ?>;'>
            <a href='javascript:void(0);' class='ml-icon ml-icon-menu <?php echo $iconShade; ?>'></a>
            <a href='javascript:void(0);' class='ml-preview-logo-holder'>
                <?php $logoSize = null;
                if(strlen(trim(get_option("ml_preview_upload_image"))) > 0) {
                    $logoSize = @getimagesize(get_option("ml_preview_upload_image")); 
                }
                if(is_array($logoSize)) {
                    $logoPath = get_option("ml_preview_upload_image");
                } else {
                    $logoPath =  MOBILOUD_PLUGIN_URL . '/images/ml_preview_nologo.png';
                }
                ?>  
                <img class='ml-preview-logo' src='<?php echo $logoPath; ?>'/>
            </a>
            <a href='javascript:void(0);' class='ml-icon ml-icon-search <?php echo $iconShade; ?>'></a>
        </div>
        <div class='ml-preview-article-list'>
            <div class='scroller'>
                <?php
                $posts = ml_preview_get_posts();
                if(count($posts) > 0) {
                    
                    foreach($posts as $post) {
                        $imageUrl = null;
                        if (has_post_thumbnail( $post->ID ) ) {
                           $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );   
                           $imageUrl = $image[0];
                        }
                        ?>
                        <div class='ml-preview-article'>
                            <?php if($imageUrl): ?>
                            <img class='ml-preview-img' src='<?php echo $imageUrl; ?>'/>   
                            <?php endif; ?>
                            <div class='ml-preview-article-body'>
                                <h3><?php echo $post->post_title; ?></h3>
                                <span class='ml-article-date'><?php echo how_long_ago(strtotime($post->post_date)); ?></span>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                ?>
                    <div class='ml-preview-article'>
                        <img src='<?php echo MOBILOUD_PLUGIN_URL; ?>/images/articles/1.jpg'/>          
                        <div class='ml-preview-article-body'>
                            <h3>Microsoft and Knewton partner up to bring adaptive learning to publishers & schools</h3>
                            <span class='ml-article-date'>42 days ago</span>
                        </div>
                    </div>
                    <div class='ml-preview-article'>
                        <img src='<?php echo MOBILOUD_PLUGIN_URL; ?>/images/articles/2.jpg'/>          
                        <div class='ml-preview-article-body'>
                            <h3>Orangina and their brilliant new campaign</h3>
                            <span class='ml-article-date'>42 days ago</span>
                        </div>
                    </div>
                    <div class='ml-preview-article'>
                        <img src='<?php echo MOBILOUD_PLUGIN_URL; ?>/images/articles/3.jpg'/>          
                        <div class='ml-preview-article-body'>
                            <h3>Float down the Colorado River using Google Street View</h3>
                            <span class='ml-article-date'>56 days ago</span>
                        </div>
                    </div>
                    <div class='ml-preview-article'>
                        <img src='<?php echo MOBILOUD_PLUGIN_URL; ?>/images/articles/4.jpg'/>          
                        <div class='ml-preview-article-body'>
                            <h3>Venture capitalists face a more competitive, global market</h3>
                            <span class='ml-article-date'>56 days ago</span>
                        </div>
                    </div>
                <?php } //has posts check ?>
            </div>
        </div>
    </div>
</div>
