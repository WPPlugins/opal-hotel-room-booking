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

if ( ! class_exists( 'OpalHotel_Woo_Order' ) ) :

	class OpalHotel_Woo_Order {

		/**
		 * Constructor Woo Order process class
		 */
		public function __construct() {

			/**
			 * trigger create hotel order relationship with woocommerce order
			 * 
			 * @since 1.1.6.1
			 */
			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'order_updated_meta' ), 20 );

			/**
			 * This action change hotel order status
			 * 
			 * when woocommerce updated status completed
			 */
			add_action( 'woocommerce_order_status_changed', array( __CLASS__, 'change_hotel_order_status' ), 10, 3 );


			/**
			 * This action change woocommercer order status
			 *
			 * when hotel order updated status completed
			 */
			add_action( 'opalhotel_update_order_status', array( __CLASS__, 'change_woo_order_status' ), 10, 3 );

		}

		/**
		 * Create hotel order on this action
		 * 
		 * @since 1.1.6.1
		 */
		public static function order_updated_meta( $order_id, $posted ) {

		}

		/**
		 * Trigger change hotel order status
		 *
		 * @since 1.1.6.1
		 */
		public static function change_hotel_order_status( $order_id, $old_status, $status ) {

		}

		/**
		 * Trigger change woocommerce order status when hotel order status have been changed
		 *
		 * @since 1.1.6.1
		 */
		public static function change_woo_order_status( $order_id, $old_status, $status ) {

		}

	}

endif;