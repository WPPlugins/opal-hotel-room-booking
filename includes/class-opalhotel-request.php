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

class OpalHotel_Request {

	public $endpoints = array();

	/* protected actions */
	protected $actions = array();

	/* protected property */
	protected static $instance = null;

	/* get instance of object */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/* constructor */
	public function __construct() {

		add_action( 'init', array( $this, 'add_rewrite_endpoint' ), 0 );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
		add_action( 'parse_request', array( $this, 'parse_request' ), 0 );
		add_filter( 'pre_get_document_title', array( $this, 'endpoint_title' ), 10 );
		add_action( 'opalhotel_api_request', array( $this, 'process_endpoint' ), 0 );
	}

	/* add rewite endpoint */
	public function add_rewrite_endpoint(){
		$this->endpoints = apply_filters( 'opalhotel_endpoints', array(
				'reservation-received'		=> get_option( 'opalhotel_reservation-received_enpoint', 'reservation-received' ),
				'reservation-cancelled'		=> get_option( 'opalhotel_reservation-cancelled_enpoint', 'reservation-cancelled' ),
				'reservation-notify'		=> get_option( 'opalhotel_reservation-notify_enpoint', 'reservation-notify' )
			) );
		foreach ( $this->endpoints as $key => $var ) {
	        add_rewrite_endpoint( $var, EP_PERMALINK | EP_PAGES );
	    }
	    flush_rewrite_rules();

	    $this->endpoint_title = apply_filters( 'opalhotel_endpoints_title', array(
				'reservation-received'		=> __( 'Reservation Recevied', 'opal-hotel-room-booking' ),
				'reservation-cancelled'		=> __( 'Reservation Cancelled', 'opal-hotel-room-booking' ),
				'reservation-notify'		=> __( 'Reservation Notify', 'opal-hotel-room-booking' ),
			) );
	}

	/* add query vars */
	public function add_query_vars( $vars ) {
		 foreach ( $this->endpoints as $k => $v ) {
	        $vars[] = $k;
	    }

	    return $vars;
	}

	/* parse request */
	public function parse_request() {
		global $wp;

	    // Map query vars to their keys, or get them if endpoints are not supported
	    foreach ( $this->endpoints as $key => $var ) {
	        if ( isset( $_GET[ $var ] ) ) {
	            $wp->query_vars[ $key ] = $_GET[ $var ];
	        } elseif ( isset( $wp->query_vars[ $var ] ) ) {
	            $wp->query_vars[ $key ] = $wp->query_vars[ $var ];
	        }
	    }
	    // Trigger generic action before request hook.
		do_action( 'opalhotel_api_request' );
	}

	/* process endpoint */
	public function process_endpoint() {
		global $wp;
		do_action( 'opalhotel_process_endpoint', $this->get_current_endpoint(), $this->endpoints, $wp->query_vars );
		do_action( 'opalhotel_process_endpoint_' . $this->get_current_endpoint(), $this->endpoints, $wp->query_vars );
	}

	/* get current endpoint */
	public function get_current_endpoint() {
		global $wp;
		foreach( $this->endpoints as $key => $var ) {
			if( isset( $wp->query_vars[ $var ] ) ) {
				return $key;
			}
		}
	}

	/* get endpoint title */
	public function endpoint_title( $endpoint = '' ) {
		global $wp;
		foreach ( $wp->query_vars as $key => $var ) {
			if ( array_key_exists( $key, $this->endpoint_title )  ) {
				$endpoint = sprintf( '%s', $this->endpoint_title[ $key ] );
			}
		}
		return $endpoint;
	}

}
