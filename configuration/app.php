<?php
add_action('wp_ajax_ml_configuration_app_redirect', 'ml_configuration_app_redirect_callback');


function ml_configuration_app_redirect_callback()
{
	//mobile promotional message
	global $ml_popup_message_on_mobile_active, 
		   $ml_popup_message_on_mobile_appid;
	if(isset($_POST['ml_popup_message_on_mobile_active']))
	{
		$ml_popup_message_on_mobile_active = $_POST['ml_popup_message_on_mobile_active'] == "true";
		
		ml_set_generic_option("ml_popup_message_on_mobile_active",
							   $ml_popup_message_on_mobile_active);
	}
	
	if(isset($_POST['ml_popup_message_on_mobile_appid']))
	{
		$ml_popup_message_on_mobile_appid = $_POST['ml_popup_message_on_mobile_appid'];

		ml_set_generic_option("ml_popup_message_on_mobile_appid",
							   $ml_popup_message_on_mobile_appid);
	}
	

	
	ml_configuration_app_redirect();
	
	die();
}

function ml_configuration_app_redirect_ajax_load()
{
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var data = {
			action: 'ml_configuration_app_redirect'
		};
		jQuery("#ml_configuration_app_redirect").css("display","none");
			
		$.post(ajaxurl, data, function(response) {
			//saving the result and reloading the div
			jQuery("#ml_configuration_app_redirect").html(response).fadeIn();
		});			
			
	});
	</script>
	<?php
}

function ml_configuration_app_redirect()
{

	ml_configuration_app_redirect_div();

	?>

	
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		jQuery("#ml_configuration_app_redirect_submit").click(function(){
			
			jQuery("#ml_configuration_app_redirect_submit").val("<?php _e('Saving...'); ?>");
			jQuery("#ml_configuration_app_redirect_submit").attr("disabled", true);
			
			jQuery("#ml_configuration_app_redirect").css("opacity","0.5");
			
			var data = {
				action: 'ml_configuration_app_redirect',
				ml_popup_message_on_mobile_active: jQuery("#ml_popup_message_on_mobile_active").is(":checked"),
				ml_popup_message_on_mobile_appid: jQuery("#ml_popup_message_on_mobile_appid").val(),
				
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_configuration_app_redirect").html(response).fadeIn();
				jQuery("#ml_configuration_app_redirect_submit").val("<?php _e('Apply'); ?>");
				jQuery("#ml_configuration_app_redirect_submit").attr("disabled", false);
				jQuery("#ml_configuration_app_redirect").css("opacity","1.0");

			});			
			
		});
	});
	</script>
	
	
	<?php
}

function ml_configuration_app_redirect_div()
{
	//mobile promotional message
	global $ml_popup_message_on_mobile_active, $ml_popup_message_on_mobile_appid;

	$ml_popup_message_on_mobile_active = get_option('ml_popup_message_on_mobile_active');
	$ml_popup_message_on_mobile_appid = get_option('ml_popup_message_on_mobile_appid');
	
	?>
	<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">
		Mobile Promotional Message
	</h3>
	
	<h2 style="font-size:20px;font-weight:normal;padding:10px;">
		<img src="<?=plugins_url("smartappbanner.png",dirname(__FILE__));?>" width="640" height="150">
	</h2>

	<p style="font-size:20px;font-weight:normal;padding:10px;">Smart&nbsp;App&nbsp;Banners&nbsp;help you promote your app from your website in a<br>
simple and unobtrusive way. To enable them make sure your Apple ID is<br>
entered below.</p>

	<h2 style="font-size:20px;font-weight:normal;padding:10px;">
		<input id="ml_popup_message_on_mobile_active" type="checkbox"
			<?php
				if($ml_popup_message_on_mobile_active)
				{
					echo " checked ";
				}
			?>
			/> Active Smart App Banners
	</h2>

	<!-- ACTIVE ? -->
	<p></p>

	
	<!-- APP ID -->
	<h2 style="font-size:20px;font-weight:normal;padding:10px;">
		App ID
	</h2>
	<input id="ml_popup_message_on_mobile_appid" placeholder="Type here the App ID" 
			name="ml_popup_message_on_mobile_appid" type="text" size="8" 
	value="<?php echo $ml_popup_message_on_mobile_appid;?>" 
	style="padding:5px;font-size:20px;margin-left:5%;width:50%;"/>
	<p></p>
		
	<!-- DESCRIPTION -->
	<div style="font-size:12px;padding:5px;margin-left:10%;margin-top:20px;margin-bottom:20px;width:70%;
		text-align:justify;">
	  <p>Once your app is live on App Store, find your app ID following the instructions below.	  </p>
	  <ol>
	    <li>Open iTunes.</li>
	    <li>Search for your app.</li>
	    <li>Click your app's name and copy the URL (right-click for PC users).</li>
	    </ol>
	  <p>App store URL’s will be in the following format:</p>
	  <div>http://itunes.apple.com/[country]/app/[App –Name]/id[App-ID]?mt=8</div>
	  <p><a href="http://support.google.com/admob/answer/1620111?hl=en" target="_blank"></a>Extract the [App-ID] part.</p>
    </div>
	
	<div style="margin-right:20px;">
		<p class="submit" align="right">
			<input type="submit" id="ml_configuration_app_redirect_submit" 
											   value="<?php _e('Apply'); ?>" />
		</p>
	</div>
	
	<?php
}
?>