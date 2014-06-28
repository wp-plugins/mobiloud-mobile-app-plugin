<script src="<?php echo MOBILOUD_PLUGIN_URL; ?>/admin/license/license.js" type="text/javascript">
</script>
<div class="wrap">
    <div style="margin-top:20px;">
        <h1 style="float: left;">Mobiloud License Keys</h1>
        <a id="intercom" style="float: right;" class="ml-contact-button button button-primary" href="mailto:h89uu5zu@incoming.intercom.io">Contact Us</a>       
        <div style="clear:both;"></div>
    </div>
    <div class="narrow">
        <div class="stuffbox" id="ml_admin_license">
            
            <p style="margin: 8px 12px; font-size: 20px;">You'll receive instructions on how to configure this once your app goes live.</p>
            
            <p style="margin: 8px 12px; font-size: 16px;">Application ID</p>
            <input name="app_id" placeholder="Insert APP ID" type="text" value="<?php echo $ml_pb_app_id; ?>" />
            
            <p style="margin: 8px 12px; font-size: 16px;">Secret Key</p>
            <input name="secret_key" placeholder="Insert Secret Key" size="40" type="text" value="<?php echo $ml_pb_secret_key; ?>" />
            
            <input class="button button-primary button-large" type="submit" value="Apply" />
        </div>
    </div>
</div>