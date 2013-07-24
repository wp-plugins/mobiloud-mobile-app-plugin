jQuery(document).ready(function(){
	jQuery.getScript("<?php echo $plugin_charts_url.'/subscriptions.php';?>?appresso_chart_div_id=appresso_chart_subscriptions",
	function(){
		hide_loading();
	});
});

function show_loading()
{
	jQuery('#loading_wheel').fadeIn("slow");
}

function hide_loading()
{
	jQuery('#loading_wheel').fadeOut("slow");
}



function appresso_subscriptions_chart()
{
	show_loading();
	jQuery.getScript("<?php echo $plugin_charts_url.'/subscriptions.php';?>?appresso_chart_div_id=appresso_chart_subscriptions",
	function(){
		hide_loading();
	});

}
