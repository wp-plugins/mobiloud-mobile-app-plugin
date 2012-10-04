<?php
//loading
add_action('wp_ajax_ml_configuration_notifications', 'ml_configuration_notifications_callback');

//sending test notification
add_action('wp_ajax_ml_configuration_notifications_send_message', 'ml_configuration_notifications_send_message_callback');

add_action('wp_ajax_ml_configuration_notifications_start_service', 'ml_configuration_notifications_start_service_callback');
add_action('wp_ajax_ml_configuration_notifications_stop_service', 'ml_configuration_notifications_stop_service_callback');

function ml_configuration_notifications_callback()
{
	
	ml_configuration_notifications();
	
	die();
}

function ml_configuration_notifications_send_message_callback()
{
	$message = $_POST["message"];
	ml_send_notification($message);
	ml_configuration_notifications();
	die();
}


function ml_configuration_notifications_start_service_callback()
{
	ml_start_service();
	sleep(3);
	ml_configuration_notifications();
	die();
}


function ml_configuration_notifications_stop_service_callback()
{
	ml_stop_service();
	sleep(3);
	ml_configuration_notifications();
	die();
}


function ml_configuration_notifications_ajax_load()
{
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var data = {
			action: 'ml_configuration_notifications'
		};
		jQuery("#ml_configuration_notifications").css("display","none");
			
		$.post(ajaxurl, data, function(response) {
			//saving the result and reloading the div
			jQuery("#ml_configuration_notifications").html(response).fadeIn().slideDown();
		});			
			
	});
	</script>
	<?php
}

function ml_configuration_notifications()
{

	ml_configuration_notifications_div();

	?>

	
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		jQuery("#ml_configuration_notification_message_submit").click(function(){
			var message = jQuery("#ml_configuration_notification_message_text").val();
			
			jQuery("#ml_configuration_notification_message_submit").val("<?php _e('Sending...'); ?>");
			jQuery("#ml_configuration_notification_message_submit").attr("disabled", true);
			
			var data = {
				action: 'ml_configuration_notifications_send_message',
				message: message
			};

			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_configuration_notifications").html(response);

				alert("sent");
			});
			
		});
		
		jQuery("#ml_start_service").click(function(){
			jQuery("#ml_start_service").val("<?php _e('Starting...'); ?>");
			jQuery("#ml_start_service").attr("disabled", true);
			
			
			var data = {
				action: 'ml_configuration_notifications_start_service'
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_configuration_notifications").html(response);

			});
			
		});
		
		
		jQuery("#ml_stop_service").click(function(){
			jQuery("#ml_stop_service").val("<?php _e('Stopping...'); ?>");
			jQuery("#ml_stop_service").attr("disabled", true);
			
			var data = {
				action: 'ml_configuration_notifications_stop_service'
			};
			
			$.post(ajaxurl, data, function(response) {
				//saving the result and reloading the div
				jQuery("#ml_configuration_notifications").html(response);

			});
			
		});
		
	});
	</script>
	
	
	<?php
}

function ml_configuration_notifications_div()
{
	$mobiloud_service = mobiloud_get_service_info();
	?>
	

	
	<!-- STATUS-->

	<!-- Service running ? -->
	<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">Push Notifications</h3>
	<?php if(isset($mobiloud_service) && $mobiloud_service != NULL){?>

		<h2 style="padding:10px;"><?php echo $mobiloud_service["name"]?></h2>

		<p>&nbsp;</p>

		<table style="margin-left:5px">
			<tr>
				<td><div class="mobiloud_title">Notifications sent</div></td>
				<td>:</td>
				<td><div class="mobiloud_text_normal"><?php echo $mobiloud_service["notifications"]["count"]?></div></td>
			</tr>


			<tr>
				<td><div class="mobiloud_title">Devices subscribed</div></td>
				<td>:</td>
				<td><div class="mobiloud_text_normal"><?php echo $mobiloud_service["device_count"]?></div></td>
			</tr>

			<tr>
				<td><div class="mobiloud_title">Environment</div></td>
				<td>:</td>
				<td><div class="mobiloud_text_normal"><?php echo $mobiloud_service["notifications"]["environment"]?></div></td>
			</tr>

		</table>

		<div style="text-align:left;">
				<div class="submit" style="margin-left:5px;">
			
					<?php 
						if(isset($mobiloud_service) && 
						   isset($mobiloud_service["notifications"]) &&
						   isset($mobiloud_service["notifications"]["service_running"]) &&
						   $mobiloud_service["notifications"]["service_running"])
						{
							echo "<div class='mobiloud_service_running' style='margin-left:10px;'>Push service running</div>";
							?>
							<p></p>
							<input type='submit' id='ml_stop_service' value="<?php _e('Stop push service &raquo;'); ?>">
							<?php 
						}  
						else
						{
							echo "<div class='mobiloud_service_not_running' style='margin-left:10px;'>Push service not running</div>";
							?>
							<p></p>
							<input type='submit' id='ml_start_service' value='<?php _e('Start push service &raquo;'); ?>'>
							<?php 
						
						}
				
					?>
			
				</div>
		</div>
		
		<input type="text" id="ml_configuration_notification_message_text" 
			   style="padding:5px;font-size:18px;margin-left:5%;width:80%;" 
			   placeholder="Send test message"/>
		<div class="submit" style="margin-left:80%;">
			<input type="submit" id="ml_configuration_notification_message_submit"
			value='<?php _e('Send'); ?>'/>
		</div>
	<?php
	}
}
?>