<?php

define('MOBILOUD_CACHE_DIR', dirname ( __FILE__ )."/../cached");

function ml_cache_path_from_key($key)
{
	return MOBILOUD_CACHE_DIR."/".$key;	
}
function ml_cache_exists($key)
{
	$cached_file = ml_cache_path_from_key($key);
	return file_exists($cached_file);
}

function ml_cache_get($key)
{
	if(ml_cache_exists($key))
	{
		$cached_file = ml_cache_path_from_key($key);
		$string = file_get_contents($cached_file);
		return $string;
	}
	return NULL;
}

function ml_cache_set($key,$data)
{
	if (!file_exists(MOBILOUD_CACHE_DIR)) {
    mkdir(MOBILOUD_CACHE_DIR, 0777, true);
	}

	$cached_file = ml_cache_path_from_key($key);
	file_put_contents($cached_file,$data);
}

function ml_cache_flush()
{
	$cached_files = glob(MOBILOUD_CACHE_DIR."/*");
	foreach($cached_files as $file){ // iterate files
  	if(is_file($file)) {
    	unlink($file); // delete file  		
  	}
	}
}

function ml_flush_posts_cache($post_id)
{
	ml_cache_flush();
}



?>