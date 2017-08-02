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

$file = dirname( __FILE__ ) . '/class-opalhotel-woo-product.php';
if ( ! class_exists( 'OpalHotel_Room_Product' ) && file_exists( $file ) ) {
	require_once $file;
}

if ( ! class_exists( 'OpalHotel_Woo_Checkout' ) ) :

	class OpalHotel_Woo_Checkout {

		/**
		 * Contructor class woo checkout process
		 *
		 * @since 1.1.6.1
		 */
		public function __construct() {

			/**
			 * Filter checkout url
			 *
			 * Redirect hotel checkout url to woo chekout url
			 */
			add_filter( 'opalhotel_get_checkout_url', array( __CLASS__, 'woo_checkout_url' ) );

			/**
			 * Reservation step 3 load woo checkout html
			 */
			add_filter( 'opalhotel_reservation_step_results', array( __CLASS__, 'reservation_step_payment' ), 10, 2 );

			/**
			 * Add Woo cart item while hotel plugin added to cart successfully
			 */
			add_filter( 'opalhotel_added_cart_item', array( __CLASS__, 'trigger_add_woo_cart_item' ), 10, 1 );

			/**
			 * Remove Woo cart item
			 *
			 */
			add_action( 'opalhotel_remove_cart_item', array( __CLASS__, 'trigger_remove_woo_cart_item' ), 10, 2 );

			/**
			 * If admin option is enabled
			 *
			 * redirect user to woocommercer checkout whn they try access hotel checkout page
			 */
			add_action( 'template_redirect', array( __CLASS__, 'redirect_to_woo_checkout_page' ) );

			/**
			 * WooCommerce action hooks
			 *
			 * @product type
			 * @add_cart_item
			 * @remove_cart_item
			 */
			add_action( 'woocommerce_get_cart_item_from_session', array( __CLASS__, 'woo_get_cart_item' ), 10, 3 );
			add_action( 'woocommerce_product_class', array( __CLASS__, 'woo_product_class' ), 10, 4 );

			add_action( 'woocommerce_remove_cart_item', array( __CLASS__, 'trigger_remove_hotel_cart_item' ), 10, 2 );

		}

		/**
		 * Redirect OpalHotel Checkout url to WooCommerce checkout url
		 *
		 * @since 1.1.6.1
		 */
		public static function woo_checkout_url( $url ) {
			return wc_get_checkout_url();
		}

		/**
		 * Load Woo checkout template
		 *
		 */
		public static function reservation_step_payment( $results, $step ) {

			if ( $step == 3 ) {
				$html = do_shortcode( '[woocommerce_checkout]' );
				$results['step'] = $html;
			}

			return $results;
		}

		/**
		 * Add Woo cart item with hotel cart item param
		 *
		 * @since 1.1.6.1
		 */
		public static function trigger_add_woo_cart_item( $cart_item_id ) {
			$cart_item = OpalHotel()->cart->get_cart_item( $cart_item_id );
			$param = array(
					'arrival'	=> $cart_item['arrival'],
					'departure'	=> $cart_item['departure'],
					'adult'		=> $cart_item['adult'],
					'child'		=> $cart_item['child']
				);
			$woo_cart_id = WC()->cart->generate_cart_id( $cart_item['product_id'], 0, array(), $param );

			// remove older item
			if ( WC()->cart->get_cart_item( $woo_cart_id ) ) {
				WC()->cart->remove_cart_item( $woo_cart_id );
			}

			// add item to woo cart
			$woo_cart_id = WC()->cart->add_to_cart( $cart_item['product_id'], 1, 0, array(), $param );

			return $cart_item_id;
		}

		/**
		 * Trigger remove Woo cart item while Hotel remove cart item
		 *
		 * @since 1.1.6.1
		 * @return mixed
		 */
		public static function trigger_remove_woo_cart_item( $cart_id, $cart_contents ) {
			// remove action woo cart
			remove_action( 'woocommerce_remove_cart_item', array( __CLASS__, 'trigger_remove_hotel_cart_item' ), 10, 2 );

			$cart_item = OpalHotel()->cart->get_cart_item( $cart_id );
			$param = array(
					'arrival'	=> $cart_item['arrival'],
					'departure'	=> $cart_item['departure'],
					'adult'		=> $cart_item['adult'],
					'child'		=> $cart_item['child']
				);
			$woo_cart_id = WC()->cart->generate_cart_id( $cart_item['product_id'], 1, 0, array(), $param );
			if ( WC()->cart->get_cart_item( $woo_cart_id ) ) {
				WC()->cart->remove_cart_item( $woo_cart_id );
			}

			add_action( 'woocommerce_remove_cart_item', array( __CLASS__, 'trigger_remove_hotel_cart_item' ), 10, 2 );
		}

		/**
		 * Redirect user to WooCommcer checkout page when they try access hotel checkout page
		 *
		 * @since 1.1.6.1
		 */
		public static function redirect_to_woo_checkout_page() {
			global $post;
			if ( $post->ID && $post->ID == opalhotel_get_page_id( 'checkout' ) ) {
				wp_safe_redirect( wc_get_checkout_url() ); exit();
			}
		}

		/**
		 * Get Product from cart item
		 *
		 * @param array $session_data
		 * @param array $values
		 * @param string $key
		 */
		public static function woo_get_cart_item( $session_data, $values, $key ) {
			$data = $session_data['data'];
			if ( $data->post->post_type === OPALHOTEL_CPT_ROOM ) {
				$session_data['data'] = new WC_Product_Room( $data->id, array(
						'arrival'	=> $values['arrival'],
						'departure'	=> $values['departure'],
						'adult'		=> $values['adult'],
						'child'		=> $values['child']
					) );
			}
			return $session_data;
		}

		/**
		 * Product class
		 *
		 * @param string $classname
		 * @param tring $product_type
		 * @param tring $post_type
		 * @param int $product_id
		 *
		 * @return string $classname
		 */
		public static function woo_product_class( $classname, $product_type, $post_type, $product_id ){

			if ( $post_type === OPALHOTEL_CPT_ROOM ) {
				$classname = 'WC_Product_Room';
			}

			return $classname;
		}

		/**
		 * Remove Hotel Cart item if exists
		 *
		 * @param WooCommerce cart key $cart_key
		 * @param WooCommerce Cart object
		 *
		 * @return mixed
		 */
		public static function trigger_remove_hotel_cart_item( $cart_key, $cart ) {

			// remove add woo cart item action
			remove_filter( 'opalhotel_added_cart_item', array( __CLASS__, 'trigger_add_woo_cart_item' ), 10, 1 );

			$cart_item = $cart->get_cart_item( $cart_key );

			$hotel_cart_param = array(
					'product_id'	=> $cart_item['product_id'],
					'arrival'		=> $cart_item['arrival'],
					'departure'		=> $cart_item['departure'],
					'adult'			=> $cart_item['adult'],
					'child'			=> $cart_item['child']
				);

			$hotel_cart_key = OpalHotel()->cart->generate_cart_id( $hotel_cart_param );

			if ( OpalHotel()->cart->get_cart_item( $hotel_cart_key ) ) {
				OpalHotel()->cart->remove_cart_item( $hotel_cart_key );
			}

			add_filter( 'opalhotel_added_cart_item', array( __CLASS__, 'trigger_add_woo_cart_item' ), 10, 1 );
		}

	}

endif;