<?php
	//shortcode
	class MobiloudShortcodeFilter extends MobiloudFilter {

		public function filter($post_html) {

			return do_shortcode($post_html);

		}
	} 
?>