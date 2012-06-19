<?php
ini_set('display_errors', 1);
require_once("../../../wp-blog-header.php");		
require_once("ml_facebook.php");


ml_facebook_register_user_with_token($_GET["token"]);

?>

