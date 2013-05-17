<?php
add_action('wp_ajax_ml_configuration_categories', 'ml_configuration_categories_callback');
add_action('wp_ajax_ml_configuration_switch_categories', 'ml_configuration_switch_categories_callback');


function ml_configuration_categories_callback()
{

	if(isset($_POST['cat_ID']))
	{
		$cat_ID = intval($_POST['cat_ID']);
		if($_POST["cat_action"] == "delete") 
		{
			ml_remove_category($cat_ID);			
		}
		else if($_POST["cat_action"] == "add")
		{
			ml_add_category($cat_ID);
		}
	}

	
	ml_configuration_categories();
	
	die();

}

function ml_configuration_switch_categories_callback()
{

	if(isset($_POST['cat_ID_a']) && isset($_POST['cat_ID_b']))
	{
		$cat_ID_a = intval($_POST['cat_ID_a']);
		$cat_ID_b = intval($_POST['cat_ID_b']);
		ml_switch_categories($cat_ID_a,$cat_ID_b);
	}

	
	ml_configuration_categories();
	
	die();

}


function ml_configuration_categories_ajax_load()
{
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var data = {
			action: 'ml_configuration_categories'
		};
		jQuery("#ml_configuration_categories").css("display","none");
			
		$.post(ajaxurl, data, function(response) {
			//saving the result and reloading the div
			jQuery("#ml_configuration_categories").html(response).fadeIn().slideDown();
		});			
			
	});
	</script>
	<?php
}

function ml_configuration_categories()
{

	ml_configuration_categories_div();

	?>

	
	<script type="text/javascript" >
		jQuery(document).ready(function($) {

		jQuery("#ml_configuration_categories_add").click(function(){
			jQuery("#ml_configuration_categories_add").val("<?php _e('Saving...'); ?>");
			jQuery("#ml_configuration_categories_add").attr("disabled", true);
			jQuery("#ml_configuration_categories").css("opacity","0.5");
			
			var data = {
				action: 'ml_configuration_categories',
				cat_action: "add",
				cat_ID: $("select[name='category']").val()
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_configuration_categories").html(response).fadeIn();
				jQuery("#ml_configuration_categories_add").val("<?php _e('Save'); ?>");
				jQuery("#ml_configuration_categories_add").attr("disabled", false);
				jQuery("#ml_configuration_categories").css("opacity","1.0");

			});			
			
		});

		jQuery("a.remove-category").click(function(){
			var data = {
				action: 'ml_configuration_categories',
				cat_action: "delete",
				cat_ID: $(this).data("cat")
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_configuration_categories").html(response).fadeIn();

			});			

		});

		jQuery("a.move-category-up").click(function(){
			var prev_cat_id = $(this).data("prev-cat");
			var cat_id = $(this).data("cat");

			var data = {
				action: 'ml_configuration_switch_categories',
				cat_action: "post",
				cat_ID_a: cat_id,
				cat_ID_b: prev_cat_id
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_configuration_categories").html(response).fadeIn();

			});			

		});

	});
	</script>
	
	
	<?php
}

function ml_configuration_categories_div()
{

	?>
	<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">Categories</h3>
	<p><span style="font-size:20px;font-weight:normal;padding:10px;">Choose which categories to include in your app's navigation menu.</span></p>
	<table style="margin-left:15px;">
		<tr valign="bottom">
			<td><h2>Add category</h2></td>

			<td><select name="category">
				<option value="0">Select a category</option>
				<?php $categories = get_categories();?>
				<?php 
					foreach($categories as $c) {
						echo "<option value='$c->cat_ID'>$c->cat_name</option>";
					}
				?>
			</select></td>
			<td>
				<input type="submit" id="ml_configuration_categories_add" 
											   value="<?php _e('Add'); ?>" />
			</td>
		</tr>
	</table>
	<table style="margin-left:15px;margin-top:50px;">

		<?php 
			global $wpdb;
			$table_name = $wpdb->prefix . "mobiloud_categories";
			
			$ml_categories = ml_categories();
			$ml_prev_cat = 0;
			foreach($ml_categories as $cat) {
				echo "<tr><td><h2>$cat->name</h2></td>";
				echo "<td><h2><a class='remove-category' data-cat='$cat->cat_ID'>remove</a></h2></td>";
				if($ml_prev_cat > 0) {
					echo "<td><a class='move-category-up' data-cat='$cat->cat_ID' data-prev-cat='$ml_prev_cat'>[move up]</a></td>";					
				}
				echo "</tr>";
				$ml_prev_cat = $cat->cat_ID;
			}
		?>

	</table>

<?php
}
?>