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

class OpalHotel_Package extends OpalHotel_Abstract_Service {

	/* $id property */
	public $id = null;

	// instance insteadof new class
	static $instance = null;

	public function __construct( $id = null ) {
		parent::__construct( $id );
	}

	/* get base price */
	public function base_price() {
		return apply_filters( 'opalhotel_package_base_price', (float)$this->package_amount );
	}

	/* get price */
	public function get_price() {
		return apply_filters( 'opalhotel_package_price', floatval( $this->base_price() ) );
	}

	/**
	 * instance insteadof new class
	 * @param  $package optional Eg: id, object
	 * @return object
	 */
	static function instance( $package = null ) {
		$id = null;
		if ( $package instanceof WP_POST ) {
			$id = $package->ID;
		} else if ( is_numeric( $package ) ) {
			$id = $package;
		} else if ( is_object( $package ) && isset( $package->ID ) ) {
			$id = $package->ID;
		}

		if ( empty( self::$instance[ $id ] ) ) {
			self::$instance[ $id ] = new self( $id );
		}

		return self::$instance[ $id ];

	}

}
