<?php
add_action('wp_ajax_ml_configuration_general', 'ml_configuration_general_callback');


function ml_configuration_general_callback()
{
	global $ml_automatic_image_resize;
	//save api key
	if(isset($_POST['ml_automatic_image_resize']))
	{
		$ml_automatic_image_resize = $_POST['ml_automatic_image_resize'] == "true";
		ml_set_generic_option("ml_automatic_image_resize",
							   $ml_automatic_image_resize);
	}

	ml_configuration_general();
	die();
}

function ml_configuration_general_ajax_load()
{
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var data = {
			action: 'ml_configuration_general'
		};
		jQuery("#ml_configuration_general").css("display","none");
			
		$.post(ajaxurl, data, function(response) {
			//saving the result and reloading the div
			jQuery("#ml_configuration_general").html(response).fadeIn().slideDown();
		});			
			
	});
	</script>
	<?php
}

function ml_configuration_general()
{

	ml_configuration_general_div();

	?>

	
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		jQuery("#ml_configuration_general_submit").click(function(){
			
			jQuery("#ml_configuration_general_submit").val("<?php _e('Saving...'); ?>");
			jQuery("#ml_configuration_general_submit").attr("disabled", true);
			jQuery("#ml_configuration_general").css("opacity","0.5");
			
			var data = {
				action: 'ml_configuration_general',
				ml_automatic_image_resize:  jQuery("#ml_automatic_image_resize_active").is(":checked")
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_configuration_general").html(response).fadeIn();
				jQuery("#ml_configuration_general_submit").val("<?php _e('Apply'); ?>");
				jQuery("#ml_configuration_general_submit").attr("disabled", false);
				jQuery("#ml_configuration_general").css("opacity","1.0");

			});			
			
		});
	});
	</script>
	
	
	<?php
}

function ml_configuration_general_div()
{
	global $ml_automatic_image_resize;
	
	$ml_automatic_image_resize = get_option('ml_automatic_image_resize');
	?>
	<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">General</h3>
	
	<h2 style="font-size:20px;font-weight:normal;padding:10px;">
		<input id="ml_automatic_image_resize_active" type="checkbox"
			<?php
				if($ml_automatic_image_resize)
				{
					echo " checked ";
				}
			?>
			/> Automatic resize main post image
	</h2>
	
	<div style="margin-right:20px;">
		<p class="submit" align="right">
			<input type="submit" id="ml_configuration_general_submit" 
											   value="<?php _e('Apply'); ?>" />
		</p>
	</div>
	
	<?php
}
?>