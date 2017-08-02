<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    opalhotel
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2016 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class OpalHotel_Field_Time {

	/**
	 * Current version number
	 */
	const VERSION = '1.0.0';

	/**
	 * Initialize the plugin by hooking into CMB2
	 */
	public static function init() {
		add_filter( 'cmb2_render_opalhotel_time', array( __CLASS__, 'render_map' ), 10, 5 );
		add_filter( 'cmb2_sanitize_opalhotel_time', array( __CLASS__, 'sanitize_map' ), 10, 4 );
	}

	/**
	 * Render field
	 */
	public static function render_map( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		$options = array();
		for ( $i = 0; $i < 24; $i++ ) {
			$options[] = $i . ':' . '00';
			$options[] = $i . ':' . 15;
			$options[] = $i . ':' . 30;
			$options[] = $i . ':' . 45;
		}
		?>
			<select name="<?php echo esc_attr( $field->args( '_name' ) )  ?>" id="<?php echo esc_attr( $field->args( '_name' ) )  ?>">
				<?php foreach ( $options as $option ) : ?>
					<option value="<?php echo esc_attr( $option ) ?>" <?php selected( $field_escaped_value, $option ) ?>><?php echo esc_html( $option ) ?></option>
				<?php endforeach; ?>
			</select>
		<?php
	}

	/**
	 * Optionally save the latitude/longitude values into two custom fields
	 */
	public static function sanitize_map( $override_value, $value, $object_id, $field_args ) {
		if ( isset( $field_args['split_values'] ) && $field_args['split_values'] ) {
			if ( ! empty( $value['latitude'] ) ) {
				update_post_meta( $object_id, $field_args['id'] . '_latitude', $value['latitude'] );
			}

			if ( ! empty( $value['longitude'] ) ) {
				update_post_meta( $object_id, $field_args['id'] . '_longitude', $value['longitude'] );
			}

			if ( ! empty( $value['address'] ) ) {
				update_post_meta( $object_id, $field_args['id'] . '_address', $value['address'] );
			}
		}

		return $value;
	}
}

OpalHotel_Field_Time::init();
