<div id="ml_push_settings" class="tabs-panel ml-compact">
    <form method="post" action="<?php echo admin_url('admin.php?page=mobiloud_push&tab=settings'); ?>">
        <?php wp_nonce_field('form-push_settings'); ?>        
        <h4>Automatic push notifications</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>Automatically send push notifications when a new post is published</p>            
            </div>
            <div class='ml-col-half'>
                <div class="ml-form-row ml-checkbox-wrap">
                    <input type="checkbox" id="ml_push_notification_enabled" name="ml_push_notification_enabled" value="true" <?php echo Mobiloud::get_option('ml_push_notification_enabled') ? 'checked' : ''; ?>/>
                    <label for="ml_push_notification_enabled">Send notifications automatically</label>
                </div>
                <p>Select which categories will generate a push notification (leave empty for all)</p>
                <select id="ml_push_notification_categories" name='ml_push_notification_categories[]' data-placeholder="Select Categories..." style="width:350px;" multiple class="chosen-select">
                    <option></option>
                    <?php $categories = get_categories();?>
                    <?php $pushCategories = ml_get_push_notification_categories(); ?>
                    <?php 
                        foreach($categories as $c) {
                            $selected = '';
                            if(is_array($pushCategories) && count($pushCategories) > 0) {
                                foreach($pushCategories as $pushCategory) {
                                    if($pushCategory->cat_ID == $c->cat_ID) {
                                        $selected = 'selected';
                                    }
                                }
                            }
                            echo "<option value='$c->cat_ID' $selected>$c->cat_name</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
        <h4>Push Post Types</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>Select which post types should be pushed.</p>                
            </div>
            <div class='ml-col-half'>
                <?php
                $posttypes = get_post_types('','names'); 
                $includedPostTypes = explode(",",Mobiloud::get_option("ml_push_post_types","post"));
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
        <h4>Security settings (advanced)</h4>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <p>Choose whether to use SSL to communicate with our push service.</p>            
            </div>
            <div class="ml-form-row ml-checkbox-wrap no-margin">
                <input type="checkbox" id="ml_pb_use_ssl" name="ml_pb_use_ssl" value="true" <?php echo Mobiloud::get_option('ml_pb_use_ssl') ? 'checked' : ''; ?>/>
                <label for="ml_pb_use_ssl">Enable SSL for push notifications</label>
            </div>
        </div>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
    </form>
</div>