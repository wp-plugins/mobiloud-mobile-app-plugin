<?php
//returns
//WP_User if login successful
//WP_Error if error
function ml_login_wordpress($username, $password) {
	$creds = array();
	$creds['user_login'] = $username;
	$creds['user_password'] = $password;
	$creds['remember'] = true;
	$user = wp_signon( $creds, false );
	return $user;
}

function ml_has_groups_library() {
	return (class_exists("Groups_Post_Access") && class_exists("Groups_User"));
}

function ml_subscriptions_enable() {
	return (ml_has_groups_library() && get_option('ml_subscriptions_enable') !== 'false');
}


//filter posts by capabilities for the user_id
function ml_subscriptions_filter_posts($posts,$user_id) {
	$filtered_posts = array();
	foreach($posts as $post) {
		if(Groups_Post_Access::user_can_read_post($post->ID,$user_id))
		{
			$filtered_posts[] = $post;
		}
	}
	return $filtered_posts;
}

function ml_subscriptions_post_capabilities($post) {
	$capabilities = array();
	foreach(Groups_Post_Access::get_read_post_capabilities($post->ID) as $capability) {
		if($capability != NULL) {
			$capabilities[] = $capability;
		}
	}
	return $capabilities;
}
?>