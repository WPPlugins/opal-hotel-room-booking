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

class OpalHotel_Install {

	public static $upgrade = array();

	/**
	 * Init Class
	 *
	 * @since 1.1.7
	 */
	public static function init() {

		add_action( 'init', array( __CLASS__, 'setup_table_name' ), 0 );
		add_action( 'switch_blog', array( __CLASS__, 'setup_table_name' ), 0 );
        // create new blog in multisite
        add_action( 'wpmu_new_blog', array( __CLASS__,'create_new_blog' ), 10, 6 );
        // multisite delete table in multisite
        add_filter( 'wpmu_drop_tables', array( __CLASS__, 'delete_tables' ) );

		// Admin notices
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
        add_action( 'admin_init', array( __CLASS__, 'do_upgrade' ), 9999 );
	}

	/**
	 * Print notices upgrade
	 * 
	 * @since 1.1.7
	 */
	public static function admin_notices() {
		$version = get_option( '_opalhotel_has_upgrade' );
		$file = OPALHOTEL_INC_PATH . '/admin/upgrades/class-opalhotel-admin-upgrade-' . $version . '.php';
		$has_upgrade = false;
		if ( file_exists( $file ) ) {
			if ( ! class_exists( 'OpalHotel_Admin_Upgrade' ) ) {
				require_once $file;
			}
			$has_upgrade = OpalHotel_Admin_Upgrade::check_upgrade();
		}
		if ( $has_upgrade ) { ?>
			<div class="notice-warning notice is-dismissible">
				<?php
					printf( '<p>%s <a href="%s">Duplicator</a>. %s.</p> <p><a href="%s" class="button">%s</a></p>',
							__( '<strong>Opal Hotel Room Booking warning:</strong> We need to upgrade your database to make more optimize, more powerful. Recommended backup your website with', 'opal-hotel-room-booking' ),
							'https://wordpress.org/plugins/duplicator/',
							__( 'Let\'s try to grow up together', 'opal-hotel-room-booking' ),
							admin_url( 'admin.php?page=opalhotel-settings&action=upgrade&version=' . $version . '&nonce=' . wp_create_nonce( 'upgrade-nonce-' . $version ) ),
							esc_html( 'Upgrade Now', 'opal-hotel-room-booking' )
						);
				?>
			</div>
		<?php }
	}

	/**
	 * Do upgrade database
	 *
	 * @since 1.1.7
	 */
	public static function do_upgrade() {
		if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] !== 'opalhotel-settings' ) {
			return;
		}
		if ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] !== 'upgrade' ) {
			return;
		}
		$version = get_option( '_opalhotel_has_upgrade' );
		if ( ! $version || ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'upgrade-nonce-' . $version ) ) {
			wp_die( __( 'Sorry, you are not allowed to access this page.', 'opal-hotel-room-booking' ) );
		}

		$upgrade_file = OPALHOTEL_INC_PATH . '/admin/upgrades/class-opalhotel-admin-upgrade-' . $version . '.php';

		if ( ! file_exists( $upgrade_file ) ) {
			wp_die( __( 'Sorry, upgrade file not found.', 'opal-hotel-room-booking' ) );
		}

		if ( ! class_exists( 'OpalHotel_Admin_Upgrade' ) ) {
			require_once $upgrade_file;
		}

		// make upgrade
		try {

			// do upgrade function
			$results = OpalHotel_Admin_Upgrade::do_upgrade();
			if ( $results ) {
				opalhotel_add_admin_notice( __( 'Opal Hotel Room Booking data update completed. Thank you for updating to lastest version.', 'opal-hotel-room-booking' ), 'updated' );
				update_option( 'opalhotel_upgraded_' . OpalHotel_Admin_Upgrade::$version, 1 );
				wp_safe_redirect( admin_url( 'admin.php?page=opalhotel-settings' ) ); exit();
			}

		} catch( Exception $e ) {
			$message = $e->getMessage();
			if ( ! empty( $message ) ) {
				opalhotel_add_admin_notice( $message );
			}
		}
	}

	public static function setup_table_name() {
		global $wpdb;

		$order_item = 'opalhotel_order_items';
		$order_itemmeta = 'opalhotel_order_itemmeta';
		$opalhotel_pricing = 'opalhotel_pricing';
		$opalhotel_ratings = 'opalhotel_ratings';
		$opalhotel_rating_item = 'opalhotel_rating_item';

		$wpdb->opalhotel_order_items = $wpdb->prefix . $order_item;
		$wpdb->opalhotel_order_itemmeta = $wpdb->prefix . $order_itemmeta;
		$wpdb->opalhotel_pricing = $wpdb->prefix . $opalhotel_pricing;
		$wpdb->opalhotel_ratings = $wpdb->prefix . $opalhotel_ratings;
		$wpdb->opalhotel_rating_item = $wpdb->prefix . $opalhotel_rating_item;

		$wpdb->tables[] = 'opalhotel_order_items';
		$wpdb->tables[] = 'opalhotel_order_itemmeta';
		$wpdb->tables[] = 'opalhotel_pricing';
		$wpdb->tables[] = 'opalhotel_ratings';
		$wpdb->tables[] = 'opalhotel_rating_item';

		return $wpdb;
	}

	// install hook
	public static function install() {

		if ( ! defined( 'OPALHOTEL_INSTALLING' ) ) {
			define( 'OPALHOTEL_INSTALLING', true );
		}

		self::$upgrade = apply_filters( 'opalhotel_upgrade_file_vesion', array(

			) );

		global $wpdb;
		if ( is_multisite() ) {
	        // store the current blog id
	        $current_blog = $wpdb->blogid;
	        // Get all blogs in the network and activate plugin on each one
	        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	        foreach ( $blog_ids as $blog_id ) {
	        	// each blog
	            switch_to_blog( $blog_id );

	            self::make_install();

	            // restore
	            restore_current_blog();
	        }
	    } else {
	        self::make_install();
	    }

	}

	/**
	 * Make install step by step
	 *
	 * @since 1.1.7
	 */
	private static function make_install(){

		// create pages
		self::create_pages();

		// create update options
		self::create_options();

		// create tables
		self::create_tables();

		// upgrade database
		self::upgrade_database();

		/* create cron job */
		self::create_cron_job();

		/**
		 * Check upgrades
		 */
		self::_pre_upgrade();

		update_option( 'opalhotel_version', OPALHOTEL_VERSION );
	}

	// upgrade database
	private static function upgrade_database() {
		self::setup_table_name();
		$version = get_option( 'opalhotel_version', false );
		foreach ( self::$upgrade as $ver => $update ) {
			if ( ! $version || version_compare( $version, $ver, '<' ) ) {
				include_once $update;
			}
		}
	}

	private static function create_cron_job() {

		/* add new schedule */
		/* time cancel payment setting */
		$time_cancel = get_option( 'opalhotel_cancel_payment', 12 );
		$time_cancel = absint( $time_cancel ) * HOUR_IN_SECONDS ;

		/* remove ola schedule */
		wp_clear_scheduled_hook( 'opalhotel_cancel_pending_boooking' );
		/**
		 * cancel booking has over single time
		 */
		wp_schedule_single_event( time() + $time_cancel, 'opalhotel_cancel_pending_boooking' );

		// 
		wp_clear_scheduled_hook( 'opalhotel_cleanup_booking_data' );
		wp_schedule_single_event( time() + 60, 'opalhotel_cleanup_booking_data' );

	}

	// create options default
	private static function create_options() {
		if ( ! class_exists( 'OpalHotel_Admin_Settings' ) ) {
			OpalHotel::instance()->_include( 'admin/class-opalhotel-admin-settings.php' );
		}

		$settings_pages = OpalHotel_Admin_Settings::get_settings_pages();

		foreach ( $settings_pages as $setting ) {
			$options = $setting->get_settings();
			foreach ( $options as $option ) {
				if ( isset( $option[ 'id' ], $option[ 'default' ] ) ) {
					if ( ! get_option( $option[ 'id' ], false ) ) {
						update_option( $option['id'], $option['default'] );
					}
				}
			}
		}

		$countries_request_uri = 'https://opendata.socrata.com/resource/mnkm-8ram.json';
		$request = wp_safe_remote_get( $countries_request_uri );
		if ( wp_remote_retrieve_response_code( $request ) ) {
			$response = wp_remote_retrieve_body( $request );
			if ( ! is_wp_error( $response ) ) {
				$response = json_decode( $response );
				update_option( 'opalhotel_countries_data', $response );
			}
		}
	}

	// create page. Eg: opalhotel-checkout, opalhotel-cart
	public static function create_pages() {
		if ( ! function_exists( 'opalhotel_create_page' ) ){
            OpalHotel::instance()->_include( 'admin/opalhotel-admin-functions.php' );
            OpalHotel::instance()->_include( 'opalhotel-functions.php' );
        }

        $pages = array(
        		'cart'	=> array(
        				'name'    => _x( 'opal-hotel-cart', 'Page Slug', 'opal-hotel-room-booking' ),
				        'title'   => _x( 'Opal Hotel Cart', 'Page Title', 'opal-hotel-room-booking' ),
				        'content' => '[' . apply_filters( 'opalhotel_cart_shortcode_tag', 'opalhotel_cart' ) . ']'
        			),
        		'hotel_available'	=> array(
        				'name'    => _x( 'opal-hotels-available', 'Page Slug', 'opal-hotel-room-booking' ),
				        'title'   => _x( 'Opal Hotel Available', 'Page Title', 'opal-hotel-room-booking' ),
				        'content' => '[' . apply_filters( 'opalhotel_hotel_available_shortcode_tag', 'opalhotel_hotel_available' ) . ']',
						'meta'	  => array(
								'_wp_page_template'		=> 'page-templates/hotels-map-sidebar-left.php'
							)
        			),
        		'reservation'	=> array(
        				'name'    => _x( 'opal-hotel-reservation', 'Page Slug', 'opal-hotel-room-booking' ),
				        'title'   => _x( 'Opal Hotel Reservation', 'Page Title', 'opal-hotel-room-booking' ),
				        'content' => '[' . apply_filters( 'opalhotel_reservation_shortcode_tag', 'opalhotel_reservation' ) . ']'
        			),
        		'checkout'	=> array(
        				'name'    => _x( 'opal-hotel-checkout', 'Page Slug', 'opal-hotel-room-booking' ),
				        'title'   => _x( 'Opal Hotel Checkout', 'Page Title', 'opal-hotel-room-booking' ),
				        'content' => '[' . apply_filters( 'opalhotel_checkout_shortcode_tag', 'opalhotel_checkout' ) . ']'
        			),
        		'available'	=> array(
        				'name'    => _x( 'opal-hotel-available', 'Page Slug', 'opal-hotel-room-booking' ),
				        'title'   => _x( 'Opal Hotel Check Available', 'Page Title', 'opal-hotel-room-booking' ),
				        'content' => '[' . apply_filters( 'opalhotel_search_shortcode_tag', 'opalhotel_check_available' ) . ']'
        			),
        		'favorited'	=> array(
        				'name'    => _x( 'opal-hotel-favorited', 'Page Slug', 'opal-hotel-room-booking' ),
				        'title'   => _x( 'Opal Hotel Favorited', 'Page Title', 'opal-hotel-room-booking' ),
				        'content' => '[' . apply_filters( 'opalhotel_search_shortcode_tag', 'opalhotel_favorited' ) . ']'
        			),
        		'rooms'	=> array(
        				'name'    => _x( 'rooms', 'Page Slug', 'opal-hotel-room-booking' ),
				        'title'   => _x( 'Rooms', 'Page Title', 'opal-hotel-room-booking' ),
				        'content' => '[' . apply_filters( 'opalhotel_rooms_shortcode_tag', 'opalhotel_rooms' ) . ']'
        			),
        		'hotels'	=> array(
        				'name'    => _x( 'hotels', 'Page Slug', 'opal-hotel-room-booking' ),
				        'title'   => _x( 'Hotels', 'Page Title', 'opal-hotel-room-booking' ),
				        'content' => '[' . apply_filters( 'opalhotel_hotels_shortcode_tag', 'opalhotel_hotels' ) . ']'
        			),
        		'terms'		=> array(
        				'name'    => _x( 'opal-hotel-term-condition', 'Page Slug', 'opal-hotel-room-booking' ),
				        'title'   => _x( 'Terms and Conditions ', 'Page Title', 'opal-hotel-room-booking' ),
				        'content' => apply_filters( 'opalhotel_terms_content', 'Something notices' )
        			),
        		'account'	=> array(
        				'name'    => _x( 'opal-hotel-account', 'Page Slug', 'opal-hotel-room-booking' ),
				        'title'   => _x( 'Opal Hotel Account', 'Page Title', 'opal-hotel-room-booking' ),
				        'content' => '[' . apply_filters( 'opalhotel_account_shortcode_tag', 'opalhotel_account' ) . ']'
        			)
        	);

		if ( $pages && function_exists( 'opalhotel_create_page' ) ) {
		    foreach ( $pages as $key => $page ) {
		    	if ( ! opalhotel_get_page_id( $key ) ) {
		        	$pageId = opalhotel_create_page( esc_sql( $page['name'] ), 'opalhotel_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? opalhotel_get_page_id( $page['parent'] ) : '' );
		        	if ( $pageId && isset( $page['meta'] ) ) {
		        		foreach ( $page['meta'] as $key => $value ) {
							update_post_meta( $pageId, $key, $value );
						}
		        	}
		    	}
		    }
		}

	}

	// create tables. Eg: order_items, order_itemmeta
	private static function create_tables( ) {
		self::schema();
	}

	/* create tables */
	public static function schema() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		/* get database charset */
		$charset_collate = $wpdb->get_charset_collate();

		$table = $wpdb->prefix . 'opalhotel_order_items';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) != $table ) {

			// booking items
			$sql = "
				CREATE TABLE IF NOT EXISTS {$wpdb->prefix}opalhotel_order_items (
					order_item_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					order_item_name longtext NOT NULL,
					order_item_type varchar(255) NOT NULL,
					order_item_parent bigint(20) NULL,
					order_id bigint(20) unsigned NOT NULL,
					UNIQUE KEY order_item_id (order_item_id),
					PRIMARY KEY  (order_item_id)
				) $charset_collate;
			";
			dbDelta( $sql );
		}

		$table = $wpdb->prefix . 'opalhotel_order_itemmeta';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) != $table ) {

			/* create order item meta table save all meta of order item */
			$sql = "
				CREATE TABLE IF NOT EXISTS {$wpdb->prefix}opalhotel_order_itemmeta (
					meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					opalhotel_order_item_id bigint(20) unsigned NOT NULL,
					meta_key varchar(255) NULL,
					meta_value longtext NULL,
					UNIQUE KEY meta_id (meta_id),
					PRIMARY KEY  (meta_id),
					KEY opalhotel_order_item_id(opalhotel_order_item_id),
					KEY meta_key(meta_key)
				) $charset_collate;
			";
			dbDelta( $sql );
		}

		$table = $wpdb->prefix . 'opalhotel_pricing';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) != $table ) {

			/* create pricing table*/
			$sql = "
				CREATE TABLE IF NOT EXISTS {$wpdb->prefix}opalhotel_pricing (
					plan_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					room_id bigint(20) unsigned NOT NULL,
					arrival timestamp NOT NULL,
					price float NULL,
					UNIQUE KEY plan_id (plan_id),
					PRIMARY KEY  (plan_id)
				) $charset_collate;
			";
			dbDelta( $sql );
		} else {
			$sql = "SHOW INDEXES FROM {$wpdb->prefix}opalhotel_pricing WHERE Key_name = 'arrival'";
			if ( $wpdb->get_var( $sql ) === $wpdb->prefix . 'opalhotel_pricing' ) {
				$sql = "ALTER TABLE {$wpdb->prefix}opalhotel_pricing DROP INDEX arrival";
				$wpdb->query( $sql );
				$sql = "ALTER TABLE {$wpdb->prefix}opalhotel_pricing ADD UNIQUE (plan_id)";
				$wpdb->query( $sql );
			}
		}

		// Create Rating Tables
		OpalHotel_Ratings::create_tables();

	}

	/*
	 * if multisite
	 *
	 * delete table when delete blog
	 */
	public static function delete_tables( $tables ) {
		global $wpdb;
   		$tables[] = $wpdb->prefix . 'opalhotel_order_items';
   		$tables[] = $wpdb->prefix . 'opalhotel_order_itemmeta';
   		$tables[] = $wpdb->prefix . 'opalhotel_pricing';
		return $tables;
	}

	/*
	 * if multisite
	 *
	 * create new table when create new blog multisite
	 *
	 */
	public static function create_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		$plugin = basename( OPALHOTEL_PATH ) . '/' . basename( OPALHOTEL_PATH ) . '.php';
		if ( is_plugin_active_for_network( $plugin ) ) {
			// switch to current blog
	        switch_to_blog( $blog_id );

	        self::create_tables( true );

	        // restore
	        restore_current_blog();
	    }
	}

	/**
	 * Check upgrade database
	 *
	 * @since 1.1.7
	 */
	public static function _pre_upgrade() {
		$file = OPALHOTEL_INC_PATH . '/admin/upgrades/class-opalhotel-admin-upgrade-' . OPALHOTEL_VERSION . '.php';
		if ( file_exists( $file ) ) {
			if ( ! class_exists( 'OpalHotel_Admin_Upgrade' ) ) {
				require_once $file;
			}
			$has_upgrade = OpalHotel_Admin_Upgrade::check_upgrade();

			if ( $has_upgrade ) {
				update_option( '_opalhotel_has_upgrade', OPALHOTEL_VERSION );
			}
		}
	}
}

OpalHotel_Install::init();