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

class OpalHotel_MetaBox_Booking_Data {

	/* render */
	public static function render( $post ) {
		require_once OPALHOTEL_PATH . '/includes/admin/metaboxes/views/booking-customer-data.php';
	}

	/* save post meta*/
	public static function save( $post_id, $post ) {
		if ( $post->post_type !== OPALHOTEL_CPT_BOOKING || empty( $_POST ) ) {
			return;
		}

		remove_action( 'save_post', array( __CLASS__, 'save' ), 10, 2 );

		foreach ( $_POST as $name => $value ) {
			if ( strpos( $name, '_customer_' ) === 0 || $name === '_transaction_id' ) {
				if ( $name === '_customer_notes' ) {
					wp_update_post( array(
							'ID' 		=> $post_id,
							'post_content'	=> $value
						) );
				} else {
					update_post_meta( $post_id, $name, $value );
				}
			}
		}

		add_action( 'save_post', array( __CLASS__, 'save' ), 10, 2 );
	}

}