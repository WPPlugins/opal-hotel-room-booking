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

if ( ! function_exists( 'opalhotel_coupon_discount_type_label' ) ) {

	function opalhotel_coupon_discount_type_label() {
		return apply_filters( 'opalhotel_coupon_discount_type_label', array(
				'fixed_cart'	=> __( 'Cart Discount', 'opal-hotel-room-booking' ),
				'percent_cart'	=> __( 'Cart % Discount', 'opal-hotel-room-booking' )
			) );
	}

}

if ( ! function_exists( 'opalhotel_coupon_useaged' ) ) {
	/* return coupon used time */
	function opalhotel_coupon_useaged( $coupon_id = null ) {
		return;
	}
}

if ( ! function_exists( 'opalhotel_get_coupon_by_code' ) ) {

	/* get coupon object by code same as post title */
	function opalhotel_get_coupon_by_code( $code = '', $price = 0 ) {
		$code = sanitize_title( $code );

		global $wpdb;
    	$coupon = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = %s AND post_status = %s", $code, 'opalhotel_coupon', 'publish' ) );

    	if ( ! $coupon ) {
    		return new WP_Error( 'coupon_not_found', sprintf( __( 'Coupon code %s not found.', 'opal-hotel-room-booking' ), $code ) );
    	}

    	$coupon = OpalHotel_Coupon::instance( $coupon )->is_available( $price );

    	if ( is_wp_error( $coupon ) ) {
    		return $coupon;
    	}

    	return $coupon;

	}

}