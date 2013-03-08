<?php
	ini_set('display_errors', 1);
	include("../../../wp-blog-header.php");

	//Mobiloud remote update script
	class MLUpgrader {
		function download_url($url) {

			//get file
    	$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    	$fd = fopen("../mobiloud_last.zip", "w");
			curl_setopt($ch, CURLOPT_FILE,$fd);

			$file = curl_exec($ch);
			curl_close($ch);
			fclose($fd);
			echo "<p>Downloaded</p>";
    	return $file;
		}


		function download() {
			$download_file = $this->download_url("http://downloads.wordpress.org/plugin/mobiloud-mobile-app-plugin.1.7.6.zip");
			return $download_file;
		}


		function unpack() {
			$zip = new ZipArchive;
    	$res = $zip->open('../mobiloud_last.zip');
			if ($res === TRUE) {
         $zip->extractTo(plugin_dir_path(__FILE__)."../");
         $zip->close();
         echo "<p>unpack ok</p>";
     	} else {
         echo 'failed';
     	}
		}

		// When the directory is not empty:
 		function rrmdir($dir,$init_dir) {
   		if (is_dir($dir)) {
     		$objects = scandir($dir);
     		foreach ($objects as $object) {
       		if ($object != "." && $object != "..") {
       			//mod for the upgrader
         		if (filetype($dir."/".$object) == "dir") {
         			$this->rrmdir($dir."/".$object, false); 
         		} 

         		else if($object != "update.php") 
         			unlink($dir."/".$object);
       		}
     		}
	      reset($objects);
	      if($init_dir == false)
	  	    rmdir($dir);
   		}
   		if($init_dir) echo "old files removed\n";
 		}

 		function verify_secret_key($secret_key) {
 			global $ml_secret_key;
 			$ml_secret_key = get_option('ml_secret_key');
 			return $ml_secret_key == $secret_key;
 		}
	}

	$upgrader = new MLUpgrader();
	if($upgrader->verify_secret_key($_GET["secret_key"]))
	{
		$upgrader->download();
		$upgrader->rrmdir(plugin_dir_path(__FILE__),true);
		$upgrader->unpack();		
		echo "<p>finished</p>";
	}
	else echo "<p>permission denied</p>";
?>
