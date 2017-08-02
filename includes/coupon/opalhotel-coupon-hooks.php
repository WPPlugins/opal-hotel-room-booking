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

add_filter( 'manage_opalhotel_coupon_posts_columns', 'opalhotel_coupon_manage_posts_columns', 99 );
if ( ! function_exists( 'opalhotel_coupon_manage_posts_columns' ) ) {

	// manage add room post type columns
	function opalhotel_coupon_manage_posts_columns( $columns ) {

		unset( $columns['title'] );
		unset( $columns['author'] );
		unset( $columns['comments'] );

		$add_columns = array(
				'cb'				=> '<input type="checkbox" />',
				'code'				=> __( 'Code', 'opal-hotel-room-booking' ),
				'type'				=> __( 'Coupon type', 'opal-hotel-room-booking' ),
				'usage_limit'		=> __( 'Usaged / Limit', 'opal-hotel-room-booking' ),
				'expire'			=> __( 'Expiry Date', 'opal-hotel-room-booking' )
			);

		return array_merge( $add_columns, $columns );
	}
}
add_action( 'manage_opalhotel_coupon_posts_custom_column', 'opalhotel_coupon_manage_posts_custom_columns', 1 );
if ( ! function_exists( 'opalhotel_coupon_manage_posts_custom_columns' ) ) {

	/* room custom columns */
	function opalhotel_coupon_manage_posts_custom_columns( $column ) {
		global $post; $coupon = OpalHotel_Coupon::instance( $post->ID );
		switch ( $column ) {
			case 'code':
				echo '<a href="' . get_edit_post_link( $post->ID ) . '">' . get_the_title( $post->ID ) . '</a>';
				break;

			case 'type':
				$labels = opalhotel_coupon_discount_type_label();
				echo isset( $labels[ $coupon->coupon_type ] ) ? esc_attr( $labels[ $coupon->coupon_type ] ) : '';
				break;

			case 'usage_limit':
				echo absint( $coupon->coupon_usaged_time );
				if ( $coupon->coupon_usage_time_limit ) {
					echo ' / ' . absint( $coupon->coupon_usage_time_limit );
				} else {
					echo ' / ' . __( 'No limit', 'opal-hotel-room-booking' );
				}
				break;

			case 'expire':
				if ( $coupon->coupon_expire_timestamp ) {
					echo opalhotel_format_date( $coupon->coupon_expire_timestamp );
				} else {
					echo __( 'No Expiry', 'opal-hotel-room-booking' );
				}
				break;
		}
	}
}

add_action( 'opalhotel_update_status_completed', 'opalhotel_update_coupon_usaged_time' );
if ( ! function_exists( 'opalhotel_update_coupon_usaged_time' ) ) {
	function opalhotel_update_coupon_usaged_time( $order_id ) {
		$order = OpalHotel_Order::instance( $order_id );
		if ( $order->coupon && isset( $order->coupon['id'] ) && $order->coupon['id'] ) {
			$coupon = OpalHotel_Coupon::instance( $order->coupon['id'] );
			$usage_time = absint( $coupon->coupon_usaged_time );
			update_post_meta( $order->coupon['id'], '_coupon_usaged_time', $usage_time + 1 );
		}
	}
}