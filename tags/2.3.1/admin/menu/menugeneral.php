<?php
add_action('wp_ajax_ml_configuration_menu_general', 'ml_configuration_menu_general_callback');


function ml_configuration_menu_general_callback()
{
	global $ml_menu_show_favorites;
	
	if(isset($_POST['ml_menu_show_favorites']))
	{
		$ml_menu_show_favorites = $_POST['ml_menu_show_favorites'] == "true";
		ml_set_generic_option("ml_menu_show_favorites", $ml_menu_show_favorites);
	}

	ml_configuration_menu_general();
	die();
}

function ml_configuration_menu_general_ajax_load()
{
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var data = {
			action: 'ml_configuration_menu_general'
		};
		jQuery("#ml_configuration_menu_general").css("display","none");
			
		$.post(ajaxurl, data, function(response) {
			//saving the result and reloading the div
			jQuery("#ml_configuration_menu_general").html(response).fadeIn().slideDown();
		});			
			
	});
	</script>
	<?php
}

function ml_configuration_menu_general()
{

	ml_configuration_menu_general_div();

	?>

	
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		jQuery("#ml_configuration_menu_general_submit").click(function(){
			
			jQuery("#ml_configuration_menu_general_submit").val("<?php _e('Saving...'); ?>");
			jQuery("#ml_configuration_menu_general_submit").attr("disabled", true);
			jQuery("#ml_configuration_menu_general").css("opacity","0.5");
			
			var data = {
				action: 'ml_configuration_menu_general',
				ml_menu_show_favorites:  jQuery("#ml_menu_show_favorites").is(":checked"),
			};
			
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_configuration_menu_general").html(response).fadeIn();
				jQuery("#ml_configuration_menu_general_submit").val("<?php _e('Apply'); ?>");
				jQuery("#ml_configuration_menu_general_submit").attr("disabled", false);
				jQuery("#ml_configuration_menu_general").css("opacity","1.0");

			});			
			
		});

	});
	</script>
	
	
	<?php
}



function ml_configuration_menu_general_div()
{
	global $ml_menu_show_favorites;
	$ml_menu_show_favorites = get_option('ml_menu_show_favorites',true);
	
	?>
	<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">General menu settings</h3>
	
	

	<h2 style="font-size:20px;font-weight:normal;padding:10px;">
	<input id="ml_menu_show_favorites" type="checkbox"
		<?php
			if($ml_menu_show_favorites)
			{
				echo " checked ";
			}
		?>
		/> Show Favorites in the app menu
	</h2>
    
	<div style="margin-right:20px;">
		<p class="submit" align="right">
			<input type="submit" id="ml_configuration_menu_general_submit" 
											   value="<?php _e('Apply'); ?>" class="button button-primary button-large"/>
		</p>
	</div>
	
	<?php
}
?>