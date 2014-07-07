<?php 
function ml_init_intercom() {
    
    //var_dump($user);exit;
	if(is_admin() && current_user_can('administrator')) {
		$user = wp_get_current_user();
        ?>
            <script>(function(){var w=window;var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://api.intercom.io/api/js/library.js';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}})();</script>	
            <script id="IntercomSettingsScriptTag">
              window.intercomSettings = {
                email: "<?php echo $user->user_email; ?>",
                name: "<?php echo $user->user_firstname; ?> <?php echo $user->user_lastname; ?>",
                site: "<?php echo site_url();?>",                
                sitename: "<?php echo get_bloginfo('name'); ?>",
                version: "<?php echo MOBILOUD_PLUGIN_VERSION;?>",
                app_id: "h89uu5zu",
                user_id: "<?php echo $user->user_email; ?>",
                user_hash: "<?php echo hash_hmac("sha256", $user->user_email, "2d8ReoNHhovD4NhWCb72DgrghadvKVwGJsR0t6YR"); ?>",
                widget: {
                    activator: "#intercom"
                }
              };
            </script>
        <?php
	}
}

function ml_init_getvero() {    
    if(is_admin() && current_user_can('administrator')) {
        $user = wp_get_current_user();
        ?>
        <script type="text/javascript">
            var _veroq = _veroq || [];

            _veroq.push(['init', {
              api_key: '36bd54bf9afde30628102337cf6dc4306a6a212a',
              development_mode: false 
              // Turn this off when you decide to 'go live'.
            } ]);

            _veroq.push(['user', {
                id: "<?php echo $user->user_email; ?>", 
                email: "<?php echo $user->user_email; ?>", 
                name: "<?php echo $user->user_firstname; ?> <?php echo $user->user_lastname; ?>",
                website: "<?php echo site_url(); ?>",
                website_name: "<?php echo get_bloginfo('name'); ?>",
                user_lever: 'administrator',
                version: "<?php echo MOBILOUD_PLUGIN_VERSION;?>",
              }]);

            (function() {var ve = document.createElement('script'); ve.type = 'text/javascript'; ve.async = true; ve.src = '//getvero.com/assets/m.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ve, s);})();

        </script>
        <?php
    }
}

function ml_track_getvero($action, $loadInit=false) {
    if(is_admin() && current_user_can('administrator')) {
        $user = wp_get_current_user();
        if($loadInit) {
            ml_init_getvero();
        }
        ?>
        <script type="text/javascript">
            _veroq.push(['track', "<?php echo $action; ?>"]);
        </script>
        <?php
    }
}

function ml_track_intercom($action, $loadInit=false) {
    if(is_admin() && current_user_can('administrator')) {
        $user = wp_get_current_user();
        if($loadInit) {
            ml_init_intercom();
        }
        ?>
        <script type="text/javascript">
            Intercom("trackUserEvent", "<?php echo $action; ?>");
        </script>
        <?php
    }
}
?>