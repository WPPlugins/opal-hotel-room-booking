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

class OpalHotel_MetaBox_Booking_Action {

	/* render */
	public static function render( $post ) {
		require_once OPALHOTEL_PATH . '/includes/admin/metaboxes/views/booking-actions.php';
	}

	/* save post meta*/
	public static function save( $post_id, $post ) {
		if ( $post->post_type !== OPALHOTEL_CPT_BOOKING || empty( $_POST ) ) {
			return;
		}
		$order = OpalHotel_Order::instance( $post_id );

		remove_action( 'save_post', array( __CLASS__, 'save' ), 10, 2 );
		if ( isset( $_POST['_payment_method'] ) ) {
			$method = sanitize_text_field( $_POST['_payment_method'] );
			update_post_meta( $post_id, '_payment_method', $method );
			$payments = OpalHotel()->payment_gateways->get_payments();
			if ( isset( $payments[ $method ] ) ) {
				update_post_meta( $post_id, '_payment_method_title', $payments[ $method ]->title );
			}
		}

		if ( isset( $_POST['_payment_status'] ) ) {
			$status = sanitize_text_field( $_POST['_payment_status'] );
			$statuses = opalhotel_get_order_statuses();
			if ( isset( $statuses[ $status ] ) ) {
				$order->update_status( $status );
			}
		}

		update_post_meta( $post_id, '_arrival', $order->get_arrival_date() );
		update_post_meta( $post_id, '_departure', $order->get_departure_date() );
		add_action( 'save_post', array( __CLASS__, 'save' ), 10, 2 );
	}

}
