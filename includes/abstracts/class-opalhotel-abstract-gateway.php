<?php
/**
 * @Author: brainos
 * @Date:   2016-04-25 23:52:13
 * @Last Modified by:   someone
 * @Last Modified time: 2016-05-14 21:17:59
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

abstract class OpalHotel_Abstract_Gateway {

	/* gateway id */
	protected $id 		= null;

	/* gateway title */
	protected $title 	= null;

	/* description */
	protected $description = null;

	public function __construct() {}

	/* get settings */
	protected function get_settings() {
		return array();
	}

	/* admin setting field */
	public function admin_settings() {
		return array();
	}

	/* is enabled */
	public function is_enabled() {
		return true;
	}

	/* payment process */
	protected function payment_process( $booking ) {}

	/* order return url */
	public function get_return_url( $order ) {
		return $order->get_checkout_order_received_url();
	}

	/* payment form if need */
	public function form() {
		return false;
	}

	/* validated payment fields */
	public function validate_fields( $fields ) {
		return true;
	}
}
