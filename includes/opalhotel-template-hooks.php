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

function opalhotel_room_set_display_mode(){
	if( isset($_REQUEST['display']) && ( $_REQUEST['display'] == 'list' || $_REQUEST['display'] == 'grid' ) ){
		setcookie( 'opalhotel_display', trim($_REQUEST['display']) , time()+3600*24*100,'/' );
		$_COOKIE['opalhotel_display'] = trim($_REQUEST['display']);
	}
}

add_action( 'init', 'opalhotel_room_set_display_mode', 0 );

function opalhotel_room_display_mode(){
	if ( isset($_COOKIE['opalhotel_display']) && $_COOKIE['opalhotel_display'] == 'list' ) {
		return 'room-list';
	}
	return 'room';
}

function opalhotel_loop_display_mode( $default = 'grid' ){
	if ( isset($_COOKIE['opalhotel_display']) && in_array( $_COOKIE['opalhotel_display'], array( 'list', 'grid' ) ) ) {
		return $_COOKIE['opalhotel_display'];
	}
	return $default;
}

function opalhotel_get_display_mode( $default ) {
	return opalhotel_loop_display_mode( $default );
}

add_filter( 'opalhotel_room_display_mode' , 'opalhotel_room_display_mode' );

add_action( 'opalhotel_before_hotel_loop', 'opalhotel_loop_sortable' , 9 );
add_action( 'opalhotel_before_room_loop', 'opalhotel_loop_sortable' , 9 );
add_action( 'opalhotel_before_hotel_loop', 'opalhotel_display_modes' , 10 );
add_action( 'opalhotel_before_room_loop', 'opalhotel_display_modes' , 10 );

add_action( 'opalhotel_before_hotel_loop', 'opalhotel_before_loop', 1 );
add_action( 'opalhotel_before_room_loop', 'opalhotel_before_loop', 1 );
add_action( 'opalhotel_before_hotel_loop', 'opalhotel_after_loop', 999 );
add_action( 'opalhotel_before_room_loop', 'opalhotel_after_loop' , 999 );
if ( ! function_exists( 'opalhotel_before_loop' ) ) {

	/**
	 * wrap before loop
	 */
	function opalhotel_before_loop() {
		?>
			<div class="opalhotel-before-loop grid-row">
		<?php
	}
}
if ( ! function_exists( 'opalhotel_after_loop' ) ) {

	/**
	 * wrap before loop
	 */
	function opalhotel_after_loop() {
		?>
			</div>
		<?php
	}
}

add_action( 'opalhotel_after_process_step', 'opalhotel_after_process_step', 10, 1 );
// add_action( 'opalhotel_reservation_step', 'opalhotel_reservation_step', 10, 1 );
// add_action( 'opalhotel_reservation_reviews', 'opalhotel_reservation_reviews', 10 ); // this hook has been removed
/**
 * opalhotel_setup_shorcode_content
 * setup shortcode
 */
add_filter( 'the_content', 'opalhotel_setup_shorcode_content' );
/**
 * the_post opalhotel_template_setup_room
 * post_class
 */
add_action( 'the_post', 'opalhotel_template_setup_room' );
add_filter( 'body_class', 'opalhotel_template_setup_body_class' );
add_filter( 'post_class', 'opalhotel_template_setup_post_class' );

/**
 * Room Summary Box.
 *
 * opalhotel_template_single_title()
 * opalhotel_template_single_price()
 */
add_action( 'opalhotel_single_room_main', 'opalhotel_template_single_title', 5 );
add_action( 'opalhotel_single_room_main', 'opalhotel_template_single_price', 10 );
add_action( 'opalhotel_single_room_main', 'opalhotel_template_single_gallery', 15 );
add_action( 'opalhotel_template_single_gallery', 'opalhotel_template_single_gallery', 5 );

/*
 * opalhotel_single_room_main hook
 */
add_action( 'opalhotel_single_room_main', 'opalhotel_single_room_details', 20 );
add_action( 'opalhotel_after_main_content', 'opalhotel_single_related_room', 20 );

/*
 * opalhotel_content_single_room_details hook
 */
add_action( 'opalhotel_content_single_room_details', 'opalhotel_single_room_attribute', 5 );
add_action( 'opalhotel_content_single_room_details', 'opalhotel_single_room_description', 10 );

/*
 * opalhotel_before_after_room_reviews hook
 */
add_action( 'opalhotel_single_room_main', 'opalhotel_single_room_overall', 40 );
add_action( 'opalhotel_single_room_pricing_plan', 'opalhotel_single_room_pricing_plan', 5 );
/// add_action( 'opalhotel_before_after_room_reviews', 'opalhotel_single_reservation_form', 5 );

/**
 * archive - taxonomy
 */
add_action( 'opalhotel_archive_loop_item_thumbnail', 'opalhotel_loop_item_thumbnail', 5 );
add_action( 'opalhotel_after_room_loop', 'opalhotel_archive_print_postcount', 4 );
add_action( 'opalhotel_after_hotel_loop', 'opalhotel_archive_print_postcount', 4 );
add_action( 'opalhotel_after_room_loop', 'opalhotel_archive_pagination', 5 );
add_action( 'opalhotel_after_hotel_loop', 'opalhotel_archive_pagination', 5 );

add_action( 'opalhotel_print_room_hotels', 'opalhotel_print_room_hotels', 5 );
add_action( 'opalhotel_print_room_packages_discounts', 'opalhotel_print_room_packages_discounts', 5 );
/**
 * archive - taxonomy
 * content-room template
 * opalhotel_archive_loop_item_title
 */
add_action( 'opalhotel_archive_loop_item_title', 'opalhotel_loop_item_title', 5 );

/**
 * opalhotel_archive_loop_item_description hook
 * opalhotel_loop_item_description()
 */
add_action( 'opalhotel_archive_loop_item_description', 'opalhotel_loop_item_description', 5 );

add_action( 'opalhotel_archive_loop_item_list_description', 'opalhotel_loop_item_description', 5 );

/**
 * opalhotel_archive_loop_item_detail hook
 * opalhotel_loop_item_details()
 */
add_action( 'opalhotel_archive_loop_item_detail', 'opalhotel_loop_item_details', 5 );
add_action( 'opalhotel_loop_item_details', 'opalhotel_loop_item_details', 5 );
/**
 * opalhotel_archive_loop_item_booknow hook
 * opalhotel_archive_loop_item_booknow()
 */
add_action( 'opalhotel_archive_loop_item_booknow', 'opalhotel_archive_loop_item_booknow', 5 );
add_action( 'opalhotel_after_archive_loop_item', 'opalhotel_loop_room_modal', 5 );
/**
 * Room loop package
 */
add_action( 'opalhotel_room_available_after', 'opalhotel_room_available_packages', 5 );

/**
 * available action room list
 */
add_action( 'opalhotel_room_available_actions', 'opalhotel_loop_item_room_available_price', 5 );
add_action( 'opalhotel_room_available_actions', 'opalhotel_loop_item_room_available_pricing', 6 );
/**
 * opalhotel_loop_item_price hook
 * opalhotel_loop_room_price()
 */
add_action( 'opalhotel_archive_loop_item_price', 'opalhotel_loop_room_price', 5 );

add_action( 'opalhotel_archive_loop_room_footer', 'opalhotel_loop_room_rating', 5 );
add_action( 'opalhotel_archive_loop_room_footer', 'opalhotel_loop_room_price', 6 );

/* ORDER RECEIVED */
add_action( 'opalhotel_reservation_order_confirm', 'opalhotel_reservation_order_confirm_template' );
add_action( 'opalhotel_reservation_order_details', 'opalhotel_reservation_order_details_template' );
add_action( 'opalhotel_reservation_customer_details', 'opalhotel_reservation_customer_details_template' );
/* END ORDER RECEIVED */

/**
 * Hotel Loop content-hotel.php
 */
add_action( 'opalhotel_hotel_loop_item_labels', 'opalhotel_hotel_loop_item_labels', 5 );
// add_action( 'opalhotel_hotel_loop_item_rating', 'opalhotel_hotel_loop_item_rating', 5 );
add_action( 'opalhotel_hotel_loop_item_rating', 'opalhotel_hotel_loop_item_star', 5 );
add_action( 'opalhotel_hotel_loop_item_thumbnail', 'opalhotel_hotel_loop_item_thumbnail', 5 );
add_action( 'opalhotel_hotel_loop_item_thumbnail', 'opalhotel_hotel_loop_item_discount', 6 );
add_action( 'opalhotel_hotel_loop_item_actions', 'opalhotel_hotel_loop_item_actions', 5 );
add_action( 'opalhotel_hotel_loop_item_title', 'opalhotel_hotel_loop_item_title', 5 );
add_action( 'opalhotel_hotel_loop_item_description', 'opalhotel_hotel_loop_item_description', 5 );
add_action( 'opalhotel_hotel_loop_item_view_details', 'opalhotel_hotel_loop_item_view_details', 5 );
add_action( 'opalhotel_hotel_loop_item_address', 'opalhotel_hotel_loop_item_address', 5 );
add_action( 'opalhotel_hotel_loop_item_includes', 'opalhotel_hotel_loop_item_includes', 5 );
add_action( 'opalhotel_hotel_loop_item_price', 'opalhotel_hotel_loop_item_price', 5 );
add_action( 'opalhotel_hotel_loop_item_book_button', 'opalhotel_hotel_loop_item_book_button', 5 );
/**
 * End Hotel Loop content-hotel.php
 */

/**
 * Single Hotel
 */
add_action( 'opalhotel_single_hotel_main', 'opalhotel_single_hotel_title', 5 );
add_action( 'opalhotel_single_hotel_main', 'opalhotel_single_hotel_gallery', 10 );
add_action( 'opalhotel_single_hotel_main', 'opalhotel_single_hotel_description', 15 );
add_action( 'opalhotel_single_hotel_main', 'opalhotel_single_hotel_amenities', 20 );
add_action( 'opalhotel_single_hotel_main', 'opalhotel_single_hotel_think_to_do', 25 );
add_action( 'opalhotel_single_hotel_main', 'opalhotel_single_hotel_rooms', 30 );


add_action( 'opalhotel_single_hotel_tabs_main', 'opalhotel_single_hotel_tabs', 5 );
add_action( 'opalhotel_single_hotel_tabs_main', 'opalhotel_single_hotel_rooms', 10 );
/**
 * End Single Hotel
 */

/* TEMPLATE JS */
add_action( 'wp_footer', 'opalhotel_template_alert_underscore' );

add_action( 'wp_head', 'opalhotel_caculator_hotel_views' );
if ( ! function_exists( 'opalhotel_caculator_hotel_views' ) ) {

	/**
	 * Caculator hotels views
	 */
	function opalhotel_caculator_hotel_views() {
		if ( ! is_singular( OPALHOTEL_CPT_HOTEL ) ) {
			return;
		}
		global $post;
		$count = get_post_meta( $post->ID, '_popular_views', true );
		$count = ! $count ? 1 : absint( $count );
		$count++;

		update_post_meta( $post->ID, '_popular_views', $count );
	}

}
add_filter( 'body_class', 'opalhotel_body_class' );
if ( ! function_exists( 'opalhotel_body_class' ) ) {

	function opalhotel_body_class( $classes ) {
		global $post;
		if ( ! $post || ! is_page() ) return $classes;

		$page_template = get_post_meta( $post->ID, '_wp_page_template', true );
		if ( $page_template && array_key_exists( $page_template, OpalHotel()->page_templates->templates ) ) {
			$classes[] = 'opalhotel-main-map';
		}

		return $classes;
	}

}


