<?php
/**
 * Form Helper class
 * @package themifyBuilder
 */
final class Themify_Builder_Form {
	/**
	 * Render Form
	 * @param array $fields 
	 */
	static public function render( $fields ) {
		foreach( $fields as $field ) {
			echo $field['type'] != 'separator' ? '<div class="themify_builder_field">' : '';
			if ( isset( $field['label'] ) ) {
				echo '<div class="themify_builder_label">'.esc_html( $field['label'] ).'</div>';
			}
			echo $field['type'] != 'separator' ? '<div class="themify_builder_input">' : '';
			if ( $field['type'] != 'multi' ) {
				self::print_field( $field );
			} else {
				foreach( $field['fields'] as $field2 ) {
					self::print_field( $field2 );
				}
			}
			echo $field['type'] != 'separator' ? '</div>' : ''; // themify_builder_input
			echo $field['type'] != 'separator' ? '</div>' : ''; // themify_builder_field	
		}
	}

	/**
	 * Print the field based on field type
	 * @param array $field 
	 */
	static public function print_field( $field ) {
		switch ($field['type']) {
			
			case 'text':
				$html = '<input id="'. esc_attr( $field['id'] ) .'" name="'. esc_attr( $field['id'] ) .'" type="text" value="" class="'.esc_attr( $field['class'] ).' tfb_lb_option">';
				$html .= isset( $field['description'] ) ? wp_kses_post( $field['description'] ) : '';
				echo $html;
			break;

			case 'separator':
				echo $html = isset($field['meta']['html']) && '' != $field['meta']['html']? $field['meta']['html'] : '<hr class="meta_fields_separator" />';
			break;

			case 'image': ?>
				<input id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['id'] ); ?>" placeholder="<?php if(isset($field['value'])) echo esc_attr( $field['value'] ); ?>" class="<?php echo esc_attr( $field['class'] ); ?> themify-builder-uploader-input tfb_lb_option" type="text" /><br />
				<input type="hidden" name="<?php echo esc_attr( $field['id'] . '_attach_id' ); ?>" class="themify-builder-uploader-input-attach-id" value="">
				<div class="small">

					<?php if ( is_multisite() && !is_upload_space_available() ): ?>
						<?php echo sprintf( __( 'Sorry, you have filled your %s MB storage quota so uploading has been disabled.', 'themify' ), get_space_allowed() ); ?>
					<?php else: ?>
					<div class="themify-builder-plupload-upload-uic hide-if-no-js tf-upload-btn" id="<?php echo esc_attr( $field['id'] ); ?>themify-builder-plupload-upload-ui">
							<input id="<?php echo esc_attr( $field['id'] ); ?>themify-builder-plupload-browse-button" type="button" value="<?php esc_attr_e(__('Upload', 'themify') ); ?>" class="builder_button" />
							<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($field['id'] . 'themify-builder-plupload'); ?>"></span>
					</div> <?php _e('or', 'themify') ?> <a href="#" class="themify-builder-media-uploader tf-upload-btn" data-uploader-title="<?php esc_attr_e('Upload an Image', 'themify') ?>" data-uploader-button-text="<?php esc_attr_e('Insert file URL', 'themify') ?>"><?php _e('Browse Library', 'themify') ?></a>

					<?php endif; ?>

				</div>
				
				<p class="thumb_preview">
					<span class="img-placeholder"></span>
					<a href="#" class="themify_builder_icon small delete themify-builder-delete-thumb"></a>
				</p>

				<?php
			break;

			case 'select': ?>
				
				<select id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['id'] ); ?>" class="tfb_lb_option <?php echo esc_attr( $field['class'] ); ?>">
					<?php if( isset( $field['default'] ) ): ?>
					<option value="<?php echo esc_attr( $field['default'] ); ?>"><?php echo esc_html( $field['default'] ); ?></option>
					<?php endif;

					foreach( $field['meta'] as $option ): ?>
					<option value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['name'] ); ?></option>
					<?php endforeach; ?>

				</select>

				<?php if ( isset( $field['description'] ) ) {
					echo wp_kses_post( $field['description'] );
				} ?>

			<?php
			break;

			case 'font_select':
			$fonts = array_merge( themify_get_web_safe_font_list(), themify_get_google_web_fonts_list() );
			 ?>
				
				<select id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['id'] ); ?>" class="tfb_lb_option <?php echo esc_attr( $field['class'] ); ?>">
					<?php if( isset( $field['default'] ) ): ?>
					<option value="<?php echo esc_attr( $field['default'] ); ?>"><?php echo esc_html( $field['default'] ); ?></option>
					<?php endif;

					foreach( $fonts as $option ): ?>
					<option value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['name'] ); ?></option>
					<?php endforeach; ?>

				</select>

				<?php if ( isset( $field['description'] ) ) {
					echo wp_kses_post( $field['description'] );
				} ?>

			<?php
			break;

			case 'color': ?>
				<span class="builderColorSelect"><span></span></span>
				<input type="text" class="<?php echo esc_attr( $field['class'] ); ?> colordisplay"/>
				<input id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['id'] ); ?>" value="" class="builderColorSelectInput tfb_lb_option" type="text" />
			<?php
			break;

			case 'radio':
				foreach( $field['meta'] as $option ) {
					$checked = isset( $option['selected'] ) && $option['selected'] == true ? 'checked="checked"' : ''; ?>
					<input type="radio" id="<?php echo esc_attr( $field['id'] . '_' . $option['value'] ); ?>" name="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $option['value'] ); ?>" class="tfb_lb_option" <?php echo $checked; ?>> <label for="<?php echo esc_attr( $field['id'] . '_' . $option['value'] ); ?>"><?php echo $option['name']; ?></label>
				<?php
				}

			break;
		}
	}
}