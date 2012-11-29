<?php

function ml_image_resize( $img_url, $width, $height, $crop = false, $jpeg_quality = 90 ) {

	$file_path = parse_url( $img_url );
	//$file_path = ltrim( $file_path['path'], '/' );
	$file_path = rtrim( ABSPATH, '/' ).$file_path['path'];

	$orig_size = @getimagesize( $file_path );
	$image_src[0] = $img_url;
	$image_src[1] = $orig_size[0];
	$image_src[2] = $orig_size[1];

	
	$file_info = pathinfo( $file_path );
	$extension = '.'. $file_info['extension'];

	// the image path without the extension
	$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];

	$cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;

	// checking if the file size is larger than the target size
	// if it is smaller or the same size, stop right here and return
	if ( $image_src[1] > $width || $image_src[2] > $height ) {

		// the file is larger, check if the resized version already exists (for crop = true but will also work for crop = false if the sizes match)
		if ( file_exists( $cropped_img_path ) ) {

			$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
			return $cropped_img_url;
		}

		// crop = false
		if ( $crop == false ) {
		
			// calculate the size proportionaly
			$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
			$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;			

			// checking if the file already exists
			if ( file_exists( $resized_img_path ) ) {
			
				$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );
				return $resized_img_url;
			}
		}

		// no cached files - let's finally resize it
		$new_img_path = image_resize( $file_path, $width, $height, $crop, NULL,$cropped_img_path,$jpeg_quality );
		$new_img_size = @getimagesize( $new_img_path );
		$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

		// resized output
		return $new_img;
	}
	

	return $image_src[0];
}
?>