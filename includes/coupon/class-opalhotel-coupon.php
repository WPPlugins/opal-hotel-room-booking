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

class OpalHotel_Coupon {

	/* protected id of post type */
	public $id = null;

	// instance insteadof new class
	static $instance = null;


	/* public data */
	public $data = null;

	/* constructor */
	public function __construct( $id ) {
		/* set data */
		if ( is_numeric( $id ) && $post = get_post( $id ) ) {
			$this->id = $post->ID;
			$this->data = $post;
		} else if ( $id instanceof WP_Post ) {
			$this->id = $id->ID;
			$this->data = $id;
		}
	}

	/* set magic method */
	public function __set( $key, $value = null ) {
		$this->data->{$key} = $value;
	}

	/* get magic method */
	public function __get( $key = null ) {
		if ( ! $key ) return;
		return $this->get( $key );
	}

	/* get data */
	public function get( $name = null, $default = null ) {

		if ( isset( $this->data->{$name} ) ) {
			/* get data post*/
			return $this->data->{$name};
		} else if ( metadata_exists( 'post', $this->id, '_' . $name ) ) {
			/* get post meta */
			return get_post_meta( $this->id, '_' . $name, true );
		} else if ( method_exists( $this, $name ) ) {
			/* get method */
			return $this->{$name}();
		}

		/* return default */
		return $default;
	}

	/* check available */
	public function is_available( $price = null ) {

		if ( ! $this->id ) {
			return new WP_Error( 'coupon_not_exists', sprintf( __( 'Coupon code <span>%s</span> is not exists.', 'opal-hotel-room-booking' ), $this->id ) );
		}

		if ( ! $price ) {
			$price = 0;
		}

		/* validate expire time */
		$current_time = time();
		if ( $current_time >= $this->coupon_expire_timestamp ) {
			return new WP_Error( 'coupon_expired', sprintf( __( 'Coupon code <span>%s</span> has been expired.', 'opal-hotel-room-booking' ), $this->id ) );
		}

		/* vaildate useage time */
		if ( $this->coupon_usage_time_limit && opalhotel_coupon_useaged( $this->id ) >= $this->coupon_usage_time_limit ) {
			return new WP_Error( 'coupon_usage_limited', sprintf( __( 'Coupon code <span>%s</span> has been limited.', 'opal-hotel-room-booking' ), $this->id ) );
		}

		/* minium spend */
		if ( $this->coupon_minimum_spend && $this->coupon_minimum_spend >= $price ) {
			return new WP_Error( 'coupon_spend', sprintf( __( 'Your total must elder <span>%s</span>.', 'opal-hotel-room-booking' ), opalhotel_format_price( $this->coupon_minimum_spend ) ) );
		}

		/* discount amount */
		if ( $this->coupon_type === 'fixed_cart' && $this->coupon_amount > $price ) {
			return new WP_Error( 'coupon_spend', sprintf( __( 'Your total must elder <span>%s</span>.', 'opal-hotel-room-booking' ), opalhotel_format_price( $this->coupon_amount ) ) );
		}

		/* minium spend */
		if ( $this->coupon_maximum_spend && $this->coupon_maximum_spend <= $price ) {
			return new WP_Error( 'coupon_spend', sprintf( __( 'Your total must lesser <span>%s</span>.', 'opal-hotel-room-booking' ), opalhotel_format_price( $this->coupon_maximum_spend ) ) );
		}

		return $this->id;

	}

	/* discount */
	public function calculate_discount( $subtotal = 0 ) {
		if ( ! $subtotal ) {
			return;
		}

		$discount = $this->coupon_amount;
		if ( $this->coupon_type === 'percent_cart' ) {
			$discount = $subtotal * floatval( $this->coupon_amount ) / 100;
		}

		return apply_filters( 'opalhotel_coupon_discount', $discount, $this );
	}

	/**
	 * instance insteadof new class
	 * @param  $coupon optional Eg: id, object
	 * @return object
	 */
	static function instance( $coupon = null ) {
		$id = null;
		if ( $coupon instanceof WP_POST ) {
			$id = $coupon->ID;
		} else if ( is_numeric( $coupon ) ) {
			$id = $coupon;
		} else if ( is_object( $coupon ) && isset( $coupon->ID ) ) {
			$id = $coupon->ID;
		}

		if ( empty( self::$instance[ $id ] ) ) {
			self::$instance[ $id ] = new self( $id );
		}

		return self::$instance[ $id ];

	}

}
