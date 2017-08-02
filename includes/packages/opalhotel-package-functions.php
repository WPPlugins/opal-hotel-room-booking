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

if ( ! function_exists( 'opalhotel_get_packages_by_title' ) ) {

	/* get package by title LIKE % title % */
	function opalhotel_get_packages_by_title( $title = '', $ignoire = array() ) {
		global $wpdb;

		if ( ! empty( $ignoire ) ) {
			$sql = $wpdb->prepare( "
					SELECT COUNT( room_meta.meta_value ) FROM $wpdb->postmeta AS room_meta
						INNER JOIN $wpdb->posts AS r ON r.ID = room_meta.post_id AND room_meta.meta_key = %s
					WHERE
						r.post_status = %s
						AND r.ID IN ( %s )
						AND r.post_type = %s
						AND room_meta.meta_value = package.ID
				", '_package_id', 'publish', implode( ',', $ignoire ), OPALHOTEL_CPT_ROOM );
			$sql = $wpdb->prepare( "
					SELECT package.ID, package.post_title, ( $sql ) AS selected FROM $wpdb->posts as package
					WHERE package.post_title LIKE %s
						AND package.post_status = %s
						AND package.post_type = %s
					HAVING selected = 0
				", '%' . $wpdb->esc_like( $title ) .'%', 'publish', 'opalhotel_package' );

		} else {
			$sql = $wpdb->prepare( "
					SELECT ID, post_title FROM $wpdb->posts
					WHERE post_title LIKE %s
						AND post_status = %s
						AND post_type = %s
				", '%' . $wpdb->esc_like( $title ) .'%', 'publish', 'opalhotel_package' );
		}

		return apply_filters( 'opalhotel_get_packages_by_title', $wpdb->get_results( $sql ) );
	}
}

if ( ! function_exists( 'opalhotel_package_types' ) ) {

	/* typeof package */
	function opalhotel_package_types() {
		return apply_filters( 'opalhotel_package_types', array(
				'trip'		=> __( 'Group / Trip', 'opal-hotel-room-booking' ),
				'room'		=> __( 'Room / Night', 'opal-hotel-room-booking' ),
				'package'	=> __( 'Package', 'opal-hotel-room-booking' )
			) );
	}
}

if ( ! function_exists( 'opalhotel_get_package_label' ) ) {

	/* get lable package by package type */
	function opalhotel_get_package_label( $id = null ) {
		if ( ! $id ) return;

		$package = OpalHotel_Package::instance( $id );
		$type = $package->package_type;
		$packages = opalhotel_package_types();

		/* return label */
		return isset( $packages[ $type ] ) ? apply_filters( 'opalhotel_package_lable', $packages[ $type ] ) : null ;
	}
}