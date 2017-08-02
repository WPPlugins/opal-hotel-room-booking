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

class OpalHotel_Order extends OpalHotel_Abstract_Service {

	/* order id */
	public $id;

	/* instance */
	public static $instance = null;

	/* constructor object */
	public function __construct( $id = null ) {
		parent::__construct( $id );
	}

	/* order number */
	public function get_order_number() {
		return '#' . $this->id;
	}

	/* order add room */
	public function add_room( $params = array() ) {

		$params = wp_parse_args( $params, array(
				'id'		=> null,
				'arrival'	=> current_time( 'timestamp' ),
				'departure'	=> current_time( 'timestamp' ),
				'qty'		=> 1
			) );

		do_action( 'opalhotel_order_add_room' );

	}

	/* get all room order_item in order */
	public function get_rooms() {
		return opalhotel_get_order_items( $this->id );
	}

	/* get packages */
	public function get_room_packages( $room_order_id = null ) {
		return opalhotel_get_order_items( $this->id, 'package', $room_order_id );
	}

	/* get room subtotal */
	public function get_room_subtotal( $room_order_id = null ) {
		if ( ! $room_order_id ) return;

		$order_item = OpalHotel_Order_Item::instance( $room_order_id );
		$subtotal = $order_item->subtotal;
		$packages = $this->get_room_packages( $room_order_id );
		foreach ( $packages as $package ) {
			$package = OpalHotel_Order_Item::instance( $package->order_item_id );
			$subtotal += $package->subtotal;
		}
		return apply_filters( 'opalhotel_order_room_subtotal', $subtotal, $this->id, $room_order_id );
	}

	/* get order subtotal */
	public function get_subtotal() {
		$order_items = $this->get_items();
		$subtotal = 0;
		foreach ( $order_items as $item ) {
			$order_item = OpalHotel_Order_Item::instance( $item->order_item_id );
			$subtotal += floatval( $order_item->subtotal );
		}
		return apply_filters( 'opalhotel_order_subtotal', $subtotal, $this->id );
	}

	/* get order taxtotal */
	public function get_tax_total() {
		$order_items = $this->get_items();
		if ( $this->get_subtotal() === 0 ) {
			return;
		}
		$subtotal = 0;
		foreach ( $order_items as $item ) {
			$order_item = OpalHotel_Order_Item::instance( $item->order_item_id );
			$subtotal += floatval( $order_item->tax_total );
		}
		$subtotal = ( $this->get_subtotal() - $this->coupon_discount ) * $subtotal / $this->get_subtotal();
		return apply_filters( 'opalhotel_order_tax_total', $subtotal, $this->id );
	}

	/* get order taxtotal */
	public function get_total() {
		return apply_filters( 'opalhotel_order_tax_total', $this->get_subtotal() + $this->get_tax_total() - $this->coupon_discount, $this->id );
	}

	/* update status */
	public function update_status( $status = '' ) {
		$new_status = $status;
		if ( strpos( $status, 'opalhotel-' ) !== 0 ) {
			$status = 'opalhotel-' . $status;
		}
		$new_status = substr( $status, 10 );

		$old_status = opalhotel_get_order_status( $this->id );
		wp_update_post( array(
				'ID' 				=> $this->id,
				'post_status'		=> $status
			) );

		/* do action append update order status */
		do_action( 'opalhotel_update_order_status', $this->id, $new_status, $old_status );
		do_action( 'opalhotel_update_status_' . $old_status . '_to_' . $new_status, $this->id );
		do_action( 'opalhotel_update_status_' . $new_status, $this->id );

	}

	/* remove all order items */
	public function remove_order_items() {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM itemmeta USING {$wpdb->prefix}opalhotel_order_itemmeta itemmeta INNER JOIN {$wpdb->prefix}opalhotel_order_items items WHERE itemmeta.opalhotel_order_item_id = items.order_item_id and items.order_id = %d", $this->id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}opalhotel_order_items WHERE order_id = %d", $this->id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE post_id = %d", $this->id ) );
	}

	/* payment completed */
	public function payment_complete( $transaction_id = '' ) {
		do_action( 'opalhotel_before_completed_payment', $this->id, $transaction_id );

		/* unset session */
		if ( OpalHotel()->session->order_waiting_payment ) {
			OpalHotel()->session->order_waiting_payment = null;
		}

		/* validate current status */
		if ( $this->id && in_array( $this->get_status(), array( 'on-hold', 'pending', 'processing' ) ) ) {
			$this->update_status( 'completed' );
			/* paid completed is uniquid meta key */
			add_post_meta( $this->id, '_paid_completed', current_time( 'mysql' ), true );
			update_post_meta( $this->id, '_transaction_id', $transaction_id );
		}

		do_action( 'opalhotel_after_completed_payment', $this->id, $transaction_id );
	}

	/* get status order */
	public function get_status() {
		$status = get_post_status( $this->id );
		if ( strpos( $status, 'opalhotel-' ) === 0 ) {
			return substr( $status, 10 );
		}
	}

	/* get order item */
	public function get_order_item( $item_type = OPALHOTEL_CPT_ROOM, $parent = null ) {
		return opalhotel_get_order_items( $this->id, $item_type, $parent );
	}

	/* add order itme */
	public function add_order_item( $param = array() ) {
		return opalhotel_add_order_item( $this->id, $param );
	}

	/* order received */
	public function get_checkout_order_received_url() {

		$order_received_url = opalhotel_get_endpoint_url( 'reservation-received', $this->id, opalhotel_get_checkout_url() );
		$order_received_url = add_query_arg( 'empty_cart', 1, $order_received_url );

		if ( is_ssl() ) {
			$order_received_url = str_replace( 'http:', 'https:', $order_received_url );
		}

		return apply_filters( 'opalhotel_get_checkout_order_received_url', $order_received_url, $this );
	}

	/* order cancelled url */
	public function get_cancel_order_url() {
		$order_cancelled_url = opalhotel_get_endpoint_url( 'reservation-cancelled', $this->id, opalhotel_get_checkout_url() );

		if ( is_ssl() ) {
			$order_cancelled_url = str_replace( 'http:', 'https:', $order_cancelled_url );
		}

		return apply_filters( 'opalhotel_get_checkout_order_cancelled_url', $order_cancelled_url, $this );
	}

	/* order notify url */
	public function get_notify_url() {
		$order_notify_url = opalhotel_get_endpoint_url( 'reservation-notify', $this->id, opalhotel_get_checkout_url() );

		if ( is_ssl() ) {
			$order_notify_url = str_replace( 'http:', 'https:', $order_notify_url );
		}

		return apply_filters( 'opalhotel_get_checkout_order_notify_url', $order_notify_url, $this );
	}

	/* get items */
	public function get_items() {
		global $wpdb;
       	$query = $wpdb->prepare("
            SELECT oporder.* FROM $wpdb->opalhotel_order_items AS oporder
                LEFT JOIN $wpdb->posts AS post ON oporder.order_id = post.ID
            WHERE post.ID = %d
        ", $this->id );

        return apply_filters( 'opalhotel_get_order_items', $wpdb->get_results( $query ), $this->id );
	}

	/* get arrival date */
	public function get_arrival_date(){
		global $wpdb;
		$sql = $wpdb->prepare( "
				SELECT MIN( oporder_meta.meta_value ) FROM $wpdb->opalhotel_order_itemmeta AS oporder_meta
					INNER JOIN $wpdb->opalhotel_order_items AS oporder_item ON oporder_item.order_item_id = oporder_meta.opalhotel_order_item_id
					INNER JOIN $wpdb->posts AS posts ON posts.ID = oporder_item.order_id
				WHERE
					oporder_item.order_id = %d
					AND oporder_meta.meta_key = %s
					AND posts.post_type = %s
			", $this->id, 'arrival', OPALHOTEL_CPT_BOOKING );
		return $wpdb->get_var( $sql );
	}

	/* get departure date */
	public function get_departure_date(){
		global $wpdb;
		$sql = $wpdb->prepare( "
				SELECT MAX( oporder_meta.meta_value ) FROM $wpdb->opalhotel_order_itemmeta AS oporder_meta
					INNER JOIN $wpdb->opalhotel_order_items AS oporder_item ON oporder_item.order_item_id = oporder_meta.opalhotel_order_item_id
					INNER JOIN $wpdb->posts AS posts ON posts.ID = oporder_item.order_id
				WHERE
					oporder_item.order_id = %d
					AND oporder_meta.meta_key = %s
					AND posts.post_type = %s
			", $this->id, 'departure', OPALHOTEL_CPT_BOOKING );
		return $wpdb->get_var( $sql );
	}

	/* get customer name */
	public function get_customer_name() {
		return sprintf( '%s %s', $this->customer_first_name, $this->customer_last_name );
	}

	/* get customer name */
	public function get_customer_notes() {
		return $this->post_content;
	}

	/* instance return exist object or initialize new object */
	public static function instance( $order = null ) {
		$id = null;
		if ( $order instanceof WP_POST ) {
			$id = $order->ID;
		} else if ( is_numeric( $order ) ) {
			$id = $order;
		} else if ( is_object( $order ) && isset( $order->ID ) ) {
			$id = $order->ID;
		}

		if ( empty( self::$instance[ $id ] ) ) {
			self::$instance[ $id ] = new self( $id );
		}

		return self::$instance[ $id ];
	}

}
