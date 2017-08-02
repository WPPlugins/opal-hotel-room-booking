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

if ( ! class_exists( 'OpalHotel_WooCommerce' ) ) :

	class OpalHotel_WooCommerce {

		public static $checkout = null;

		public static $order = null;

		/**
		 * Init hooks
		 */
		public static function init() {

			add_action( 'wp_loaded', array( __CLASS__, 'woo_init' ) );

		}

		/**
		 * Check WooCommerce enabled
		 *
		 * @since 1.0.0
		 * @return mixed
		 */
		public static function woo_init() {
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) || function_exists( 'WC' ) ) {

				/**
				 * Checkout class
				 */
				OpalHotel()->_include( 'woocommerce/class-opalhotel-woo-checkout.php' );
				// checkout object
				self::$checkout = new OpalHotel_Woo_Checkout();

				/**
				 * Order process
				 */
				OpalHotel()->_include( 'woocommerce/class-opalhotel-woo-order.php' );

				self::$order = new OpalHotel_Woo_Order();

			} else {
				add_action( 'admin_notices', array( __CLASS__, 'admin_print_notices' ) );
			}
		}

		/**
		 * Print notices if WooCommerce plugin not install or active
		 *
		 * @since 1.0.0
		 * @return moxed
		 */
		public static function admin_print_notices() {
			?>
				<div class="notice-warning notice is-dismissible">
					<p><?php printf( '<a href="%s" target="_blank"><strong>%s</strong></a> %s', 'https://wordpress.org/plugins/woocommerce/', __( 'WooCommerce', 'opal-hotel-room-booking' ), __( 'plugin is required to enable WooCommerce payment.', 'opal-hotel-room-booking' ) ) ?></p>
				</div>
			<?php
		}

	}

	OpalHotel_WooCommerce::init();

endif;