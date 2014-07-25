<?php
add_action('wp_ajax_ml_configuration_article_list', 'ml_configuration_article_list_callback');


function ml_configuration_article_list_callback()
{

	if(isset($_POST['posttypes']))
	{
		ml_set_generic_option("ml_article_list_include_post_types",$_POST['posttypes']);
		$ml_article_list_include_post_types = get_option('ml_article_list_include_post_types');
	}

	if(isset($_POST['categories']))
	{
		ml_set_generic_option("ml_article_list_exclude_categories",$_POST['categories']);
		$sticky_category_2 = get_option('ml_article_list_exclude_categories');
	}
	
	ml_configuration_article_list();
	
	die();

}

function ml_configuration_article_list_ajax_load()
{
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var data = {
			action: 'ml_configuration_article_list'
		};
		jQuery("#ml_configuration_article_list").css("display","none");
			
		$.post(ajaxurl, data, function(response) {
			//saving the result and reloading the div
			jQuery("#ml_configuration_article_list").html(response).fadeIn().slideDown();
		});			
			
	});
	</script>
	<?php
}

function ml_configuration_article_list()
{

	ml_configuration_article_list_div();

	?>

	
	<script type="text/javascript" >
		jQuery(document).ready(function($) {

		jQuery("#ml_configuration_article_list_submit").click(function(){
			jQuery("#ml_configuration_article_list_submit").val("<?php _e('Saving...'); ?>");
			jQuery("#ml_configuration_article_list_submit").attr("disabled", true);
			jQuery("#ml_configuration_article_list").css("opacity","0.5");
			
			var ptvals = [];
			$(':checkbox:checked[name^=posttypes]').val(function() {
			   ptvals.push(this.value);
			});
			var postTypesV = ptvals.join(',');
			
			var cvals = [];
			$(':checkbox:not(:checked[name^=categories])').val(function() {
			   cvals.push(this.value);
			});
			var categoriesV = cvals.join(',');

			var data = {
				action: 'ml_configuration_article_list',
				posttypes: postTypesV,
				categories: categoriesV,
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_configuration_article_list").html(response).fadeIn();
				jQuery("#ml_configuration_article_list_submit").val("<?php _e('Save'); ?>");
				jQuery("#ml_configuration_article_list_submit").attr("disabled", false);
				jQuery("#ml_configuration_article_list").css("opacity","1.0");

			});			
			
		});
	});
	</script>
	
	
	<?php
}

function ml_configuration_article_list_div()
{	
	?>
	<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">Article List Configuration</h3>


	<div style="margin-left"><p><span style="font-size:20px;font-weight:normal;padding:10px;">Select which post types to include in your article list</span></p>
	<?php
	
    $posttypes = get_post_types('','names'); 
	$includedPostTypes = explode(",",get_option("ml_article_list_include_post_types","post"));
	
	
    foreach( $posttypes as $v ) {
		if($v!="attachment" && $v!="revision" && $v!="nav_menu_item"){
			echo "<h2 style='font-size:20px;font-weight:normal;padding:2px;'>";
			echo "<input type='checkbox' name='posttypes[]' value='" . $v . "' ";
			if(in_array($v,$includedPostTypes)){
				echo 'checked';
			} else {
				echo '';
			}
			echo " />";
			echo $v;
			echo "</h2>";
		}
    }

	
	?>
	</div>
    
	<div style="margin-left;margin-top:20px"><p><span style="font-size:20px;font-weight:normal;padding:10px;">Select which categories to include in your article list</span></p>
	<?php
	
    $categories = get_categories('orderby=name');  
    $wp_cats = array();  
	
	$excludedCategories = explode(",",get_option("ml_article_list_exclude_categories",""));

    foreach( $categories as $category_list ) {  
        $wp_cats[$category_list->cat_ID] = $category_list->cat_name;  
    }  

    foreach( $wp_cats as $v ) {
		 echo "<h2 style='font-size:20px;font-weight:normal;padding:2px;'>";
        echo "<input type='checkbox' name='categories[]' value='" . $v . "' ";
		if(in_array($v,$excludedCategories)){
			echo '';
		} else {
			echo 'checked';
		}
		 echo " />";
        echo $v;
        echo '</h2>';
    }

	
	?>
	</div>
    
	<div style="margin-right:20px;">
		<p class="submit" align="right">
			<input type="submit" id="ml_configuration_article_list_submit" 
											   value="<?php _e('Save'); ?>" />
		</p>
	</div>
<?php
}
?>