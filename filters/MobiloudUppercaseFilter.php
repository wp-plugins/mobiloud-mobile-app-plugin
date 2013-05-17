<?php
	class MobiloudUppercaseFilter extends MobiloudFilter {
		public function filter($post_html) {
			return strtoupper($post_html);
		}
	} 
?>