<?php
include_once dirname( __FILE__ ) . '/notification_categories.php';

//function that sets the last notified post
function ml_set_post_id_as_notified($postID)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_notifications";
	$wpdb->insert( 
		$table_name, 
		array( 
			'time' => current_time("timestamp"),
			'post_id' => $postID, 
		)
	);	
}

function ml_is_notified($post_id)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_notifications";
	$num = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM %s WHERE post_id = %d",$table_name, $post_id));
	return $num > 0;
}

function ml_post_published_notification($post_id)
{
	$post = get_post($post_id,OBJECT);
	if(($_POST['post_status'] == 'publish') && ($_POST['original_post_status'] != 'publish')){ // only send push if it's a new publish

	$alert = $post->post_title;
	$custom_properties = array('post_id' => $post_id);
	
	//tags
	$tags = array();
	//subscriptions
	// if(ml_subscriptions_enable()) {
	// 	$tags[] = "all";
	// 	$capabilities = ml_subscriptions_post_capabilities($post);
	// 	foreach($capabilities as $c) {
	// 		$tags[] = $c;
	// 	}
	// } else {
	$tags[] = "all";
	$categories = wp_get_post_categories($post->ID);
	foreach($categories as $c) {
		if($c != NULL) $tags[] = $c;
	}

	// ml_send_notification($alert, true,NULL,$custom_properties,$tags,$post_id);
	ml_send_notification($alert, true,NULL,$custom_properties,NULL,$post_id);

	}
}

function ml_pb_post_published_notification($post_id) {
    if(ml_is_notified($post_id) || !ml_check_post_notification_required($post_id)) {
        return;
    }
    $post = get_post($post_id,OBJECT);
    
	if(($_POST['post_status'] == 'publish') && ($_POST['original_post_status'] != 'publish')) { // only send push if it's a new publish
        $payload = array(
            'post_id' => $post_id,            
        );
        
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'single-post-thumbnail' );
        if(is_array($image)) {
            $payload['featured_image'] = $image[0];
        }  
        $tags = ml_get_post_tags($post_id);
        $tags[] = 'all';
        $data = array(
            'platform'=>array(0,1),
            'msg'=>trim($post->post_title),
            'sound'=>'default',
            'badge'=>null,
            'notags'=>true,
            'tags'=>$tags,
            'payload'=>$payload
        );
        ml_pb_send_batch_notification($data);
    }
}



//true if the notification was sent successfully
//false if there was an error
function ml_send_notification($alert, $sound=true, $badge=NULL, $custom_properties=NULL, $tags=NULL,$remote_identifier=NULL)
{
	global $ml_api_key, $ml_secret_key;
	
	//push notification only when api key is set
	if(($ml_api_key == NULL || strlen($ml_api_key) < 5) &&
		 ($ml_secret_key == NULL || strlen($ml_secret_key) < 5))
	{
		return false;
	}
	$notification = array('alert' => $alert);
	if($sound) $notification['sound'] = $sound;
	if($badge) $notification['badge'] = $badge;
	if($custom_properties) $notification['custom_properties'] = $custom_properties;
	if($tags) $notification['tags'] = $tags;

	$parameters = array(
		'api_key' => $ml_api_key,	
		'api_secret' => $ml_secret_key,	
		'notification' => $notification,
	);
	
	//postID
	if($remote_identifier)
	{
		$parameters['remote_identifier'] = "$remote_identifier";
	}	

	$request = new WP_Http;
	$headers = array('Content-Type: application/json');
	$result = $request->request(MOBILOUD_PUSH_API_PUBLISH_URL,
		array('method' => 'POST', 'timeout' => 10,'body' => $parameters, 'headers' => $headers) );
	return false;
} 

function ml_pb_send_batch_notification($data) {
    $data['msg'] = stripslashes($data['msg']);
    $json_data = json_encode($data);
    
    $headers = array(
        'X-PUSHBOTS-APPID' => get_option('ml_pb_app_id'),
        'X-PUSHBOTS-SECRET' => get_option('ml_pb_secret_key'),
        'Content-Type'=> 'application/json',
        'Content-Length'=> strlen($json_data)
    );
    $url = 'https://api.pushbots.com/push/all';
    
    $request = new WP_Http;
    $result = $request->post($url, array(
        'timeout' => 10,
        'headers' => $headers,
        'sslverify'=>false,
        'body'=>$json_data
    ));
    global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_notifications";
	$wpdb->insert( 
		$table_name, 
		array( 
			'time' => current_time("timestamp"),
			'post_id' => isset($data['payload']['post_id']) ? $data['payload']['post_id'] : null,
            'msg'=>$data['msg'],
            'android'=> is_array($data['platform']) && in_array(1, $data['platform']) ? 'Y' : 'N',
            'ios'=> is_array($data['platform']) && in_array(0, $data['platform']) ? 'Y' : 'N',
            'tags'=> is_array($data['tags']) && count($data['tags']) > 0 ? implode(",", $data['tags']) : ''
		)
	);    
}

function ml_registered_devices() {
    $request = new WP_Http;
    $headers = array(
        'X-PUSHBOTS-APPID' => get_option('ml_pb_app_id'),
        'X-PUSHBOTS-SECRET' => get_option('ml_pb_secret_key'),
        'Content-Type'=> 'application/json',
        'Content-Length'=> 0
    );
    $url = 'https://api.pushbots.com/deviceToken/all';
    $result = $request->get($url, array(
        'timeout' => 10,
        'headers' => $headers,
        'sslverify'=>false
    ));
    if(isset($result['response']['code']) && $result['response']['code'] === 200) {
        $body = json_decode($result['body']);
        return $body;
    } else {
        return null;
    }
}

function ml_registered_devices_count() {    
    $devices = ml_registered_devices();
    
    return count($devices);
}

function ml_notifications($limit=null) {
    global $wpdb;
    $table_name = $wpdb->prefix . "mobiloud_notifications";
    $sql = "SELECT * FROM $table_name ORDER BY time DESC";
    if($limit != null) {
        $sql .= " LIMIT " . $limit;
    }
    return $wpdb->get_results($sql);
}

function ml_get_notification_by($filter=array()) {
    global $wpdb;
    $table_name = $wpdb->prefix . "mobiloud_notifications";
    $sql = "
        SELECT * FROM ".$table_name."
        WHERE
            msg = '".$wpdb->escape($filter['msg'])."'
    ";
    if($filter['post_id'] != null) {
        $sql .= " AND post_id = ".$wpdb->escape($filter['post_id']);
    }
    $sql .= " AND android = '".$wpdb->escape($filter['android'])."'";
    $sql .= " AND ios = '".$wpdb->escape($filter['ios'])."'";

    $results = $wpdb->get_results($sql);
    return $results;
}

function ml_get_post_tags($postId) {
    $post_categories = wp_get_post_categories( $postId );
    $tags = array();

    foreach($post_categories as $c){
        $cat = get_category( $c );
        $tags[] = $cat->slug;
    }
    return $tags;
}

function ml_check_post_notification_required($postId) {
    $notification_categories = ml_get_push_notification_categories();
    if(is_array($notification_categories) && count($notification_categories) > 0) {
        $post_categories = wp_get_post_categories( $postId );
        $found = false;
        if(is_array($post_categories) && count($post_categories) > 0) {
            foreach($post_categories as $post_category_id) {
                foreach($notification_categories as $notification_category) {
                    if($notification_category->cat_ID == $post_category_id) {
                        $found = true;
                    }
                }
            }
        }
        return $found;
    }
    return true;
}
?>