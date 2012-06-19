<?php

include_once dirname( __FILE__ ) . '/libs/fb/facebook.php';


function ml_facebook()
{
	global $ml_fb_app_id, $ml_fb_secret_key;
	
	$config = array();
	$config['appId'] = $ml_fb_app_id;
	$config['secret'] = $ml_fb_secret_key;
	$config['fileUpload'] = false; // optional
	$config['cookie'] = false;
	
	if($ml_fb_app_id == NULL || $ml_fb_secret_key == NULL) return NULL;
	
	$facebook = new Facebook($config);
	return $facebook;
}

function ml_facebook_get_app_info()
{
	global $ml_fb_app_id;
	$facebook = ml_facebook();
	
	if($facebook == NULL)
	{
		//no valid facebook info
		return NULL;
	}
	else
	{
		return $facebook->api("/$ml_fb_app_id");
	}
}

function ml_facebook_register_user_with_token($token=NULL)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_fb_users";
	$facebook = ml_facebook();
	
	if($facebook == NULL) return NULL;
	
	$facebook->setAccessToken($token);
	$fb_id = $facebook->getUser();
	$num = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE fb_id = $fb_id" ));
	$email = NULL;

	$user_profile = $facebook->api("/me");	

	if($user_profile)
	{
		$email = $user_profile['email'];
	}
	
	if($email)
	{
		//check if is already registered
		if($num == 1)
		{
			//already registered
			//updating email
			$wpdb->update($table_name,
				array(
				'email' => $email
				),
				array('fb_id' => $fb_id)
			);
		}
		else if($fb_id != NULL && $fb_id != 0 && $fb_id != "0")
		{
			//not registered
			$wpdb->insert( 
				$table_name, 
				array( 
					'fb_id' => "$fb_id",
					'email' => $email, 
				)
			);	
		}
		else return array();
		return array('fb_id' => $fb_id, 'email' => $email);
	}
	
	else return array();
}
function ml_facebook_get_user_from_email($email)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_fb_users";
	
	$local_users = $wpdb->get_results("SELECT * FROM $table_name WHERE email = '$email'");
	if(count($local_users) > 0) return $local_users[0];
	else return NULL;
}

function ml_facebook_get_picture_url($email,$size="square")
{
	$user = ml_facebook_get_user_from_email($email);
	if($user)
	{
		$fb_id = $user->fb_id;
		return "https://graph.facebook.com/$fb_id/picture?type=$size";
	}
	return NULL;
}


?>