<?php
add_action('wp_ajax_ml_configuration_facebook', 'ml_configuration_facebook_callback');


function ml_configuration_facebook_callback()
{
	//facebook
	global $ml_fb_app_id, $ml_fb_secret_key;

	//save facebook id and secret
	if(isset($_POST["ml_fb_app_id"]))
	{
		ml_set_fb_app_id($_POST["ml_fb_app_id"]);
		$ml_fb_app_id = get_option('ml_fb_app_id');
	}
	
	if(isset($_POST["ml_fb_secret_key"]))
	{
		ml_set_fb_secret_key($_POST["ml_fb_secret_key"]);
		$ml_fb_secret_key = get_option('ml_fb_secret_key');
	}

	ml_configuration_facebook();
	
	die();
}

function ml_configuration_facebook_ajax_load()
{
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var data = {
			action: 'ml_configuration_facebook'
		};
		jQuery("#ml_facebook_keys").css("display","none");
			
		$.post(ajaxurl, data, function(response) {
			//saving the result and reloading the div
			jQuery("#ml_facebook_keys").html(response).fadeIn();

		});			
			
	});
	</script>
	<?php
}

function ml_configuration_facebook()
{

	ml_configuration_facebook_div();

	?>

	
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		jQuery("#ml_configuration_facebook_submit").click(function(){
			
			jQuery("#ml_configuration_facebook_submit").val("<?php _e('Saving...'); ?>");
			jQuery("#ml_configuration_facebook_submit").attr("disabled", true);

			jQuery("#ml_facebook_keys").css("opacity","0.5");
			
			var data = {
				action: 'ml_configuration_facebook',
				ml_fb_app_id: jQuery("#ml_fb_app_id").val(),
				ml_fb_secret_key: jQuery("#ml_fb_secret_key").val()
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_facebook_keys").html(response).fadeIn();
				jQuery("#ml_configuration_facebook_submit").val("<?php _e('Apply'); ?>");
				jQuery("#ml_facebook_keys").css("opacity","1.0");
				jQuery("#ml_configuration_facebook_submit").attr("disabled", false);

			});			
			
		});
	});
	</script>
	
	
	<?php
}

function ml_configuration_facebook_div()
{
	//facebook
	global $ml_fb_app_id, $ml_fb_secret_key;
	
	//loading variables
	$ml_fb_app_id = get_option('ml_fb_app_id');
	$ml_fb_secret_key = get_option('ml_fb_secret_key');
	?>
	
	<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">Facebook API Settings</h3>

	<p><span style="font-size:20px;font-weight:normal;padding:10px;">
		You'll receive instructions on how to configure this once your app goes live. 
	</span></p>


	<?php 
		$fb_app = ml_facebook_get_app_info();
		if($fb_app){
			echo "<table><tr valign=top>";
			echo "<td><div style='margin-left:10px;'><img src='".$fb_app['logo_url']."'></div></td>";
			echo "<td><h2 style='font-size:20px;font-weight:normal;margin-left:10px;'>".$fb_app['name']."</h2></td>";
			echo "</tr></table>";
		}
		else
		{
			//not connected to facebook
			echo "<h4 style='font-size:15px;margin-left:10px;'>Not connected to Facebook</h4>";						
		}
	?>


	<h2 style="font-size:20px;font-weight:normal;padding:10px;">
		App ID
	</h2>

	<!-- API KEY -->
	<input id="ml_fb_app_id" placeholder="Insert App ID" name="ml_fb_app_id" type="text"
		value="<?php echo $ml_fb_app_id ?>" style="padding:5px;font-size:15px;margin-left:5%;width:90%;"/>
	<p></p>


	<!-- SECRET KEY -->
	<h2 style="font-size:20px;font-weight:normal;padding:10px;">
		Secret Key
	</h2>
	<input id="ml_fb_secret_key" placeholder="Insert Secret Key" name="ml_fb_secret_key" type="text" size="40" 
	value="<?php echo $ml_fb_secret_key?>" 
	style="padding:5px;font-size:15px;margin-left:5%;width:90%;"/>
	<p></p>
	
	<div style="margin-right:20px;">
		<p class="submit" align="right"><input type="submit" id="ml_configuration_facebook_submit" 
											   value="<?php _e('Apply'); ?>" /></p>
	</div>
	
	<?php
}
?>