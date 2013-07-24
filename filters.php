<?php 

	class MobiloudFilter {
		public function header($post_id) {
			return "";
		}


		//filter function
		public function filter($post_html)
		{
			return $post_html;
		}

	}

	$ml_filters = Array();

	function ml_filters_init() {
		global $ml_filters;

		$fd = fopen( dirname( __FILE__ )."/filters.txt","r");
		if(!$fd) return false;
		

		while($line = fgets($fd)) {
			$line = trim($line);
			include_once dirname( __FILE__ )."/filters/".$line.".php";
			$filter = new $line;
			$ml_filters[] = $filter;
		}

		fclose($fd);
		return true;
	}

	function ml_filters_get_filtered($post_html)
	{
		global $ml_filters;

		foreach($ml_filters as $filter)
		{
			$post_html = $filter->filter($post_html);
		}
		return $post_html;
	}

	function ml_filters_header($post_id) {
		global $ml_filters;
		$head_html = "";

		foreach($ml_filters as $filter)
		{
			$head_html .= $filter->header($post_id);
		}
		return $head_html;		
	}

	ml_filters_init();

?>