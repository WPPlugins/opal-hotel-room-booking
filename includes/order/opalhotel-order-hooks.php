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

add_filter( 'manage_opalhotel_booking_posts_columns', 'opalhotel_booking_manage_posts_columns', 99 );
if ( ! function_exists( 'opalhotel_booking_manage_posts_columns' ) ) {

	// manage add room post type columns
	function opalhotel_booking_manage_posts_columns( $columns ) {

		unset( $columns['title'] );
		unset( $columns['author'] );
		unset( $columns['comments'] );
		unset( $columns['date'] );

		$add_columns = array(
				'cb'				=> '<input type="checkbox" />',
				'id'				=> __( 'ID', 'opal-hotel-room-booking' ),
				'arrival'			=> __( 'Arrival Date', 'opal-hotel-room-booking' ),
				'departure'			=> __( 'Departure Date', 'opal-hotel-room-booking' ),
				'reservation_date'	=> __( 'Date', 'opal-hotel-room-booking' ),
				'total'				=> __( 'Total', 'opal-hotel-room-booking' ),
				'status'			=> __( 'Status', 'opal-hotel-room-booking' )
			);

		return array_merge( $add_columns, $columns );
	}
}

add_action( 'manage_opalhotel_booking_posts_custom_column', 'opalhotel_booking_manage_posts_custom_columns', 999 );
if ( ! function_exists( 'opalhotel_booking_manage_posts_custom_columns' ) ) {

	/* room custom columns */
	function opalhotel_booking_manage_posts_custom_columns( $column ) {
		global $post;
		$order = OpalHotel_Order::instance( $post->ID );
		switch ( $column ) {
			case 'id':
				echo sprintf( __( '<a href="%s"><strong>%s</strong></a> by <small>%s</small>', 'opal-hotel-room-booking' ), get_edit_post_link( $post->ID ), $order->get_order_number(), $order->customer_email );
				break;
			case 'title':
				echo sprintf( '<a href="%s">%s</a>', get_edit_post_link( $post->ID ), $order->get_order_number() );
				break;
			case 'arrival':
				echo opalhotel_format_date( $order->get_arrival_date() );
				break;
			case 'departure':
				echo opalhotel_format_date( $order->get_departure_date() );
				break;
			case 'reservation_date':
				echo opalhotel_format_date( strtotime( $order->post_date ) );
				break;
			case 'total':
				echo opalhotel_format_price( $order->get_total(), $order->payment_currency ? $order->payment_currency : opalhotel_get_currency_symbol() );
				echo sprintf( __( '<br><small>Via %s</small>', 'opal-hotel-room-booking' ), $order->payment_method_title );
				break;
			case 'status':
				echo sprintf( '%s', opalhotel_get_order_status_label( $post->ID ) );
				break;
			default:
				# code...
				break;
		}
	}
}

add_filter( 'manage_edit-opalhotel_booking_sortable_columns', 'opalhotel_booking_sortable_columns' );
if ( ! function_exists( 'opalhotel_booking_sortable_columns' ) ) {
	function opalhotel_booking_sortable_columns( $columns ) {
		$columns['total']		= 'total';
		$columns['id']			= 'id';
		$columns['arrival']		= 'arrival';
		$columns['departure']	= 'departure';
		return $columns;
	}
}

/* AUTOMATIC CANCEL PENDING PAYMENT ORDER */
add_action( 'opalhotel_reservartion_create_order', 'opalhotel_schedule_order_cancel' );
if ( ! function_exists( 'opalhotel_schedule_order_cancel' ) ) {

	function opalhotel_schedule_order_cancel( $order_id ) {
		if ( ! $order_id ) { return; }
		$order = opalhotel_get_order( $order_id );
		$status = $order->get_status();
		if ( $status !== 'pending' ) {
			return;
		}

		wp_clear_scheduled_hook( 'opalhotel_cancel_order_status', array( $order_id ) );
        $time = get_option( 'opalhotel_cancel_payment', 12 ) * HOUR_IN_SECONDS;
        wp_schedule_single_event( time() + $time, 'opalhotel_cancel_order_status', array( $order_id ) );
	}
}

add_action( 'opalhotel_cancel_order_status', 'opalhotel_cancel_order_status' );
if ( ! function_exists( 'opalhotel_cancel_order_status' ) ) {

	function opalhotel_cancel_order_status( $order_id ) {
		if ( ! $order_id ) { return; }
		$order = opalhotel_get_order( $order_id );
		$status = $order->get_status();
		if ( $status === 'pending' ) {
			$order->update_status( 'cancelled' );
		}
	}
}
/* END AUTOMATIC CANCEL PENDING PAYMENT ORDER */

/* CLEANUP BOOKING DATA */
add_action( 'opalhotel_cleanup_booking_data', 'opalhotel_cleanup_booking_data' );
if ( ! function_exists( 'opalhotel_cleanup_booking_data' ) ) {

	/**
	 * Clean up booking data
	 */
	function opalhotel_cleanup_booking_data() {
		global $wpdb;

		// clear order meta
		$wpdb->query( "DELETE FROM itemmeta USING {$wpdb->prefix}opalhotel_order_itemmeta itemmeta INNER JOIN {$wpdb->prefix}opalhotel_order_items items LEFT JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = items.order_id WHERE itemmeta.opalhotel_order_item_id = items.order_item_id and {$wpdb->prefix}posts.ID IS NULL" );
		// clearn order item in opalhotel_order_items
		$wpdb->query( "DELETE FROM items USING {$wpdb->prefix}opalhotel_order_items items LEFT JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = items.order_id WHERE {$wpdb->prefix}posts.ID IS NULL" );
	}

}
/* END CLEANUP BOOKING DATA */

/* ADMIN BOOKING FILTER */
add_action( 'restrict_manage_posts', 'opalhotel_order_restrict_manage_posts' );
if ( ! function_exists( 'opalhotel_order_restrict_manage_posts' ) ) {
	function opalhotel_order_restrict_manage_posts() {
			if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] !== OPALHOTEL_CPT_BOOKING ) {
				return;
			}
			$email = isset( $_GET['_customer_email'] ) ? sanitize_text_field( $_GET['_customer_email'] ) : '';
			$arrival = isset( $_GET['arrival_date_datetime'] ) ? sanitize_text_field( $_GET['arrival_date_datetime'] ) : date( 'Y-m-d', current_time( 'timestamp' ) );
			$departure = isset( $_GET['departure_date_datetime'] ) ? sanitize_text_field( $_GET['departure_date_datetime'] ) : date( 'Y-m-d', current_time( 'timestamp' ) + DAY_IN_SECONDS );

		?>
			<select style="width: 200px" type="text" name="_customer_email" id="_customer_email_select" data-placeholder="<?php esc_attr_e( 'Customer Email', 'opal-hotel-room-booking' ); ?>" >
				<?php if ( $email ) : ?>
					<option value="<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></option>
				<?php endif; ?>
			</select>
			<div class="opalhotel_datepick_wrap">
				<input name="arrival_date" value="<?php echo esc_attr( opalhotel_format_date( strtotime( $arrival ) ) ); ?>" data-min-date="false" class="opalhotel_arrival_date opalhotel-has-datepicker" data-end="opalhotel-departure-date" type="text" placeholder="<?php esc_attr_e( 'Arrival Date', 'opal-hotel-room-booking' ); ?>" />
				<input name="arrival_date_datetime" type="hidden" value="<?php echo esc_attr( $arrival );?>" />
				<input name="departure_date" value="<?php echo esc_attr( opalhotel_format_date( strtotime( $departure ) ) ); ?>" data-min-date="false" class="opalhotel-departure-date opalhotel-has-datepicker" data-start="opalhotel_arrival_date" type="text" placeholder="<?php esc_attr_e( 'Departure Date', 'opal-hotel-room-booking' ); ?>" />
				<input name="departure_date_datetime" type="hidden" value="<?php echo esc_attr( $departure );?>" />
			</div>
		<?php
	}
}

add_filter( 'posts_join_paged', 'opalhotel_order_posts_join_paged' );
if ( ! function_exists( 'opalhotel_order_posts_join_paged' ) ) {

	function opalhotel_order_posts_join_paged( $join ) {
		if ( ! is_admin() || ! isset( $_GET['post_type'] ) || $_GET['post_type'] !== OPALHOTEL_CPT_BOOKING ) {
			return $join;
		}
		global $wpdb;
		if ( isset( $_GET['_customer_email'] ) ) {
			$email = sanitize_text_field( $_GET['_customer_email'] );
	        $join .= "
	            INNER JOIN {$wpdb->postmeta} AS customer_email ON customer_email.post_id = $wpdb->posts.ID AND customer_email.meta_key = '_customer_email' AND customer_email.meta_value = '" . $email . "'
	        ";
		}

		/* search arrival date */
		if ( isset( $_GET['arrival_date'], $_GET['arrival_date_datetime'] ) && $_GET['arrival_date_datetime'] ) {
			$arrival_date = sanitize_text_field( $_GET['arrival_date_datetime'] );
	        $join .= "
	            INNER JOIN {$wpdb->postmeta} AS arrival_date ON arrival_date.post_id = $wpdb->posts.ID AND arrival_date.meta_key = '_arrival' AND from_unixtime( arrival_date.meta_value, '%Y-%m-%d' ) = '" . $arrival_date . "'
	        ";
		}

		/* search departure date */
		if ( isset( $_GET['departure_date'], $_GET['departure_date_datetime'] ) && $_GET['departure_date_datetime'] ) {
			$departure_date = sanitize_text_field( $_GET['departure_date_datetime'] );
	        $join .= "
	            INNER JOIN {$wpdb->postmeta} AS departure ON departure.post_id = $wpdb->posts.ID AND departure.meta_key = '_departure' AND from_unixtime( departure.meta_value, '%Y-%m-%d' ) = '" . $departure_date . "'
	        ";
		}
        return $join;
	}
}

/* END ADMIN BOOKING FILTER */
/* remove order item */
add_action( 'opalhotel_remove_order_item', 'opalhotel_remove_sub_item' );
if ( ! function_exists( 'opalhotel_remove_sub_item' ) ) {
	function opalhotel_remove_sub_item( $order_item_id ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM itemmeta USING {$wpdb->prefix}opalhotel_order_itemmeta itemmeta INNER JOIN {$wpdb->prefix}opalhotel_order_items items WHERE itemmeta.opalhotel_order_item_id = items.order_item_parent AND items.order_item_parent = %d", $order_item_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}opalhotel_order_items WHERE order_item_parent = %d", $order_item_id ) );
	}
}

add_action( 'delete_post', 'opalhotel_clean_hotel_order' );
if ( ! function_exists( 'opalhotel_clean_hotel_order' ) ) {

	function opalhotel_clean_hotel_order( $post_id ) {
		if ( get_post_type( $post_id ) != OPALHOTEL_CPT_BOOKING ) return;

		$order = opalhotel_get_order( $post_id );
		// remove order items
		$order->remove_order_items();

	}

}
