<?php

function mobiloud_home_page()
{
	global $current_user;
	get_currentuserinfo();

	?>
	<script type="text/javascript">
		var _veroq = _veroq || [];

		_veroq.push(['init', {
		  api_key: '36bd54bf9afde30628102337cf6dc4306a6a212a',
		  development_mode: true 
		  // Turn this off when you decide to 'go live'.
		} ]);

		(function() {var ve = document.createElement('script'); ve.type = 'text/javascript'; ve.async = true; ve.src = '//getvero.com/assets/m.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ve, s);})();
	</script>


	<div class="wrap">
		
		<p>&nbsp;</p>
		
		<div class="narrow full">

			<!-- GENERAL -->
			<div class="ml_homepage_general">
				<div class='img_homepage'><img src="<?php echo MOBILOUD_PLUGIN_URL;?>/app_overview.png"></div>
				<div class="content center">
					<h1 class='bigtitle'>Turn your site into a stunning app</h1>
					<h2 class='subtitle'>Enter your details below to get started</h2>
					
					<!-- FORM -->
					<form class="form-horizontal formContact" target="_blank" action="<?php echo MOBILOUD_PLUGIN_URL;?>form-mail.php" id="contactForm" method="post">
					  <div class="control-group inputGroup" >
						<div class="controls">
						  <label for="contactName">Your name</label>
						  <input type="text" id="contactName" name="contactName" placeholder="Enter your name" value='<?= $current_user->display_name ?>' required>
						</div>
					  </div>
					  <div class="control-group inputGroup">
						<div class="controls">
						  <label for="email">Your email</label>
						  <input type="email" id="email" name="email" placeholder="Enter your email" value='<?= $current_user->user_email ?>' required>
						</div>
					  </div>
					  <div class="control-group inputGroup last">
						<div class="controls">
						  <label for="website">Your website</label>
						  <input type="text" id="website" name="website" placeholder="Enter your website" value='<?= get_site_url() ?>'>
						</div>
					  </div>
					  
		  			  <input type="checkbox" name="terms" value="agree" required><span class="checkbox">I agree to Mobiloud's <a href="http://mobiloud.com/terms.php">terms of service</a> and <a href="https://www.iubenda.com/privacy-policy/435863/legal">privacy policy</span></a>
		  			  <br /><br />

						<input type="submit" value="Get started now" id="submitted" name="submitted" class="btn-submit">
	  			 					  
					</form>
					
					<p id='confirm-msg' style='display:none;'>Thanks for getting started with Mobiloud!</p>

				</div>
				
			</div>
		</div>

	</div>
	<script type="text/javascript">

		jQuery("#contactForm").submit(function(e){

			var email = jQuery("#email").val();
			var website = jQuery("#website").val();
			var name = jQuery("#contactName").val();

			_veroq.push(['user', {
			  id: email, 
			  email: email,
			  name: name,
			  website: website
			}]);

			_veroq.push(['track', 'new_app_init']); 
			
			jQuery("#email").val("");
			jQuery("#website").val("");
			jQuery("#contactName").val("");
			jQuery("#confirm-msg").attr("style", "display:block;");

		});
	</script>


	<?php
}

?>