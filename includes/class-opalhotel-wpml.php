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

if ( ! class_exists( 'OpalHotel_Wpml' ) ) :

	class OpalHotel_Wpml {

		/**
		 * SitePress object
		 *
		 * @since 1.6.5
		 */
		public static $sitepress = null;

		/**
		 * Default language
		 *
		 * @since 1.6.5
		 */
		public static $default = null;

		/**
		 * Current language code
		 *
		 * @since1.6.5
		 */
		public static $current = null;

		/**
		 * Initialize object hook
		 *
		 * @since 1.6.5
		 */
		public static function init() {
			global $sitepress;
			self::$sitepress = $sitepress;
			// default language
			self::$default = self::$sitepress->get_default_language();

			// current language
			self::$current = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : null;

			/**
			 * Switch Reservation URL
			 */
			add_filter( 'opalhotel_page_id', array( __CLASS__, 'sitepress_switch_page_id' ) );

			// Add Join Filter Room Query
			add_filter( 'opalhotel_check_available_join', array( __CLASS__, 'sitepress_filter_room' ), 10, 2 );

			// Available WHERE
			add_filter( 'opalhotel_check_available_where', array( __CLASS__, 'sitepress_available_where' ), 10, 2 );

			// order language created
			add_action( 'opalhotel_reservartion_create_order', array( __CLASS__, 'sitepress_create_order' ), 10 );

			// Pre process checkout
			add_action( 'opalhotel_process_checkout', array( __CLASS__, 'sitepress_pre_process_checkout' ) );
		}

		public static function sitepress_is_ready() {
			global $wpdb;

			$table = $wpdb->prefix . 'icl_translations';
			return $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) == $table;
		}

		/**
		 * Default Object id
		 *
		 * @since 1.6.5
		 */
		public static function get_default_object_id( $id = null, $type = 'page', $default = false, $lang = false ) {
			$lang = ! $lang ? self::$default : $lang;
			return $id ? icl_object_id( $id, $type, $default, $lang ) : 0;
		}

		/**
		 * Switch page id
		 *
		 * @since 1.6.5
		 */
		public static function sitepress_switch_page_id( $page_id = null ) {
			return self::get_default_object_id( $page_id, 'page', true, self::$current );
		}

		/**
		 * SitePress Filter Room
		 *
		 * @since 1.6.5
		 */
		public static function sitepress_filter_room( $join, $args ) {
			global $wpdb;
			$is_ready = self::sitepress_is_ready();
			if ( ! $is_ready )
				return $join;

			$join .= "INNER JOIN {$wpdb->prefix}icl_translations AS current_lang ON current_lang.element_id = rooms.ID
						INNER JOIN {$wpdb->prefix}icl_translations AS default_lang ON default_lang.trid = current_lang.trid AND default_lang.element_type = current_lang.element_type";

			return $join;
		}

		/**
		 * SitePress available where
		 *
		 * @since 1.6.5
		 */
		public static function sitepress_available_where( $where, $args ) {
			global $wpdb;
			$is_ready = self::sitepress_is_ready();
			if ( ! $is_ready )
				return $where;

			$where .= $wpdb->prepare("
					AND current_lang.language_code = %s
					AND default_lang.language_code = %s
					AND current_lang.element_type = %s
				", self::$current, self::$default, 'post_opalhotel_room' );
			return $where;
		}

		/**
		 * Update language current when create order completed successfully
		 *
		 * @since 1.6.5
		 */
		public static function sitepress_create_order( $order_id ) {
			update_post_meta( $order_id, '_order_lang_code', self::$current );
		}

		/**
		 * Pre Process checkout
		 */
		public static function sitepress_pre_process_checkout() {
			add_filter( 'opalhotel_get_room_cart', array( __CLASS__, 'default_room_item' ) );
			add_filter( 'opalhotel_get_packages_cart', array( __CLASS__, 'default_room_item' ) );
		}

		/**
		 * Switch current room id to default room id
		 *
		 * @since 1.6.5
		 */
		public static function default_room_item( $rooms ) {
			foreach ( $rooms as $id => $room ) {
				$room['product_id'] = isset( $room['product_id'] ) ? self::get_default_object_id( $room['product_id'], get_post_type( $room['product_id'] ), true ) : 0;
				$rooms[$id] = $room;
			}
			return $rooms;
		}

	}

	OpalHotel_Wpml::init();

endif;