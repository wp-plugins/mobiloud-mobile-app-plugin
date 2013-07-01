<?php 
function ml_init_intercom() {
	if(is_admin()) {
		$user = wp_get_current_user();
	?><script id="IntercomSettingsScriptTag">
		  var intercomSettings = {
		    email: "<?php echo $user->user_email; ?>",
		    name: "<?php echo $user->user_firstname; ?> <?php echo $user->user_lastname; ?>",
		    site: "<?php echo site_url();?>",
		    version: "<?php echo MOBILOUD_PLUGIN_VERSION;?>",
		    app_id: "h89uu5zu"
		  };
		</script>
		<script>(function(){var w=window;var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://api.intercom.io/api/js/library.js';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}})();</script>	
	<?php
	}
}?>