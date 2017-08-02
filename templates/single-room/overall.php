<?php
/**
 * The template for displaying room content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-room/room-details/pricing-plans.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

// print hotels of room
do_action( 'opalhotel_print_room_hotels' );

// print package and discounts
do_action( 'opalhotel_print_room_packages_discounts' );

// print package and discounts
do_action( 'opalhotel_print_room_pricing_plans' );
