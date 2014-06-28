<?php
add_action('wp_ajax_ml_configuration_pages', 'ml_configuration_pages_callback');


function ml_configuration_pages_callback()
{

	if(isset($_POST['page_ID']))
	{
		$page_ID = intval($_POST['page_ID']);
		if($_POST["page_action"] == "delete") 
		{
			ml_remove_page($page_ID);			
		}
		else if($_POST["page_action"] == "add")
		{
			ml_add_page($page_ID);
		}
	}

	
	ml_configuration_pages();
	
	die();

}

function ml_configuration_switch_pages_callback()
{

	if(isset($_POST['page_ID_a']) && isset($_POST['page_ID_b']))
	{
		$page_ID_a = intval($_POST['page_ID_a']);
		$page_ID_b = intval($_POST['page_ID_b']);
		//ml_switch_categories($page_ID_a,$page_ID_b);
	}

	
	ml_configuration_pages();
	
	die();

}


function ml_configuration_pages_ajax_load()
{
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var data = {
			action: 'ml_configuration_pages'
		};
		jQuery("#ml_configuration_pages").css("display","none");
			
		$.post(ajaxurl, data, function(response) {
			//saving the result and reloading the div
			jQuery("#ml_configuration_pages").html(response).fadeIn().slideDown();
		});			
			
	});
	</script>
	<?php
}

function ml_configuration_pages()
{

	ml_configuration_pages_div();

	?>

	
	<script type="text/javascript" >
		jQuery(document).ready(function($) {

		jQuery("#ml_configuration_pages_add").click(function(){
			jQuery("#ml_configuration_pages_add").val("<?php _e('Adding...'); ?>");
			jQuery("#ml_configuration_pages_add").attr("disabled", true);
			jQuery("#ml_configuration_pages").css("opacity","0.5");
			
			var data = {
				action: 'ml_configuration_pages',
				page_action: "add",
				page_ID: $("select[name='page']").val()
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
                _veroq.push(['track', "menuconfig_saved"]);
                Intercom("trackUserEvent", "menuconfig_saved");
				jQuery("#ml_configuration_pages").html(response).fadeIn();
				jQuery("#ml_configuration_pages_add").val("<?php _e('Add'); ?>");
				jQuery("#ml_configuration_pages_add").attr("disabled", false);
				jQuery("#ml_configuration_pages").css("opacity","1.0");

			});			
			
		});

		jQuery("a.remove-page").click(function(){
			var data = {
				action: 'ml_configuration_pages',
				page_action: "delete",
				page_ID: $(this).data("page")
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
                _veroq.push(['track', "menuconfig_saved"]);
                Intercom("trackUserEvent", "menuconfig_saved");
				jQuery("#ml_configuration_pages").html(response).fadeIn();

			});			

		});

		jQuery("a.move-page-up").click(function(){
			var prev_cat_id = $(this).data("prev-cat");
			var cat_id = $(this).data("cat");

			var data = {
				action: 'ml_configuration_categories',
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

function ml_configuration_pages_div()
{

	?>
	<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">Add pages to your app's menu</h3>

	<p><span style="font-size:20px;font-weight:normal;padding:10px;">Choose which pages to include in your app's navigation menu.

</span></p>
	
	<table style="margin-left:15px;">
		<tr valign="bottom">
			<td><h2>Add page</h2></td>

			<td><select name="page">
				<option value="0">Select a page</option>
				<?php $pages = get_pages();?>
				<?php 
					foreach($pages as $p) {
						echo "<option value='$p->ID'>$p->post_title</option>";
					}
				?>
			</select></td>
			<td>
				<input type="submit" id="ml_configuration_pages_add" 
											   value="<?php _e('Add'); ?>" />
			</td>
		</tr>
	</table>
	<table style="margin-left:15px;margin-top:50px;">

		<?php 
			global $wpdb;
			$table_name = $wpdb->prefix . "mobiloud_pages";
			
			$ml_pages = ml_pages();
			$ml_prev_page = 0;
			foreach($ml_pages as $page) {
				echo "<tr><td><h2>$page->post_title</h2></td>";
				echo "<td><h2><a class='remove-page' data-page='$page->ID'>remove</a></h2></td>";
				if($ml_prev_cat > 0) {
					echo "<td><a class='move-page-up' data-page='$page->ID' data-prev-page='$ml_prev_page'>[move up]</a></td>";					
				}
				echo "</tr>";
				$ml_prev_page = $page->ID;
			}
		?>

	</table>
<?php
}
?>