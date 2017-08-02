<?php
/**
 * @Author: brainos
 * @Date:   2016-04-28 20:41:29
 * @Last Modified by:   someone
 * @Last Modified time: 2016-05-09 19:23:21
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

abstract class OpalHotel_Abstract_Service {

	/* protected id of post type */
	protected $id = null;

	/* public data */
	public $data = null;

	/* constructor */
	public function __construct( $id, $args = array() ) {
		/* set data */
		if ( is_numeric( $id ) && $post = get_post( $id ) ) {
			$this->id = $post->ID;
			$this->data = $post;
		} else if ( $id instanceof WP_Post ) {
			$this->id = $id->ID;
			$this->data = $id;
		}

		/* set property */
		if ( ! empty( $args ) ) {
			foreach ( $args as $key => $value ) {
				$this->$key = $value;
			}
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
			return $this->$name();
		}

		/* return default */
		return $default;
	}

	/* get price */
	protected function get_price() {}

	/* get total include tax */
	public function get_price_incl_tax( $price = '', $qty = 1 ) {

		if ( ! $price ) {
			$price = $this->get_price();
		}

		$price = $price * $qty;

		$price += $price * opalhotel_get_tax() / 100;

		return $price;
	}

	/* get total exclude tax */
	public function get_price_excl_tax( $price = '', $qty = 1 ) {

		if ( ! $price ) {
			$price = $this->get_price();
		}
		return $price * $qty;

	}

	/*
	 *
	 * price display
	 *
	 * @return include tax if setting enable include tax in room or not
	 *
	 */
	public function get_price_display( $price = '', $qty = 1 ) {
		if ( ! $price ) {
			$price = $this->get_price();
		}
		$incl = get_option( 'opalhotel_tax_incl_room', 0 );
		if ( opalhotel_tax_is_enable() && $incl ) {
			return $this->get_price_incl_tax( $price, $qty );
		} else {
			return $this->get_price_excl_tax( $price, $qty );
		}
	}

	/* title service */
	public function get_title() {
		return $this->data->post_title;
	}

	/* title */
	public function get_description() {
		return apply_filters( 'the_content', $this->data->post_content );
	}

	/* post status is publish */
	public function is_exists() {
		return get_post_status( $this->id ) === 'publish' ;
	}

	/* get permalink */
	public function get_permalink() {
		return get_permalink( $this->id );
	}

}