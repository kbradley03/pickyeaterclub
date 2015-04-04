<?php
/**
 * Routines for generation of custom image sizes and deletion of these sizes.
 *
 * @since 1.9.0
 * @package themify
 */

if ( ! function_exists( 'themify_do_img' ) ) {
	/**
	 * Resize images dynamically using wp built in functions
	 *
	 * @param string $img_url
	 * @param int $width
	 * @param int $height
	 * @param bool $crop
	 * @return array
	 */
	function themify_do_img( $img_url = null, $width, $height, $crop = false ) {

		$height = absint( $height );
		$width = absint( $width );
		$src = esc_url( $img_url );
		$needs_resize = true;
		$out = array(
			'url' => $src,
			'width' => $width,
			'height' => $height,
		);

		$upload_dir = wp_upload_dir();
		$base_url = $upload_dir['baseurl'];

		// Check if the image is an attachment. If it's external return url, width and height.
		if ( substr( $src, 0, strlen( $base_url ) ) != $base_url ) {
			return $out;
		}

		// Get post's attachment meta data to look for references to the requested image size
		$attachment_id = themify_get_attachment_id_from_url( $src );

		// If no relationship between a post and a image size was found, return url, width and height.
		if ( ! $attachment_id ) {
			return $out;
		}

		// Go through the attachment meta data sizes looking for an image size match.
		$meta = wp_get_attachment_metadata( $attachment_id );

		if ( is_array( $meta ) && isset( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
			foreach( $meta['sizes'] as $key => $size ) {
				if ( $size['width'] == $width && $size['height'] == $height ) {
					$src = str_replace( basename( $src ), $size['file'], $src );
					$needs_resize = false;
					break;
				}
			}
		}

		// Requested image size doesn't exists, so let's create one
		if ( $needs_resize ) {
			$out = themify_make_image_size( $attachment_id, $width, $height, $meta, $src );
		} else {
			$out = array(
				'url' => $src,
				'width' => $width,
				'height' => $height,
			);
		}

		// Return image url, width and height.
		return $out;
	}
}

if ( ! function_exists( 'themify_make_image_size' ) ) {
	/**
	 * Creates new image size.
	 *
	 * @uses get_attached_file()
	 * @uses image_make_intermediate_size()
	 * @uses wp_update_attachment_metadata()
	 * @uses get_post_meta()
	 * @uses update_post_meta()
	 *
	 * @param $attachment_id
	 * @param $width
	 * @param $height
	 * @param $meta
	 * @param $original_src
	 *
	 * @return array
	 */
	function themify_make_image_size( $attachment_id, $width, $height, $meta, $original_src ) {
		$attached_file = get_attached_file( $attachment_id );
		$resized = image_make_intermediate_size( $attached_file, $width, $height, true );
		if ( $resized && ! is_wp_error( $resized ) ) {

			// Save the new size in meta data
			$key = sprintf( 'resized-%dx%d', $width, $height );
			$meta['sizes'][$key] = $resized;
			$src = str_replace( basename( $original_src ), $resized['file'], $original_src );

			wp_update_attachment_metadata( $attachment_id, $meta );

			// Save size in backup sizes so it's deleted when original attachment is deleted.
			$backup_sizes = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );
			if ( ! is_array( $backup_sizes ) ) $backup_sizes = array();
			$backup_sizes[$key] = $resized;
			update_post_meta( $attachment_id, '_wp_attachment_backup_sizes', $backup_sizes );

			// Return resized image url, width and height.
			return array(
				'url' => esc_url( $src ),
				'width' => $width,
				'height' => $height,
			);
		}
		// Return resized image url, width and height.
		return array(
			'url' => $original_src,
			'width' => $width,
			'height' => $height,
		);
	}
}

/**
 * Disable the min commands to choose the minimum dimension, thus enabling image enlarging.
 *
 * @param $default
 * @param $orig_w
 * @param $orig_h
 * @param $dest_w
 * @param $dest_h
 * @return array
 */
function themify_img_resize_dimensions( $default, $orig_w, $orig_h, $dest_w, $dest_h ) {
	// set portion of the original image that we can size to $dest_w x $dest_h
	$aspect_ratio = $orig_w / $orig_h;
	$new_w = $dest_w;
	$new_h = $dest_h;

	if ( !$new_w ) {
		$new_w = intval( $new_h * $aspect_ratio );
	}

	if ( !$new_h ) {
		$new_h = intval( $new_w / $aspect_ratio );
	}

	$size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

	$crop_w = round( $new_w / $size_ratio );
	$crop_h = round( $new_h / $size_ratio );

	$s_x = floor( ( $orig_w - $crop_w ) / 2 );
	$s_y = floor( ( $orig_h - $crop_h ) / 2 );

	// the return array matches the parameters to imagecopyresampled()
	// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
	return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
}

/**
 * Get attachment ID for image from its url.
 *
 * @param string $url
 * @return bool|null|string
 */
function themify_get_attachment_id_from_url( $url = '' ) {
	global $wpdb;
	$attachment_id = false;

	// If there is no url, return.
	if ( '' == $url ) {
		return false;
	}

	// Get the upload directory paths
	$upload_dir_paths = wp_upload_dir();

	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
	if ( false !== strpos( $url, $upload_dir_paths['baseurl'] ) ) {

		// If this is the URL of an auto-generated thumbnail, get the URL of the original image
		$url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $url );

		// Remove the upload path base directory from the attachment URL
		$url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $url );

		// Finally, run a custom database query to get the attachment ID from the modified attachment URL
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $url ) );

	}

	return $attachment_id;
}