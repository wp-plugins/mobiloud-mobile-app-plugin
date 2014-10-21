<div id="ml_settings_advertising" class="tabs-panel ml-compact">
    <form method="post" action="<?php echo admin_url('admin.php?page=mobiloud_settings&tab=advertising'); ?>">
        <?php wp_nonce_field('form-settings_advertising'); ?>
		<p>With Mobiloud's support for a number of networks and ad servers and the possibility of adding any image, javascript or HTML based ads (including Adsense) within the contents of your app, the possibilities to monetize your content are endless! Should you have any questions or require our assistance, get in touch at <a href='mailto:support@mobiloud.com'>support@mobiloud.com</a>.</p>
		
        <h3>Banner, Interstitial and Native ads</h3>
	    <?php if( strlen(Mobiloud::get_option('ml_pb_app_id')) > 0 && Mobiloud::get_option('ml_pb_app_id') < "543e7b3f1d0ab16d148b4599"): ?>			
        <div class='update-nag'>
            <p> The settings below are only available for recently published apps. Use the section "Embed HTML ads" to add your own banners or Adsense code.</p>
			<p> Should you have any questions, get in touch at <a href='mailto:support@mobiloud.com'>support@mobiloud.com</a>.</p>
        </div>
        <?php endif; ?>
		
		
        <p>With AdMob, MoPub and Google DFP you'll be able to sell your own ad inventory or display banners from a range of mobile ad networks.</p>
        <p>The following ad types are supported:</p>
        <ul class="ml-info-list">
			<li><strong>AdMob</strong>: AdMob is a leading global mobile advertising network that helps you monetize your mobile apps. Banner ads and interstitials are supported. <a href="https://www.google.com/ads/admob/index.html" target="_blank">Read more</a></li>
			<li><strong>MoPub</strong>: MoPub is a hosted ad serving solution built specifically for mobile publishers. As an ad server, MoPub allows you to monetize with your own inventory of ads or combine multiple ad networks. Banner ads, interstitials and native ads are supported. <a href="http://www.mopub.com/" target="_blank">Read more</a></li>
			<li><strong>Google DFP</strong>: DoubleClick for Publishers by Google (DFP), can be used as an ad server but also provides a variety of useful features for managing the sales process of online ads using your dedicated sales team. Banner ads and, interstitials are supported. <a href="https://www.google.com/dfp" target="_blank">Read more</a></li>
        </ul>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row">Select Advertising Platform</th>
                    <td>
                        <select id="ml_advertising_platform" name="ml_advertising_platform">
                            <option value="admob" <?php echo Mobiloud::get_option('ml_advertising_platform') === 'admob' ? 'selected="selected"' : ''; ?>>AdMob</option>
                            <option value="mopub" <?php echo Mobiloud::get_option('ml_advertising_platform') === 'mopub' ? 'selected="selected"' : ''; ?>>MoPub</option>
                            <option value="gdfp" <?php echo Mobiloud::get_option('ml_advertising_platform') === 'gdfp' ? 'selected="selected"' : ''; ?>>Google DoubleClick (DFP)</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class='ml-col-row'>
            <div class='ml-col-half'>
                <h3>iOS Ad Units</h3>       
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">Phone Banner Unit ID</th>
                            <td>
                                <input type="text" id="ml_ios_phone_banner_unit_id" name="ml_ios_phone_banner_unit_id" value="<?php echo esc_attr(Mobiloud::get_option('ml_ios_phone_banner_unit_id')); ?>"/>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Tablet Banner Unit ID</th>
                            <td>
                                <input type="text" id="ml_ios_tablet_banner_unit_id" name="ml_ios_tablet_banner_unit_id" value="<?php echo esc_attr(Mobiloud::get_option('ml_ios_tablet_banner_unit_id')); ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="ml-radio-wrap">
                                    <input type="radio" id="ml_ios_banner_position_top" name="ml_ios_banner_position" value="top" <?php echo Mobiloud::get_option('ml_ios_banner_position', 'bottom') === 'top' ? 'checked' : ''; ?>>
                                    <label for="ml_ios_banner_position_top">Show banners at the top of the screen</label>
                                </div>
                                <div class="ml-radio-wrap">
                                    <input type="radio" id="ml_ios_banner_position_bottom" name="ml_ios_banner_position" value="bottom" <?php echo Mobiloud::get_option('ml_ios_banner_position', 'bottom') === 'bottom' ? 'checked' : ''; ?>>
                                    <label for="ml_ios_banner_position_bottom">Show banners at the bottom of the screen (recommended)</label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">Interstitial Ad Unit ID</th>
                            <td>
                                <input type="text" id="ml_ios_interstitial_unit_id" name="ml_ios_interstitial_unit_id" value="<?php echo esc_attr(Mobiloud::get_option('ml_ios_interstitial_unit_id')); ?>"/>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Interval</th>
                            <td class="ml_ad_interval">                                
                                <p>Show interstitial ads every<br/>
                                    article or page screens.</p>
                                <select id="ml_ios_interstitial_interval" name="ml_ios_interstitial_interval">
                                    <?php for($a = 1; $a <= 10; $a++): ?>
                                    <option value="<?php echo esc_attr($a); ?>" <?php echo Mobiloud::get_option('ml_ios_interstitial_interval', 5) == $a ? 'selected="selected"' : ''; ?>>
                                        <?php echo esc_html($a); ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="ml_native_ads_wrap">
                    <hr/>
                    <table class="form-table">
                        <tbody>
                            <tr valign="top">
                                <th scope="row">Native Ad Unit ID</th>
                                <td>
                                    <input type="text" id="ml_ios_native_ad_unit_id" name="ml_ios_native_ad_unit_id" value="<?php echo esc_attr(Mobiloud::get_option('ml_ios_native_ad_unit_id')); ?>"/>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Interval</th>
                                <td class="ml_ad_interval">                                
                                    <p>Show native ads every<br/>
                                        articles in the article list.</p>
                                    <select id="ml_ios_native_ad_interval" name="ml_ios_native_ad_interval">
                                        <?php for($a = 1; $a <= 10; $a++): ?>
                                        <option value="<?php echo esc_attr($a); ?>" <?php echo Mobiloud::get_option('ml_ios_native_ad_interval', 5) == $a ? 'selected="selected"' : ''; ?>>
                                            <?php echo esc_html($a); ?>
                                        </option>
                                        <?php endfor; ?>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class='ml-col-half'>
                <h3>Android Ad Units</h3>       
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">Phone Banner Unit ID</th>
                            <td>
                                <input type="text" id="ml_android_phone_banner_unit_id" name="ml_android_phone_banner_unit_id" value="<?php echo esc_attr(Mobiloud::get_option('ml_android_phone_banner_unit_id')); ?>"/>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Tablet Banner Unit ID</th>
                            <td>
                                <input type="text" id="ml_android_tablet_banner_unit_id" name="ml_android_tablet_banner_unit_id" value="<?php echo esc_attr(Mobiloud::get_option('ml_android_tablet_banner_unit_id')); ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="ml-radio-wrap">
                                    <input type="radio" id="ml_android_banner_position_top" name="ml_android_banner_position" value="top" <?php echo Mobiloud::get_option('ml_android_banner_position', 'bottom') === 'top' ? 'checked' : ''; ?>>
                                    <label for="ml_android_banner_position_top">Show banners at the top of the screen</label>
                                </div>
                                <div class="ml-radio-wrap">
                                    <input type="radio" id="ml_android_banner_position_bottom" name="ml_android_banner_position" value="bottom" <?php echo Mobiloud::get_option('ml_android_banner_position', 'bottom') === 'bottom' ? 'checked' : ''; ?>>
                                    <label for="ml_android_banner_position_bottom">Show banners at the bottom of the screen (recommended)</label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">Interstitial Ad Unit ID</th>
                            <td>
                                <input type="text" id="ml_android_interstitial_unit_id" name="ml_android_interstitial_unit_id" value="<?php echo esc_attr(Mobiloud::get_option('ml_android_interstitial_unit_id')); ?>"/>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Interval</th>
                            <td class="ml_ad_interval">                                
                                <p>Show interstitial ads every<br/>
                                    article or page screens.</p>
                                <select id="ml_android_interstitial_interval" name="ml_android_interstitial_interval">
                                    <?php for($a = 1; $a <= 10; $a++): ?>
                                    <option value="<?php echo esc_attr($a); ?>" <?php echo Mobiloud::get_option('ml_android_interstitial_interval', 5) == $a ? 'selected="selected"' : ''; ?>>
                                        <?php echo esc_html($a); ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="ml_native_ads_wrap">
                    <hr/>
                    <table class="form-table">
                        <tbody>
                            <tr valign="top">
                                <th scope="row">Native Ad Unit ID</th>
                                <td>
                                    <input type="text" id="ml_android_native_ad_unit_id" name="ml_android_native_ad_unit_id" value="<?php echo esc_attr(Mobiloud::get_option('ml_android_native_ad_unit_id')); ?>"/>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Interval</th>
                                <td class="ml_ad_interval">                                
                                    <p>Show native ads every<br/>
                                        articles in the article list.</p>
                                    <select id="ml_android_native_ad_interval" name="ml_android_native_ad_interval">
                                        <?php for($a = 1; $a <= 10; $a++): ?>
                                        <option value="<?php echo esc_attr($a); ?>" <?php echo Mobiloud::get_option('ml_android_native_ad_interval', 5) == $a ? 'selected="selected"' : ''; ?>>
                                            <?php echo esc_html($a); ?>
                                        </option>
                                        <?php endfor; ?>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <h3>Embed HTML ads within the content</h3>
        <div class='ml-col-twothirds'>
            <p>You can use the editor to add HTML or Javascript code in a number of ad positions within the post and page screens.</p>

            <div class="ml-editor-controls">
                <select id="ml_ad_banner_position_select" name="ml_ad_banner_position_select">
                    <option value="">
                        Select a position...
                    </option>
                    <?php foreach(Mobiloud_Admin::$banner_positions as $position_key=>$position_name): ?>
                    <option value='<?php echo esc_attr($position_key); ?>'?>
                        <?php echo esc_html($position_name); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <a href="#" class='button-primary ml-save-banner-btn'>Save</a>
            </div>
            <textarea class='ml-editor-area ml-show'></textarea>
            <?php foreach(Mobiloud_Admin::$banner_positions as $position_key=>$position_name): ?>
            <textarea class='ml-editor-area' name='<?php echo esc_attr($position_key); ?>'><?php echo stripslashes(htmlspecialchars(Mobiloud::get_option($position_key, ''))); ?></textarea>
            <?php endforeach; ?>
            
            <h4>Preview the results</h4>
            <p>Select a post or page to preview the results of your edits.</p>
            <select id="preview_popup_post_select">
                <?php $posts = get_posts(array('posts_per_page' => 10,'orderby' => 'post_date','order' => 'DESC','post_type' => 'post')); ?>
                <?php $pages = get_pages(array('sort_order' => 'ASC', 'sort_column' => 'post_title', 'post_type' => 'page','post_status' => 'publish')); ?>
                <optgroup label="Posts">
                <?php foreach($posts as $post) { ?>

                <option value="<?php echo MOBILOUD_PLUGIN_URL; ?>post/post.php?post_id=<?php echo $post->ID; ?>">
                <?php if(strlen($post->post_title) > 40) { ?>

                <?php echo substr($post->post_title,0,40); ?>

                ..
                <?php } else { ?>

                <?php echo $post->post_title; ?>

                <?php } ?>
                </option><?php } ?>
                </optgroup><optgroup label="Pages">
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
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
    </form>
</div>
<div id="preview_popup_content">
<div class="iphone5s_device">
<iframe id="preview_popup_iframe">
</iframe></div><div class="ipadmini_device">
<iframe id="preview_popup_iframe">
</iframe></div></div>