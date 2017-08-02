<?php
/*
 *	Plugin Name: Opal Hotel Room Booking
 * 	Plugin URI: http://www.wpopal.com/
 *	Description: Opal Hotel Plugin is a user-friendly booking plugin built with woocommerce that allows you to integrate a booking / reservation system into your WordPress website.
 *	Author: wpopal
 *	Version: 1.1.7.1
 *	Author URI: http://www.wpopal.com/
 *	Text Domain: opal-hotel-room-booking
 *	Domain Path: /languages/
 */

/* exit if access without WP */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * @class OpalHotel Initialize class. Final class no way to extends
 *
 * @version 1.0
 */
if ( ! class_exists( 'OpalHotel' ) ) {

	final class OpalHotel {

		/**
		 * single instance of OpalHotel class
		 */
		protected static $instance = null;

		/**
		 * @var OpalHotel_Cart cart object
		 */
		public $cart = null;

		/*
		 * current user
		 */
		public $user = null;

		/**
		 * @var OpalHotel_Session session storge
		 */
		public $session = null;

		/**
		 * @var OpalHotel_Payment_Gateways payment gateways
		 */
		public $payment_gateways = null;

		/**
		 * @var OpalHotel_Request request handler
		 */
		public $request = null;

		/**
		 * @var OpalHotel_Emails mailer
		 */
		public $mailer = null;

		public $page_templates = null;

		/**
		 * contructor initialize class
		 */
		public function __construct() {

			/* define */
			$this->init_constants();

			/* includes files required */
			$this->includes();

			/* init hooks */
			$this->init_hooks();

		}

		/**
		 * get instance of this
		 */
		public static function instance() {

			/* not set with first time call */
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Define Constants
		 */
		public function init_constants(){
			$this->set_define( 'OPALHOTEL_FILE', plugin_basename( __FILE__ ) );
			/* define root directory path */
			$this->set_define( 'OPALHOTEL_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

			/* define plugin url */
			$this->set_define( 'OPALHOTEL_URI', plugin_dir_url( __FILE__ ) );

			/* define languages directory path */
			$this->set_define( 'OPALHOTEL_LANG_PATH', OPALHOTEL_PATH . '/languages' );

			/* define includes directory path */
			$this->set_define( 'OPALHOTEL_INC_PATH', OPALHOTEL_PATH . '/includes' );

			/* define plugin current version */
			$this->set_define( 'OPALHOTEL_VERSION', '1.1.7.1' );
			/* option group */
			$this->set_define( 'OPALHOTEL_SETTING_GROUP_NAME', 'opalhotel_' );

			// Posttype define
			$this->set_define( 'OPALHOTEL_CPT_HOTEL', 'opalhotel_hotel' );
			$this->set_define( 'OPALHOTEL_CPT_BOOKING', 'opalhotel_booking' );
			// $this->set_define( 'OPALHOTEL_CPT_DEST', 'opalhotel_dest' );
			$this->set_define( 'OPALHOTEL_CPT_ROOM', 'opalhotel_room' );
			$this->set_define( 'OPALHOTEL_CPT_PACKAGE', 'opalhotel_package' );
			$this->set_define( 'OPALHOTEL_CPT_COUPON', 'opalhotel_coupon' );
			$this->set_define( 'OPALHOTEL_CPT_ANT', 'opalhotel_amenities' );

			// Taxonomy define
			$this->set_define( 'OPALHOTEL_TXM_HOTEL_CAT', 'opalhotel_hotel_cat' );
			$this->set_define( 'OPALHOTEL_TXM_HOTEL_INC', 'opalhotel_hotel_inc' );
			$this->set_define( 'OPALHOTEL_TXM_HOTEL_DES', 'opalhotel_hotel_des' );
			$this->set_define( 'OPALHOTEL_TXM_ROOM_CAT', 'opalhotel_room_cat' );
			$this->set_define( 'OPALHOTEL_TXM_ROOM_TAG', 'opalhotel_room_tag' );
		}

		public function set_define( $name = '', $value = '' ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * include files
		 */
		public function includes() {

			/* autoloader class when init class name */
			$this->_include( 'class-opalhotel-autoload.php' );
			$this->_include( 'class-opalhotel-ajax.php' );
			$this->_include( 'opalhotel-core-functions.php' );
			$this->_include( 'opalhotel-template-hooks.php' );
			$this->_include( 'opalhotel-template-functions.php' );
			$this->_include( 'opalhotel-sidebar-functions.php' );
			$this->_include( 'class-opalhotel-template-loader.php' );
			$this->_include( 'opalhotel-validation-functions.php' );
			$this->_include( 'class-opalhotel-request.php' );
			$this->_include( 'class-opalhotel-page-templates.php' );
			if ( is_admin() ) {
				$this->_include( 'admin/class-opalhotel-admin.php' );
			}

			/* room */
			$this->_include( 'room/opalhotel-room-functions.php' );
			$this->_include( 'room/opalhotel-room-hooks.php' );
			$this->_include( 'room/class-opalhotel-room.php' );

			/* coupon */
			$this->_include( 'coupon/opalhotel-coupon-functions.php' );
			$this->_include( 'coupon/opalhotel-coupon-hooks.php' );
			$this->_include( 'coupon/class-opalhotel-coupon.php' );

			/* package */
			$this->_include( 'packages/opalhotel-package-functions.php' );
			$this->_include( 'packages/opalhotel-package-hooks.php' );
			$this->_include( 'packages/class-opalhotel-package.php' );

			/* order */
			$this->_include( 'order/opalhotel-order-functions.php' );
			$this->_include( 'order/opalhotel-order-hooks.php' );
			$this->_include( 'order/class-opalhotel-order-item.php' );
			$this->_include( 'order/class-opalhotel-order.php' );

			/* user */
			$this->_include( 'user/opalhotel-user-functions.php' );
			$this->_include( 'user/class-opalhotel-abstract-user.php' );
			$this->_include( 'user/class-opalhotel-user.php' );

			/* cart */
			$this->_include( 'cart/class-opalhotel-cart.php' );
			$this->_include( 'cart/opalhotel-cart-functions.php' );

			/* shortcodes */
			$this->_include( 'shortcodes/opalhotel-shortcode-functions.php' );
			$this->_include( 'class-opalhotel-shortcodes.php' );

			/* session storge */
			$this->_include( 'class-opalhotel-session.php' );

			/* post types */
			$this->_include( 'post-types/class-opalhotel-post-type-room.php' );
			$this->_include( 'post-types/class-opalhotel-post-type-booking.php' );
			$this->_include( 'post-types/class-opalhotel-post-type-package.php' );
			$this->_include( 'post-types/class-opalhotel-post-type-coupon.php' );
			// $this->_include( 'post-types/class-opalhotel-post-type-destination.php' );

			/* taxonomies */
			$this->_include( 'taxonomies/class-opalhotel-taxonomy-room-cat.php' );
			$this->_include( 'taxonomies/class-opalhotel-taxonomy-room-tag.php' );

			if ( opalhotel_enable_hotel_mode() ) {
				$this->_include( 'post-types/class-opalhotel-post-type-hotel.php' );
				$this->_include( 'post-types/class-opalhotel-post-type-amenities.php' );
				$this->_include( 'taxonomies/class-opalhotel-taxonomy-hotel-cat.php' );
				$this->_include( 'taxonomies/class-opalhotel-taxonomy-hotel-destination.php' );
				$this->_include( 'taxonomies/class-opalhotel-taxonomy-hotel-includes.php' );

			}
			// hotel functions
			$this->_include( 'hotel/class-opalhotel-hotel.php' );
			$this->_include( 'hotel/opalhotel-hotel-functions.php' );

			/* Rating */
			$this->_include( 'ratings/class-opalhotel-ratings.php' );
			$this->_include( 'ratings/opalhotel-rating-functions.php' );

			/* comment review */
			$this->_include( 'class-opalhotel-comment.php' );

			/* widgets init */
			$this->_include( 'widgets/opalhotel-widget-functions.php' );

			/* request process endpoint url and webhook */
			if ( class_exists( 'OpalHotel_Request' ) ) {
				$this->request = new OpalHotel_Request();
			}

			$this->vendors_init();

			// if ( get_option( 'opalhotel_enable_woo_payment' ) ) {
			// 	 //widgets init
			// 	$this->_include( 'woocommerce/class-opalhotel-woocommerce.php' );
			// }

			$this->_include( 'class-opalhotel-install.php' );
		}

		/**
		 * include file
		 */
		public function _include( $file = null ) {
			// file exists
			if ( ( $file = OPALHOTEL_INC_PATH . '/' . $file ) && file_exists( $file ) ) {
				require_once $file;
			}
		}

		/**
		 * Init hooks
		 *
		 * @since 1.1.7
		 */
		public function init_hooks() {
			// plugins_loaded WP hook
			add_action( 'plugins_loaded', array( $this, 'loaded_action' ) );

			/* init processer */
			add_action( 'init', array( $this, 'init' ), 0 );

			/* enqueue scripts */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			/* image setup */
			add_action( 'after_setup_theme', array( $this, 'add_image_sizes' ) );
			register_activation_hook( OPALHOTEL_FILE, array( 'OpalHotel_Install', 'install' ) );
		}

		/**
		 * init hook and object for application
		 */
		public function init() {

			/* before setup init hook */
			do_action( 'opalhotel_before_init' );

			/* session storge */
			$this->session = OpalHotel_Session_Handle::instance();

			/* cart process class */
			$this->cart = new OpalHotel_Cart();

			/* checkout process */
			$this->checkout = new Opalhotel_Checkout();

			/* payment gateways */
			$this->payment_gateways = OpalHotel_Payment_Gateways::instance();

			/* mailer */
			$this->mailer = OpalHotel_Emails::instance();

			/* page templates */
			$this->page_templates = OpalHotel_Page_Templates::instance();

			/* setup init completed */
			do_action( 'opalhotel_init' );

		}

		/**
		 * Hook plugin loaded
		 */
		public function loaded_action() {

			// Before opalhotel loaded do somethings like DROP database =)))
			do_action( 'opalhotel_before_loaded' );

			// Load text domain
			$this->load_textdomain();

			if ( class_exists( 'SitePress' ) ) {
				$this->_include( 'class-opalhotel-wpml.php' );
			}

			// After opalhotel loaded
			do_action( 'opalhotel_after_loaded' );
		}

		/**
		 * Load plugin language
		 */
		public function load_textdomain() {

			/* text domain */
			$prefix = 'opal-hotel-room-booking';

	        $locale = get_locale();
	        $mofile = false;

	        $globalFile = WP_LANG_DIR . '/plugins/' . $prefix . '-' . $locale . '.mo';
	        $pluginFile = OPALHOTEL_LANG_PATH . '/' . $prefix . '-' . $locale . '.mo';

	        if ( file_exists( $globalFile ) ) {
	            $mofile = $globalFile;
	        } else if ( file_exists( $pluginFile ) ) {
	            $mofile = $pluginFile;
	        }

            // Load themes/plugins/mu-plugins directory
	        if ( $mofile ) {
	            load_textdomain( 'opal-hotel-room-booking', $mofile );
	        }
		}

		/**
		 * include vendors
		 */
		public function vendors_init() {
			if ( ! defined( 'CMB2_LOADED' ) ) {
	            add_filter( 'cmb2_meta_box_url', array( $this, 'cmb2_meta_box_url' ) );
	            $this->_include( 'vendors/cmb2/init.php' );
	        }
	        // cmb2 custom field
			if( file_exists( OPALHOTEL_INC_PATH . '/vendors/cmb2/custom-fields/map/map.php' ) ){
				$this->_include( 'vendors/cmb2/custom-fields/map/map.php' );
			}
			// cmb2 custom field
			if( file_exists( OPALHOTEL_INC_PATH . '/vendors/cmb2/custom-fields/opal_time/opal_time.php' ) ){
				$this->_include( 'vendors/cmb2/custom-fields/opal_time/opal_time.php' );
			}
		}

		/**
		 * cmb2 vendor url
		 */
		public function cmb2_meta_box_url( $url ) {
			$url = OPALHOTEL_URI . 'includes/vendors/cmb2/';
        	return $url;
		}

		/**
		 * get template path of plugin
		 */
		public function plugin_path() {
			return apply_filters( 'opalhotel_plugin_path', OPALHOTEL_PATH );
		}

		/**
		 * get template path of actived template
		 */
		public function template_path() {
			return apply_filters( 'opalhotel_template_path', 'opalhotel' );
		}

		/**
		 * register image sizes
		 */
		public function add_image_sizes() {
			if ( ! current_theme_supports( 'post-thumbnails' ) ) {
				add_theme_support( 'post-thumbnails' );
			}
			add_post_type_support( 'opalhotel_room', 'thumbnail' );
			add_image_size( 'room_thumb', get_option( 'opalhotel_room_thumbnail_width', 85 ), get_option( 'opalhotel_room_thumbnail_height', 85 ), 1 );
			add_image_size( 'room_gallery', get_option( 'opalhotel_room_gallery_width', 850 ), get_option( 'opalhotel_room_gallery_height', 450 ), 1 );
			add_image_size( 'room_catalog', get_option( 'opalhotel_room_catalog_image_size_width', 250 ), get_option( 'opalhotel_room_catalog_image_size_height', 250 ), 1 );

			// hotel catalog image
			add_image_size( 'hotel_catalog', get_option( 'opalhotel_hotel_catalog_image_size_width', 360 ), get_option( 'opalhotel_hotel_catalog_image_size_height', 360 ), 1 );
		}

		/**
		 * load javascript and style files
		 */
		public function enqueue_scripts() {
			/* deregister font-awesome */
			wp_deregister_style( 'font-awesome' );
			wp_register_style( 'font-awesome', OPALHOTEL_URI . 'assets/libraries/font-awesome/css/font-awesome.min.css', array(), OPALHOTEL_VERSION );
			wp_enqueue_style( 'font-awesome' );

			/* register global js, css */
			wp_register_style( 'opalhotel-global', OPALHOTEL_URI . 'assets/libraries/globals.css', array(), OPALHOTEL_VERSION );
			wp_register_script( 'opalhotel-global', OPALHOTEL_URI . 'assets/libraries/globals.js', array( 'jquery' ), OPALHOTEL_VERSION, true );

			/* wp core js */
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'jquery-ui-accordion' );
			wp_enqueue_script( 'wp-util' );
            wp_enqueue_script( 'backbone' );

            /* global enqueue */
            wp_enqueue_style( 'opalhotel-global' );
            wp_enqueue_script( 'opalhotel-global' );

		}
	}

}

if ( ! function_exists( 'OpalHotel' ) ) {

	/* function init OpalHotel class */
	function OpalHotel() {
		return OpalHotel::instance();
	}
}

$GLOBALS[ 'opalhotel' ] = OpalHotel();


