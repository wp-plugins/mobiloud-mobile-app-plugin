<?php
add_action('wp_ajax_ml_configuration_pages', 'ml_configuration_pages_callback');
add_action('wp_ajax_ml_configuration_switch_pages', 'ml_configuration_switch_pages_callback');
add_action('wp_ajax_ml_configuration_remove_page', 'ml_configuration_remove_page_callback');
add_action('wp_ajax_ml_app_menu_enable', 'ml_app_menu_enable_callback');



function ml_configuration_pages_callback()
{

	if(isset($_POST['page_ID']) == false)
	{
		$_POST['page_ID'] = '0';
	}

	$page_ID = intval($_POST['page_ID']);
	$id = intval($_POST['id']);
	$title = $_POST['title'];
	$url = $_POST['url'];

	if($_POST["page_action"] == "delete") 
	{
		ml_remove_page($id);			
	}
	else if($_POST["page_action"] == "add")
	{
		ml_add_page($page_ID,$title,$url);
	}

	
	ml_configuration_pages();
	
	die();

}

function ml_configuration_remove_page_callback() {
	if(isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		ml_remove_page($id);			
	}
	die();
}

function ml_configuration_switch_pages_callback()
{
	if(isset($_POST['id_a']) && isset($_POST['id_b']))
	{
		$id_a = intval($_POST['id_a']);
		$id_b = intval($_POST['id_b']);
		ml_switch_pages($id_a,$id_b);
	}
	die();
}

function ml_app_menu_enable_callback()
{
	$e = $_POST['app_menu_enable'];
	$enable = ($e == '1' || $e == 'true' || $e == 1);
	ml_set_app_menu_enable($enable);
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
			
			mlConfigurationDraggablePagesInit();
			mlConfigurationRemovePagesInit();

			jQuery("#ml_configuration_pages_add").click(function(){
				if($("select[name='page']").val() == '0') return false;
				jQuery("#ml_configuration_pages_add").val("<?php _e('Adding...'); ?>");
				jQuery("#ml_configuration_pages_add").attr("disabled", true);
				jQuery("#ml_configuration_pages").css("opacity","0.5");
				var data = {
					action: 'ml_configuration_pages',
					page_action: "add",
					page_ID: $("select[name='page']").val(),
					title: $("input[name='page[title]']").val(),
					url: $("input[name='page[url]']").val()
				};
				jQuery.post(ajaxurl, data, function(response) {
					//saving the result and reloading the div
                    
					jQuery("#ml_configuration_pages").html(response).fadeIn();
					jQuery("#ml_configuration_pages_add").val("<?php _e('Add'); ?>");
					jQuery("#ml_configuration_pages_add").attr("disabled", false);
					jQuery("#ml_configuration_pages").css("opacity","1.0");

				});			
				
			});



	});
	</script>
	
	
	<?php
}

function ml_configuration_pages_div()
{

	?>
	<label class='switch'>
		<h4>App Main Menu</h4>
		<div class='switch' id='ml-app-menu-enable'>
			<input type='checkbox' 
			<?php 
				$enable = ml_is_app_menu_enable();
				if(isset($enable) && $enable) echo " checked='checked' ";
			?>
			data-toggle='switch'>
		</div>
	</label>
	<div class='clearfix'></div>
	<legend>Add pages to your app.</legend>


	<p>&nbsp;</p>
	<table style="margin-left:15px;">
		<tr valign="top">
			<td><select name="page">
				<option value="0">Select a page..</option>
				<option value="url">External Page URL</option>

				<?php $pages = get_pages();?>
				<?php 
					foreach($pages as $p) {
						echo "<option value='$p->ID'>$p->post_title</option>";
					}
				?>
			</select></td>
		</tr>
		<tr valign='top'>
			<td><input type='text' name='page[url]' placeholder="External page url (optional)"></td>
		</tr>
		<tr valign='top'>
			<td><input type='text' name='page[title]' placeholder="Title on the app (optional)"></td>

			<td>
				<input type="submit" id="ml_configuration_pages_add" class="btn btn-primary" style="padding:5px 20px 5px 20px;"
											   value="Add" />
			</td>
		</tr>
	</table>

	<ul id='ml-conf-app-pages' class="ml-conf-pages" >

		<?php 
			global $wpdb;
			$table_name = $wpdb->prefix . "mobiloud_pages";
			
			$ml_pages = ml_pages();
			$ml_prev_page = 0;
			foreach($ml_pages as $page) {
			?>				
				<li class='page' 
					<?php if($page["id"] != NULL) 
						echo " data-page-id='"+$page["id"]+"' ";
					?>
					
					data-id='<?php echo $page["row_id"]?>'>
					
					<span class='ui-icon ui-icon-arrowthick-2-n-s'></span>
					<?php 
						if (has_post_thumbnail( $page['id'] )) {
							$image = wp_get_attachment_image_src( get_post_thumbnail_id( $page['id'] ), 'single-post-thumbnail' ); ?>
							<img src='<?php echo $image[0] ?>' style='height:50px;'>
							<?php
						}
					?>
					<b><?php echo $page['ml_page_title']?></b>
					(<?php echo $page['post_title']?>)
					<?php 
						if($page['ml_page_url'] != NULL) {
							echo "<br>".$page['ml_page_url'];
						}
					?>
					<div class='page-remove'>remove</div>
				</li>
			<?php
			}
		?>
	</ul>
	<script>
		jQuery(document).ready(function($){
		    // Switch
 			$('#ml-app-menu-enable').bootstrapSwitch();

 			$("input[name='page[url]']").hide();

 			$("select[name='page']").change(function(){
 				if($(this).val() == 'url') {
   	 			$("input[name='page[url]']").show();   	 					
 				} else {
   	 			$("input[name='page[url]']").hide();
 				}
 			});

 			$('#ml-app-menu-enable').on('switch-change', function (e, data) {
				var value = data.value;

				var data = {
					action: 'ml_app_menu_enable',
					app_menu_enable: value
				};
				$.post(ajaxurl, data, function(response) {
				});		
			});

		});
	</script>

<?php
}
?>