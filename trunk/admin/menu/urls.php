<?php
add_action('wp_ajax_ml_configuration_menu_urls', 'ml_configuration_menu_urls_callback');
//add_action('wp_ajax_ml_configuration_switch_menu_urls', 'ml_configuration_switch_menu_urls_callback');


function ml_configuration_menu_urls_callback()
{

	
	if(isset($_POST['url']))
	{
		$url = $_POST['url'];
		$prevUrls = get_option("ml_menu_urls",array());
		
		if($_POST["url_action"] == "delete") 
		{
			
			
			/*for($i = 0; $i < count($prevUrls); ++$i) {
				$uo = $prevUrls[$i];
				
				if(!is_array($uo)){
					$prevUrls[$i] = array("url"=>$uo,"urlTitle"=>"");
				}
				
			}*/
			
			for($i = 0; $i < count($prevUrls); ++$i) {
				$uo = $prevUrls[$i];
				if($uo['url']==$url){
					unset($prevUrls[$i]);
				}
			}
			$prevUrls = array_values($prevUrls);
						
		}
		else if($_POST["url_action"] == "add")
		{
			array_push($prevUrls,array("url"=>$url,"urlTitle"=>$_POST['urlTitle']));
		}
		
		ml_set_generic_option("ml_menu_urls",$prevUrls);
	}

	
	ml_configuration_menu_urls();
	
	die();

}

/*function ml_configuration_switch_categories_callback()
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
*/

function ml_configuration_menu_urls_ajax_load()
{
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var data = {
			action: 'ml_configuration_menu_urls'
		};
		jQuery("#ml_configuration_menu_urls").css("display","none");
			
		$.post(ajaxurl, data, function(response) {
			//saving the result and reloading the div
			jQuery("#ml_configuration_menu_urls").html(response).fadeIn().slideDown();
		});			
			
	});
	</script>
	<?php
}

function ml_configuration_menu_urls()
{

	ml_configuration_menu_urls_div();

	?>

	
	<script type="text/javascript" >
		jQuery(document).ready(function($) {

		jQuery("#ml_configuration_menu_urls_add").click(function(){
			jQuery("#ml_configuration_menu_urls_add").val("<?php _e('Saving...'); ?>");
			jQuery("#ml_configuration_menu_urls_add").attr("disabled", true);
			jQuery("#ml_configuration_menu_urls").css("opacity","0.5");
			
			var data = {
				action: 'ml_configuration_menu_urls',
				url_action: "add",
				url: jQuery("#ml_menu_url").val(),
				urlTitle: jQuery("#ml_menu_url_title").val()
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_configuration_menu_urls").html(response).fadeIn();
				jQuery("#ml_configuration_menu_urls_add").val("<?php _e('Save'); ?>");
				jQuery("#ml_configuration_menu_urls_add").attr("disabled", false);
				jQuery("#ml_configuration_menu_urls").css("opacity","1.0");

			});			
			
		});

		jQuery("a.remove-url").click(function(){
			var data = {
				action: 'ml_configuration_menu_urls',
				url_action: "delete",
				url: $(this).data("url")
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_configuration_menu_urls").html(response).fadeIn();

			});			

		});

		/*jQuery("a.move-category-up").click(function(){
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

		});*/

	});
	</script>
	
	
	<?php
}

function ml_configuration_menu_urls_div()
{

	?>
	<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">Add URLs to your app's menu</h3>
	<p><span style="font-size:20px;font-weight:normal;padding:10px;">Add external URLs to include in your app's navigation menu.</span></p>
	<table style="margin-left:15px;">
		<tr valign="bottom">
			<td><h2>Add URL</h2></td>

			<td><input id="ml_menu_url_title" placeholder="Menu item title" 
			name="ml_menu_url_title" type="text" size="8" value="" style="padding:5px;font-size:15px;margin-left:0;width:100%;"/>
            <input id="ml_menu_url" placeholder="Type the URL here" 
			name="ml_menu_url" type="text" size="8" value="" style="padding:5px;font-size:15px;margin-left:0;width:100%;"/></td>
			<td>
				<input type="submit" id="ml_configuration_menu_urls_add" 
											   value="<?php _e('Add'); ?>" />
			</td>
		</tr>
	</table>
	<table style="margin-left:15px;margin-top:50px;">

		<?php 
			$menu_urls = get_option("ml_menu_urls",array());
			
			foreach($menu_urls as $url) {
				$ut = $url['urlTitle'];
				$ul = $url['url'];
				echo "<tr><td><h2>$ut</h2><h3>$ul</h3></td>";
				echo "<td><h2><a class='remove-url' data-url='$ul'>remove</a></h2></td>";
				/*if($ml_prev_cat > 0) {
					echo "<td><a class='move-category-up' data-cat='$cat->cat_ID' data-prev-cat='$ml_prev_cat'>[move up]</a></td>";					
				}*/
				echo "</tr>";
				//$ml_prev_cat = $cat->cat_ID;
			}
		?>

	</table>

<?php
}
?>