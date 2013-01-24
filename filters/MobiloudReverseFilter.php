<?php
	class MobiloudReverseFilter extends MobiloudFilter {
		public function filter($post_html) {
			return strrev($post_html);
		}
	} 
?>