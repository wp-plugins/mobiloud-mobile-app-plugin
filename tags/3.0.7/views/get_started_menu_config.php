<div id="get_started_menu_config" class="tabs-panel">
    <form method="post" action="<?php echo admin_url('admin.php?page=mobiloud&tab=menu_config'); ?>">
        <?php wp_nonce_field('form-get_started_menu_config'); ?>
        <h3>Menu Structure</h3>
        <p>Drag each item into the order you prefer.</p>
        <div class='ml-col-row'>
            <div class="ml-col-twothirds">
                <h4>Categories</h4>
                <div class="ml-form-row">
                    <select name="ml-category" class="ml-select-add">
                        <option value="">Select a category</option>
                        <?php $categories = get_categories(); ?>
                        <?php
                        foreach ($categories as $c) {
                            echo "<option value='$c->cat_ID'>$c->cat_name</option>";
                        }
                        ?>
                    </select>
                    <a href="#" class="button-secondary ml-add-category-btn">Add</a>
                </div>
                <ul class="ml-menu-holder ml-menu-categories-holder">
                    <?php
                    global $wpdb;
                    $table_name = $wpdb->prefix . "mobiloud_categories";

                    $ml_categories = ml_categories();
                    $ml_prev_cat = 0;
                    foreach ($ml_categories as $cat) {
                        ?>
                        <li rel="<?php echo $cat->cat_ID; ?>">
                            <span class="dashicons-before dashicons-menu"></span><?php echo $cat->name; ?>
                            <input type="hidden" name="ml-menu-categories[]" value="<?php echo $cat->cat_ID; ?>"/>
                            <a href="#" class="dashicons-before dashicons-trash ml-item-remove"></a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>

                <h4>Pages</h4>
                <div class="ml-form-row">
                    <select name="ml-page" class="ml-select-add">
                        <option value="">Select a page</option>
                        <?php $pages = get_pages(); ?>
                        <?php
                        foreach ($pages as $p) {
                            echo "<option value='$p->ID'>$p->post_title</option>";
                        }
                        ?>
                    </select>
                    <a href="#" class="button-secondary ml-add-page-btn">Add</a>
                </div>
                <ul class="ml-menu-holder ml-menu-pages-holder">
                    <?php
                    global $wpdb;
                    $table_name = $wpdb->prefix . "mobiloud_pages";

                    $ml_pages = ml_pages();
                    foreach ($ml_pages as $page) {
                        ?>
                        <li rel="<?php echo $page->ID; ?>">
                            <span class="dashicons-before dashicons-menu"></span><?php echo $page->post_title; ?>
                            <input type="hidden" name="ml-menu-pages[]" value="<?php echo $page->ID; ?>"/>
                            <a href="#" class="dashicons-before dashicons-trash ml-item-remove"></a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>

                <h4>Links</h4>
                <div class="ml-form-row">
                    <input type="text" placeholder="Menu Title"  id="ml_menu_url_title" name="ml_menu_url_title"/>
                    <input type="text" placeholder="Menu URL"  size="32" id="ml_menu_url" name="ml_menu_url"/>
                    <a href="#" class="button-secondary ml-add-link-btn">Add</a>
                </div>
                <ul class="ml-menu-holder ml-menu-links-holder">
                    <?php
                    $menu_urls = get_option("ml_menu_urls", array());
                    foreach ($menu_urls as $menu_url) {
                        ?>
                        <li rel="<?php echo $menu_url['url']; ?>">
                            <span class="dashicons-before dashicons-menu"></span><?php echo esc_html($menu_url['urlTitle']); ?> - <span class="ml-sub-title"><?php echo Mobiloud::trim_string(esc_html($menu_url['url']), 50); ?></span>
                            <input type="hidden" name="ml-menu-links[]" value="<?php echo esc_attr($menu_url['urlTitle']) . ':=:' . esc_attr($menu_url['url']); ?>"/>
                            <a href="#" class="dashicons-before dashicons-trash ml-item-remove"></a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
        <h3 class="ml-push-top">Menu Settings</h3>
        <div class='ml-col-row'>
            <div class="ml-col-half">
                <p>Customise your app menu by adjusting what it should display.</p>
            </div>
            <div class="ml-col-half">
                <div class="ml-form-row ml-checkbox-wrap">
                    <input type="checkbox" id="ml_menu_show_favorites" name="ml_menu_show_favorites" value="true" <?php echo Mobiloud::get_option('ml_menu_show_favorites') ? 'checked' : ''; ?>/>
                    <label for="ml_menu_show_favorites">Show Favourites in the app menu</label>
                </div>
                <div class="ml-form-row ml-checkbox-wrap no-margin">
                    <input type="checkbox" id="ml_show_android_cat_tabs" name="ml_show_android_cat_tabs" value="true" <?php echo Mobiloud::get_option('ml_show_android_cat_tabs') ? 'checked' : ''; ?>/>
                    <label for="ml_show_android_cat_tabs">Show categories tab menu at the top of the screen on Android</label>
                </div>
            </div>
        </div>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
    </form>
</div>