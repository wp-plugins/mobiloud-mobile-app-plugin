<?php
include_once dirname( __FILE__ ) . '/push.php';

function mobiloud_configuration_page()
{
	global $ml_api_key, $ml_secret_key, $mobiloud_cert, $ml_cert_type, $ml_server_host, $ml_server_port;
	global $ml_has_prod_cert, $ml_has_dev_cert;


	//submit, elaboro
	$ml_connection_message = NULL;
	if(isset($_POST["send_test_notification"]))
	{
		ml_send_notification_test();
	}
	
	else if(isset($_POST["ml_start_service"]))
	{
		ml_start_service();
	}
	
	else if(isset($_POST["ml_stop_service"]))
	{
		ml_stop_service();
	}
	
	else if(isset($_POST["save_configuration"]))
	{
		//salvo api key
		if(isset($_POST['ml_api_key']))
		{
			ml_set_api_key($_POST['ml_api_key']);
			$ml_api_key = get_option('ml_api_key');
		}

		//salvo api secret
		if(isset($_POST['ml_secret_key']))
		{
			ml_set_secret_key($_POST['ml_secret_key']);
			$ml_secret_key = get_option('ml_secret_key');
		}


		if(isset($_POST['ml_cert_type']))
		{
			$ml_cert_type = $_POST['ml_cert_type'];
		}

		//salvo il certificato
		$cert_content = NULL;
		if(isset($_FILES['mobiloud_cert']))
		{
			$tmp_name = $_FILES['mobiloud_cert']['tmp_name'];
			if(file_exists($tmp_name))
			{
				$cert_content = file_get_contents($tmp_name);
			}
		}	
		
		//ml_send_certificate($cert_content,$ml_cert_type);			
		ml_update_configuration($cert_content,$ml_cert_type);
	}
	
	
	
	$mobiloud_service = mobiloud_get_service_info();
	
	?>
	
	<div></div>


	<div class="wrap">

		<div id="mobiloud_analytics_title" style="margin-top:20px;">
			<img src="<?php echo MOBILOUD_PLUGIN_URL;?>/mobiloud_36.png" style="float:left;margin-top:5px;">
			<h1 style="float:left;margin-left:20px;color:#555">Mobiloud Configuration</h3>
			<div style="clear:both;">
		</div>
		
		<p>&nbsp;</p>
		
		<!-- STATUS-->
		<div class="narrow">
	
		<div id="mobiloud_status" class="stuffbox">
			<!-- Service running ? -->
			<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">Status</h3>
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
					<form action="" method="post" id="mobiloud-start-stop-service" >
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
									<input type='submit' name='ml_stop_service' value="<?php _e('Stop push service &raquo;'); ?>">
									<?php 
								}  
								else
								{
									echo "<div class='mobiloud_service_not_running' style='margin-left:10px;'>Push service not running</div>";
									?>
									<p></p>
									<input type='submit' name='ml_start_service' value='<?php _e('Start push service &raquo;'); ?>'>
									<?php 
								
								}
						
							?>
					
						</div>

					</form>			
					
				</div>
				<p></p>
			
				<form action="" method="post" id="mobiloud-send-test" >
					<?php
						if ( isset($_POST['send_test_message']) ) {
							gsapp_send_test();
						}
					?>
					<div class="submit" style="margin-left:5px;">
						<input type="submit" name="send_test_notification" value="<?php _e('Send test notification &raquo;'); ?>" />
					</div>
					
				</form>			
				<?php } else {?>
					<h2 align="center">Not connected</h2>
					<p></p>
				<?php }?>
		</div>
			
		<p>&nbsp;</p>
		<form action="" method="post" id="mobiloud-conf" enctype="multipart/form-data">
			
				<div id="mobiloud_certificate" class="stuffbox" style="min-width:400px;">
					<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">Push Certificate</h3>

					<div class="inside">
						<div>
							<div>
								<input type="radio" name="ml_cert_type" value="production" 
								<?php if( $mobiloud_service["notifications"]["environment"] == "production")
								      { echo " checked='checked' "; }
								?>
								style="float:left;"/> 
								<h2 style="float:left;margin-top:-16px;margin-left:10px;">Production</h2>
								<div style="clear:both;">
							</div>
							<div style="margin-left:20px">
								(Recommended) Choose this option if you would like to run Mobiloud with a live blog
							</div>
						</div>

						<div style="height:30px;"></div>

						<div>
							<div>
								<input type="radio" name="ml_cert_type" value="development" 
								<?php if( $mobiloud_service["notifications"]["environment"] == "development")
								      { echo " checked='checked' "; }
								?>
								style="float:left;"/> 
								<h2 style="float:left;margin-top:-16px;margin-left:10px;">Development</h2>
								<div style="clear:both;">

							</div>
							<div style="margin-left:20px">
								(Advanced) Choose this option if you would like to run Mobiloud in development mode
							</div>
						</div>

						<div style="height:30px;"></div>

						<div>
							Select a certificate<br>
							<input type="file" id="cert" name="mobiloud_cert">
						</div>

					</div>
				</div>
			</div>
			</div>
			<div id="ml_secret_key" class="stuffbox">
				<h3 style="font-family:arial;font-size:20px;font-weight:normal;padding:10px;">Keys</h3>
				
				<h2 style="font-size:20px;font-weight:normal;padding:10px;">
					Api Key
				</h2>

				<!-- API KEY -->
				<input id="key" placeholder="Insert API KEY" name="ml_api_key" type="text"
					value="<?php echo $ml_api_key ?>" style="padding:5px;font-size:20px;margin-left:5%;width:90%;"/>
				<p></p>

				
				<!-- SECRET KEY -->
				<h2 style="font-size:20px;font-weight:normal;padding:10px;">
					Secret Key
				</h2>
				<input id="key" placeholder="Insert Secret Key" name="ml_secret_key" type="text" size="40" 
				value="<?php echo $ml_secret_key?>" 
				style="padding:5px;font-size:20px;margin-left:5%;width:90%;"/>
				<p></p>
				
				
				
			</div>
			
			
			
			<p class="submit" align="right"><input type="submit" name="save_configuration" value="<?php _e('Save'); ?>" /></p>
			
		</div>

	</form>


	</div>
	<?php
}


function mobiloud_get_service_info()
{
	global $ml_api_key, $ml_secret_key, $ml_server_host, $ml_server_port;
	
	$parameters = array('api_key' => $ml_api_key,'secret_key' => $ml_secret_key);
						
	$request = new WP_Http;
	$url = "$ml_server_host/notifications/details";
	$result = $request->request( $url, array('method' => 'POST', 
											 'timeout' => 5,
											 'body' => $parameters) );
	
	if($result != NULL && !isset($result->errors))
	{
		$dict = json_decode($result['body']);
		$service = NULL;
		if($dict)
		{
			$service = array();
			$service["name"] = $dict->name;
			$service["description"] = $dict->description;
			$service["device_count"] = $dict->device_count;
			
			$service["notifications"] = array();
			$service["notifications"]["count"] = $dict->push_sent;
			$service["notifications"]["service_running"] = $dict->is_service_running;			
			$service["notifications"]["environment"] = $dict->apn_env;	
		}
		
		return $service;
	}	
	
	return NULL;
}

function ml_update_configuration($cert_content,$ml_cert_type)
{
	global $ml_api_key, $ml_secret_key, $ml_server_host, $ml_server_port;
	
	$parameters = array('api_key' => $ml_api_key,'secret_key' => $ml_secret_key);
	if($ml_cert_type != NULL) $parameters['apn_env'] = $ml_cert_type;
	if($cert_content != NULL) $parameters['apn_cert'] = $cert_content;
	
	$request = new WP_Http;
	$url = "$ml_server_host/notifications/update";
	$result = $request->request( $url, array('method' => 'POST', 
											 'timeout' => 20,
											 'body' => $parameters) );

	if($result != NULL && !isset($result->errors))
	{
		$dict = json_decode($result['body']);
		$service = NULL;
		if($dict)
		{
			echo "<script>alert('Saved');</script>";
		}
	}
}

function ml_send_notification_test()
{
	ml_send_notification("This is a test notification from Mobiloud");
	echo "<script>alert('Sent');</script>";
}


function ml_start_service()
{
	global $ml_api_key, $ml_secret_key, $ml_server_host, $ml_server_port;
	
	$parameters = array('api_key' => $ml_api_key,'secret_key' => $ml_secret_key);
	
	$request = new WP_Http;
	$url = "$ml_server_host/notifications/admin/start_service";
	$result = $request->request( $url, array('method' => 'POST', 
											 'timeout' => 20,
											 'body' => $parameters) );
	
}


function ml_stop_service()
{
	global $ml_api_key, $ml_secret_key, $ml_server_host, $ml_server_port;
	
	$parameters = array('api_key' => $ml_api_key,'secret_key' => $ml_secret_key);
	
	$request = new WP_Http;
	$url = "$ml_server_host/notifications/admin/stop_service";
	$result = $request->request( $url, array('method' => 'POST', 
											 'timeout' => 20,
											 'body' => $parameters) );
	
}

?>