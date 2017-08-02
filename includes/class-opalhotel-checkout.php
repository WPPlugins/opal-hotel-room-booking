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

class OpalHotel_Checkout {

	/* required checkout field */
	public $required_fields = array();

	/* error fields */
	public $error_fields = array();

	/* constructor */
	public function __construct() {

		$this->required_fields = array(
				'customer_first_name'	=> __( '<strong>First Name</strong> is a required field.', 'opal-hotel-room-booking' ),
				'customer_last_name'	=> __( '<strong>Last Name</strong> is a required field.', 'opal-hotel-room-booking' ),
				'customer_email'		=> __( '<strong>Email</strong> is a required field.', 'opal-hotel-room-booking' ),
				'customer_phone'		=> __( '<strong>Phone</strong> is a required field.', 'opal-hotel-room-booking' ),
				'customer_state'		=> __( '<strong>State</strong> is a required field.', 'opal-hotel-room-booking' ),
				'customer_city'			=> __( '<strong>City</strong> is a required field.', 'opal-hotel-room-booking' ),
				'customer_country'		=> __( '<strong>Country</strong> is a required field.', 'opal-hotel-room-booking' ),
				'customer_postcode'		=> __( '<strong>Postcode</strong> is a required field.', 'opal-hotel-room-booking' )
			);

		$this->checkout_fields = array(
				'customer_first_name'	=> array(
						'type'		=> 'text',
						'required'	=> true,
						'lalbel'	=> __( 'First Name', 'opal-hotel-room-booking' )
					),
				'customer_last_name'	=> array(
						'type'		=> 'text',
						'required'	=> true,
						'lalbel'	=> __( 'Last Name', 'opal-hotel-room-booking' )
					),
				'customer_email'	=> array(
						'type'		=> 'email',
						'required'	=> true,
						'lalbel'	=> __( 'Email', 'opal-hotel-room-booking' )
					),
				'customer_phone'	=> array(
						'type'		=> 'tel',
						'required'	=> true,
						'lalbel'	=> __( 'Phone', 'opal-hotel-room-booking' )
					),
				'customer_state'	=> array(
						'type'		=> 'text',
						'required'	=> true,
						'lalbel'	=> __( 'State', 'opal-hotel-room-booking' )
					),
				'customer_city'	=> array(
						'type'		=> 'text',
						'required'	=> true,
						'lalbel'	=> __( 'City', 'opal-hotel-room-booking' )
					),
				'customer_country'	=> array(
						'type'		=> 'select',
						'required'	=> true,
						'lalbel'	=> __( 'Country', 'opal-hotel-room-booking' )
					),
				'customer_postcode'	=> array(
						'type'		=> 'text',
						'required'	=> true,
						'lalbel'	=> __( 'Postcode', 'opal-hotel-room-booking' )
					),
				'customer_address'	=> array(
						'type'		=> 'textarea',
						'required'	=> false,
						'label'		=> __( 'Address', 'opal-hotel-room-booking' ),
						'placeholder'	=>	__( 'Enter your address.', 'opal-hotel-room-booking' )
					),
				'customer_notes'			=> array(
						'type'		=> 'textarea',
						'required'	=> false,
						'label'		=> __( 'Additional Notes', 'opal-hotel-room-booking' ),
						'placeholder'	=>	__( 'Notes.', 'opal-hotel-room-booking' )
					)
			);	

		/* apply filter require checkout field */
		$this->required_fields = apply_filters( 'opalhotel_required_checkout_field', $this->required_fields );

		/* customer info */
		add_action( 'opalhotel_checkout_customer_form', array( $this, 'checkout_customer_form' ), 10 );

		/**
		 * Print Coupon input
		 */
		add_action( 'opalhotel_checkout_review', array( $this, 'coupon' ), 10 );

		/**
		 * Preview order
		 */
		add_action( 'opalhotel_checkout_review', array( $this, 'review' ), 15 );

		/**
		 * print payment methods select
		 */
		add_action( 'opalhotel_checkout_payment_method', array( $this, 'payment_methods' ), 5 );

		/* empty cart */
		add_action( 'init', array( $this, 'empty_cart' ) );
	}

	public function checkout_form() {
		do_action( 'opalhotel_checkout_room_available' );

		global $wp;
		$checkout_page_id = opalhotel_get_page_id( 'checkout' );
		$checkout = get_post( $checkout_page_id );
		if ( isset( $wp->query_vars['pagename'], $wp->query_vars['reservation-received'] ) && ( $checkout && $wp->query_vars['pagename'] === $checkout->post_name ) && $wp->query_vars['reservation-received'] ) {
			$order_id = absint( $wp->query_vars['reservation-received'] );
			/* order received */
			opalhotel_get_template( 'checkout/order-received.php', array( 'order' => opalhotel_get_order( $order_id ) ) );
		} else if ( isset( $_REQUEST['reservation-received'] ) && get_post( $_REQUEST['reservation-received'] ) ) {
			/* order received */
			opalhotel_get_template( 'checkout/order-received.php', array( 'order' => opalhotel_get_order( $_REQUEST['reservation-received'] ) ) );
		} else if ( OpalHotel()->cart->is_empty || empty( OpalHotel()->cart->cart_contents ) ) {
			/* cart is empty */
			opalhotel_get_template( 'checkout/empty.php' );
		} else {
			/* cart is not empty */
			opalhotel_get_template( 'checkout/checkout.php' );
		}
	}

	/* customer info */
	public function checkout_customer_form() {
		opalhotel_get_template( 'checkout/customer-info.php' );
	}

	public function payment_methods() {
		opalhotel_get_template( 'checkout/payment-method.php' );
	}

	public function empty_cart() {
		/* opalhotel_is_endpoint_url( 'reservation-received' ) &&  */
		if ( isset( $_REQUEST['empty_cart'] ) && $_REQUEST['empty_cart'] == 1 ) {
			OpalHotel()->cart->empty_cart();
		}
	}

	/* order review */
	public function review() {
		opalhotel_get_template( 'checkout/review.php' );
	}

	/* coupon */
	public function coupon() {
		opalhotel_get_template( 'checkout/coupon.php' );
	}

	/* check avaialble again */
	public function check_available() {
		do_action( 'opalhotel_checkout_room_available' );
	}

	/* create order */
	public function create_order( $posted = null ) {

		$order_id = null;

		try {
			$order_id = OpalHotel()->session->order_waiting_payment;
			if ( $order_id && $order_id > 0 && ( ( $order = OpalHotel_Order::instance( $order_id ) ) && in_array( $order->get_status(), array( 'on-hold', 'pending' ) ) ) ) {
				/* remove order items */
				$order->remove_order_items();
			} else {
				$order_id = opalhotel_create_new_order();
				OpalHotel()->session->order_waiting_payment = $order_id;
			}

			$arrival = $departure = false;
			/* add order items */
			$rooms = OpalHotel()->cart->get_rooms();
			foreach ( $rooms as $cart_room_id => $room ) {
				$room_id = isset( $room['product_id'] ) ? absint( $room['product_id'] ) : 0;
				$arrival = isset( $room['arrival'] ) ? sanitize_text_field( $room['arrival'] ) : 0;
				$departure = isset( $room['departure'] ) ? sanitize_text_field( $room['departure'] ) : 0;
				$qty = isset( $room['qty'] ) ? absint( $room['qty'] ) : 0;

				$room['arrival'] = strtotime( $arrival );
				$room['departure'] = strtotime( $departure );
				$name = $room['data']->get_title();

				if ( $room_id && $arrival && $departure && $qty && $name ) {
					$order_item_id = opalhotel_add_order_item( $order_id, array(
							'order_item_name'		=> $name,
							'order_item_type'		=> 'room',
							'order_item_parent'		=> null
						) );

					/* add item meta */
					foreach ( $room as $meta => $value ) {
						if ( $meta !== 'data' ) {
							opalhotel_add_order_item_meta( $order_item_id, $meta, $value );
						}
					}
				}

				/* add package item */
				$packages = OpalHotel()->cart->get_packages( $cart_room_id );
				if ( $packages ) {
					foreach ( $packages as $package ) {
						$package_id = isset( $package['product_id'] ) ? absint( $package['product_id'] ) : 0;
						if ( $package_id ) {
							$order_package_item_id = opalhotel_add_order_item( $order_id, array(
									'order_item_name'		=> $package['data']->get_title(),
									'order_item_type'		=> 'package',
									'order_item_parent'		=> $order_item_id
								) );

							/* add item meta */
							foreach ( $package as $meta => $value ) {
								if ( $meta !== 'data' ) {
									opalhotel_add_order_item_meta( $order_package_item_id, $meta, $value );
								}
							}
						}
					}
				}

				/* create order item hook */
				do_action( 'opalhotel_create_order_item', $order_id, $room );
			}

			if ( isset( $posted['customer_notes'] ) && $posted['customer_notes'] ) {
				opalhotel_update_order( array( 'ID' => $order_id, 'post_content' => $posted['customer_notes'] ) );
			}

			/* add order meta */
			add_post_meta( $order_id, '_customer_first_name', $posted['customer_first_name'] );
			add_post_meta( $order_id, '_customer_last_name', $posted['customer_last_name'] );
			add_post_meta( $order_id, '_customer_email', $posted['customer_email'] );
			add_post_meta( $order_id, '_customer_phone', $posted['customer_phone'] );
			add_post_meta( $order_id, '_customer_state', $posted['customer_state'] );
			add_post_meta( $order_id, '_customer_city', $posted['customer_city'] );
			add_post_meta( $order_id, '_customer_address', $posted['customer_address'] );
			add_post_meta( $order_id, '_customer_country', $posted['customer_country'] );
			add_post_meta( $order_id, '_subtotal', OpalHotel()->cart->get_cart_subtotal_display() );
			add_post_meta( $order_id, '_tax_total', OpalHotel()->cart->tax_total );
			add_post_meta( $order_id, '_total', OpalHotel()->cart->get_cart_total() );
			if ( OpalHotel()->cart->coupons ) {
				add_post_meta( $order_id, '_coupon', OpalHotel()->cart->coupons );
				add_post_meta( $order_id, '_coupon_discount', OpalHotel()->cart->coupon_discounts[ OpalHotel()->cart->coupons['code'] ] );
			}
			add_post_meta( $order_id, '_payment_method', $posted['payment_method'] );
			$payments = OpalHotel()->payment_gateways->get_payments();
			$payment = $payments[ $posted['payment_method'] ];
			add_post_meta( $order_id, '_payment_method_title', $payment->title );
			if ( is_user_logged_in() ) {
				add_post_meta( $order_id, '_customer_id', get_current_user_id() );
			}
			add_post_meta( $order_id, '_payment_currency', opalhotel_get_currency() );
			add_post_meta( $order_id, '_order_key', uniqid( 'resevation-key' ) );

			add_post_meta( $order_id, '_arrival', $room['arrival'] );
			add_post_meta( $order_id, '_departure', $room['departure'] );

			do_action( 'opalhotel_reservartion_create_order', $order_id );

		} catch ( Exception $e ) {
			opalhotel_add_notices( sprintf( __( '%s. Can not create your reservation. Please try again.' ), $e->getMessage() ) );
		}

		return $order_id;
	}

	/* process checkout */
	public function process_checkout() {

		try {

			if ( ! isset( $_POST['opalhotel_checkout_nonce'] ) || ! wp_verify_nonce( $_POST['opalhotel_checkout_nonce'], 'opalhotel-checkout' ) ) {
				throw new Exception( __( 'Your request is invalid. Please try again.', 'opal-hotel-room-booking' ) );
			}

			@set_time_limit( 0 );

			if ( Opalhotel()->cart->is_empty || empty( OpalHotel()->cart->cart_contents ) ) {
				throw new Exception( __( 'Sorry, you cannot make a reservation right now because your session has expired.', 'opal-hotel-room-booking' ) );
			}

			do_action( 'opalhotel_process_checkout' );

			/* sanitize post */
			$this->posted = wp_unslash( $_POST );

			foreach ( $this->posted as $fullname => $value ) {
				if ( strpos( $fullname, 'opalhotel_' ) === 0 ) {
					$name = substr( $fullname, 10 );
					unset( $this->posted[ $fullname ] );
					$this->posted[ $name ] = $value;
				}
			}

			/* lower validate data */
			$post_names = array_keys( $this->posted );
			foreach ( $this->required_fields as $name => $message ) {
				if ( ! array_key_exists( $name, $this->posted ) || empty( $this->posted[ $name ] ) ) {
					$this->error_fields[] = $name;
					opalhotel_add_notices( $message, 'error' );
				}
			}

			/* last name */
			if ( isset( $this->posted['customer_first_name'] ) && $this->posted['customer_first_name'] && strlen( $this->posted['customer_first_name'] ) <= 1 ) {
				$this->error_fields[] = 'customer_first_name';
				opalhotel_add_notices( __( '<strong>First Name</strong> is too short.', 'opal-hotel-room-booking' ), 'error' );
			}

			/* last name */
			if ( isset( $this->posted['customer_last_name'] ) && $this->posted['customer_last_name'] && strlen( $this->posted['customer_last_name'] ) <= 1 ) {
				$this->error_fields[] = 'customer_last_name';
				opalhotel_add_notices( __( '<strong>Last Name</strong> is too short.', 'opal-hotel-room-booking' ), 'error' );
			}

			/* validate email */
			if ( isset( $this->posted['customer_email'] ) && $this->posted['customer_email'] && ! is_email( $this->posted['customer_email'] ) ) {
				$this->error_fields[] = 'customer_email';
				opalhotel_add_notices( sprintf( __( '<strong>%s</strong> is not a valid email address.', 'opal-hotel-room-booking' ), $this->posted['customer_email'] ), 'error' );
			}

			/* validate postcode */
			if ( isset( $this->posted['customer_postcode'], $this->posted['customer_country'] ) && $this->posted['customer_postcode'] && ! opalhotel_validate_postcode( $this->posted['customer_country'], $this->posted['customer_postcode'] ) ) {
				$this->error_fields[] = 'customer_postcode';
				opalhotel_add_notices( sprintf( __( '<strong>%s</strong> is not a valid postcode.', 'opal-hotel-room-booking' ), $this->posted['customer_postcode'] ), 'error' );
			}

			/* validate postcode */
			if ( isset( $this->posted['customer_phone'] ) && $this->posted['customer_phone'] && ! opalhotel_validate_phone( $this->posted['customer_phone'] ) ) {
				$this->error_fields[] = 'customer_phone';
				opalhotel_add_notices( sprintf( __( '<strong>%s</strong> is not a valid phone number.', 'opal-hotel-room-booking' ), $this->posted['customer_phone'] ), 'error' );
			}

			/* term and conditional */
			if ( get_option( 'opalhotel_terms_require', false ) && ! isset( $this->posted['term_conditional'] ) ) {
				opalhotel_add_notices( __( '<strong>Term and Conditional</strong> is require field. Please accept the our terms.', 'opal-hotel-room-booking' ), 'error' );
			}

			/* validate field */
			$payment = OpalHotel()->payment_gateways->get_payment_process( $this->posted['payment_method'] );
			if ( $payment && OpalHotel()->cart->need_payment() ) {
				$validate_payment_fields = $payment->validate_fields( $this->posted );
				if ( is_array( $validate_payment_fields ) && ! empty( $validate_payment_fields ) ) {
					foreach ( $validate_payment_fields as $code => $error ) {
						$this->error_fields[] = $code;
						opalhotel_add_notices( $error, 'error' );
					}
				}
			}

			/* check room available again to make sure rooms is available */
			do_action( 'opalhotel_checkout_room_available' );

			/* have no error process next step */
			if ( ! opalhotel_count_notices( 'error' ) ) {

				/* process create order */
				$order_id = $this->create_order( $this->posted );

				if ( OpalHotel()->cart->need_payment() ) {
					$page = isset( $this->posted['page'] ) && $this->posted['page'] ? $this->posted['page'] : null;

					/* filter payment process */
					$result = $payment->payment_process( $order_id, $page );

					if ( isset( $result['status'] ) && $result['status'] === true ) {
						if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
							wp_send_json( $result );
						} else if ( ! empty( $result['redirect'] ) ) {
							wp_safe_redirect( $result['redirect'] ); exit;
						}
					}
				} else {
					if ( empty( $order_id ) ) {
						$order = opalhotel_get_order( $order_id );
					}

					// No payment was required for order
					$order->payment_complete();

					// Empty the Cart
					OpalHotel()->cart->empty_cart();

					// Get redirect
					$return_url = $order->get_checkout_order_received_url();

					// Redirect to success/confirmation/payment page
					if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
						wp_send_json( array(
							'result' 	=> true,
							'redirect'  => apply_filters( 'opalhotel_checkout_no_payment_needed_redirect', $return_url, $order )
						) );
					} else {
						wp_safe_redirect(
							apply_filters( 'opalhotel_checkout_no_payment_needed_redirect', $return_url, $order )
						);
						exit;
					}
				}
			}

		} catch ( Exception $e ){
			opalhotel_add_notices( $e->getMessage() );
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			/* send error status and message */
			ob_start();
			opalhotel_print_notices();
			$message = ob_get_clean();
			wp_send_json( array(
					'status'	=> false,
					'messages'	=> $message,
					'fields'	=> array_unique( $this->error_fields )
				) );
		}

	}

}