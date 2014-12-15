<?php
ini_set('display_errors', 1);
include("../../../../wp-load.php");
include_once('functions.php');

$username = $_POST['username'];
$password = $_POST['password'];

$data = array();
$user = ml_login_wordpress($username,$password);

if(get_class($user) == "WP_User")
{
	//user
	$group_user = new Groups_User($user->ID);
	$data['user'] = array();
	$data['user']['name'] = "$user->user_firstname $user->user_lastname";
	$data['groups'] =  array();
	$data['capabilities'] = array();

	$groups = $group_user->__get('groups');
	foreach($groups as $group) {
		$g = array();
		$g['id'] = $group->group_id;
		$g['name'] = $group->name;
		$data['groups'][] = $g;

		//capabilities
		$capabilities = $group->__get('capabilities');
		if($capabilities != NULL) {
			foreach($capabilities as $capability) {
				$c = array();
				$data['capabilities'][] = $capability->__get('capability');
			}			
		}
	}
}
else {
	//error
}

echo json_encode($data);
?>