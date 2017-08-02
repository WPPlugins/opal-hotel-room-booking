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

class OpalHotel_Tempalte_Loader {

	public function __construct() {

		/*
		 * template include filter
		 *
		 * filter single room, room taxonomy template
		 */
		add_filter( 'template_include', array( $this, 'template_include' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );
	}

	/*
	 *
	 * include template
	 *
	 * @return template
	 */
	public function template_include( $template ) {

		$post_type = get_post_type();

        $file = '';
        $find = array();

        if ( is_post_type_archive( OPALHOTEL_CPT_ROOM ) ) {
        	/* archive */
            $file = 'archive-room.php';
            $find[] = $file;
            $find[] = OpalHotel()->template_path() . '/' . $file;

        } else if ( opalhotel_is_room_taxonomy() || opalhotel_is_hotel_taxonomy() ) {
        	/* taxonomy */
            $term   = get_queried_object();
            $taxonomy = '';
            if ( strpos( $term->taxonomy, 'opalhotel_' ) === 0 ) {
            	$taxonomy = substr( $term->taxonomy, strlen( 'opalhotel_' ) );
            }

            if ( is_tax( OPALHOTEL_TXM_ROOM_CAT ) || is_tax( OPALHOTEL_TXM_ROOM_TAG ) ) {
            	$file = 'taxonomy-' . $taxonomy . '.php';
            } else if( is_tax( OPALHOTEL_TXM_HOTEL_CAT ) || is_tax( OPALHOTEL_TXM_HOTEL_DES ) || is_tax(  OPALHOTEL_TXM_HOTEL_INC) ){
                $file = 'taxonomy-' . $taxonomy . '.php';
            } else {
                if ( opalhotel_is_room_taxonomy() ) {
                    $file = 'archive-room.php';
                } else if ( opalhotel_is_hotel_taxonomy() ){
                    $file = 'archive-hotel.php';
                } else {
                    $file = 'archive-room.php';
                }
            }

            $find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
            $find[] = OpalHotel()->template_path() . 'taxonomy-' . $taxonomy . '-' . $term->slug . '.php';
            $find[] = 'taxonomy-' . $term->taxonomy . '.php';
            $find[] = OpalHotel()->template_path() . 'taxonomy-' . $taxonomy . '.php';
            $find[] = $file;

        } else if ( is_single() && get_post_type() === OPALHOTEL_CPT_ROOM ) {
        	/* single */
            $file = 'single-room.php';
            $find[] = $file;
            $find[] = OpalHotel()->template_path() . '/' . $file;
        } else if ( is_post_type_archive( OPALHOTEL_CPT_HOTEL ) ){
        	/* archive */
            $file = 'archive-hotel.php';
            $find[] = $file;
            $find[] = OpalHotel()->template_path() . '/' . $file;
        } else if ( is_single() && get_post_type() === OPALHOTEL_CPT_HOTEL ) {
            global $post;
            $layout = get_post_meta( $post->ID, '_opalhotel_layout', true );
            $layout = $layout ? $layout : opalhotel_get_option( 'single_hotel_layout' );
            $layout = $layout !== 'v1' ? $layout : '';
        	/* single */
            $file = ! $layout ? 'single-hotel.php' : 'single-hotel-' . $layout . '.php';
            $find[] = $file;
            $find[] = OpalHotel()->template_path() . '/' . $file;
        }

        if( $file ) {
            $find[] = OpalHotel()->template_path() . '/' . $file;
            $template = locate_template( array_unique( $find ) );

            if ( ! $template ) {
				$template = OpalHotel()->plugin_path() . '/templates/' . $file;
			}
        }

        return $template;
	}

	/* enqueue scripts */
	public function enqueue_scripts( $hook ) {
		wp_enqueue_script( 'jquery-ui' );
		/* register */
        $key = opalhotel_get_option( 'google_map_api_key', 'AIzaSyCjGR4v4QlGj5CjOCcAGyXwnFNGLIQj-nY' ); // 'AIzaSyDRVUZdOrZ1HuJFaFkDtmby0E93eJLykIk'
        $api = apply_filters( 'opalhotel_google_map_api_uri', '//maps.googleapis.com/maps/api/js?key=' . $key . '&amp;libraries=geometry,geocoder' );

        wp_register_script( 'opalhotel-map-api', $api );
        wp_enqueue_script( 'opalhotel-map-api' );
		wp_register_script( 'owl-carousel', OPALHOTEL_URI . 'assets/libraries/owl-carousel/owl.carousel.min.js', array( 'jquery' ), OPALHOTEL_VERSION, true );
		wp_register_style( 'owl-carousel', OPALHOTEL_URI . 'assets/libraries/owl-carousel/owl.carousel.css', array(), OPALHOTEL_VERSION );

		wp_deregister_script( 'opal-hotel-room-booking' );
		wp_register_script( 'opal-hotel-room-booking', OPALHOTEL_URI . 'assets/site/js/opalhotel.js', array( 'jquery', 'backbone', 'opalhotel-global' ), OPALHOTEL_VERSION, true );

		wp_localize_script( 'opal-hotel-room-booking', 'OpalHotel', opalhotel_i18n() );
		wp_register_script( 'opalhotel-paymentjs', OPALHOTEL_URI . 'assets/site/js/jquery.payment.min.js', array( 'jquery', 'opalhotel-global' ), OPALHOTEL_VERSION, true );

		/* enqueue */
		wp_enqueue_script( 'opal-hotel-room-booking' );
		wp_enqueue_script( 'opalhotel-paymentjs' );

        $uri = OPALHOTEL_URI . 'assets/site/css/opalhotel.css';
        $themedir = get_template_directory() . '/css/opalhotel.css';
		if( file_exists( $themedir ) ){
            $uri = get_template_directory_uri() . '/css/opalhotel.css';
		}
        wp_enqueue_style( 'opal-hotel-room-booking', $uri, array(), OPALHOTEL_VERSION );
        wp_enqueue_style( 'opal-hotel-room-booking' );

		wp_enqueue_script( 'owl-carousel' );
		wp_enqueue_style( 'owl-carousel' );

        wp_register_script( 'youtube-api', '//www.youtube.com/iframe_api', false, array() );
        wp_enqueue_script( 'youtube-api' );

        wp_register_script( 'prettyPhoto', OPALHOTEL_URI . 'assets/libraries/prettyPhoto/js/jquery.prettyPhoto.js' );
        wp_register_style( 'prettyPhoto-css', OPALHOTEL_URI . 'assets/libraries/prettyPhoto/css/prettyPhoto.css' );
        wp_enqueue_script( 'prettyPhoto' );
        wp_enqueue_style( 'prettyPhoto-css' );

        // jBox
        wp_enqueue_script( 'jBox', OPALHOTEL_URI . 'assets/libraries/jBox/jBox.min.js', array( 'jquery' ), OPALHOTEL_VERSION, true );
        wp_enqueue_style( 'jBox', OPALHOTEL_URI . 'assets/libraries/jBox/jBox.css', array(), OPALHOTEL_VERSION );
        // jBox plugins
        wp_enqueue_script( 'jBox-notice', OPALHOTEL_URI . 'assets/libraries/jBox/plugins/Notice/jBox.Notice.min.js', array( 'jquery' ), OPALHOTEL_VERSION, true );
        wp_enqueue_style( 'jBox-notice', OPALHOTEL_URI . 'assets/libraries/jBox/plugins/Notice/jBox.Notice.css', array(), OPALHOTEL_VERSION );

        // accounting
        wp_enqueue_script( 'accounting', OPALHOTEL_URI . 'assets/libraries/accounting/accounting.min.js', array( 'jquery' ), OPALHOTEL_VERSION, true );
        wp_enqueue_script( 'opal-hotel-scripts', OPALHOTEL_URI . 'assets/site/js/scripts.min.js', array( 'jquery' ), OPALHOTEL_VERSION, true );

        // mCustomScrollbar
        wp_enqueue_script( 'mCustomScrollbar', OPALHOTEL_URI . 'assets/libraries/mCustomScrollbar/jquery.mCustomScrollbar.js', array( 'jquery' ), OPALHOTEL_VERSION, true );
        wp_enqueue_style( 'mCustomScrollbar', OPALHOTEL_URI . 'assets/libraries/mCustomScrollbar/jquery.mCustomScrollbar.css', array(), OPALHOTEL_VERSION );

	}

}

new OpalHotel_Tempalte_Loader();