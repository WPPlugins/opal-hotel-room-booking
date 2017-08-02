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

add_filter( 'manage_opalhotel_room_posts_columns', 'opalhotel_room_manage_posts_columns', 99 );
if ( ! function_exists( 'opalhotel_room_manage_posts_columns' ) ) {

	// manage add room post type columns
	function opalhotel_room_manage_posts_columns( $columns ) {

		unset( $columns['title'] );
		unset( $columns['author'] );
		unset( $columns['comments'] );

		$add_columns = array(
				'cb'				=> '<input type="checkbox" />',
				'title'			=> __( 'Name', 'opal-hotel-room-booking' ),
				'thumb'				=> '<span class="opalhotel_tiptip" data-tip="' . esc_attr__( 'Image', 'opal-hotel-room-booking' ) . '"><i class="fa fa-file-image-o" aria-hidden="true"></i></span>',
				'base_price'		=> __( 'Base Price', 'opal-hotel-room-booking' ),
				'hotel'				=> __( 'Hotel', 'opal-hotel-room-booking' ),
				'room_cat'			=> __( 'Categories', 'opal-hotel-room-booking' ),
				'room_tag'			=> __( 'Tags', 'opal-hotel-room-booking' ),
			);

		return array_merge( $add_columns, $columns );
	}
}

add_action( 'manage_opalhotel_room_posts_custom_column', 'opalhotel_room_manage_posts_custom_columns', 1 );
if ( ! function_exists( 'opalhotel_room_manage_posts_custom_columns' ) ) {

	/* room custom columns */
	function opalhotel_room_manage_posts_custom_columns( $column ) {
		global $post; global $opalhotel_room;
		$arrival = isset( $_REQUEST['arrival_datetime'] ) ? sanitize_text_field( $_REQUEST['arrival_datetime'] ) : current_time( 'mysql' );
		switch ( $column ) {
			case 'thumb':
				echo '<a href="' . get_edit_post_link( $post->ID ) . '">'
					. ( $opalhotel_room->has_discounts( $arrival ) ? '<img class="sale_icon" src="' . OPALHOTEL_URI . '/assets/images/sale_icon.png' . '" />' : '' )
					. opalhotel_room_placeholder_image( 'room_thumb' )
					. '</a>';
				break;

			case 'title':
				echo '<a href="' . get_edit_post_link( $post->ID ) . '"><strong>' . get_the_title( $post->ID ) . '</strong></a>';
				break;

			case 'base_price':
				printf( '%s', opalhotel_format_price( $opalhotel_room->base_price() ) );
				break;

			case 'hotel':
				$hotel_id = get_post_meta( $post->ID, '_hotel', true );
				$hotel_id && is_int( $hotel_id ) ? printf( '<a href="%s">%s</a>', get_permalink( $hotel_id ), get_the_title( $hotel_id ) ) : '';
				break;

			case 'room_cat':
				$terms = wp_get_post_terms( $post->ID, 'opalhotel_room_cat' );
				if ( $terms ) {
					$html = array();
					foreach ( $terms as $term ) {
						$html[] = '<a href="' . get_edit_term_link( $term->term_id, $term->taxonomy ) . '">' . $term->name . '</a>';
					}
					echo implode( ', ', $html );
				} else {
					echo '---';
				}
				break;

			case 'room_tag':
				$terms = wp_get_post_terms( $post->ID, 'opalhotel_room_tag' );
				if ( $terms ) {
					$html = array();
					foreach ( $terms as $term ) {
						$html[] = '<a href="' . get_edit_term_link( $term->term_id, $term->taxonomy ) . '">' . $term->name . '</a>';
					}
					echo implode( ', ', $html );
				} else {
					echo '---';
				}
				break;

			default:
				# code...
				break;
		}
	}
}

if ( ! function_exists( 'opalhotel_join_search_rooms_available_filter' ) ) {

	function opalhotel_join_search_rooms_available_filter( $join ) {
		global $wpdb;
		$arrival = isset( $_REQUEST['arrival_datetime'] ) ? strtotime( $_REQUEST['arrival_datetime'] ) : current_time( 'timestamp' );
		$departure = isset( $_REQUEST['departure_datetime'] ) ? strtotime( $_REQUEST['departure_datetime'] ) : current_time( 'timestamp' ) + DAY_IN_SECONDS;
		$night = opalhotel_count_nights( $arrival, $departure );
		$adult = isset( $_REQUEST['adult'] ) ? absint( $_REQUEST['adult'] ) : false;
		$child = isset( $_REQUEST['child'] ) ? absint( $_REQUEST['child'] ) : false;
		$qty = isset( $_REQUEST['number_of_rooms'] ) ? absint( $_REQUEST['number_of_rooms'] ) : 1;
		// $hotel_id = get_the_ID();
		$room_types = array();

		$args = compact( 'arrival', 'departure', 'night', 'adult', 'child', 'room_types', 'qty' ); //, 'hotel_id'

		$sql = opalhotel_get_room_available_sql( $args );
		// echo $sql;
		$join .= " LEFT JOIN ($sql) AS roomsAv ON roomsAv.ID = $wpdb->posts.ID";

		return $join;
	}

}

if ( ! function_exists( 'opalhotel_join_search_rooms_available_fields' ) ) {

	function opalhotel_join_search_rooms_available_fields( $fields ) {
		global $wpdb;
		$fields .= ", roomsAv.available AS available";

		return $fields;
	}

}

if ( ! function_exists( 'opalhotel_join_search_rooms_available_where' ) ) {

	function opalhotel_join_search_rooms_available_where( $where ) {
		global $wpdb;
		$qty = isset( $_REQUEST['number_of_rooms'] ) ? absint( $_REQUEST['number_of_rooms'] ) : 1;
		$where .= " AND ( roomsAv.available >= $qty )";
		return $where;
	}

}

