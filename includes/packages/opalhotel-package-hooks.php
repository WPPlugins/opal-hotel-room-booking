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

add_filter( 'manage_opalhotel_package_posts_columns', 'opalhotel_package_manage_posts_columns', 99 );
if ( ! function_exists( 'opalhotel_package_manage_posts_columns' ) ) {

	// manage add room post type columns
	function opalhotel_package_manage_posts_columns( $columns ) {

		unset( $columns['title'] );
		unset( $columns['author'] );
		unset( $columns['comments'] );

		$add_columns = array(
				'cb'				=> '<input type="checkbox" />',
				'name'				=> __( 'Name', 'opal-hotel-room-booking' ),
				'description'		=> __( 'Description', 'opal-hotel-room-booking' ),
				'base_price'		=> __( 'Base Price', 'opal-hotel-room-booking' ),
			);

		return array_merge( $add_columns, $columns );
	}
}

add_action( 'manage_opalhotel_package_posts_custom_column', 'opalhotel_package_manage_posts_custom_columns', 1 );
if ( ! function_exists( 'opalhotel_package_manage_posts_custom_columns' ) ) {

	/* room custom columns */
	function opalhotel_package_manage_posts_custom_columns( $column ) {
		global $post; $package = OpalHotel_Package::instance( $post->ID );
		switch ( $column ) {
			case 'name':
				echo '<a href="' . get_edit_post_link( $post->ID ) . '">'. $package->post_title .'</a>';
				break;

			case 'description':
				printf( '%s', $package->post_content );
				break;

			case 'base_price':
				printf( '%s', opalhotel_format_price( $package->base_price() ) );
				break;
		}
	}
}

add_filter( 'opalhotel_cart_item_subtotal_incl_tax', 'opalhotel_cart_item_subtotal_package', 10, 2 );
add_filter( 'opalhotel_cart_item_subtotal_excl_tax', 'opalhotel_cart_item_subtotal_package', 10, 2 );
if ( ! function_exists( 'opalhotel_cart_item_subtotal_package' ) ) {

	function opalhotel_cart_item_subtotal_package( $subtotal, $cart_item_id ) {
		if ( ! isset( OpalHotel()->cart->cart_contents[ $cart_item_id ] ) ) {
			return $subtotal;
		}

		$cart_item = OpalHotel()->cart->cart_contents[ $cart_item_id ];
		$cart_item_data = $cart_item['data'];
		if ( $cart_item_data->post_type !== 'opalhotel_package' || ! isset( $cart_item['parent_id'] ) || ! isset( OpalHotel()->cart->cart_contents[ $cart_item['parent_id'] ] ) ) {
			return $subtotal;
		}

		/**
		 * package type is 'package' || 'strip' not change
		 * @var amount
		 */
		if ( $cart_item_data->package_type === 'room' ) {

			/* cart item parent */
			$cart_parent = OpalHotel()->cart->cart_contents[ $cart_item['parent_id'] ];

			$subtotal = $subtotal * absint( $cart_parent['qty'] ) * absint( opalhotel_count_nights( $cart_parent['arrival'], $cart_parent['departure'] ) ) ;

		}

		return $subtotal;
	}

}