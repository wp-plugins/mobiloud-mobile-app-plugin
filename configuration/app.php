<?php
add_action('wp_ajax_ml_configuration_app_redirect', 'ml_configuration_app_redirect_callback');


function ml_configuration_app_redirect_callback()
{
	//mobile promotional message
	global $ml_popup_message_on_mobile_active, 
		   $ml_popup_message_on_mobile_url,
		   $ml_popup_message_on_mobile_message;
	if(isset($_POST['ml_popup_message_on_mobile_active']))
	{
		$ml_popup_message_on_mobile_active = $_POST['ml_popup_message_on_mobile_active'];

		ml_set_generic_option("ml_popup_message_on_mobile_active",
							   $ml_popup_message_on_mobile_active);
	}
	
	if(isset($_POST['ml_popup_message_on_mobile_url']))
	{
		$ml_popup_message_on_mobile_url = $_POST['ml_popup_message_on_mobile_url'];

		ml_set_generic_option("ml_popup_message_on_mobile_url",
							   $ml_popup_message_on_mobile_url);
	}
	
	if(isset($_POST['ml_popup_message_on_mobile_message']))
	{
		$ml_popup_message_on_mobile_message = $_POST['ml_popup_message_on_mobile_message'];

		ml_set_generic_option("ml_popup_message_on_mobile_message",
							   $ml_popup_message_on_mobile_message);
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
				ml_popup_message_on_mobile_url: jQuery("#ml_popup_message_on_mobile_url").val(),
				ml_popup_message_on_mobile_message: jQuery("#ml_popup_message_on_mobile_message").val()
				
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
	global $ml_popup_message_on_mobile_active, 
		   $ml_popup_message_on_mobile_url,
		   $ml_popup_message_on_mobile_message;
	
	$ml_popup_message_on_mobile_active = get_option('ml_popup_message_on_mobile_active');
	$ml_popup_message_on_mobile_url = get_option('ml_popup_message_on_mobile_url');
	$ml_popup_message_on_mobile_message = get_option('ml_popup_message_on_mobile_message');
	
	?>
	<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">
		Mobile Promotional Message
	</h3>
	
	<h2 style="font-size:20px;font-weight:normal;padding:10px;">
		<input id="ml_popup_message_on_mobile_active" type="checkbox"
			<?php echo ($ml_popup_message_on_mobile_active == 1 ? "checked='checked'":"") ?>/> Active
	</h2>

	<!-- ACTIVE ? -->
	<p></p>

	
	<!-- REDIRECT URL -->
	<h2 style="font-size:20px;font-weight:normal;padding:10px;">
		iTunes App URL
	</h2>
	<input id="ml_popup_message_on_mobile_url" placeholder="Type here the iTunes URL of the app" 
			name="ml_popup_message_on_mobile_url" type="text" size="40" 
	value="<?php echo $ml_popup_message_on_mobile_url?>" 
	style="padding:5px;font-size:20px;margin-left:5%;width:90%;"/>
	<p></p>
	
	<!-- POPUP MESSAGE -->
	<h2 style="font-size:20px;font-weight:normal;padding:10px;">
		Message
	</h2>
	<input id="ml_popup_message_on_mobile_message" placeholder="Type here the message you want to be dispalyed" 
			name="ml_popup_message_on_mobile_message" type="text" size="40" 
	value="<?php echo $ml_popup_message_on_mobile_message?>" 
	style="padding:5px;font-size:20px;margin-left:5%;width:90%;"/>
	<p></p>
	
	<!-- DESCRIPTION -->
	<div style="font-size:12px;padding:5px;margin-left:10%;margin-top:20px;margin-bottom:20px;width:70%;
		text-align:justify;">
			Notifies users who are using mobile web browsers about the availability of your app on the App Store. 
			The user will receive a popup message with whatever text you choose and be redirected to your App Store URL. 
			They will also have the option to cancel and continue viewing your website with the browser.				
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