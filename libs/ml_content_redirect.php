<?php
	//Mobiloud remote update script
	class MLContentRedirect {
		public $ml_content_redirect_enable = false;
		public $ml_content_redirect_url = null;
		public $ml_content_redirect_slug = null;
		public $ml_content_redirect_app_version = null;

		function __construct() {
 			$this->ml_content_redirect_enable = get_option("ml_content_redirect_enable");
			$this->ml_content_redirect_url = get_option("ml_content_redirect_url");
			$this->ml_content_redirect_slug = get_option("ml_content_redirect_slug");
			$this->ml_content_redirect_app_version = get_option("ml_content_redirect_app_version");

			if($this->ml_content_redirect_enable == null) {
				$this->set_option("ml_content_redirect_enable",false);
			}
		}

		function set_option($a_option,$a_value)
		{
			if(!update_option($a_option,$a_value))
				add_option($a_option,$a_value);
	
			if($a_option == "ml_content_redirect_enable")
				$this->ml_content_redirect_enable = $a_value;
			else if($a_option == "ml_content_redirect_url")
				$this->ml_content_redirect_url = $a_value;
			else if($a_option == "ml_content_redirect_slug")
				$this->ml_content_redirect_slug = $a_value;
			else if($a_option == "ml_content_redirect_app_version")
				$this->ml_content_redirect_app_version = $a_value;

		}

 		function verify_secret_key($secret_key) {
 			$ml_secret_key = get_option('ml_secret_key');
 			return $ml_secret_key == $secret_key;
 		}

 		function is_valid_version($v) {
 			return $v == $this->ml_content_redirect_app_version;
 		}

 		//load http content of the other site
 		function load_content($options) {
 			$url = $this->ml_content_redirect_url."/wp-content/plugins/mobiloud-mobile-app-plugin/posts.php";

			//url-ify the data for the POST
			foreach($options as $key=>$value) { $options_string .= $key.'='.$value.'&'; }
			rtrim($options_string, '&');

    	$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);

			curl_setopt($ch,CURLOPT_POST, count($options));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $options_string);
			$response = curl_exec($ch);

			curl_close($ch);
			if($response == false) return json_encode(["posts"=>[]]);
			else return $response;
 		}

	}
?>