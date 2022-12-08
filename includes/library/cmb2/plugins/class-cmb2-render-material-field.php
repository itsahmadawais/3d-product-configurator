<?php

/**
 * Handles 'material' custom field type.
 */
class CMB2_Render_Material_Field extends CMB2_Type_Base {

	/**
	 * List of states. To translate, pass array of states in the 'state_list' field param.
	 *
	 * @var array
	 */

	public static function init() {
		add_filter( 'cmb2_render_class_material', array( __CLASS__, 'class_name' ) );
		add_filter( 'cmb2_sanitize_material', array( __CLASS__, 'maybe_save_split_values' ), 12, 4 );

		/**
		 * The following snippets are required for allowing the address field
		 * to work as a repeatable field, or in a repeatable group
		 */
		add_filter( 'cmb2_sanitize_material', array( __CLASS__, 'sanitize' ), 10, 5 );
		add_filter( 'cmb2_types_esc_material', array( __CLASS__, 'escape' ), 10, 4 );
	}

	public static function class_name() { return __CLASS__; }

	/**
	 * Handles outputting the address field.
	 */
	public function render() {

		// make sure we assign each part of the value we need.
		$value = wp_parse_args( $this->field->escaped_value(), array(
			'mat_title'	=> '',
			'color'     => '',
			'map'       => '',
			'mat_price'		=> '',
			'default'	=> ''
		) );

		ob_start();
		// Do html
		?>
		<div class="rc_wc_custom_fields" style="overflow: hidden;">
			<table cellspacing="0" cellpadding="10" class="cmb_table_field">
				<tbody>
					<tr class="rc_wc_first_row">
						<td>
							<p><label for="<?php echo $this->_id( '_mat_title', false ); ?>'"><?php echo esc_html( $this->_text( 'material_mat_title_text', 'Title' ) ); ?></label></p>
							<?php echo $this->types->input( array(
								'name'  => $this->_name( '[mat_title]' ),
								'id'    => $this->_id( '_mat_title' ),
								'value' => $value['mat_title'],
							) ); ?>
						</td>
						<td>
							<p><label for="<?php echo $this->_id( '_color', false ); ?>'"><?php echo esc_html( $this->_text( 'material_color_text', 'Color' ) ); ?></label></p>
							<?php echo $this->types->colorpicker( array(
								'name'  => $this->_name( '[color]' ),
								'id'    => $this->_id( '_color' ),
								'value' => $value['color'],
							), '#' ); ?>
						</td>
						<td>
							<p><label for="<?php echo $this->_id( '_mat_price', false ); ?>'"><?php echo esc_html( $this->_text( 'material_mat_price_text', 'Price' ) ); ?></label></p>
							<?php echo $this->types->input( array(
								'name'  => $this->_name( '[mat_price]' ),
								'id'    => $this->_id( '_mat_price' ),
								'value' => $value['mat_price'],
							) ); ?>
						</td>
						<td>
							<p><label for="<?php echo $this->_id( '_default', false ); ?>'"><?php echo esc_html( $this->_text( 'material_default_text', 'Default' ) ); ?></label></p>
							<?php echo $this->types->checkbox( array(
								'name'  => $this->_name( '[default]' ),
								'id'    => $this->_id( '_default' ),
							), $value['default'] ); ?>
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<p><label for="<?php echo $this->_id( '_map', false ); ?>'"><?php echo esc_html( $this->_text( 'material_map_text', 'Texure' ) ); ?></label></p>
							<?php echo $this->types->file( array(
								'name'  => $this->_name( '[map]' ),
								'id'    => $this->_id( '_map' ),
								'value' => $value['map'],
							) ); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<p class="clear">
			<?php echo $this->_desc();?>
		</p>
		<?php

		// grab the data from the output buffer.
		return $this->rendered( ob_get_clean() );
	}

	/**
	 * Optionally save the Address values into separate fields
	 */
	public static function maybe_save_split_values( $override_value, $value, $object_id, $field_args ) {
		if ( ! isset( $field_args['split_values'] ) || ! $field_args['split_values'] ) {
			// Don't do the override
			return $override_value;
		}

		$material_keys = array( 'mat_title', 'color', 'map', 'mat_price', 'default' );

		foreach ( $material_keys as $key ) {
			if ( ! empty( $value[ $key ] ) ) {
				update_post_meta( $object_id, $field_args['id'] . 'mat_'. $key, sanitize_text_field( $value[ $key ] ) );
			}
		}

		remove_filter( 'cmb2_sanitize_material', array( __CLASS__, 'sanitize' ), 10, 5 );

		// Tell CMB2 we already did the update
		return true;
	}

	public static function sanitize( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {

		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'sanitize_text_field', $val ) );
		}

		return array_filter($meta_value);
	}

	public static function escape( $check, $meta_value, $field_args, $field_object ) {
		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'esc_attr', $val ) );
		}

		return array_filter($meta_value);
	}

}
