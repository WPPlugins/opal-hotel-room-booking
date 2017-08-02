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

class OpalHotel_Cart {

	/* instance */
	static $instance = null;

	/* cart contents */
	public $cart_contents = array();

	/* total */
	public $total = 0;

	/* sub total */
	public $subtotal = 0;

	/* var sub total exclude tax */
	public $subtotal_excl_tax = 0;

	/* var tax total */
	public $tax_total = 0;

	/* var discoun total */
	public $discount_total = 0;

	/* var coupons array */
	public $coupons = array();

	/* var coupon_discounts array */
	public $coupon_discounts = array();

	/* is empty cart */
	public $is_empty = true;

	/* is enable tax */
	private $tax_enable = true;

	/* is enable tax include cart */
	private $incl_tax = false;

	private $packages = null; 
	/* cart session data */
	protected $cart_session_data = array(
			'total'					=> 0,
			'subtotal'				=> 0,
			'subtotal_excl_tax'		=> 0,
			'tax_total'				=> 0,
			'discount_total'		=> 0
		);

	public function __construct(){

		$this->tax_enable = opalhotel_tax_enable();

		$this->incl_tax = opalhotel_tax_enable_cart();

		/**
		 * wp_loaded load cart contents
		 * 'wp_loaded' hook before 'init' hook
		 */
		add_action( 'wp_loaded', array( $this, 'init' ) );

		/* calculate_refresh cart content */
		// add_action( 'opalhotel_add_to_cart', array( $this, 'calculate_refresh' ), 99 );

		add_action( 'wp', array( $this, 'maybe_set_cart_cookies' ), 99 ); // Set cookies
		add_action( 'shutdown', array( $this, 'maybe_set_cart_cookies' ), 0 ); // Set cookies before shutdown and ob flushing

		/* check room available after added to cart */
		add_action( 'opalhotel_checkout_room_available', array( $this, 'check_room_available' ) );
	}

	public function init() {
		/* load cart contents */
		$this->load_cart_contents();
	}

	/* load cart content from seesion database or cookie */
	public function load_cart_contents( $calculate = true ) {

		// cart contents
		$cart_session = OpalHotel()->session->cart;
		$this->coupons = OpalHotel()->session->coupons;

		if ( is_array( $cart_session ) && ! empty( $cart_session ) ) {
			foreach ( $cart_session as $cart_item_id => $cart_item_param ) {
				$data = opalhotel_get_product_class( $cart_item_param['product_id'], $cart_item_param );
				$cart_item = array_merge( $cart_item_param, array( 'data' => $data ) );
				$cart_item['base_price'] = $data->base_price();
				$cart_item['price']	= $data->get_price( $cart_item_param );
				$this->cart_contents[ $cart_item_id ] = apply_filters( 'opalhotel_load_cart_item', $cart_item );
				$this->cart_contents[ $cart_item_id ]['subtotal']	= OpalHotel()->cart->get_cart_item_subtotal_excl_tax( $cart_item_id );
				$this->cart_contents[ $cart_item_id ]['tax_total']	= OpalHotel()->cart->get_cart_item_subtotal_incl_tax( $cart_item_id ) - $this->cart_contents[ $cart_item_id ]['subtotal'];
			}
		}

		/* cart contents */
		if ( ! empty( $this->cart_contents ) ) {
			$this->is_empty = false;
		}

		foreach ( $this->cart_session_data as $name => $value ) {
			$this->{$name} = OpalHotel()->session->get( $name, $value );
		}

		if ( $calculate ) {
			/* calculate_refresh cart */
			$this->calculate_refresh();
		}

		do_action( 'opalhotel_load_cart_contents', $this );
	}

	public function is_empty() {
		return empty( $this->cart_contents );
	}

	/* get rooms */
	public function get_rooms() {

		$rooms = array();
		foreach ( $this->cart_contents as $cart_item_id => $cart_item ) {
			$data = $cart_item['data'];
			if ( $data->post_type === OPALHOTEL_CPT_ROOM ) {
				$rooms[ $cart_item_id ] = $cart_item;
			}
		}

		return apply_filters( 'opalhotel_get_room_cart', $rooms );
	}

	/* get package extra room */
	public function get_packages( $cart_room_id = null ) {
		if( $this->packages == null ){
			$packages = array();
			if ( ! $cart_room_id ) {
				return $packages;
			}

			foreach ( $this->cart_contents as $package_cart_id => $package ) {
				if ( isset( $package['parent_id'] ) && $package['parent_id'] == $cart_room_id ) {
					$packages[ $package_cart_id ] = $package;
				}
			}

			$this->packages = $packages;
		}
		return apply_filters( 'opalhotel_get_packages_cart', $this->packages, $cart_room_id );
	}

	/* maybe set cart cookies */
	public function maybe_set_cart_cookies() {
		/* cannot set cookie after headers sents  */
		if ( ! headers_sent() && did_action( 'wp_loaded' ) ) {
			/* trigger save user id to cookie */
			do_action( 'opalhotel_user_maybe_has_cart', $this->is_empty );
		}
	}

	/* add to cart */
	public function add_to_cart( $product_id = null, $qty = 0, $cart_item_param = array() ) {
		/* product id invalid */
		if ( ! $product_id ) {
			return new WP_Error( 'add_to_cart', sprintf( __( 'Product id %s unavailable.', 'opal-hotel-room-booking' ), $product_id ) );
		}
		/* sanitize product id */
		$product_id = absint( $product_id );

		if ( $qty <= 0 ) {
			return new WP_Error( 'add_to_cart', sprintf( __( 'Product quanity %s not valid.', 'opal-hotel-room-booking' ), $qty ) );
		}
		/* sanitize quanity */
		$qty = absint( $qty );

		/* generate cart item id */
		$cart_item_param['product_id'] = $product_id;
		// $adult = isset( $cart_item_param['adult'] ) ? $cart_item_param['adult'] :
		$cart_item_id = $this->generate_cart_id( $cart_item_param );
		$cart_item_param['qty']	= $qty;
		$cart_item_param = apply_filters( 'opalhotel_add_to_cart_params', $cart_item_param, $product_id );

		/* cart item exists */
		if ( isset( $this->cart_contents[ $cart_item_id ] ) ) {
			$this->cart_contents[ $cart_item_id ][ 'qty' ] = absint( $qty );
		} else {
			/* load cart_item['data'] */
			$data = opalhotel_get_product_class( $cart_item_param['product_id'], $cart_item_param );
			$cart_item = apply_filters( 'opalhotel_add_to_cart_item', $cart_item_param, $product_id );
			/* merge cart item */
			$cart_item = array_merge( $cart_item, array( 'data' => $data ));
			$this->cart_contents[ $cart_item_id ] = $cart_item;
		}

		do_action( 'opalhotel_add_to_cart', $cart_item_id, $product_id, $qty, $cart_item_param );

		$this->calculate_refresh();

		/* not empty */
		$this->is_empty = false;

		return apply_filters( 'opalhotel_added_cart_item', $cart_item_id );

	}

	/* add coupons */
	public function add_coupon( $code = null ) {
		$coupon = opalhotel_get_coupon_by_code( $code, $this->subtotal );
		if ( is_wp_error( $coupon ) ) {
			return $coupon;
		}

    	if ( isset( $this->coupons['id'] ) && $this->coupons['id'] === $code ) {
    		return new WP_Error( 'coupon_not_found', sprintf( __( 'Coupon code %s was added. Can not uses one more time.', 'opal-hotel-room-booking' ), $code ) );
    	}

    	$this->coupons['id'] = $coupon;
		$this->coupons['code'] = $code;

		OpalHotel()->session->coupons = $this->coupons;

		/* refresh cart */
		$this->calculate_refresh();

		return true;
	}

	/* remove coupon */
	public function remove_coupon( $code = null ) {
		if ( isset( $this->coupons['id'] ) && $this->coupons['id'] === $code ) {
			$this->coupons = array();
			OpalHotel()->session->coupons = $this->coupons;

			/* refresh cart */
			$this->calculate_refresh();
		}
	}

	/* remove cart item */
	public function remove_cart_item( $cart_item_id = null ) {

		if ( isset( $this->cart_contents[ $cart_item_id ] ) ) {
			unset( $this->cart_contents[ $cart_item_id ] );
		}

		$this->remove_cart_item_child( $cart_item_id, false );

		/* trigger save data session */
		do_action( 'opalhotel_remove_cart_item', $cart_item_id, $this->cart_contents );

		/* refresh cart */
		$this->calculate_refresh();

		/* check available again */
		do_action( 'opalhotel_checkout_room_available' );
	}

	/* remove cart item has parent_id is $cart_item_id */
	public function remove_cart_item_child( $cart_parent_item_id = null, $refresh = true ) {

		if ( empty( $this->cart_contents ) ) {
			return;
		}

		foreach ( $this->cart_contents as $cart_id => $cart_content ) {
			if ( isset( $cart_content[ 'parent_id' ] ) && $cart_content[ 'parent_id' ] === $cart_parent_item_id ) {
				unset( $this->cart_contents[ $cart_id ] );
			}
		}

		if ( $refresh ) {
			$this->calculate_refresh();
		}

	}

	/* generate cart item id */
	public function generate_cart_id( $params = array() ) {
    	return opalhotel_generate_uniqid_hash( $params );
    }

    /* calculate_refresh cart */
    public function calculate_refresh() {

    	if ( $this->is_empty || empty( $this->cart_contents ) ) {
    		return $this->set_session();
    	}

    	/* reset calculate_refresh cart contents */
    	foreach ( $this->cart_session_data as $name => $value ) {
    		$this->$name = $value;
    	}

    	/* calculate_refresh process */
    	foreach ( $this->cart_contents as $cart_item_id => $cart_item ) {
    		if ( ! $cart_item ) { continue; }
    		$this->subtotal += $this->get_cart_item_subtotal_incl_tax( $cart_item_id );
    		$this->subtotal_excl_tax += $this->get_cart_item_subtotal_excl_tax( $cart_item_id );
    	}

    	/* tax total */
    	$this->tax_total = $this->subtotal - $this->subtotal_excl_tax;

    	/* discount coupon */
    	if ( $this->coupons ) {
    		$coupon_id = $this->coupons['id']; /* coupon id */
    		$coupon_code = $this->coupons['code'];

	    	$coupon = OpalHotel_Coupon::instance( $coupon_id );
    		$discount = $coupon->calculate_discount( $this->subtotal_excl_tax );

    		$this->discount_total += $discount;

    		$this->coupon_discounts[ $coupon_code ] = $discount;

    		/* calculate new tax total */
	    	$new_subtotal_excl_tax = $this->subtotal_excl_tax - $this->discount_total;
	    	$this->tax_total = $this->subtotal - ( $this->subtotal - ( $new_subtotal_excl_tax ) * $this->subtotal / $this->subtotal_excl_tax ) - $new_subtotal_excl_tax;
    	}

    	/* total */
    	$this->total = $this->subtotal_excl_tax - $this->discount_total + $this->tax_total;
    	/* set session */
    	$this->set_session();
    }

    /* get cart item return class process */
    public function get_cart_item( $cart_item_id = null ) {
    	if ( ! $cart_item_id || ! isset( $this->cart_contents[ $cart_item_id ] ) ) {
    		return false;
    	}

    	return $this->cart_contents[ $cart_item_id ];
    }

    /* set session storge data */
    public function set_session() {

    	$cart_session = $this->session_for_cart();

    	OpalHotel()->session->cart = $cart_session;

    	foreach ( $this->cart_session_data as $name => $value ) {
    		OpalHotel()->session->{ $name } = $this->{$name};
    	}

    	do_action( 'opalhotel_cart_set_session' );
    }

    /**
     * session_for_cart
     * @return array cart param
     */
    public function session_for_cart() {
    	$cart_session = array();

    	if ( ! $this->cart_contents ) {
    		return $cart_session;
    	}
    	foreach ( $this->cart_contents as $cart_item_id => $cart_item_param ) {
    		$cart_session[ $cart_item_id ] = array();
    		if ( ! $cart_item_param ) { continue; }
    		foreach ( $cart_item_param as $name => $value ) {
    			$cart_session[ $cart_item_id ][ $name ] = $value;
    			unset( $cart_session[ $cart_item_id ][ 'data' ] );
    		}
    	}

    	return apply_filters( 'opalhotel_session_for_cart', $cart_session, $this );
    }

    /* get room subtotal in cart if exists */
    public function get_room_subtotal( $cart_room_id = null ) {
    	$subtotal = 0;
    	if ( ! $cart_room_id ) {
    		return false;
    	}

    	$subtotal += $this->get_cart_item_subtotal( $cart_room_id );
    	$packages = $this->get_packages( $cart_room_id );
    	foreach ( $packages as $package_cart_id => $package ) {
    		$subtotal += $this->get_cart_item_subtotal( $package_cart_id );
    	}

    	return apply_filters( 'opalhotel_cart_room_subtotal', $subtotal, $cart_room_id );
    }

    /**
     * get_cart_subtotal get subtotal of cart item
     * @param  $cart_item_id string uninid key
     * @return float
     */
    public function get_cart_item_subtotal( $cart_item_id = null ) {
    	$subtotal = 0;
    	/* check include tax setting */
    	if ( $this->tax_enable && $this->incl_tax ) {
    		$subtotal = $this->get_cart_item_subtotal_incl_tax( $cart_item_id );
    	} else {
    		$subtotal = $this->get_cart_item_subtotal_excl_tax( $cart_item_id );
    	}

    	return apply_filters( 'opalhotel_cart_item_subtotal', $subtotal, $cart_item_id );
    }

    /* get cart item include tax */
    public function get_cart_item_subtotal_incl_tax( $cart_item_id = null ) {
    	$subtotal = 0;
    	if ( ! isset( $this->cart_contents[ $cart_item_id ] ) ) {
    		return $subtotal;
    	}

    	$cart_item = $this->cart_contents[ $cart_item_id ];
    	$cart_item_data = $cart_item['data'];
    	$subtotal = $cart_item_data->get_price_incl_tax( $cart_item_data->get_price( $cart_item ), $cart_item['qty'] );

    	return apply_filters( 'opalhotel_cart_item_subtotal_incl_tax', $subtotal, $cart_item_id );
    }

    /* get cart item exclude tax */
    public function get_cart_item_subtotal_excl_tax( $cart_item_id = null ) {
    	$subtotal = 0;
    	if ( ! isset( $this->cart_contents[ $cart_item_id ] ) ) {
    		return $subtotal;
    	}

    	$cart_item = $this->cart_contents[ $cart_item_id ];
    	$cart_item_data = $cart_item['data'];
    	$subtotal = $cart_item_data->get_price_excl_tax( $cart_item_data->get_price( $cart_item ), $cart_item['qty'] );

    	return apply_filters( 'opalhotel_cart_item_subtotal_excl_tax', $subtotal, $cart_item_id );
    }

    /* get tax total */
    public function get_tax_total() {
    	return apply_filters( 'opalhotel_cart_tax_total', $this->tax_total );
    }

    /* get cart subtotal display */
    public function get_cart_subtotal_display() {
    	$subtotal = 0;
    	if ( $this->tax_enable && $this->incl_tax ) {
    		$subtotal = $this->subtotal;
    	} else {
    		$subtotal = $this->subtotal_excl_tax;
    	}

    	return apply_filters( 'opalhotel_get_cart_subtotal_display', $subtotal );
    }

    /* get subtotal of cart */
    public function get_cart_subtotal() {
    	return apply_filters( 'opalhotel_cart_subtotal', $this->subtotal );
    }

    /* get total of cart */
    public function get_cart_total() {
    	return apply_filters( 'opalhotel_cart_total', $this->total );
    }

    /* need payment when cart total > 0 */
    public function need_payment() {
    	return $this->get_cart_total() > 0;
    }

    /* check room, coupon available after added */
    public function check_room_available() {

    	if ( $this->is_empty || empty( $this->cart_contents ) ) {
    		return;
    	}

    	foreach ( $this->cart_contents as $cart_item_id => $cart_item ) {
    		$data = $cart_item['data'];
    		if ( $data->post_type !== OPALHOTEL_CPT_ROOM ) continue;

    		$available = opalhotel_room_check_available( $cart_item['product_id'], $cart_item['arrival'], $cart_item['departure'], $cart_item['qty'] );

    		if ( ! $available || is_wp_error( $available ) ) {
    			$this->remove_cart_item( $cart_item_id );
    			opalhotel_add_notices( sprintf( __( '%s room is not available right now. This has removed from your reservation.' ), $data->post_title ) );
    		}
    	}

    	if ( $this->coupons && isset( $this->coupons['id'], $this->coupons['code'] ) ) {
    		$coupon = opalhotel_get_coupon_by_code( $this->coupons['code'], $this->subtotal );
    		if ( is_wp_error( $coupon ) ) {
    			$this->remove_coupon( $this->coupons['id'] );

    			opalhotel_add_notices( sprintf( __( 'Coupon %s has removed, because %s' ), $this->coupons['code'], $coupon->get_error_message() ) );
    		}
    	}

    }

    /* remove cart */
    public function empty_cart() {
    	OpalHotel()->session->cart = null;
    	OpalHotel()->session->coupons = null;
    	OpalHotel()->session->order_waiting_payment = null;
    	OpalHotel()->session->total = 0;
    	OpalHotel()->session->subtotal = 0;
    	OpalHotel()->session->subtotal_excl_tax = 0;
    	OpalHotel()->session->tax_total = 0;
    	OpalHotel()->session->discount_total = 0;
    }

	/* static instance insteadof new class single time*/
	public static function instance () {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
