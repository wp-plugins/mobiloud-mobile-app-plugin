<?php
add_action('wp_ajax_ml_preview_app_display', 'ml_preview_app_display');

function mobiloud_home_page() {
    global $current_user;
	get_currentuserinfo();

    if(strlen(trim(get_option('ml_preview_theme_color'))) <=2) {
        ml_set_generic_option("ml_preview_theme_color", '#1e73be');
    }
        
	$root_url = network_site_url('/');
	$plugins_url = plugins_url();
	$appname = get_bloginfo('name');
    
    mobiloud_home_page_process();
    include dirname( __FILE__ ) . '/app_preview/app_preview_page.php';
}

function ml_preview_app_display() {
    mobiloud_home_page_process();
    
    $iconShade = 'ml-icon-dark';
    if(ml_get_color_brightness(get_option('ml_preview_theme_color')) < 190) {
        $iconShade = 'ml-icon-white';
    }
    include dirname( __FILE__ ) . '/app_preview/app_preview_display.php';
    exit;
}

function ml_get_color_brightness($hex) {
    // returns brightness value from 0 to 255

    // strip off any leading #
    $hex = str_replace('#', '', $hex);

    $c_r = hexdec(substr($hex, 0, 2));
    $c_g = hexdec(substr($hex, 2, 2));
    $c_b = hexdec(substr($hex, 4, 2));

    return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}

function mobiloud_home_page_process() {
    if(isset($_POST['ml_preview_upload_image'])) {
        ml_set_generic_option("ml_preview_upload_image", $_POST['ml_preview_upload_image']);        
    }   
    if(isset($_POST['ml_preview_theme_color'])) {
        
        ml_set_generic_option("ml_preview_theme_color", $_POST['ml_preview_theme_color']);
        
    }
    if(isset($_POST['ml_preview_os'])) {
        ml_set_generic_option("ml_preview_os", $_POST['ml_preview_os']);
    }
}

function mobiloud_home_page_enqueue_scripts($hook) {
    if($hook != 'mobiloud_page_mobiloud_menu_homepage') {
        return;
    }
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_media();
    
    wp_register_script('mobiloud_app_preview',MOBILOUD_PLUGIN_URL.'/app_preview/app_preview.js', array( 'jquery', 'wp-color-picker' ));
    wp_enqueue_script('mobiloud_app_preview');
    
    wp_register_style('mobiloud_app_preview', MOBILOUD_PLUGIN_URL.'/app_preview/app_preview.css');
    wp_enqueue_style('mobiloud_app_preview');
    
    wp_enqueue_style( 'wp-color-picker' );
}

function ml_preview_get_posts() {
    $args = array(
        'posts_per_page'   => 5,
        'offset'           => 0,
        'category'         => '',
        'orderby'          => 'post_date',
        'order'            => 'DESC',
        'include'          => '',
        'exclude'          => '',
        'meta_key'         => '',
        'meta_value'       => '',
        'post_type'        => 'post',
        'post_mime_type'   => '',
        'post_parent'      => '',
        'post_status'      => 'publish',
        'suppress_filters' => true 
    );
    
    return get_posts($args);
}

function mobiloud_home_page_old()
{
	global $current_user;
	get_currentuserinfo();

	$root_url = network_site_url('/');
	$plugins_url = plugins_url();
	$appname = get_bloginfo('name');
	?>
	
<link rel='stylesheet' href="<?php echo MOBILOUD_PLUGIN_URL.'/mobiloud.css';?>" type='text/css' media='all' />

	<div class="wrap">
		
		<p>&nbsp;</p>
		
		<div class=" ull">

			<!-- GENERAL -->
			<div class="ml_homepage_general">
				<div class='img_homepage'><img src="<?php echo MOBILOUD_PLUGIN_URL;?>/app_overview.png"></div>
				<div class="content center">
					<h1 class='bigtitle'>Turn your site into a stunning app</h1>
					<!-- <h2 class='subtitle'>Enter your details below to get started</h2> -->
					
					<!-- FORM -->
					<form class="form-horizontal formContact" target="_blank" action="<?php echo MOBILOUD_PLUGIN_URL;?>/form-mail.php" id="contactForm" method="post">
		  			<input type='hidden' value="<?php echo $root_url?>" name='root_url'/>
						<input type='hidden' value="<?php echo $plugins_url?>" name='plugins_url'/>
						<input type='hidden' value="<?php echo $appname?>" name='appname'/>

					  <div class="control-group inputGroup" >
						<div class="controls">
						  <label for="contactName">Your name</label>
						  <input type="text" id="contactName" name="contactName" placeholder="Enter your name" value='<?php echo $current_user->display_name; ?>' required>
						</div>
					  </div>
					  <div class="control-group inputGroup">
						<div class="controls">
						  <label for="email">Your email</label>
						  <input type="email" id="email" name="email" placeholder="Enter your email" value='<?php echo $current_user->user_email; ?>' required>
						</div>
					  </div>
					  <div class="control-group inputGroup last">
						<div class="controls">
						  <label for="website">Your website</label>
						  <input type="text" id="website" name="website" placeholder="Enter your website" value='<?php echo get_site_url(); ?>'>
						</div>
					  </div>
					  
						<input type="submit" value="Get Started and See A Demo" id="submitted" name="submitted" class="btn-submit">
	  			 					  
					</form>
					
					
					<br/><br/><br/><br/><br/>
					
					<small> By using Mobiloud you agree to Mobiloud's <a href="http://mobiloud.com/terms.php">terms of service</a> and <a href="https://www.iubenda.com/privacy-policy/435863/legal">privacy policy</span></a> </small>

				</div>
				
			</div>
		</div>

	</div>
	<script type="text/javascript">
	
		jQuery(document).ready(function($) {
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

				_veroq.push(['track', 'get_started']); 
			
				jQuery("#confirm-msg").attr("style", "display:block;");

			});
		});
	</script>


	<?php
}

if(!function_exists('how_long_ago')){
        function how_long_ago($timestamp){
            $difference = time() - $timestamp;

            if($difference >= 60*60*24*365){        // if more than a year ago
                $int = intval($difference / (60*60*24*365));
                $s = ($int > 1) ? 's' : '';
                $r = $int . ' year' . $s . ' ago';
            } elseif($difference >= 60*60*24*7*5){  // if more than five weeks ago
                $int = intval($difference / (60*60*24*30));
                $s = ($int > 1) ? 's' : '';
                $r = $int . ' month' . $s . ' ago';
            } elseif($difference >= 60*60*24*7){        // if more than a week ago
                $int = intval($difference / (60*60*24*7));
                $s = ($int > 1) ? 's' : '';
                $r = $int . ' week' . $s . ' ago';
            } elseif($difference >= 60*60*24){      // if more than a day ago
                $int = intval($difference / (60*60*24));
                $s = ($int > 1) ? 's' : '';
                $r = $int . ' day' . $s . ' ago';
            } elseif($difference >= 60*60){         // if more than an hour ago
                $int = intval($difference / (60*60));
                $s = ($int > 1) ? 's' : '';
                $r = $int . ' hour' . $s . ' ago';
            } elseif($difference >= 60){            // if more than a minute ago
                $int = intval($difference / (60));
                $s = ($int > 1) ? 's' : '';
                $r = $int . ' minute' . $s . ' ago';
            } else {                                // if less than a minute ago
                $r = 'moments ago';
            }

            return $r;
        }
    }
?>