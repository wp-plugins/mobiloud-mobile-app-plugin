<div class="wrap">
    <div class="ml_header">
        <h1 style="float:left;">Mobiloud - Design Your App</h1>
        <a id="intercom" class="ml-contact-button button button-primary" href="mailto:h89uu5zu@incoming.intercom.io">Contact Us</a>       
        <div style="clear:both;"></div>
    </div>
    <p>&nbsp;</p>
    <div class="stuffbox">
        <div class='ml-preview-app-design'>
            <h3>Customize your app</h3>
            <table class="form-table table-preview-config">
                <tbody>
                    <tr valign="top">
                        <th scope="row">Choose your app logo</th>
                        <td>
                            <label for="upload_image">
                                <input id="ml_preview_upload_image" type="text" size="36" name="ml_preview_upload_image" value="<?php echo get_option("ml_preview_upload_image"); ?>" />
                                <input id="ml_preview_upload_image_button" type="button" value="Upload Image" />
                                <br />Enter an URL or upload an image for the logo.
                            </label>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Choose theme color</th>
                        <td>
                            <input name="ml_preview_theme_color" id="ml_preview_theme_color" type="text" value="<?php echo get_option("ml_preview_theme_color"); ?>" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Choose device</th>
                        <td>
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
                        </td>
                    </tr>
                </tbody>
            </table>
            <p style="height:20px;">&nbsp;</p>
            <h3>Your details</h3>
            <input type='hidden' value="<?php echo $root_url ?>" id='root_url'/>
            <input type='hidden' value="<?php echo $mobiloudPluginUrl ?>" id='mobiloud_plugin_url'/>
            <input type='hidden' value="<?php echo $mobiloudPluginVersion ?>" id='mobiloud_plugin_version'/>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">Your name</th>
                        <td>
                            <input size="36" type="text" id="contactName" name="contactName" placeholder="Enter your name" value='<?php echo $current_user->display_name; ?>' required>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Your email</th>
                        <td>
                            <input size="36" type="email" id="email" name="email" placeholder="Enter your email" value='<?php echo $current_user->user_email; ?>' required>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Your website</th>
                        <td>
                            <input size="36" type="text" id="website" name="website" placeholder="Enter your website" value='<?php echo get_site_url(); ?>'>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p style="height:20px;">&nbsp;</p>
            <h3>Test-drive your app in our simulator</h3>
            <p class="submit">
                <?php add_thickbox(); ?>
                <a href="" class="thickbox button button-hero button-primary signup-button">See Live Preview &amp Sign Up</a>
            </p>
        </div>
        <div class="ml-preview-app"></div>
        <div style='clear:both;'></div>
        
        
    </div>
    <div id="ml_preview_loading"><img src="<?php echo MOBILOUD_PLUGIN_URL; ?>/images/spinner.gif" alt="Loading..."/><br/>Loading Preview...</div>

    <small> By using Mobiloud you agree to Mobiloud's <a href="http://mobiloud.com/terms.php">terms of service</a> and <a href="https://www.iubenda.com/privacy-policy/435863/legal">privacy policy</span></a> </small>
</div>
