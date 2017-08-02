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

class OpalHotel_Paypal_Request {

	/* order process */
	protected $payment = null;

	/* payment url */
	protected $payment_url = null;

	public function __construct( $payment ) {
		/* payment instance */
		$this->payment = $payment;

		$this->payment_url = $this->payment->payment_url;
	}

	/* request checkout url */
	public function get_request_url( $order ) {
		$url = apply_filters( 'opalhotel_checkout_request_url', $this->payment_url . '?' . http_build_query( $this->request_args( $order ) ), $this );
		OpalHotel()->cart->empty_cart();
		return apply_filters( 'opalhotel_paypal_request_url', $url, $this, $order );
	}

	/* request args */
	protected function request_args( $order ) {
		return apply_filters( 'opalhotel_paypal_args', array(
				'cmd'           => '_xclick',
				'business'      => $this->payment->email,
				'item_name'		=> 'Reservation ID: ' . $order->get_order_number(),
				'amount'		=> $order->total,
				'no_note'       => 1,
				'currency_code' => opalhotel_get_currency(),
				'charset'       => 'utf-8',
				'rm'            => is_ssl() ? 2 : 1,
				'return'        => $this->payment->get_return_url( $order ),
				'cancel_return' => $order->get_cancel_order_url(),
				'bn'            => 'OpalHotel',
				'invoice'       => $order->get_order_number(),
				'custom'        => json_encode( array( 'order_id' => $order->id, 'order_key' => $order->order_key ) ),
				'notify_url'    => $order->get_notify_url(),
				'email'         => $order->customer_email,
				'first_name'    => $order->customer_first_name,
				'last_name'     => $order->customer_last_name,
				'address1'      => $order->customer_address,
				'city'          => $order->customer_city,
				'country'       => $order->customer_country,
            	'shipping'      => '0'
		) );
	}

}
