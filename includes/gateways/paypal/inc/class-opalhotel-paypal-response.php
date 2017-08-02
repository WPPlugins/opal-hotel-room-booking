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

class OpalHotel_Paypal_Response {

	protected $payment = null;

	/* constructor */
	public function __construct( $gateway ) {
		$this->payment = $gateway;
		add_action( 'opalhotel_process_endpoint_reservation-notify', array( $this, 'check_response' ), 10, 2 );
		add_action( 'opalhotel_verify_order_ipn_request', array( $this, 'verify_paypal_request' ), 10, 2 );
	}

	/* init process some action verify paypal request */
	public function check_response( $endpoint = '', $query_vars ) {
		if ( isset( $query_vars['reservation-notify'] ) && ! empty( $_POST ) ) {
			$posted = wp_unslash( $_POST );
			$valid_request = $this->ipn_verfiy( $posted );
			if ( ! $valid_request ) {
				return;
			}
			$order_id = absint( $query_vars['reservation-notify'] );
			do_action( 'opalhotel_verify_order_ipn_request', $posted, $order_id );
		}
	}

	/* verify paypal request */
	public function ipn_verfiy( $posted ) {
		// Get received values from post data
		$validate_ipn = array( 'cmd' => '_notify-validate' );
		$validate_ipn += $posted;

		// Send back post vars to paypal
		$params = array(
			'body'        => $validate_ipn,
			'timeout'     => 60,
			'httpversion' => '1.1',
			'compress'    => false,
			'decompress'  => false,
			'user-agent'  => 'OpalHotel/' . OPALHOTEL_VERSION
		);

		// Post back to get a response.
		$response = wp_safe_remote_post( $this->payment->payment_url, $params );

		// Check to see if the request was valid.
		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && strstr( $response['body'], 'VERIFIED' ) ) {
			return true;
		}

		return false;
	}

	/* verify paypal request */
	public function verify_paypal_request( $posted, $order_id ) {
		if ( ! empty( $posted['custom'] ) && ( $order = $this->get_order( $posted['custom'] ) ) ) {

			// Lowercase returned variables.
			$posted['payment_status'] = strtolower( $posted['payment_status'] );

			// Sandbox fix.
			if ( isset( $posted['test_ipn'] ) && 1 == $posted['test_ipn'] && 'pending' == $posted['payment_status'] ) {
				$posted['payment_status'] = 'completed';
			}

			if ( method_exists( $this, 'payment_status_' . $posted['payment_status'] ) ) {
				if ( is_callable( array( $this, 'payment_status_' . $posted['payment_status'] ) ) ) {
					call_user_func( array( $this, 'payment_status_' . $posted['payment_status'] ), $order, $posted );
				} else {
					/* cancelled */
					$order->update_status( 'cancelled' );
				}
			}
		}
	}

	/* get order by paypal request raw custom data */
	public function get_order( $paypal_custom ) {
		$order = $order_id = $order_key = false;
		// We have the data in the correct format, so get the order.
		if ( ( $custom = json_decode( $paypal_custom ) ) && is_object( $custom ) ) {
			$order_id  = $custom->order_id;
			$order_key = $custom->order_key;

		// Fallback to serialized data if safe. This is @deprecated in 2.3.11
		} elseif ( preg_match( '/^a:2:{/', $paypal_custom ) && ! preg_match( '/[CO]:\+?[0-9]+:"/', $paypal_custom ) && ( $custom = maybe_unserialize( $paypal_custom ) ) ) {
			$order_id  = $custom[0];
			$order_key = $custom[1];
		}

		if ( $order_id && $order_key && ! ( $order = opalhotel_get_order( $order_id ) ) ) {
			// We have an invalid $order_id, probably because invoice_prefix has changed.
			$order_id = opalhotel_get_order_id_by_key( $order_key );
			$order    = opalhotel_get_order( $order_id );
		}

		return $order;
	}

	/* payment status completed */
	public function payment_status_completed( $order, $posted ) {
		if ( $order->get_status() === 'completed' ) {
			return;
		}

		/* validate transaction type */
		if ( ! isset( $posted['txn_type'] ) || ! in_array( $posted['txn_type'], array( 'cart', 'instant', 'express_checkout', 'web_accept', 'masspay', 'send_money' ) ) ) {
			$order->update_status( 'on-hold' );
		}

		/* validate currency */
		if ( ! isset( $posted['mc_currency'] ) && $order->payment_currency != $posted['mc_currency'] ) {
			$order->update_status( 'on-hold' );
		}

		/* validate total */
		if ( number_format( $order->total, 2, '.', '' ) != number_format( $posted['mc_gross'], 2, '.', '' ) ) {
			$order->update_status( 'on-hold' );
		}

		/* validate email */
		if ( ! isset( $posted['receiver_email'] ) && $this->payment->email != $posted['receiver_email'] ) {
			$order->update_status( 'on-hold' );
		}

		if ( 'completed' === $posted['payment_status'] ) {
			$this->payment_completed( $order, ! empty( $posted['txn_id'] ) ? sanitize_text_field( $posted['txn_id'] ) : '' );

			if ( ! empty( $posted['mc_fee'] ) ) {
				// Log paypal transaction fee.
				update_post_meta( $order->id, 'PayPal Transaction Fee', $posted['mc_fee'] );
			}

		} else {
			$order->update_status( 'on-hold' );
		}

	}

	/**
	 * Handle a refunded order.
	 */
	protected function payment_status_refunded( $order, $posted ) {
		// Only handle full refunds, not partial.
		if ( $order->get_total == ( $posted['mc_gross'] * -1 ) ) {

			// Mark order as refunded.
			$order->update_status( 'refunded' );

			$this->send_ipn_email_notification(
				sprintf( __( 'Payment for reservation %s refunded', 'opal-hotel-room-booking' ), '<a class="link" href="' . esc_url( admin_url( 'post.php?post=' . $order->id . '&action=edit' ) ) . '">' . $order->get_order_number() . '</a>' ),
				sprintf( __( 'Reservation #%s has been marked as refunded - PayPal reason code: %s', 'opal-hotel-room-booking' ), $order->get_order_number(), $posted['reason_code'] )
			);
		}
	}

	/* payment completed */
	public function payment_completed( $order = null, $txn_id = '' ) {
		$order->payment_complete( $txn_id );
	}

	/* send email notification */
	protected function send_ipn_email_notification( $subject, $message  ) {
		$email = new OpalHotel_Email();
		return $email->send( get_option( 'admin_email' ), $subject, $message );
	}

}