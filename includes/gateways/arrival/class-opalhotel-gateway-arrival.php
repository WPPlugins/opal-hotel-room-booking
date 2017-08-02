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

class OpalHotel_Gateway_Arrival extends OpalHotel_Abstract_Gateway {

	/* gateway id */
	public $id 		= null;

	/* gateway title */
	public $title 	= null;

	/* description */
	public $description = null;

	public function __construct() {

		/* init gateway id */
		$this->id = 'arrival';

		$this->title = __( 'Offline', 'opal-hotel-room-booking' );

		$this->description = __( 'Pay on Arrival', 'opal-hotel-room-booking' );
	}

	/* get settings */
	public function admin_settings() {
		return array(
				array(
							'type'		=> 'section_start',
							'id'		=> 'offline_settings',
							'title'		=> __( 'Pay on Arrival', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Have your customers pay with cash.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_offline_enable',
							'title'		=> __( 'Enable/Disable', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Enable payment offline', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'offline_settings'
						)
			);
	}

	/* is enabled. ready to payment */
	public function is_enabled() {
		return get_option( 'opalhotel_offline_enable', 1 );
	}

	/* process payment */
	public function payment_process( $order_id = null, $page = '' ) {
		$order = OpalHotel_Order::instance( $order_id );
		/* change status processing */
		$order->update_status( 'processing' );

		OpalHotel()->cart->empty_cart();

		$results = array(
				'status'	=> true
			);

		if ( $page && $page == opalhotel_get_page_id( 'checkout' ) ) {
			$results['redirect']	= $this->get_return_url( $order );
		} else {
			ob_start();
			echo do_shortcode( '[opalhotel_reservation step="4" reservation-received="' . $order->id . '"]' );
			$results['reservation'] = ob_get_clean();
			$results['order_received'] = $order->id;
		}

		return apply_filters( 'opalhotel_payment_arrival_result', $results, $order );
	}

}
