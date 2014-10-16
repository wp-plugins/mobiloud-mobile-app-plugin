<?php


function mobiloud_charts()
{
	global $ml_api_key, $ml_secret_key, $ml_server_host;

	mobiloud_display_charts();
}

function mobiloud_display_charts()
{
	global $ml_api_key, $ml_secret_key;
	
	$parameters = array(
		'api_key' => $ml_api_key,	
		'secret_key' => $ml_secret_key
	);
	
	
	//SUBSCRIPTIONS
	$request = new WP_Http;
	$ml_host = "https://api.mobiloud.com";
	$url_subscriptions = "$ml_host/product/stats/devices/count";
	
	$result_subscriptions = $request->request( $url_subscriptions,
		array('method' => 'POST', 'timeout' => 10,'body' => $parameters));
		
	if($result_subscriptions)
	{
		$json_subscriptions = $result_subscriptions['body'];
	}
	
	//MODELS
	$request = new WP_Http;
	$url_models = "$ml_host/product/stats/devices/models";
	
	$result_models = $request->request( $url_models,
		array('method' => 'POST', 'timeout' => 10,'body' => $parameters));
		
	if($result_models)
	{
		$json_models = $result_models['body'];
	}
	
	//SESSIONS
	$request = new WP_Http;
	$url_sessions = "$ml_host/product/stats/events/sessions";
	
	$result_sessions = $request->request( $url_sessions,
		array('method' => 'POST', 'timeout' => 10,'body' => $parameters));
		
	if($result_sessions)
	{
		$json_sessions = $result_sessions['body'];
	}
	
	?>
	
	<script type="text/javascript" src="<?php echo $ml_host;?>/assets/highcharts.js"></script> 
	<div id="mobiloud_analytics_title" style="margin-top:20px;">
		<h1>Mobiloud Analytics</h1>
		<div style="clear:both;">
	</div>
	<table width="100%">
		<tr>
			<td align="center">
				<div id="mobiloud_subscriptions_chart" style="width: 400px; height: 400px;margin-top:50px;margin-left:50px;"></div>
			</td>
			<td align="center">
				<div id="mobiloud_sessions_chart" style="width: 400px; height: 400px;margin-top:50px;margin-left:50px;"></div>
			</td>
		</tr>
		<tr>
			<td align="center" colspan=2>
				<div id="mobiloud_models_chart" style="width: 700px; height: 400px;margin-top:50px;"></div>
			</td>
		</tr>
	</table>
	
	<script type="text/javascript">

		jQuery(document).ready(function(){
			var today = new Date();
			var oneday = 24 * 3600 * 1000;
			
			var start_date = today - oneday*7;
			var end_date = today;
			
			var result_subscriptions = <?php echo $json_subscriptions; ?>;
			var result_sessions = <?php echo $json_sessions; ?>;

			//dates
			var x_values = [];
			
			//DEVICE SUBSCRIPTIONS	
			var y_devices_subscriptions = [];
			var avg_subs = 0.0;
			
			//DEVICE USING APP PER DAY
			var y_sessions = [];
			
			//filling the dates	
			for(loopTime = start_date; loopTime <= end_date; loopTime += oneday)
			{
				
			    var d = new Date(loopTime);
				var day = d.getDate();
				var oday = day;

				var month = d.getMonth()+1;
				var omonth = month;
				
				var year = d.getUTCFullYear();
				
				if(oday < 10) oday = "0"+oday;
				if(omonth < 10) omonth = "0"+omonth;
				
				x_values.push(day + "/" + month);
				var key = year + "-" + omonth + "-" + oday;
				
				//DEVICES SUBSCRIPTIONS
				var v = result_subscriptions[key];
				if(!v) v = 0;
				y_devices_subscriptions.push(v);
				
				//DEVICE SESSIONS
				var v = result_sessions[key];
				if(!v) v = 0;
				y_sessions.push(v);				
			}
			
			//MODELS
			var result_models = <?php echo $json_models; ?>;
			var models = result_models['models'];
			
			var models_data = new Array();
			
			for(var k in models)
			{	
				var model = new Array();
				model.push(k);
				model.push(models[k]);
				models_data.push(model);
			}

		    subscriptions_chart = new Highcharts.Chart({
		    	 chart: {
		            renderTo: 'mobiloud_subscriptions_chart',
		         },
				 legend: {
							enabled: false
						},
		         title: {
		            text: 'New App Installs'
		         },
		         xAxis: {
					categories: x_values
		         },
				 yAxis: {
					title: {text: "Devices"},
					min: 0,
					allowDecimals: false,
					minRange: 5
				 },

		         series: [{
	                name: 'Devices',
					type: 'column',
					data: y_devices_subscriptions
				}]
		      });
		
		
		
		  	models_chart = new Highcharts.Chart({
		    	 chart: {
		            renderTo: 'mobiloud_models_chart',
		            type: 'pie',
					backgroundColor: 'rgba(255,255,255,0)'
		         },
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							formatter: function() {
								return '<b>'+ this.point.name +'</b><br/>'+ Math.round(this.percentage) +' %';
							}
							
						}
					}
				},
				tooltip: {
						formatter: function() {
							return '<b>'+ this.point.name +'</b>'+ Math.round(this.percentage) +' %';
						}
					},
				 legend: {
					enabled: false
				 },
		         title: {
		            text: "Models"
		         },

		         series: [{
	                name: 'Models',
					data: models_data
				}]
		      });
		
		
		
		    sessions_chart = new Highcharts.Chart({
		    	 chart: {
		            renderTo: 'mobiloud_sessions_chart',
		            type: 'column'
		         },
				 legend: {
							enabled: false
						},
		         title: {
		            text: 'Sessions'
		         },
		         xAxis: {
					categories: x_values
		         },
				 yAxis: {
					title: {text: "Devices"},
					min: 0,
					allowDecimals: false,
					minRange: 5
				 },
		         series: [{
	                name: 'Unique devices',
					data: y_sessions
				}]
		      });


		});
	</script>


	<?php


}
?>