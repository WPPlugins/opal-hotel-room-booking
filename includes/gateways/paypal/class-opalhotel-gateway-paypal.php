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

/**
 * PayPal IPN
 */
class OpalHotel_Gateway_Paypal extends OpalHotel_Abstract_Gateway {

	/* sandbox mode */
	public $sandbox = false;

	/* gateway id */
	public $id 		= null;

	/* gateway title */
	public $title 	= null;

	/* description */
	public $description = null;

	/* enabled */
	public $enabled = false;

	/* paypal email */
	public $email = false;

	/* payment url */
	public $payment_url = null;

	public function __construct() {

		/* init gateway id */
		$this->id = 'paypal';

		$this->title = __( 'Paypal', 'opal-hotel-room-booking' );

		$this->description = __( 'Pay via PayPal', 'opal-hotel-room-booking' );

		$this->enabled = get_option( 'opalhotel_paypal_enable', 1 );

		$this->email = get_option( 'opalhotel_paypal_email' );

		$this->sandbox = get_option( 'opalhotel_paypal_sandbox_mode', 0 );

		$this->init();
	}

	/* initialize */
	public function init() {

		if ( $this->sandbox ) {
			$this->payment_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		} else {
			$this->payment_url = 'https://www.paypal.com/cgi-bin/webscr';
		}

		$enabled = $this->is_enabled();
		if ( $enabled ) {
			OpalHotel()->_include( 'gateways/paypal/inc/class-opalhotel-paypal-response.php' );
			new OpalHotel_Paypal_Response( $this );
		}
	}

	/* get settings */
	public function admin_settings() {
		return array(
				array(
							'type'		=> 'section_start',
							'id'		=> 'paypal_settings',
							'title'		=> __( 'PayPal', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'PayPal IPN requires fsockopen/cURL support to update order statuses after payment.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_paypal_enable',
							'title'		=> __( 'Enable/Disable', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Enable PayPal', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_paypal_sandbox_mode',
							'title'		=> __( 'Sanbox mode', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Sandbox mode on is the test environment', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'email',
							'id'		=> 'opalhotel_paypal_email',
							'title'		=> __( 'PayPal Email', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Enter your PayPal email. This is required when take a payment', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'paypal_settings'
						)
			);
	}

	/* is enabled. ready to payment */
	public function is_enabled() {
		return $this->enabled && $this->email;
	}

	/* process payment */
	public function payment_process( $order_id = null ) {
		$order = opalhotel_get_order( $order_id );
		OpalHotel::instance()->_include( 'gateways/paypal/inc/class-opalhotel-paypal-request.php' );
    	$request = new OpalHotel_Paypal_Request( $this );
    	return array(
    			'status'		=> true,
    			'redirect'		=> $request->get_request_url( $order )
    		);
	}

}
