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
 * TwoCheckout Standard
 */
class OpalHotel_Gateway_2checkout extends OpalHotel_Abstract_Gateway {

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
	protected $email = false;

	/* api endpoint url */
	protected $api_endpoint = null;
	protected $payment_url = null;

	protected $sellerId = null;
	protected $secret_key = null;
	protected $publish_key = null;

	protected $card_number = null;
	protected $card_exp_month = null;
	protected $card_exp_year = null;
	protected $card_cvc = null;
	protected $card_type = null;

	public function __construct() {

		/* init gateway id */
		$this->id = '2checkout-standard-checkout';

		$this->title = __( 'Standard 2Checkout', 'opal-hotel-room-booking' );

		$this->description = __( '2Checkout - Standard Checkout', 'opal-hotel-room-booking' );

		$this->init();
	}

	/* initialize */
	public function init() {

		$this->enabled = opalhotel_get_option( '2checkout_enable', 1 );
		$this->sandbox = opalhotel_get_option( '2checkout_sandbox_mode', 0 );

		$this->sellerId = opalhotel_get_option( '2checkout_seller_id' );
		$this->secret_key = opalhotel_get_option( '2checkout_private_key' );
		$this->publish_key = opalhotel_get_option( '2checkout_publish_key' );

		if ( $this->sandbox ) {
			$this->payment_url = 'https://sandbox.2checkout.com/checkout/purchase';
			$this->api_endpoint = 'https://sandbox.2checkout.com/checkout/api/1/' . $this->sellerId . '/rs/';
		} else {
			$this->payment_url = 'https://www.2checkout.com/checkout/purchase';
			$this->api_endpoint = 'https://2checkout.com/checkout/api/1/' . $this->sellerId . '/rs/';
		}
		add_action( 'opalhotel_process_endpoint_reservation-notify', array( $this, 'check_response' ), 10, 2 );
	}

	public function check_response( $endpoint = '', $query_vars ) {
		if ( ! isset( $_REQUEST['method'] ) || $_REQUEST['method'] !== $this->id ) {
			return;
		}
		if ( isset( $query_vars['reservation-notify'] ) && ! empty( $_POST ) ) {

			@ob_clean();

			$order_id = absint( $query_vars['reservation-notify'] );
			$merchant_order_id = isset( $_REQUEST['merchant_order_id'] ) ? absint( $_REQUEST['merchant_order_id'] ) : 0;
			if ( $merchant_order_id !== $order_id ) {
				return;
			}
			$order 	= opalhotel_get_order( absint( $order_id ) );

			if ( $this->sandbox == 'yes' || ( isset($_REQUEST['demo']) && $_REQUEST['demo'] == 'Y' ) ){
				$compare_string = $this->secret_key . $this->sellerId . "1" . $_REQUEST['total'];
			}else{
				$compare_string = $this->secret_key . $this->sellerId . $_REQUEST['order_number'] . $_REQUEST['total'];
			}

			$compare_hash1 = strtoupper(md5($compare_string));


			$compare_hash2 = $_REQUEST['key'];
			if ($compare_hash1 != $compare_hash2) {
				wp_die( "2Checkout Hash Mismatch... check your secret word." );
			} else {
				// set comleted payment
				$order->payment_complete();
				// Clean cart
				OpalHotel()->cart->empty_cart();
				wp_redirect( $this->get_return_url( $order ) );
				exit;
			}
		}
	}

	/* get settings */
	public function admin_settings() {
		return array(
				array(
							'type'		=> 'section_start',
							'id'		=> '2checkout_settings',
							'title'		=> __( '2Checkout', 'opal-hotel-room-booking' ),
							'desc'		=> __( '2Checkout gateway support payment by Credit Card.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_2checkout_enable',
							'title'		=> __( 'Enable/Disable', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Enable 2checkout', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_2checkout_sandbox_mode',
							'title'		=> __( 'Sanbox mode', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Sandbox mode on is the test environment', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_2checkout_seller_id',
							'title'		=> __( 'SellerID', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Your sellerID form 2Checkout', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_2checkout_publish_key',
							'title'		=> __( 'Publishable Key', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Publishable Key', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_2checkout_private_key',
							'title'		=> __( 'Private Key', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Private Key', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'section_end',
							'id'		=> '2checkout_settings'
						)
			);
	}

	/* is enabled. ready to payment */
	public function is_enabled() {
		return $this->enabled && $this->secret_key && $this->publish_key && $this->is_valid();
	}

	/**
	 * Currency valid for payment
	 */
	public function is_valid() {
		$valid_currencies = array(
			'AFN', 'ALL', 'DZD', 'ARS',
			'AUD', 'AZN', 'BSD', 'BDT',
			'BBD', 'BZD', 'BMD', 'BOB',
			'BWP', 'BRL', 'GBP', 'BND',
			'BGN', 'CAD', 'CLP', 'CNY',
			'COP', 'CRC', 'HRK', 'CZK',
			'DKK', 'DOP', 'XCD', 'EGP',
			'EUR', 'FJD', 'GTQ', 'HKD',
			'HNL', 'HUF', 'INR', 'IDR',
			'ILS', 'JMD', 'JPY', 'KZT',
			'KES', 'LAK', 'MMK', 'LBP',
			'LRD', 'MOP', 'MYR', 'MVR',
			'MRO', 'MUR', 'MXN', 'MAD',
			'NPR', 'TWD', 'NZD', 'NIO',
			'NOK', 'PKR', 'PGK', 'PEN',
			'PHP', 'PLN', 'QAR', 'RON',
			'RUB', 'WST', 'SAR', 'SCR',
			'SGD', 'SBD', 'ZAR', 'KRW',
			'LKR', 'SEK', 'CHF', 'SYP',
			'THB', 'TOP', 'TTD', 'TRY',
			'UAH', 'AED', 'USD', 'VUV',
			'VND', 'XOF', 'YER'
		);

		return in_array( opalhotel_get_currency(), $valid_currencies );
	}

	/* process payment */
	public function payment_process( $order_id = null, $page = null ) {
		$order = opalhotel_get_order( $order_id );
    	return array(
    			'status'		=> true,
    			'redirect'		=> $this->get_request_url( $order )
    		);
	}

	/* request checkout url */
	public function get_request_url( $order ) {
		$url = apply_filters( 'opalhotel_checkout_request_url', $this->payment_url . '?' . http_build_query( $this->request_args( $order ) ), $this );
		// OpalHotel()->cart->empty_cart();
		return apply_filters( 'opalhotel_2checkout_request_url', $url, $this );
	}

	/* request args */
	protected function request_args( $order ) {
		// insert secret order key
		return apply_filters( 'opalhotel_standard_2checkout_args', array(
				'sid'				=> $this->sellerId,
				'merchant_order_id'	=> $order->id,
				'mode'				=> '2CO',
				'country'			=> $order->customer_country,
				'city'				=> $order->customer_city,
				'state'				=> $order->customer_state,
				'street_address'	=> $order->customer_address,
				'phone'				=> $order->customer_phone,
				'email'				=> $order->customer_email,
				'zip'				=> $order->customer_postcode,
				'currency_code'		=> opalhotel_get_currency(),
				'card_holder_name'	=> sprintf( '%s %s', $order->customer_firstname, $order->customer_lastname ),
				'li_0_type'			=> 'product',
				'li_0_name'			=> sprintf( '%s %s', __( 'Order Num:', 'opal-hotel-room-booking' ), $order->get_order_number() ),
				'li_0_quantity'		=> 1,
				'li_0_price'		=> number_format( $order->get_total(), 2, '.', '' ),
				'li_0_tangible'		=> 'N',
				'x_receipt_link_url'=> str_replace( 'https:', 'http:', add_query_arg( 'method', $this->id, $order->get_notify_url() ) ),
				'return_url'		=> str_replace( 'https:', 'http:', add_query_arg( 'method', $this->id, $order->get_cancel_order_url() ) ),
				'demo'				=> $this->sandbox ? 'Y' : 'N'
		) );
	}

}

