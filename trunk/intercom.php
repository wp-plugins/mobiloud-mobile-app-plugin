<?php 

function ml_init_intercom() {
    
    //var_dump($user);exit;
	if(is_admin() && current_user_can('administrator') && Mobiloud::get_option('ml_initial_details_saved')) {
		$user_email = Mobiloud::get_option('ml_user_email');
        $user_name = Mobiloud::get_option('ml_user_name');
        $user_site = Mobiloud::get_option('ml_user_site');
        ?>
            <script>(function(){var w=window;var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://api.intercom.io/api/js/library.js';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}})();</script>	
            <script id="IntercomSettingsScriptTag">
              window.intercomSettings = {
                email: "<?php echo esc_js($user_email); ?>",
                name: "<?php echo esc_js($user_name); ?>",
                site: "<?php echo esc_js($user_site); ?>",
				installurl: "<?php echo get_site_url(); ?>",              
                sitename: "<?php echo get_bloginfo('name'); ?>",
                version: "<?php echo MOBILOUD_PLUGIN_VERSION;?>",
				post_count: "<?php echo wp_count_posts()->publish; ?>",
				homepage_type: "<?php echo get_option( 'show_on_front '); ?>",
                app_id: "h89uu5zu",
                user_id: "<?php echo esc_js($user_email); ?>",
                user_hash: "<?php echo hash_hmac("sha256", $user_email, "2d8ReoNHhovD4NhWCb72DgrghadvKVwGJsR0t6YR"); ?>",
                widget: {
                    activator: ".ml-intercom"
                }
              };
            </script>
        <?php
	}
}

function ml_init_getvero() {    
    if(is_admin() && current_user_can('administrator') && Mobiloud::get_option('ml_initial_details_saved')) {
        $user_email = Mobiloud::get_option('ml_user_email');
        $user_name = Mobiloud::get_option('ml_user_name');
        $user_site = Mobiloud::get_option('ml_user_site');
        ?>
        <script type="text/javascript">
            var _veroq = _veroq || [];

            _veroq.push(['init', {
              api_key: '36bd54bf9afde30628102337cf6dc4306a6a212a',
              development_mode: false 
              // Turn this off when you decide to 'go live'.
            } ]);

            _veroq.push(['user', {
                id: "<?php echo esc_js($user_email); ?>", 
                email: "<?php echo esc_js($user_email); ?>", 
                name: "<?php echo esc_js($user_name); ?>",
                website: "<?php echo esc_js($user_site); ?>",
				installurl: "<?php echo get_site_url(); ?>",              
                website_name: "<?php echo get_bloginfo('name'); ?>",
				post_count: "<?php echo wp_count_posts()->publish; ?>",
				homepage_type: "<?php echo get_option( 'show_on_front '); ?>",
                sitename: "<?php echo get_bloginfo('name'); ?>",
                version: "<?php echo MOBILOUD_PLUGIN_VERSION;?>",
                user_lever: 'administrator',
                version: "<?php echo MOBILOUD_PLUGIN_VERSION;?>",
              }]);

            (function() {var ve = document.createElement('script'); ve.type = 'text/javascript'; ve.async = true; ve.src = '//getvero.com/assets/m.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ve, s);})();

        </script>
        <?php
    }
}

function ml_init_mixpanel() {
    ?>
        <!-- start Mixpanel -->
        <script type="text/javascript">
            (function(f,b){if(!b.__SV){var a,e,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");
            for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=f.createElement("script");a.type="text/javascript";a.async=!0;a.src="//cdn.mxpnl.com/libs/mixpanel-2.2.min.js";e=f.getElementsByTagName("script")[0];e.parentNode.insertBefore(a,e)}})(document,window.mixpanel||[]);
            mixpanel.init("3e7cc38a0abe4ea3a16a0e7538144f23");
        </script>
        <!-- end Mixpanel -->
    <?php
}

function ml_track_mixpanel($action) {
    ?>
    <script type="text/javascript">
        mixpanel.track(
            "<?php echo esc_js($action); ?>"
        );        
    </script>
    <?php
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