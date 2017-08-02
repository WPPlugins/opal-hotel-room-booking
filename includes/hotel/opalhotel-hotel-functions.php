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

if ( ! function_exists( 'opalhotel_single_hotel_layouts' ) ) {
	/**
	 * 'opalhotel_single_hotel_layouts'
	 *
	 * @since 1.1.7
	 */
	function opalhotel_single_hotel_layouts( $single = false ) {
		$layouts = array(
					'v1'	=> __( 'Inherit - Gallery', 'opal-hotel-room-booking' ),
					'v2'	=> __( 'Layout 2 - FullScreen Header', 'opal-hotel-room-booking' ),
					'v3'	=> __( 'Layout 3 - FullWitdh Header', 'opal-hotel-room-booking' ),
					'v4'	=> __( 'Layout 4 - Map Tabs Content', 'opal-hotel-room-booking' ),
					'v5'	=> __( 'Layout 5 - Map Header', 'opal-hotel-room-booking' ),
					'v6'	=> __( 'Layout 6 - Gallery - Tabs Content', 'opal-hotel-room-booking' )
				);
		if ( $single ) {
			$layouts = array_merge( array( '' => __( 'Global', 'opal-hotel-room-booking' ) ), $layouts );
		}
		return apply_filters( 'opalhotel_single_hotel_layouts', $layouts );
	}
}

if ( ! function_exists( 'opalhotel_is_favorited' ) ) {
	function opalhotel_is_favorited( $post_id = null ) {
		return apply_filters( 'opalhotel_is_favorited', in_array( $post_id, get_user_meta( get_current_user_id(), '_opalhotel_favorited' ) ) );
	}
}

if ( ! function_exists( 'opalhotel_get_amenities' ) ) {
	function opalhotel_get_amenities() {
		$posts = new WP_Query( apply_filters( 'opalhotel_get_amenities_args', array(
				'post_type'		=> OPALHOTEL_CPT_ANT,
				'post_status'	=> 'publish',
				'posts_per_page'	=> -1
			) ) );

		$amenities = array();
		if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) {
				$posts->the_post();
				$amenities[ get_the_ID() ] = get_the_title();
			}
			wp_reset_postdata();
		}
		return apply_filters( 'opalhotel_get_amenities', $amenities );
	}
}

if ( ! function_exists( 'opalhotel_get_the_most_cheap_room' ) ) {

	/**
	 * Get the most cheap of room
	 *
	 * @since 1.1.7
	 */
	function opalhotel_get_the_most_cheap_room( $hotel_id = null ) {
		if ( ! $hotel_id ) {
			global $post;
			$hotel_id = $post->ID;
		}

		if ( ! is_array( $hotel_id ) ) {
			$hotel_id = array( $hotel_id );
		}
		global $wpdb;

		$select = "SELECT CAST( price.meta_value AS unsigned ) AS minprice, rooms.ID FROM $wpdb->postmeta AS price";

		$join = " INNER JOIN $wpdb->posts AS rooms ON rooms.ID = price.post_id
				INNER JOIN $wpdb->postmeta AS roomMeta ON roomMeta.post_id = rooms.ID AND roomMeta.meta_key = '_hotel'
				INNER JOIN $wpdb->posts AS hotels ON hotels.ID = roomMeta.meta_value";

		$where = $wpdb->prepare("
				WHERE price.meta_key = %s
				AND rooms.post_type = %s
				AND rooms.post_status = %s
				AND hotels.post_type = %s
				AND hotels.post_status = %s
				AND hotels.ID IN ( '".implode( '\', \' ', $hotel_id )."' )
			", '_base_price', OPALHOTEL_CPT_ROOM, 'publish', OPALHOTEL_CPT_HOTEL, 'publish' );

		if ( ! empty( $_REQUEST['min-price'] ) && ! empty( $_REQUEST['max-price'] ) ) {
			$where .= " AND price.meta_value >= " . $_REQUEST['min-price'] . " AND price.meta_value <= " . $_REQUEST['max-price'];
		}

		$orderby = " ORDER BY minprice ASC";
		$sql = $select . $join . $where . $orderby;

		$result = $wpdb->get_row( $sql );
		return apply_filters( 'opalhotel_the_most_cheap_room', isset( $result->ID ) ? $result->ID : 0, $hotel_id );
	}

}

if ( ! function_exists( 'opalhotel_get_hotels_available' ) ) {

	/**
	 * Get Hotels Available when user search available
	 *
	 * @since 1.1.7
	 */
	function opalhotel_get_hotels_available() {
		$args = array(
					'post_type'			=> OPALHOTEL_CPT_HOTEL,
					'post_status'		=> 'publish',
					'posts_per_page'	=> get_option( 'posts_per_page' ),
					'paged'				=> isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : max( 1, get_query_var( 'paged' ) )
				);

		if ( ! empty( $_REQUEST['location'] ) ) {
			$args['s'] = sanitize_text_field( $_REQUEST['location'] );
		}

		$meta_query = $tax_query = array();
		// hotels star
		if ( ! empty( $_REQUEST['hotel-stars'] ) ) {
			$meta_query[] = array(
					'key'		=> '_star',
					'value'		=> $_REQUEST['hotel-stars'],
					'compare'	=> 'IN'
				);
		}
		// amenities
		if ( ! empty( $_REQUEST['amenities'] ) ) {
			$meta_query[] = array(
					'key'		=> '_amenity',
					'value'		=> $_REQUEST['amenities'],
					'compare'	=> 'IN'
				);
		}

		$taxonomy = get_query_var( 'taxonomy' );
		$term = get_query_var( 'term' );
		if ( $taxonomy ) {
			$tax_query[] = array(
					'taxonomy'	=> $taxonomy,
					'field'		=> 'slug',
					'terms'		=> array( $term ),
					'operator'  => 'IN'
				);
		}

		add_filter( 'posts_fields', 'opalhotel_hotel_available_posts_fields_filter' );
		add_filter( 'posts_join', 'opalhotel_hotel_available_join_filter' );
		add_filter( 'posts_where', 'opalhotel_hotel_available_where_filter' );
		add_filter( 'posts_orderby', 'opalhotel_hotel_available_orderby_filter' );
		add_filter( 'posts_groupby', 'opalhotel_hotel_available_groupby_filter' );

		if ( ! empty( $meta_query ) ) {
			if ( count( $meta_query ) > 1 ) {
				$meta_query[] = 'AND';
			}
			$args['meta_query'] = $meta_query;
		}

		if ( ! empty( $tax_query ) ) {
			if ( count( $tax_query ) > 1 ) {
				$taxonomy[] = 'AND';
			}
			$args['taxonomy'] = $taxonomy;
		}

		$args = apply_filters( 'opalhotel_get_hotels_available_args', $args );
		$query = new WP_Query( $args );
		// echo $query->request;
		remove_filter( 'posts_fields', 'opalhotel_hotel_available_posts_fields_filter' );
		remove_filter( 'posts_join', 'opalhotel_hotel_available_join_filter' );
		remove_filter( 'posts_where', 'opalhotel_hotel_available_where_filter' );
		remove_filter( 'posts_orderby', 'opalhotel_hotel_available_orderby_filter' );
		remove_filter( 'posts_groupby', 'opalhotel_hotel_available_groupby_filter' );

		return $query;
	}
}

if ( ! function_exists( 'opalhotel_hotel_available_posts_fields_filter' ) ) {

	function opalhotel_hotel_available_posts_fields_filter( $fields ) {
		global $wpdb;
		$fields .= ", SUM( roomAvailable.available ) AS available, minprice.minprice";
		return $fields;
	}
}

if ( ! function_exists( 'opalhotel_hotel_available_join_filter' ) ) {

	function opalhotel_hotel_available_join_filter( $join ) {

		global $wpdb;
		$join .= " INNER JOIN $wpdb->postmeta AS hotelMeta ON hotelMeta.meta_value = $wpdb->posts.ID AND hotelMeta.meta_key = '_hotel'";
		$join .= " INNER JOIN $wpdb->posts AS rooms ON hotelMeta.post_id = rooms.ID AND rooms.post_status = 'publish' AND rooms.post_type ='".OPALHOTEL_CPT_ROOM."'";

		$minPrice = ! empty( $_REQUEST['min-price'] ) ? sanitize_text_field( $_REQUEST['min-price'] ) : 0;
		$mmaxPrice = ! empty( $_REQUEST['max-price'] ) ? sanitize_text_field( $_REQUEST['max-price'] ) : 9999999999999;

		$minprice = $wpdb->prepare( "
				SELECT MIN( price.meta_value ) AS minprice, hotels.ID AS ID FROM $wpdb->postmeta AS price
					INNER JOIN $wpdb->posts AS subRooms ON subRooms.ID = price.post_id AND price.meta_key = %s
					INNER JOIN $wpdb->postmeta AS subHotelMeta ON subHotelMeta.post_id = subRooms.ID AND subHotelMeta.meta_key = %s
					INNER JOIN $wpdb->posts AS hotels ON hotels.ID = subHotelMeta.meta_value
					WHERE subRooms.post_type = %s
						AND subRooms.post_status = %s
						AND hotels.post_type = %s
						AND ( price.meta_value >= %g AND price.meta_value <= %g )
						GROUP BY hotels.ID
			", '_base_price', '_hotel', OPALHOTEL_CPT_ROOM, 'publish', OPALHOTEL_CPT_HOTEL, $minPrice, $mmaxPrice );

		$join .= " INNER JOIN ( $minprice ) AS minprice ON minprice.ID = $wpdb->posts.ID";

		if ( ! empty( $_REQUEST['adult'] ) ) {
			$join .= " INNER JOIN $wpdb->postmeta AS adultMeta ON adultMeta.post_id = rooms.ID AND adultMeta.meta_key = '_adults'";
		}

		$arrival_datetime = isset( $_REQUEST['arrival_datetime'] ) ? strtotime( $_REQUEST['arrival_datetime'] ) : current_time( 'timestamp' );
		$departure_datetime = isset( $_REQUEST['departure_datetime'] ) ? strtotime( $_REQUEST['departure_datetime'] ) : current_time( 'timestamp' ) + DAY_IN_SECONDS;
		$args = array(
				'arrival'	=> $arrival_datetime,
				'departure'	=> $departure_datetime,
				'night'		=> opalhotel_count_nights( $arrival_datetime, $departure_datetime ),
				'adult'		=> ! empty( $_REQUEST['adult'] ) ? absint( $_REQUEST['adult'] ) : 1,
				'child'		=> ! empty( $_REQUEST['child'] ) ? absint( $_REQUEST['child'] ) : 0,
				'room_type'	=> ! empty( $_REQUEST['room_type'] ) ? array( $_REQUEST['room_type'] ) : array(),
				'hotel_id'	=> ! empty( $_REQUEST['hotel_id'] ) ? absint( $_REQUEST['hotel_id'] ) : 0,
				'qty'		=> ! empty( $_REQUEST['number_of_rooms'] ) ? absint( $_REQUEST['number_of_rooms'] ) : 1
			);
		$rooms = opalhotel_get_room_available_sql( $args );

		// join rooms
		$join .= " INNER JOIN ( $rooms ) AS roomAvailable ON roomAvailable.ID = rooms.ID";

		// rating join
		if ( isset( $_REQUEST['sortable'] ) && in_array( $_REQUEST['sortable'], array( 3, 4 ) ) ) {
			$join .= " LEFT JOIN $wpdb->postmeta AS commeta ON commeta.post_ID = $wpdb->posts.ID AND commeta.meta_key = 'opalhotel_average_rating'";
		}

		return $join;
	}

}

if ( ! function_exists( 'opalhotel_hotel_available_where_filter' ) ) {

	function opalhotel_hotel_available_where_filter( $where ) {
		global $wpdb;
		if ( ! empty( $_REQUEST['adult'] ) ) {
			$where .= " AND adultMeta.meta_value >= " . esc_sql( $_REQUEST['adult'] );
		}
		$where .= " AND minprice.minprice >= 0";

		if ( ! empty( $_REQUEST['number_of_rooms'] ) && ! empty( $_REQUEST['arrival_datetime'] ) && ! empty( $_REQUEST['departure_datetime'] ) ) {
			$where .= " AND roomAvailable.available >= " . absint( $_REQUEST['number_of_rooms'] );
		} else {
			$where .= " AND roomAvailable.available > 0";
		}
		return $where;
	}

}

if ( ! function_exists( 'opalhotel_hotel_available_orderby_filter' ) ) {

	function opalhotel_hotel_available_orderby_filter( $orderby ) {

		if ( ! empty( $_REQUEST['sortable'] ) ) {
			switch ( $_REQUEST['sortable'] ) {
				case 1:
					$orderby = " minprice.minprice ASC";
					break;
				case 2:
					$orderby = " minprice.minprice DESC";
					break;
				case 3:
					$orderby = " commeta.meta_value ASC";
					break;
				case 4:
					$orderby = " commeta.meta_value DESC";
					break;

				default:
					# code...
					break;
			}
		}

		return $orderby;
	}

}

if ( ! function_exists( 'opalhotel_hotel_available_groupby_filter' ) ) {

	function opalhotel_hotel_available_groupby_filter( $groupby ) {
		global $wpdb;

		if ( strpos( $groupby, "$wpdb->posts.ID" ) === false ) {
			$groupby .= " $wpdb->posts.ID";
		}
		return $groupby;
	}

}

if ( ! function_exists( 'opalhotel_get_rooms_hotel_count' ) ) {

	/**
	 * Count rooms of hotel
	 *
	 * @param @hotel_id
	 * @return numeric
	 * @since 1.1.7
	 */
	function opalhotel_get_rooms_hotel_count( $hotel_id = null ) {
		global $wpdb;

		$sql = $wpdb->prepare("
				SELECT COUNT( meta.post_id ) FROM $wpdb->postmeta AS meta
					INNER JOIN $wpdb->posts AS rooms ON rooms.ID = meta.post_id
					INNER JOIN $wpdb->posts AS hotels ON hotels.ID = meta.meta_value
				WHERE
					rooms.post_type = %s
					AND rooms.post_status = %s
					AND hotels.post_type = %s
					AND hotels.post_status = %s
					AND meta.meta_key = %s
					AND hotels.ID = %d
			", OPALHOTEL_CPT_ROOM, 'publish', OPALHOTEL_CPT_HOTEL, 'publish', '_hotel', $hotel_id );

		return apply_filters( 'opalhotel_get_rooms_hotel_count', $wpdb->get_var( $sql ), $hotel_id );
	}

}



