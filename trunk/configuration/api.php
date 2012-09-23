<?php
add_action('wp_ajax_ml_configuration_api_keys', 'ml_configuration_api_keys_callback');


function ml_configuration_api_keys_callback()
{
	global $ml_api_key, $ml_secret_key;
	
	//save api key
	if(isset($_POST['ml_api_key']))
	{
		ml_set_api_key($_POST['ml_api_key']);
		$ml_api_key = get_option('ml_api_key');
	}

	//save api secret
	if(isset($_POST['ml_secret_key']))
	{
		ml_set_secret_key($_POST['ml_secret_key']);
		$ml_secret_key = get_option('ml_secret_key');
	}
	
	ml_configuration_api_keys_div();
	die();
}

function ml_configuration_api_keys_ajax_load()
{
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var data = {
			action: 'ml_configuration_api_keys'
		};
		jQuery("#ml_configuration_api_keys").css("display","none");
			
		$.post(ajaxurl, data, function(response) {
			//saving the result and reloading the div
			jQuery("#ml_configuration_api_keys").html(response).fadeIn().slideDown();
		});			
			
	});
	</script>
	<?php
}

function ml_configuration_api_keys()
{

	ml_configuration_api_keys_div();

	?>

	
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		jQuery("#ml_configuration_api_keys_submit").click(function(){
			
			jQuery("#ml_configuration_api_keys_submit").val("<?php _e('Saving...'); ?>");
			jQuery("#ml_configuration_api_keys_submit").attr("disabled", true);
			jQuery("#ml_configuration_api_keys").css("opacity","0.5");
			
			var data = {
				action: 'ml_configuration_api_keys',
				ml_api_key: jQuery("#ml_configuration_api_key").val(),
				ml_secret_key: jQuery("#ml_configuration_secret_key").val()
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_configuration_api_keys").html(response).fadeIn();
				jQuery("#ml_configuration_api_keys_submit").val("<?php _e('Apply'); ?>");
				jQuery("#ml_configuration_api_keys_submit").attr("disabled", false);
				jQuery("#ml_configuration_api_keys").css("opacity","1.0");

			});			
			
		});
	});
	</script>
	
	
	<?php
}

function ml_configuration_api_keys_div()
{
	global $ml_api_key, $ml_secret_key;
	
	$ml_api_key = get_option('ml_api_key');
	$ml_secret_key = get_option('ml_secret_key');
	
	
	?>
	<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">Keys</h3>
	
	<h2 style="font-size:20px;font-weight:normal;padding:10px;">
		Api Key
	</h2>

	<!-- API KEY -->
	<input id="ml_configuration_api_key" placeholder="Insert API KEY" name="ml_api_key" type="text"
		value="<?php echo $ml_api_key ?>" style="padding:5px;font-size:20px;margin-left:5%;width:90%;"/>
	<p></p>

	
	<!-- SECRET KEY -->
	<h2 style="font-size:20px;font-weight:normal;padding:10px;">
		Secret Key
	</h2>
	<input id="ml_configuration_secret_key" placeholder="Insert Secret Key" name="ml_secret_key" type="text" size="40" 
	value="<?php echo $ml_secret_key?>" 
	style="padding:5px;font-size:20px;margin-left:5%;width:90%;"/>
	<p></p>
	
	<div style="margin-right:20px;">
		<p class="submit" align="right">
			<input type="submit" id="ml_configuration_api_keys_submit" 
											   value="<?php _e('Apply'); ?>" />
		</p>
	</div>
	
	<?php
}
?>