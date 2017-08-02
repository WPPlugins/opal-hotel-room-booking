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

class OpalHotel_Hotel extends OpalHotel_Abstract_Service {

	/* $id property */
	public $id = null;

	// instance insteadof new class
	static $instance = null;

	/* constructor set object data */
	public function __construct( $id = null ) {
		parent::__construct( $id );
	}

	/* get galleries full image */
	public function get_gallery_image_item( $id = null, $size = 'room_gallery' ) {
		return wp_get_attachment_image( $id, $size, true );
	}

	/* get galleries url image */
	public function get_gallery_image_item_url( $id = null, $size = 'room_gallery' ) {
		$src = wp_get_attachment_image_src( $id, $size, true );
		return $src[0];
	}

	/* get galleries thumb image */
	public function get_gallery_thumb_item( $id = null, $size = 'room_thumb' ) {
		return wp_get_attachment_image( $id, $size, true );
	}

	/* get catalog thumbnail */
	public function get_catalog_thumbnail( $size = 'room_catalog' ) {
		return get_the_post_thumbnail( $this->id, $size, true );
	}

	public function get_rooms( $args = array() ) {
		$args = wp_parse_args( $args, array(
				'posts_per_page'	=> 5
			) );

		extract( $args );
		$args = array(
				'post_type'			=> OPALHOTEL_CPT_ROOM,
				'post_status'		=> 'publish',
				'posts_per_page'	=> $posts_per_page,
				'meta_key'			=> '_hotel',
				'meta_value'		=> $this->id
			);

		return new WP_Query( apply_filters( 'opalhotel_get_hotel_rooms', $args ) );
	}

	public function get_rooms_available( $args = array() ) {
		$args = wp_parse_args( $args, array(
				'posts_per_page'	=> 5//,
				// 'arrival_datetime'	=> '',
				// 'departure_datetime'=> '',
				// 'rooms'				=> 1,
				// 'adult'				=> 1,
				// 'child'				=> 0
			) );

		extract( $args );

		// if ( isset( $_REQUEST['arrival_datetime'] ) && isset( $_REQUEST['departure_datetime'] ) ) {
			add_filter( 'posts_fields', array( $this, 'rooms_available_posts_fields' ) );
			add_filter( 'posts_join', array( $this, 'rooms_available_posts_join' ) );
		// }
		$args = array(
				'post_type'			=> OPALHOTEL_CPT_ROOM,
				'post_status'		=> 'publish',
				'posts_per_page'	=> $posts_per_page,
				'meta_key'			=> '_hotel',
				'meta_value'		=> $this->id
			);

		$query = new WP_Query( apply_filters( 'opalhotel_get_hotel_rooms_available', $args ) );

		// if ( isset( $_REQUEST['arrival_datetime'] ) && isset( $_REQUEST['departure_datetime'] ) ) {
			remove_filter( 'posts_fields', array( $this, 'rooms_available_posts_fields' ) );
			remove_filter( 'posts_join', array( $this, 'rooms_available_posts_join' ) );
		// }

		return $query;
	}

	/**
	 * posts fields rooms available
	 */
	public function rooms_available_posts_fields( $fields ) {
		$fields .= ", roomAvailable.available";
		return $fields;
	}

	/**
	 * posts join rooms available
	 */
	public function rooms_available_posts_join( $join ) {
		global $wpdb;

		$current_day_timestamp = strtotime( date( 'Y-m-d', current_time( 'timestamp' ) ) );
		$arrival = isset( $_REQUEST['arrival_datetime'] ) ? strtotime( $_REQUEST['arrival_datetime'] ) : $current_day_timestamp;
		$departure = isset( $_REQUEST['departure_datetime'] ) ? strtotime( $_REQUEST['departure_datetime'] ) : $current_day_timestamp + DAY_IN_SECONDS;

		$adult = isset( $_REQUEST['adult'] ) ? absint( $_REQUEST['adult'] ) : 1;
		$child = isset( $_REQUEST['child'] ) ? absint( $_REQUEST['child'] ) : 0;
		$qty = isset( $_REQUEST['number_of_rooms'] ) ? absint( $_REQUEST['number_of_rooms'] ) : 1;
		$rooms_sql = opalhotel_get_room_available_sql( array(
				'arrival'		=> $arrival,
				'departure'		=> $departure,
				'night'			=> opalhotel_count_nights( $arrival, $departure ),
				'adult'			=> $adult,
				'child'			=> $child,
				'qty'			=> $qty,
				'hotel_id'		=> $this->id
			) );

		$join .= " INNER JOIN $wpdb->postmeta AS hMeta ON hMeta.post_id = $wpdb->posts.ID AND hMeta.meta_key = '_hotel' AND hMeta.meta_value = " . $this->id;
		$join .= " INNER JOIN ($rooms_sql) AS roomAvailable ON roomAvailable.ID = $wpdb->posts.ID";
		return $join;
	}

	/**
	 * Get the most cheap room
	 * 
	 * @since 1.1.7
	 */
	public function get_the_most_cheap_room( $hotel_id = null ) {
		return opalhotel_get_the_most_cheap_room( $hotel_id );
	}

	/**
	 * Get get_average_rating
	 * @return floatval
	 */
	public function get_average_rating( $rating_item_ID = null, $calculate = false ) {
		return opalhotel_get_average_rating( $this->id, $rating_item_ID, $calculate );
	}

	/**
	 * Total review has rating
	 */
	public function get_total_review_has_rating( $rating_item_ID = null ) {
		return opalhotel_get_review_has_rating_count( $this->id, $rating_item_ID );
	}

	/**
	 * Count review has rating is 5 stars, 4 stars, 3 stars
	 * @return int
	 */
	public function get_rating_count( $star = 5 ) {
		return opalhotel_get_rating_count( $this->id, $star );
	}

	/**
	 * Calculator
	 * Star Rating Percent
	 */
	public function get_rating_percent( $star = 5, $rating_item_ID = null ) {
		return opalhotel_get_rating_percent( $this->id, $star, $rating_item_ID );
	}

	/**
	 * Check hotel has discount
	 *
	 * @since 1.1.7
	 */
	public function get_discount( $date = '' ) {
		if ( ! $date ) {
			$date = date( 'Y-m-d' );
		}

		$discounts = $this->get( '_discount_group', false );

		$value = false;
		if ( $discounts ) {
			$current_time = current_time( 'timestamp' );
			foreach ( $discounts as $k => $discount ) {
				if ( empty( $discount['start'] ) || empty( $discount['end'] ) || empty( $discount['value'] ) ) continue;

				$start = strtotime( $discount['start'] );
				$end = strtotime( $discount['end'] );

				if ( $start <= $current_time && $end > $current_time ) {
					$value = floatval( $discount['value'] );
					break;
				}
			}
		}
		return apply_filters( 'opalhotel_hotel_get_discount', $value, $this->id );
	}

	/**
	 * instance insteadof new class
	 * @param  $hotel optional Eg: id, object
	 * @return object
	 */
	public static function instance( $hotel = null ) {
		$id = null;
		if ( $hotel instanceof WP_POST ) {
			$id = $hotel->ID;
		} else if ( is_numeric( $hotel ) ) {
			$id = $hotel;
		} else if ( is_object( $hotel ) && isset( $hotel->ID ) ) {
			$id = $hotel->ID;
		}

		if ( empty( self::$instance[ $id ] ) ) {
			self::$instance[ $id ] = new self( $id );
		}

		return self::$instance[ $id ];

	}

}
