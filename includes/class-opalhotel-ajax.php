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

class OpalHotel_Ajax {

	public function __construct() {

		$actions = array(
				/* admin room load package */
				'load_package_api'				=> false,
				'add_room_package'				=> false,
				'add_to_cart'					=> true,
				'reservation_step'				=> true,
				'remove_cart_item'				=> true,
				'apply_coupon_code'				=> true,
				'remove_coupon_code'			=> true,
				'process_checkout'				=> true,
				'update_pricing'				=> false,
				'load_pricing'					=> false,
				'load_order_user_email'			=> false,
				'load_order_user_name'			=> false,
				'remove_order_item'				=> false,
				'load_coupon_available'			=> false,
				'order_add_coupon'				=> false,
				'order_remove_coupon'			=> false,
				'load_room_by_name'				=> false,
				'admin_check_available'			=> false,
				'admin_add_order_item'			=> false,
				'admin_edit_order_item'			=> false,
				'admin_update_order_item'		=> false,
				'load_room_available_data'		=> true,
				'load_more_ajax'				=> true,
				'toggle_favorite'				=> false,

				'creator_custom_type'			=> false,
				'create_option_select'			=> false,
				'search_hotel'					=> true,
				'load_rooms_hotel'				=> true,
				'loops_hotel_ajax_action'		=> true,
				'get_position_by_agent_ip'		=> true
			);

		foreach ( $actions as $action => $nopriv ) {
			add_action( 'wp_ajax_opalhotel_' . $action, array( $this, $action ) );
			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_opalhotel_' . $action, array( $this, $action ) );
			} else {
				add_action( 'wp_ajax_nopriv_opalhotel_' . $action, array( $this, 'must_login' ) );
			}
		}

	}

	/* room load package */
	public function load_package_api() {
		check_admin_referer( 'opalhotel_nonce', 'opalhotel-nonce' );

		if ( ! isset( $_POST['package'] ) || ! isset( $_POST['room_id'] ) ) {
			return;
		}

		/* package title */
		$pakage = sanitize_text_field( $_POST['package'] );
		$room_id = absint( $_POST['room_id'] );

		/* get package by title LILE */
		$packages = opalhotel_get_packages_by_title( $pakage, array( $room_id ) );
		wp_send_json( $packages );
	}

	/* add room package */
	public function add_room_package() {
		check_admin_referer( 'opalhotel_nonce', 'opalhotel-nonce' );
		if ( ! isset( $_POST['package_id'] ) || ! isset( $_POST['room_id'] ) ) {
			wp_send_json( array( 'status' => false, 'message' => __( 'Could not do action.', 'opal-hotel-room-booking' ) ) );
		}

		/* sanitize room id, package id */
		$room_id = absint( $_POST['room_id'] );
		$package_id = absint( $_POST['package_id'] );

		/* add new package */
		if ( add_post_meta( $room_id, '_package_id', $package_id ) ) {
			wp_send_json( array(
				'status' 	=> true,
				'id' 		=> $package_id,
				'id_format' => opalhotel_format_id( $package_id ),
				'title'		=> get_the_title( $package_id )
			) );
		}
		wp_send_json( array( 'status' => false, 'message' => __( 'Could not add post meta. Please try again.', 'opal-hotel-room-booking' ) ) );
	}

	/* add to cart */
	public function add_to_cart() {
		check_ajax_referer( 'opalhotel_add_to_cart', 'opalhotel-add_to-cart' );

		if ( ! isset( $_POST['id'] ) ) {
			wp_send_json( array(
					'status'		=> false,
					'message'		=> __( 'Room invalid.', 'opal-hotel-room-booking' )
				) );
		}

		$room_id = absint( $_POST['id'] );
		if ( ! isset( $_POST['arrival'] ) ) {
			wp_send_json( array(
					'status'		=> false,
					'message'		=> __( 'Arrival Date invalid.', 'opal-hotel-room-booking' )
				) );
		}
		$arrival = sanitize_text_field( $_POST['arrival'] );

		if ( ! isset( $_POST['departure'] ) ) {
			wp_send_json( array(
					'status'		=> false,
					'message'		=> __( 'Departure Date invalid.', 'opal-hotel-room-booking' )
				) );
		}
		$departure = sanitize_text_field( $_POST['departure'] );

		$qty = isset( $_POST['qty'] ) ? absint( $_POST['qty'] ) : 1;

		$adult = isset( $_POST['adult'] ) ? absint( $_POST['adult'] ) : 0;
		$child = isset( $_POST['child'] ) ? absint( $_POST['child'] ) : 0;

		$param = array(
				'arrival'		=> $arrival,
				'departure'		=> $departure,
				'adult'			=> $adult,
				'child'			=> $child
			);
		if ( ! empty( $_POST['hotel-id'] ) ) {
			// $param['hotel'] = absint( $_POST['hotel-id'] );
		}
		$cart_item_id = OpalHotel()->cart->add_to_cart( $room_id, $qty, $param );

		$packages = array();

		$results = array();
		/* add to cart successfully */
		if ( $cart_item_id && ! is_wp_error( $cart_item_id ) ) {
			if ( ! empty( $_POST['packages'] ) && ! empty( $_POST['packages']['checked'] ) ) {

				foreach ( $_POST['packages']['checked'] as $package_id => $val ) {
					if ( $val === 'on' ) {
						$package = OpalHotel_Package::instance( $package_id );

						$package_param = array(
								'parent_id'		=> $cart_item_id
							);
						if ( $package->package_type === 'package' && isset( $_POST['packages']['qty'], $_POST['packages']['qty'][$package_id] ) ) {
							$package_qty = absint( $_POST['packages']['qty'][$package_id] );
							$package_cart_item_id = OpalHotel()->cart->add_to_cart( $package_id, $package_qty, $package_param );
						} else {
							$package_cart_item_id = OpalHotel()->cart->add_to_cart( $package_id, 1, $package_param );
						}
						$packages[] = $package_cart_item_id;
					}
				}

				do_action( 'opalhotel_add_package_to_cart', $_POST['packages'], $packages );
			} else if ( ! isset( $_POST['packages'] ) || ! isset( $_POST['packages']['checked'] ) ) {
				OpalHotel()->cart->remove_cart_item_child( $cart_item_id );
			}

			ob_start();
			opalhotel_get_template( 'search/review.php' );
			$html = trim( ob_get_clean() );

			opalhotel_add_notices( sprintf( __( 'Added room %s to cart successfully.', 'opal-hotel-room-booking' ), get_the_title( $room_id ) ), 'success' );

			$message = '<div class="opalhotel-flash-message" style="display: none">';
			ob_start();
			opalhotel_print_notices();
			$message .= ob_get_clean();
			$message .= '</div>';

			$results = array( 'status' => true, 'cart_item_id' => $cart_item_id, 'review' => $html, 'message' => $message );

			$results = apply_filters( 'opalhotel_add_to_cart_ajax_results', $results );
		} else {
			$results = array( 'status' => false, 'message' => $cart_item_id->get_error_message() );
		}

		if ( ! empty( $_POST['redirect'] ) ) {
			$results['redirect'] = esc_url( $_POST['redirect'] );
		}

		if ( isset( $_REQUEST['is_hotel'] ) && $_REQUEST['is_hotel'] ) {
			$results['redirect'] = opalhotel_get_checkout_url();
		}

		$results = apply_filters( 'opalhotel_add_to_cart_results', $results, $room_id, $_POST );
		wp_send_json( $results );
	}

	/* reservation step */
	public function reservation_step() {

		check_admin_referer( 'opalhotel_nonce', 'opalhotel-nonce' );

		$step = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : 1;
		$page_id = ! empty( $_POST['current_page_id'] ) ? absint( $_POST['current_page_id'] ) : 0;

		$results = array();
		if ( $step === 1 ) {
			OpalHotel()->cart->empty_cart();
		}
		if( isset( $_POST['current_page_id'] ) && $step === 2 && $page_id != opalhotel_get_page_id( 'reservation' ) ){
			$results = array(
					'status'	=> true,
					'redirect'	=> add_query_arg( $_POST, opalhotel_get_reservation_url() )
				);
		} else if ( $step === 3 && OpalHotel()->cart->is_empty() ) {
			$results = array(
				'status' 	=> false,
				'message'	=> __( 'Can not make a reservation. You have not selected any room.', 'opal-hotel-room-booking' )
			);
		} else {
			ob_start();
			opalhotel_get_template( 'reservation.php', array( 'atts' => $_POST ) );
			$html = ob_get_clean();

			$results = array(
				'status' 	=> true,
				'html'		=> $html,
				'step'		=> $step
			);
		}

		wp_send_json( apply_filters( 'opalhotel_reservation_step_results', $results, $step ) );

	}

	/* remove cart review item */
	public function remove_cart_item() {
		check_admin_referer( 'opalhotel_nonce', 'opalhotel-nonce' );
		if ( ! isset( $_POST['cart_item_id'] ) ) {
			wp_send_json( array(
					'status'		=> false,
					'message'		=> __( 'Review item not found.', 'opal-hotel-room-booking' )
				) );
		}

		/* sanitize data */
		$cart_item_id = sanitize_text_field( $_POST['cart_item_id'] );

		OpalHotel()->cart->remove_cart_item( $cart_item_id );

		$results = array(
				'status'	=> true
			);
		if ( OpalHotel()->cart->is_empty || empty( OpalHotel()->cart->cart_contents ) ) {
			ob_start();
			do_shortcode( '[opalhotel_checkout]' );
			$html = ob_get_clean();
			$results['cart_empty'] = $html;
		}

		ob_start();
		do_action( 'opalhotel_checkout_review' );
		$checkout_review = ob_get_clean();
		$results['checkout_review']	= $checkout_review;

		ob_start();
		opalhotel_get_template( 'search/review.php' );
		$reservation_review = ob_get_clean();
		$results['reservation_review']	= $reservation_review;

		$results = apply_filters( 'opalhotel_reservation_remove_cart_item', $results, $cart_item_id );
		wp_send_json( $results );
	}

	/* apply coupon action */
	public function apply_coupon_code() {
		check_admin_referer( 'opalhotel_nonce', 'opalhotel-nonce' );
		if ( ! isset( $_POST['coupon'] ) || ! $_POST['coupon'] ) {
			wp_send_json( array(
					'status'	=> false,
					'message'	=> sprintf( __( 'Coupon %s is not exists.', 'opal-hotel-room-booking' ), $_POST['coupon'] )
				) );
		}

		/* sanitize */
		$code = sanitize_text_field( $_POST['coupon'] );
		/* add new coupon to cart */
		$coupon = OpalHotel()->cart->add_coupon( $code );
		if ( ! is_wp_error( $coupon ) ) {

			// ob_start();
			opalhotel_add_notices( sprintf( __( 'Coupon code <strong>%s</strong> has been applied successfully.', 'opal-hotel-room-booking' ), $code ), 'success' );
			// ob_end_clean();

			/* review reload */
			ob_start();
			do_action( 'opalhotel_checkout_review' );
			$checkout_review = ob_get_clean();

			ob_start();
			opalhotel_get_template( 'search/review.php' );
			$reservation_review = ob_get_clean();

			wp_send_json( array(
					'status'			=> true,
					'checkout_review'	=> $checkout_review,
					'reservation_review'	=> $reservation_review
				) );

		} else {
			wp_send_json( array(
					'status'	=> false,
					'message'	=> $coupon->get_error_message()
				) );
		}

	}

	/* remove coupon applied */
	public function remove_coupon_code() {
		check_admin_referer( 'opalhotel_nonce', 'opalhotel-nonce' );
		if ( ! isset( $_POST['coupon'] ) || ! $_POST['coupon'] ) {
			wp_send_json( array(
					'status'	=> false,
					'message'	=> sprintf( __( 'Coupon %s is not exists.', 'opal-hotel-room-booking' ), $_POST['coupon'] )
				) );
		}
		/* sanitize */
		$code = sanitize_text_field( $_POST['coupon'] );
		$coupon = opalhotel_get_coupon_by_code( $code, OpalHotel()->cart->subtotal );

		if ( $coupon && ! is_wp_error( $coupon ) ) {
			OpalHotel()->cart->remove_coupon( $coupon );

			// ob_start();
			opalhotel_add_notices( sprintf( __( 'Coupon code <strong>%s</strong> has been removed.', 'opal-hotel-room-booking' ), $code ), 'success' );
			// ob_end_clean();

			/* review reload */
			ob_start();
			do_action( 'opalhotel_checkout_review' );
			$checkout_review = ob_get_clean();

			ob_start();
			opalhotel_get_template( 'search/review.php' );
			$reservation_review = ob_get_clean();

			wp_send_json( array(
					'status'			=> true,
					'checkout_review'	=> $checkout_review,
					'reservation_review'	=> $reservation_review
				) );
		} else {
			wp_send_json( array(
					'status'	=> false,
					'message'	=> $coupon->get_error_message()
				) );
		}
	}

	/* process checkout */
	public function process_checkout() {
		/* define const */
		if ( ! defined( 'OPALHOTEL_PROCESS_CHECKOUT' ) ) {
			define( 'OPALHOTEL_PROCESS_CHECKOUT', true );
		}

		/* callable process_checkout function */
		OpalHotel()->checkout->process_checkout(); die();
	}

	/* upload pricing */
	public function update_pricing() {
		check_admin_referer( 'opalhotel_nonce', 'opalhotel-nonce' );
		if ( ! isset( $_POST['room_id'] ) ) {
			wp_send_json( array(
					'status' 	=> false,
					'message'	=> __( 'Room not found.', 'opal-hotel-room-booking' )
				) );
		}
		$room_id = absint( $_POST['room_id'] );
		$pricing_type = isset( $_POST['price_type'] ) ? sanitize_text_field( $_POST['price_type'] ) : 'new_price';
		if ( ! isset( $_POST['pricing_arrival_datetime'] ) || ! isset( $_POST['pricing_departure_datetime'] ) || strtotime( $_POST['pricing_arrival_datetime'] ) > strtotime( $_POST['pricing_departure_datetime'] ) ) {
			wp_send_json( array(
					'status' 	=> false,
					'message'	=> __( 'Arrival date in not valid.', 'opal-hotel-room-booking' )
				) );
		}
		$arrival = strtotime( sanitize_text_field( $_POST['pricing_arrival_datetime'] ) );
		$departure = strtotime( sanitize_text_field( $_POST['pricing_departure_datetime'] ) );

		if ( ! isset( $_POST['week_days'] ) ) {
			wp_send_json( array(
					'status'	=> false,
					'message'	=> __( 'Week day is empty.', 'opal-hotel-room-booking' )
				) );
		}

		$amount = isset( $_POST[ 'amount' ] ) ? floatval( $_POST[ 'amount' ] ) : 0;
		$range = ( $departure - $arrival ) / DAY_IN_SECONDS + 1;

		for ( $i = 0; $i < $range; $i++ ) {
			$arrival_time = $arrival + $i * DAY_IN_SECONDS;
			$day = date( 'w', $arrival_time );
			if ( ! in_array( $day, $_POST['week_days'] ) ) {
				continue;
			}
			$price = opalhotel_get_room_price_by_day( array( 'room_id' => $room_id, 'arrival' => $arrival_time ) );
			if ( $pricing_type === 'new_price' ) {
				$price = $amount;
			} else if ( $pricing_type === 'subtract_price' ) {
				$price = $price - $amount;
			} else if ( $pricing_type === 'append_price' ) {
				$price = $price + $amount;
			} else if ( $pricing_type === 'increase_percent' ) {
				$price = $price + $price * $amount / 100;
			} else if ( $pricing_type === 'decrease_percent' ) {
				$price = $price - $price * $amount / 100;
			}
			opalhotel_update_pricing( $room_id, $arrival_time, $price );
		}

		wp_send_json( array(
				'status' 		=> true,
				'go_to_date'	=> date( 'Y-m-d', $arrival )
			) );
	}

	/* load pricing */
	public function load_pricing(){
		check_admin_referer( 'opalhotel_nonce', 'opalhotel-nonce' );
		if ( ! isset( $_REQUEST['room_id'] ) ) {
			return;
		}
		$room_id = absint( $_REQUEST['room_id'] );
		$data = array();
		$prices = opalhotel_get_room_prices( $room_id );
		if ( $prices ) {
			foreach ( $prices as $price ) {
				$data[] = array(
						'title'	=> $price->price,
						'start'	=> date( 'Y-m-d', strtotime( $price->arrival ) )
					);
			}
		}
		wp_send_json( $data );
	}

	/* load user email */
	public function load_order_user_email() {
		check_admin_referer( 'customer-email', 'nonce' );
		$email = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
		global $wpdb;
		$sql = $wpdb->prepare( "
				SELECT meta.meta_value FROM $wpdb->postmeta AS meta
					JOIN $wpdb->posts AS book ON book.ID = meta.post_id
				WHERE meta.meta_key = %s
					AND meta.meta_value LIKE %s
				GROUP BY meta.meta_value
			", '_customer_email', '%' . $wpdb->esc_like( $email ) . '%' );

		$results = $wpdb->get_results( $sql );
		wp_send_json( $results ); die();
	}

	/* load user name */
	public function load_order_user_name() {
		check_admin_referer( 'customer-user-name', 'nonce' );
		$user_name = isset( $_POST['user_name'] ) ? sanitize_text_field( $_POST['user_name'] ) : '';
		global $wpdb;
		$sql = $wpdb->prepare("
				SELECT user.ID, user.user_email, user.user_login FROM $wpdb->users AS user
				WHERE
					user.user_login LIKE %s
			", '%' . $wpdb->esc_like( $user_name ) . '%' );

		$users = $wpdb->get_results( $sql );
		wp_send_json( $users ); die();
	}

	/* remove order item id */
	public function remove_order_item() {
		check_admin_referer( 'remove-order-item', 'nonce' );
		$order_item_id = isset( $_POST['order_item_id'] ) ? absint( $_POST['order_item_id'] ) : 0;
		$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
		if ( ! $order_item_id ) { return; }
		opalhotel_remove_order_item( $order_item_id );

		$post = get_post( $order_id );
		require_once OPALHOTEL_INC_PATH . '/admin/metaboxes/views/booking-items-data.php'; die();
	}

	/* load coupon available */
	public function load_coupon_available() {
		check_admin_referer( 'load-coupon-available', 'nonce' );
		if ( ! isset( $_POST['code'] ) || ! $_POST['code'] || ! $_POST['order_id'] ) {
			wp_send_json( array(
					'status'	=> false,
					'message'	=> sprintf( __( 'Coupon %s is not exists.', 'opal-hotel-room-booking' ), $_POST['code'] )
				) );
		}

		/* sanitize */
		$code = sanitize_text_field( $_POST['code'] );
		global $wpdb;
		$sql = $wpdb->prepare( "
					SELECT ID, post_title FROM $wpdb->posts
					WHERE post_title LIKE %s
						AND post_status = %s
						AND post_type = %s
				", '%' . $wpdb->esc_like( $code ) .'%', 'publish', 'opalhotel_coupon' );
		wp_send_json( $wpdb->get_results( $sql ) );

	}

	/* add coupon to an order */
	public function order_add_coupon() {
		check_admin_referer( 'add-coupon-code', 'nonce' );
		$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		if ( ! $order_id || ! $id ) { return; }
		$code = get_the_title( $id );

		$order = OpalHotel_Order::instance( $order_id );
		$id = opalhotel_get_coupon_by_code( $code, $order->get_subtotal() );
		if ( is_wp_error( $id ) ) {
			wp_send_json( array(
					'status'	=> false,
					'message'	=> $id->get_error_message()
				) );
		}

		if ( ! is_wp_error( $id ) ) {
			$order_id = absint( $_POST['order_id'] );
			$coupon_ob = OpalHotel_Coupon::instance( $id );
			$coupons = array(
					'id'	=> $id,
					'code'	=> $code
				);
			update_post_meta( $order_id, '_coupon', $coupons );
			update_post_meta( $order_id, '_coupon_discount', $coupon_ob->calculate_discount( $order->get_subtotal() ) );
			$post = get_post( $order_id );
			ob_start();
			require_once OPALHOTEL_INC_PATH . '/admin/metaboxes/views/booking-items-data.php';
			$html = ob_get_clean();
			wp_send_json( array(
					'status'			=> true,
					'html'				=> $html
				) );

		} else {
			wp_send_json( array(
					'status'	=> false,
					'message'	=> __( 'Coupon is not exist or expired.', 'opal-hotel-room-booking' )
				) );
		}
	}

	/* remove coupon an order */
	public function order_remove_coupon() {
		check_admin_referer( 'remove-coupon-code', 'nonce' );
		$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
		if ( ! $order_id ) { return; }
		delete_post_meta( $order_id, '_coupon' );
		delete_post_meta( $order_id, '_coupon_discount' );
		$post = get_post( $order_id );
		require_once OPALHOTEL_INC_PATH . '/admin/metaboxes/views/booking-items-data.php'; die();
	}

	/* load room by room name */
	public function load_room_by_name() {
		check_admin_referer( 'load-room-by-name', 'nonce' );
		global $wpdb;
		$name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
		$sql = $wpdb->prepare( "
					SELECT ID, post_title FROM $wpdb->posts
					WHERE post_title LIKE %s
						AND post_status = %s
						AND post_type = %s
				", '%' . $wpdb->esc_like( $name ) .'%', 'publish', OPALHOTEL_CPT_ROOM );
		wp_send_json( $wpdb->get_results( $sql ) );
	}

	/* check room available */
	public function admin_check_available() {
		check_admin_referer( 'load-available-room-nonce', 'nonce' );
		if ( ! isset( $_POST['room_id'] ) ) {
			wp_send_json( array( 'status' => false, 'message' => __( 'Please select room before check avaibility.', 'opal-hotel-room-booking' ) ) );
		}
		if ( ! isset( $_POST['arrival'] ) || ! isset( $_POST['departure'] ) || $_POST['departure'] <= $_POST['arrival'] ) {
			wp_send_json( array( 'status' => false, 'message' => __( 'Arrival date or Departure date invalid.', 'opal-hotel-room-booking' ) ) );
		}
		$room_id = absint( $_POST['room_id'] );
		$arrival = sanitize_text_field( $_POST['arrival'] );
		$departure = sanitize_text_field( $_POST['departure'] );
		$qty = opalhotel_room_check_available( $room_id, $arrival, $departure );
		if ( ! $qty ) {
			wp_send_json( array(
					'status'	=> false,
					'message'	=> sprintf( __( '%s is not available', 'opal-hotel-room-booking' ), get_the_title( $room_id ) )
				) );
		}
		$room = OpalHotel_Room::instance( $room_id );
		$html = array();
		$html[] = '<select name="qty" class="qty">';
		$html[] = '<option value="">' . __( 'Quantity', 'opal-hotel-room-booking' ) . '</option>';
		for ( $i = 1; $i <= $qty; $i++ ) {
			$html[] = '<option value="' . esc_attr( $i ) . '">' . esc_html( $i ) . '</option>';
		}
		$html[] = '</select>';

		/* adults */
		$html[] = '<select name="adult">';
		$html[] = '<option value="0">' . __( 'Adults', 'opal-hotel-room-booking' ) . '</option>';
		for( $i = absint( $room->adults ); $i > 0; $i-- ) {
			$html[] = '<option value="'.esc_attr( $i ).'">'. $i .'</option>';
		}
		$html[] = '</select>';

		/* childs */
		$html[] = '<select name="child">';
		$html[] = '<option value="0">' . __( 'Childs', 'opal-hotel-room-booking' ) . '</option>';
		for( $i = absint( $room->childrens ); $i > 0; $i-- ) {
			$html[] = '<option value="'.esc_attr( $i ).'">'. $i .'</option>';
		}
		$html[] = '</select>';
		wp_send_json( array(
					'status'	=> true,
					'html'		=> implode( '', $html )
				) );
	}

	/* add, update order item */
	public function admin_add_order_item() {
		check_ajax_referer( 'add-order-item', 'nonce' );
		if ( ! isset( $_POST['room_id'] ) ) {
			wp_send_json( array(
					'status' 	=> false,
					'message'	=> __( 'Room not found.', 'opal-hotel-room-booking' )
				) );
		}
		$room_id = absint( $_POST['room_id'] );
		$room_title = get_the_title( $room_id );
		if ( ! isset( $_POST['arrival'] ) || ! isset( $_POST['arrival'] ) ) {
			wp_send_json( array(
					'status' 	=> false,
					'message'	=> __( 'Arrival Date or Departure Date invalid.', 'opal-hotel-room-booking' )
				) );
		}

		$arrival = sanitize_text_field( $_POST['arrival'] );
		$departure = sanitize_text_field( $_POST['departure'] );

		if ( ! isset( $_POST['qty'] ) || $_POST['qty'] === '' ) {
			wp_send_json( array(
					'status' 	=> false,
					'message'	=> __( 'Please select number of room.', 'opal-hotel-room-booking' )
				) );
		}
		$qty = absint( $_POST['qty'] );
		$check_avaialble = opalhotel_room_check_available( $room_id, $arrival, $departure, $qty );
		if ( ! $check_avaialble ) {
			wp_send_json( array(
					'status' 	=> false,
					'message'	=> sprintf( __( '%s is not available right now.', 'opal-hotel-room-booking' ), $room_title )
				) );
		}

		/* order id */
		$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
		$param = apply_filters( 'opalhotel_admin_add_order_item_params', array(
				'order_item_name'		=> $room_title
			) );
		$order_item_id = opalhotel_add_order_item( $order_id, $param );

		/* hook */
		do_action( 'opalhotel_admin_added_order_item', $order_item_id, $order_id, $param );
		$room = OpalHotel_Room::instance( $room_id );
		$adult = isset( $_POST['adult'] ) ? absint( $_POST['adult'] ) : 1;
		$child = isset( $_POST['child'] ) ? absint( $_POST['child'] ) : 0;
		$room = opalhotel_get_room( $room_id );

		/* meta */
		$base_price = $room->base_price();
		$price = $room->get_price( array(
				'arrival'	=> $arrival,
				'departure'	=> $departure,
				'adult'		=> $adult,
				'child'		=> $child,
				'qty'		=> $qty
			) );
		$subtotal = $room->get_price_excl_tax( $price, $qty );
		$tax_total = $room->get_price_incl_tax( $price, $qty ) - $subtotal;

		opalhotel_update_order_item_meta( $order_item_id, 'arrival', strtotime( $arrival ) );
		opalhotel_update_order_item_meta( $order_item_id, 'departure', strtotime( $departure ) );
		opalhotel_update_order_item_meta( $order_item_id, 'adult', $adult );
		opalhotel_update_order_item_meta( $order_item_id, 'child', $child );
		opalhotel_update_order_item_meta( $order_item_id, 'product_id', $room_id );
		opalhotel_update_order_item_meta( $order_item_id, 'qty', $qty );
		opalhotel_update_order_item_meta( $order_item_id, 'base_price', $base_price );
		opalhotel_update_order_item_meta( $order_item_id, 'price', $price );
		opalhotel_update_order_item_meta( $order_item_id, 'subtotal', $subtotal );
		opalhotel_update_order_item_meta( $order_item_id, 'tax_total', $tax_total );

		ob_start();
		$post = get_post( $order_id );
		require_once OPALHOTEL_INC_PATH . '/admin/metaboxes/views/booking-items-data.php';
		$html = ob_get_clean();
		wp_send_json( array(
				'status' 	=> true,
				'html'		=> $html
			) );
	}

	/* edit order item */
	public function admin_edit_order_item() {
		check_ajax_referer( 'edit-order-item', 'nonce' );

		if ( ! isset( $_POST['order_id'] ) || ! isset( $_POST['order_item_id'] ) ) {
			wp_send_json( array(
					'status' 	=> false,
					'message'	=> __( 'Counld not found order item.', 'opal-hotel-room-booking' )
				) );
		}

		$order_item_id = absint( $_POST['order_item_id'] );
		$order_id = absint( $_POST['order_id'] );

		/* get order item */
		$order_item = OpalHotel_Order_Item::instance( $order_item_id );
		if ( ! $order_item->id ) {
			wp_send_json( array(
					'status' 	=> false,
					'message'	=> __( 'Counld not found order item.', 'opal-hotel-room-booking' )
				) );
		}
		$room = OpalHotel_Room::instance( $order_item->product_id );
		$room_packages = $room->get_packages();
		$pk_selecteds = opalhotel_get_sub_item_order_item_id( $order_item_id );
		$pk_selecteds = array_map( 'OpalHotel_Order_Item::instance', $pk_selecteds );
		$package_selecteds = array();
		foreach ( $pk_selecteds as $pk_selected ) {
			$package_selecteds[ $pk_selected->product_id ] = array(
					'qty'	=> $pk_selected->qty,
					'order_item_id'	=> $pk_selected->id
				);
		}

		$packages = array();
		if ( $room_packages ) {
			foreach ( $room_packages as $package ) {
				// $package = OpalHotel_Order_Item::instance( $package->order_item_id );
				$packages[] = array(
						'id'		=> $package->id,
						'name'		=> $package->get_title(),
						'package'	=> $package->package_type === 'package',
						'qty'		=> array_key_exists( $package->id, $package_selecteds ) ? $package_selecteds[$package->id]['qty'] : 1,
						'checked'	=> array_key_exists( $package->id, $package_selecteds ) ? 'checked' : '',
						'order_item_id'	=> array_key_exists( $package->id, $package_selecteds ) ? $package_selecteds[$package->id]['order_item_id'] : 0
					);
			}
		}

		$hotels = array();
		$hoteldata = $room->get_hotels();
		if ( $hoteldata ) {
			foreach ( $hoteldata as $data ) {
				setup_postdata( $data );
				$hotels[] = array( 'id' => $data->ID, 'name' => $data->post_title, 'selected' => $order_item->hotel == $data->ID );
			}wp_reset_postdata();
		}

		wp_send_json( apply_filters( 'opalhotel_admin_load_edit_order_item_param', array(
 				'status' 	=> true,
 				'action'	=> 'opalhotel_admin_update_order_item',
 				'nonce'		=> wp_create_nonce( 'update-order-item' ),
 				'order_id'	=> $order_id,
 				'order_item_id'	=> $order_item_id,
 				'message'	=> sprintf( '(#%s) %s', $order_item_id, $order_item->order_item_name ),
 				'room_id'	=> $room->id,
 				'room_name'	=> $room->get_title(),
 				'adults'	=> $room->adults,
 				'childs'	=> $room->childrens,
 				'adult'		=> $order_item->adult,
 				'child'		=> $order_item->child,
 				'quantity'	=> $room->qty,
 				'qty'		=> $order_item->qty,
 				'arrival'	=> opalhotel_format_date( $order_item->arrival ),
 				'departure'	=> opalhotel_format_date( $order_item->departure ),
 				'packages'	=> $packages,
 				'hotels'	=> $hotels
			) ) );
	}

	/* update order item */
	public function admin_update_order_item() {
		check_admin_referer( 'update-order-item', 'nonce' );
		if ( ! isset( $_POST['order_id'] ) || ! isset( $_POST['order_item_id'] ) ) {
			wp_send_json( array(
					'status' 	=> false,
					'message'	=> __( 'Order item is not exist.', 'opal-hotel-room-booking' )
				) );
		}
		$order_id = absint( $_POST['order_id'] );
		$order_item_id = absint( $_POST['order_item_id'] );

		if ( ! isset( $_POST['room_id'] ) ) {
			wp_send_json( array(
					'status' 	=> false,
					'message'	=> __( 'Room field is empty.', 'opal-hotel-room-booking' )
				) );
		}
		$room_id = absint( $_POST['room_id'] );
		if ( ! isset( $_POST['arrival'] ) || ! isset( $_POST['departure'] ) ) {
			wp_send_json( array(
					'status' 	=> false,
					'message'	=> __( 'Arrival date and Departure date is required.', 'opal-hotel-room-booking' )
				) );
		}
		$arrival = sanitize_text_field( $_POST['arrival'] );
		$departure = sanitize_text_field( $_POST['departure'] );
		$adult = isset( $_POST['adult'] ) ? absint( $_POST['adult'] ) : 0;
		$child = isset( $_POST['child'] ) ? absint( $_POST['child'] ) : 0;
		$hotel_id = isset( $_POST['hotel'] ) ? absint( $_POST['hotel'] ) : 0;

		if ( ! isset( $_POST['qty'] ) || ! isset( $_POST['qty'] ) ) {
			wp_send_json( array(
					'status' 	=> false,
					'message'	=> __( 'Please select room quantity.', 'opal-hotel-room-booking' )
				) );
		}
		$qty = absint( $_POST['qty'] );
		$room = opalhotel_get_room( $room_id );
		$price = $room->get_price( array(
				'arrival'	=> $arrival,
				'departure'	=> $departure,
				'adult'		=> $adult,
				'child'		=> $child
			) );
		$subtotal = $room->get_price_excl_tax( $price, $qty );
		$subtotal_incl_tax = $room->get_price_incl_tax( $price, $qty );
		/* update order item */
		opalhotel_update_order_item( $order_item_id, apply_filters( 'opalhotel_admin_update_order_item', array(
				'order_item_name'	=> $room->get_title()
			) ) );
		opalhotel_update_order_item_meta( $order_item_id, 'qty', $qty );
		opalhotel_update_order_item_meta( $order_item_id, 'product_id', $room_id );
		opalhotel_update_order_item_meta( $order_item_id, 'arrival', strtotime( $arrival ) );
		opalhotel_update_order_item_meta( $order_item_id, 'departure', strtotime( $departure ) );
		opalhotel_update_order_item_meta( $order_item_id, 'adult', $adult );
		opalhotel_update_order_item_meta( $order_item_id, 'child', $child );
		opalhotel_update_order_item_meta( $order_item_id, 'hotel', $hotel_id );
		opalhotel_update_order_item_meta( $order_item_id, 'base_price', $room->base_price() );
		opalhotel_update_order_item_meta( $order_item_id, 'price', $price );
		opalhotel_update_order_item_meta( $order_item_id, 'subtotal', $subtotal );
		opalhotel_update_order_item_meta( $order_item_id, 'tax_total', $subtotal_incl_tax - $subtotal );
		/* remove all sub order item (packages) */
		opalhotel_remove_sub_items( $order_item_id );
		/* add new package item */
		if ( ! empty( $_POST['packages'] ) ) {
			$packages = $_POST['packages'];
			foreach ( $packages as $id => $option ) {
				if ( ! isset( $option['checked'] ) || $option['checked'] !== 'on' || ! isset( $option['qty'] ) || $option['qty'] < 1 ) { continue; }
				$qty = absint( $option['qty'] );

				$package = OpalHotel_Package::instance( $id );
				$package_item_id = opalhotel_add_order_item( $order_id, array(
						'order_item_name'	=> $package->get_title(),
						'order_item_type'	=> 'package',
						'order_item_parent'	=> $order_item_id
					) );

				$subtotal = $package->get_price_excl_tax( $package->get_price(), $qty );
				$subtotal_incl_tax = $package->get_price_incl_tax( $package->get_price(), $qty );
				opalhotel_update_order_item_meta( $package_item_id, 'qty', $qty );
				opalhotel_update_order_item_meta( $package_item_id, 'product_id', $package->id );
				// opalhotel_update_order_item_meta( $package_item_id, 'arrival', $arrival );
				// opalhotel_update_order_item_meta( $package_item_id, 'departure', $departure );
				// opalhotel_update_order_item_meta( $package_item_id, 'adult', $adult );
				// opalhotel_update_order_item_meta( $package_item_id, 'child', $child );
				opalhotel_update_order_item_meta( $package_item_id, 'base_price', $package->base_price() );
				opalhotel_update_order_item_meta( $package_item_id, 'price', $package->get_price() );
				opalhotel_update_order_item_meta( $package_item_id, 'subtotal', $subtotal );
				opalhotel_update_order_item_meta( $package_item_id, 'tax_total', $subtotal_incl_tax - $subtotal );
			}
		}

		ob_start();
		$post = get_post( $order_id );
		require_once OPALHOTEL_INC_PATH . '/admin/metaboxes/views/booking-items-data.php';
		$html = ob_get_clean();
		wp_send_json( array(
				'status' 	=> true,
				'html'		=> $html
			) );
	}

	public function load_room_available_data() {
		if ( empty( $_POST['nonce'] ) || empty( $_POST['room_id'] ) ) {
			return;
		}
		$nonce = sanitize_text_field( $_POST['nonce'] );
		$room_id = absint( $_POST['room_id'] );
		if ( ! wp_verify_nonce( $nonce, 'opalhotel-single-room-available' ) ) {
			return;
		}

		$arrival = ! empty( $_POST['arrival_timestamp'] ) ? absint( $_POST['arrival_timestamp'] ) : current_time( 'timestamp' );
		$departure = ! empty( $_POST['departure_timestamp'] ) ? absint( $_POST['departure_timestamp'] ) : current_time( 'timestamp' );

		if ( $arrival >= $departure || ( $arrival && ! $departure ) ) {
			ob_start();
			opalhotel_print_notice_message( __( 'Start day must before end day.', 'opal-hotel-room-booking' ), 'error' );
			echo ob_get_clean(); exit();
		}
		$args = array(
				'arrival' => date( 'Y-m-d H:i:s', $arrival ),
				'departure' => date( 'Y-m-d H:i:s', $departure )
			);
		$room = opalhotel_get_room( $room_id, $args );
		$pricing = $room->get_pricing( $args['arrival'], $args['departure'] );

		$action_url = add_query_arg( array(
			'add-to-cart'		=> 1
		), opalhotel_get_available_url() );
		ob_start();
		echo '<form method="POST" action="'.esc_url( $action_url ).'" name="opalhotel-available-form" class="opalhotel-available-form">';
		opalhotel_get_template( 'search/loop/packages.php', array( 'room' => $room ) );
		echo '<input type="hidden" name="redirect" value="'.esc_url( opalhotel_get_checkout_url() ).'" />';
		echo '</form>';
		echo ob_get_clean(); exit();
	}

	/* must login */
	public function must_login() {
		wp_send_json( array( 'status' => false, 'message' => __( 'You must login to do action.', 'opal-hotel-room-booking' ) ) );
	}

	/**
	 * Load more post type
	 *
	 * @since 1.1.7
	 */
	public function load_more_ajax() {
		if ( ! isset( $_POST['opalhotel-load-more-nonce'] ) || ! wp_verify_nonce( $_POST['opalhotel-load-more-nonce'], 'opalhotel-ajax-load-more' ) )
			return;

		$args = ! empty( $_POST['args'] ) ? maybe_unserialize( stripcslashes( $_POST['args'] ) ) : array();
		$atts = ! empty( $_POST['atts'] ) ? maybe_unserialize( stripcslashes( $_POST['atts'] ) ) : array();
		$args['paged'] = ! empty( $_POST['paged'] ) ? absint( $_POST['paged'] ) : ( ! empty( $args['paged'] ) ? absint( $args['paged'] ) + 1 : 1 );
		$args['style'] = ! empty( $args['style'] ) ? sanitize_text_field( $arg['style'] ) : '';
		$args['pagination'] = ! empty( $args['pagination'] ) ? absint( $arg['pagination'] ) : 0;
		$args['columns'] = ! empty( $args['columns'] ) ? absint( $arg['pagination'] ) : 3;
		extract( $args );
		extract( $atts );

		global $opalhotel_loop;
		$opalhotel_loop['columns'] = $columns;
		$query = new WP_Query( apply_filters( 'opalhotel_shortcode_ajax_loadmore_args', $args ) );

		if ( ! $layout )
			$layout = opalhotel_get_display_mode();
		ob_start();
		?>

			<?php if ( $args['post_type'] === OPALHOTEL_CPT_HOTEL ) : ?>
				<?php if ( $query->have_posts() ) : ?>
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<?php if ( $layout === 'grid' ) : ?>
							<?php opalhotel_get_template_part( 'content-hotel-grid', $style ); ?>
						<?php else: ?>
							<?php opalhotel_get_template_part( 'content-hotel', 'list' ); ?>
						<?php endif; ?>
					<?php endwhile; ?>
					<?php switch ( $pagination ) {
						case 1:
							opalhotel_archive_print_postcount( $query );
							opalhotel_get_template( 'pagination.php', array( 'query' => $query, 'args' => $args, 'atts' => $atts ) );
							break;
						case 2:
							opalhotel_get_template( 'pagination.php', array( 'query' => $query, 'ajax' => true, 'args' => $args, 'atts' => $atts ) );
							break;

						default:
							break;
					} ?>
				<?php wp_reset_postdata(); endif; ?>
			<?php elseif ( $args['post_type'] === OPALHOTEL_CPT_ROOM ) : ?>
				<?php if ( $query->have_posts() ) : ?>
					<?php while( $query->have_posts() ) { $query->the_post(); ?>

						<?php opalhotel_get_template_part( 'content-room', $layout ); ?>

					<?php } ?>

					<?php if ( $pagination ) : ?>
						<?php switch ( $pagination ) {
							case 1:
								opalhotel_archive_print_postcount( $query );
								opalhotel_get_template( 'pagination.php', array( 'query' => $query, 'args' => $args, 'atts' => $atts ) );
								break;
							case 2:
								opalhotel_get_template( 'pagination.php', array( 'query' => $query, 'ajax' => true, 'args' => $args, 'atts' => $atts ) );
								break;

							default:
								break;
						} ?>
					<?php endif; ?>
				<?php wp_reset_postdata(); endif; ?>
			<?php endif; ?>

		<?php
		echo ob_get_clean(); exit();
	}

	/**
	 * Toggle Favorited
	 *
	 * @since 1.1.7
	 */
	public static function toggle_favorite() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'opalhotel-toggle-nonce' ) ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			wp_send_json( array( 'status' => false, 'redirect'	=> wp_login_url() ) );
		}

		$post_id = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		$favorited = get_user_meta( get_current_user_id(), '_opalhotel_favorited' );

		if ( in_array( $post_id, $favorited ) ) {
			delete_user_meta( get_current_user_id(), '_opalhotel_favorited', $post_id );
			$results = array(
					'status'	=> true,
					'icon'		=> 'fa fa-heart-o',
					'message'	=> sprintf( __( '<b>%s</b> removed from favorite.', 'opal-hotel-room-booking' ), get_the_title( $post_id ) ) . ( opalhotel_get_page_id( 'favorited' ) ? sprintf( '<a href="%s"> %s</a?', opalhotel_get_favorite_url(), __( 'View now', 'opal-hotel-room-booking' ) ) : '' )
				);
		} else {
			add_user_meta( get_current_user_id(), '_opalhotel_favorited', $post_id );
			$results = array(
					'status'	=> true,
					'icon'		=> 'fa fa-heart',
					'message'	=> sprintf( __( '<b>%s</b> added from favorite.', 'opal-hotel-room-booking' ), get_the_title( $post_id ) ) . ( opalhotel_get_page_id( 'favorited' ) ? sprintf( '<a href="%s"> %s</a?', opalhotel_get_favorite_url(), __( 'View now', 'opal-hotel-room-booking' ) ) : '' )
				);
		}
		wp_send_json( $results );
	}

	public static function creator_custom_type() {
		$type = $_POST['type'];

        $elements = new OpalHotel_Admin_Elements();

        ob_start();

        switch( $type ){
            case "label":
                $elements->label();
                break;
            case "textarea":
                $elements->textarea();
                break;
            case "select":
                $elements->select();
                break;
            case "checkbox";
                $elements->checkbox();
                break;
            case "text":
            default:
                $elements->text();
                break;
        }

        $html = ob_get_contents();
        ob_end_clean();

        $result = array(
                'type' => 'success',
                'html' => $html
            );

        wp_send_json( $result );
	}

	/**
	 * Create Option Select
	 *
	 * @since 1.1.7
	 */
	public static function create_option_select() {
		if( isset( $_POST['index'] ) && isset( $_POST['checked_default'] ) && isset( $_POST['option_index'] ) ){

            $args = array(
                'index' 			=> $_POST['index'],
                'checked_default' 	=> $_POST['checked_default'],
                'option_index' 		=> $_POST['option_index']
            );

            $elements = new OpalHotel_Admin_Elements();

            ob_start();
            $elements->select_option($args);
            $html = ob_get_contents();
            ob_end_clean();

            $result = array(
            		'type' => 'success',
            		'html' => $html
            	);
        } else {
            $result = array(
            		'type' => 'fail',
            		'html' => ''
            	);
        }

        wp_send_json( $result );
	}

	/**
	 * Ajax Search Hotels
	 *
	 * @since 1.1.7
	 */
	public static function search_hotel() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'opalhotel-search-hotel' ) ) {
			return;
		}

		$atts = isset( $_POST['atts'] ) ? maybe_unserialize( stripcslashes( $_POST['atts'] ) ) : array();

		$results = $data = array();

		$query = opalhotel_get_hotels_available();

		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {
				$query->the_post();
				global $post;
				$rooms_count = isset( $post->available ) ? absint( $post->available ) : 0;
				$map = get_post_meta( get_the_ID(), '_map', true );
				if ( ! isset( $map['latitude'] ) || ! isset( $map['longitude'] ) ) continue;
				$data[] = array(
						'id'			=> get_the_ID(),
						'title'			=> get_the_title(),
						'rooms_count'	=> 10,
						'permalink'		=> get_the_permalink(),
						'thumbnail' 	=> get_the_post_thumbnail_url( get_the_ID() ),
						'lat'			=> floatval( $map['latitude'] ),
						'lng'			=> floatval( $map['longitude'] ),
						'address'		=> esc_html( $map['address'] ),
						'content'		=> get_the_content(),
						'rooms_count'	=> sprintf( _n( '%d room left', '%d rooms left', $rooms_count, 'opal-hotel-room-booking' ), $rooms_count )
					);
			}

			wp_reset_postdata();
		}

		$results['places'] = $data;

		ob_start();
		OpalHotel_Shortcodes::hotel_available_results( $atts );
		$results['html'] = ob_get_clean();
		$results['sortable'] = isset( $_REQUEST['sortable'] ) ? absint( $_REQUEST['sortable'] ) : 0;
		wp_send_json( $results ); exit();
	}

	/**
	 * Ajax Load Rooms Available of hotel
	 */
	public static function load_rooms_hotel() {
		if ( empty( $_REQUEST['opalhotel-load-rooms-nonce'] ) || ! wp_verify_nonce( $_REQUEST['opalhotel-load-rooms-nonce'], 'opalhotel-rooms-avaialble-of-hotel' ) ) {
			return;
		}

		$hotel_id = ! empty( $_REQUEST['hotel_id'] ) ? absint( $_REQUEST['hotel_id'] ) : 0;

		# code...
		$hotel = opalhotel_get_hotel( $hotel_id );
		$rooms = $hotel->get_rooms_available( array(
			'posts_per_page'	=> -1
		) );

		ob_start();
		if ( $rooms->have_posts() ) {
			remove_action( 'opalhotel_archive_loop_item_list_description', 'opalhotel_loop_item_description', 5 );
			remove_action( 'opalhotel_room_available_actions', 'opalhotel_loop_item_room_available_pricing', 6 );
			remove_action( 'opalhotel_room_available_after', 'opalhotel_room_available_packages', 5 );
			add_action( 'opalhotel_after_available_room_info', 'opalhotel_room_available_optional', 5 );
			add_action( 'opalhotel_room_available_actions', 'opalhotel_loop_item_room_available_button', 99 );

			while ( $rooms->have_posts() ) : $rooms->the_post();
				opalhotel_get_template_part( 'content-room', 'form' );
			endwhile;

			add_action( 'opalhotel_archive_loop_item_list_description', 'opalhotel_loop_item_description', 5 );
			add_action( 'opalhotel_room_available_actions', 'opalhotel_loop_item_room_available_pricing', 6 );
			add_action( 'opalhotel_room_available_after', 'opalhotel_room_available_packages', 5 );
			remove_action( 'opalhotel_after_available_room_info', 'opalhotel_room_available_optional', 5 );
			remove_action( 'opalhotel_room_available_actions', 'opalhotel_loop_item_room_available_button', 99 );

			wp_reset_postdata();
		} else {
			opalhotel_get_template( 'loop/no-room-found.php' );
		}

		echo ob_get_clean(); die();
	}

	/**
	 * Ajax load sortable and layout in shortcode [opalhotel_rooms] && [opalhotel_hotels]
	 */
	public static function loops_hotel_ajax_action() {
		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'opalhotel-sortable-layout' ) ) return;

		$atts = maybe_unserialize( isset( $_REQUEST['atts'] ) ? stripcslashes( $_REQUEST['atts'] ) : array() );
		$args = maybe_unserialize( isset( $_REQUEST['args'] ) ? stripcslashes( $_REQUEST['args'] ) : array() );

		if ( ! isset( $atts['type'] ) ) return;

		ob_start();
		if ( $atts['type'] === 'room' ) {
			OpalHotel_Shortcodes::rooms( $atts );
		} else if( $atts['type'] === 'hotel' ) {
			OpalHotel_Shortcodes::hotels( $atts );
		}

		echo ob_get_clean(); exit();
	}

	/**
	 * Get Location by Agent IP
	 */
	public static function get_position_by_agent_ip() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'opalhotel-get-location-by-ip' ) ) {
			return;
		}

		$ip = opalhotel_get_ip();
		$url = 'http://freegeoip.net/json/' . $ip;
		$request = wp_safe_remote_get( $url );
		$body = wp_remote_retrieve_body( $request );
		if ( wp_remote_retrieve_response_code( $request ) === 200 && ! is_wp_error( $body ) ) {
			$body = json_decode( $body, true );

			if ( ! isset( $_REQUEST['country_code'] ) || ! $_REQUEST['country_code'] ) {
				wp_send_json( array(
						'status'	=> false,
						'message'	=> esc_html( 'We can not find your location in development.', 'opal-hotel-room-booking' )
					) );
			}

			wp_send_json( array(
					'status'	=> true,
					'lat'		=> $body['latitude'],
					'lng'		=> $body['longitude'],
					'message'	=> $body['region_name'] . ' - ' . $body['country_name']
				) );
		} else {
			wp_send_json( array(
					'status'	=> false,
					'message'	=> __( 'Sorry we can not find your position right now.', 'opal-hotel-room-booking' )
				) );
		}
	}

}

new OpalHotel_Ajax();