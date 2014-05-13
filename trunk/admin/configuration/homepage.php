<?php
add_action('wp_ajax_ml_configuration_home', 'ml_configuration_home_callback');


function ml_configuration_home_callback()
{

	global $ml_home_article_list_enabled;
	global $ml_home_page_enabled;
	global $ml_home_url_enabled;

	global $ml_home_page_id;
	global $ml_home_url;
	
	if(isset($_POST['ml_home_article_list_enabled']))
	{
		$ml_home_article_list_enabled = $_POST['ml_home_article_list_enabled'] == "true";
		ml_set_generic_option("ml_home_article_list_enabled",
							   $ml_home_article_list_enabled);
	}
	
	if(isset($_POST['ml_home_page_enabled']))
	{
		$ml_home_page_enabled = $_POST['ml_home_page_enabled'] == "true";
		ml_set_generic_option("ml_home_page_enabled",
							   $ml_home_page_enabled);
	}
	
	if(isset($_POST['ml_home_url_enabled']))
	{
		$ml_home_url_enabled = $_POST['ml_home_url_enabled'] == "true";
		ml_set_generic_option("ml_home_url_enabled",
							   $ml_home_url_enabled);
	}
	
	if(isset($_POST['ml_home_page_id']))
	{
		$ml_home_page_id = $_POST['ml_home_page_id'];
		ml_set_generic_option("ml_home_page_id",
							   $ml_home_page_id);
	}
	
	if(isset($_POST['ml_home_url']))
	{
		$ml_home_url = $_POST['ml_home_url'];
		ml_set_generic_option("ml_home_url",
							   $ml_home_url);
	}
	
	ml_configuration_home();
	
	die();

}


function ml_configuration_home_ajax_load()
{
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var data = {
			action: 'ml_configuration_home'
		};
		jQuery("#ml_configuration_home").css("display","none");
			
		$.post(ajaxurl, data, function(response) {
			//saving the result and reloading the div
			jQuery("#ml_configuration_home").html(response).fadeIn().slideDown();
		});			
			
	});
	</script>
	<?php
}

function ml_configuration_home()
{

	ml_configuration_home_div();

	?>

	
	<script type="text/javascript" >
		jQuery(document).ready(function($) {
		jQuery("#ml_configuration_home_submit").click(function(){
			
			jQuery("#ml_configuration_home_submit").val("<?php _e('Saving...'); ?>");
			jQuery("#ml_configuration_home_submit").attr("disabled", true);
			jQuery("#ml_configuration_home").css("opacity","0.5");
			
			var data = {
				action: 'ml_configuration_home',
				ml_home_article_list_enabled:  jQuery("#ml_home_article_list_enabled").is(":checked"),
				ml_home_page_enabled: jQuery("#ml_home_page_enabled").is(":checked"),
				ml_home_url_enabled: jQuery("#ml_home_url_enabled").is(":checked"),
				ml_home_page_id: jQuery("select[name='home_page']").val(),
				ml_home_url: jQuery("#ml_home_url").val(),
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_configuration_home").html(response).fadeIn();
				jQuery("#ml_configuration_home").val("<?php _e('Apply'); ?>");
				jQuery("#ml_configuration_home").attr("disabled", false);
				jQuery("#ml_configuration_home").css("opacity","1.0");

			});			
			
		});


	});
	</script>
	
	
	<?php
}

function ml_configuration_home_div()
{
	global $ml_home_article_list_enabled;
	global $ml_home_page_enabled;
	global $ml_home_url_enabled;

	global $ml_home_page_id;
	global $ml_home_url;
	
	$ml_home_article_list_enabled = get_option("ml_home_article_list_enabled",true);
	$ml_home_page_enabled = get_option("ml_home_page_enabled",false);
	$ml_home_url_enabled = get_option("ml_home_url_enabled",false);

	$ml_home_page_id = get_option("ml_home_page_id");
	$ml_home_url = get_option("ml_home_url");
	
	?>
	<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">Select a home page</h3>

	<p><span style="font-size:20px;font-weight:normal;padding:10px;">Choose which page to start your app on.

</span></p>
	<h2 style="font-size:20px;font-weight:normal;padding:10px;">
	<input id="ml_home_article_list_enabled" type="radio" name="homepagetype"
		<?php
			if($ml_home_article_list_enabled)
			{
				echo " checked ";
			}
		?>
		/> Article list
	</h2>
    
    <h2 style="font-size:20px;font-weight:normal;padding:10px;">
	<input id="ml_home_page_enabled" type="radio" name="homepagetype"
		<?php
			if($ml_home_page_enabled)
			{
				echo " checked ";
			}
		?>
		/> Page <select name="home_page">
				<option value="0">Select a page</option>
				<?php $pages = get_pages();?>
				<?php 
					foreach($pages as $p) {
						echo "<option value='$p->ID' ";
						if($ml_home_page_id == $p->ID) echo "selected='selected'";
						echo ">$p->post_title</option>";
					}
				?>
			</select>
	</h2>
	<h2 style="font-size:20px;font-weight:normal;padding:10px;">
	<input id="ml_home_url_enabled" type="radio" name="homepagetype"
		<?php
			if($ml_home_url_enabled)
			{
				echo " checked ";
			}
		?>
		/> URL <input id="ml_home_url" placeholder="Type the URL here" 
			name="ml_home_url" type="text" size="8" 
	value="<?php echo $ml_home_url;?>" 
	style="padding:5px;font-size:15px;margin-left:0;width:50%;"/>
	</h2>
	<div style="margin-right:20px;">
		<p class="submit" align="right">
			<input type="submit" id="ml_configuration_home_submit" 
											   value="<?php _e('Apply'); ?>" class="button button-primary button-large"/>
		</p>
	</div>
<?php
}
?>