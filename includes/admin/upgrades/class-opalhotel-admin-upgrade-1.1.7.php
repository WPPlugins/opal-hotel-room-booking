<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    $package$
 * @author     Opal Team <info@wpopal.com >
 * @copyright  Copyright (C) 2014 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'OpalHotel_Admin_Upgrade' ) ) :

	class OpalHotel_Admin_Upgrade {

		public static $version = OPALHOTEL_VERSION;

		/**
		 * Check Upgrade
		 *
		 * @since 1.1.1
		 */
		public static function check_upgrade() {
			return ! get_option( 'opalhotel_upgraded_' . self::$version );
		}

		/**
		 * Do Upgrade Database
		 *
		 * @since 1.1.1
		 */
		public static function do_upgrade() {
			global $wpdb;

			$sql = $wpdb->prepare("
					SELECT meta.* FROM $wpdb->postmeta AS meta
						INNER JOIN $wpdb->posts AS rooms ON rooms.ID = meta.post_id
						INNER JOIN $wpdb->posts AS hotels ON hotels.ID = meta.meta_value
					WHERE
						rooms.post_type = %s 
						AND hotels.post_type = %s
						AND meta.meta_key = %s 
						GROUP BY meta.post_id
				", OPALHOTEL_CPT_ROOM, OPALHOTEL_CPT_HOTEL, '_hotel' );

			$results = $wpdb->get_results( $sql );
			foreach ( $results as $result ) {
				delete_post_meta( $result->post_id, '_hotel' );
				update_post_meta( $result->post_id, '_hotel', $result->meta_value );
			}

			return true;
		}

	}

endif;