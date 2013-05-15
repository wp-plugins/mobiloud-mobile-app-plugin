<?php
	//filter using the AppStore plugin
	class MobiloudAppStoreFilter extends MobiloudFilter {
		public function header($post_id) {
			$head = $this->css();
			return $head;
		}

		public function css() {
			return "<style>.appmeta {display:block;width:320px;border:1px solid #d3d3d3;} .appicon{height:60px;float:left;}  .appicon img{width:50px;padding:5px;} .appdata {float:left;display:block;font-size:14px; width:190px;} .appdata-price {font-size:13px; float:right;margin:5px;} .appdata-price a{color:#444;} .appdata-title{float:left;clear:both;} .appdata-title a {font-size:15px; color:#444;text-decoration:none;font-weight:bold; display:block;} .appdata-developer{font-size:10px;display:block;clear:both;width:150px;} .appdata-categ{font-size:11px;}</style>";
		}

		public function filter($post_html) {

			return do_shortcode($post_html);

		}
	} 
?>