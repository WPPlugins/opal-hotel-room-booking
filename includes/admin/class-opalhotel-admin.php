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

class OpalHotel_Admin {

	public function __construct() {

		/* includes files */
		add_action( 'init', array( $this, 'includes' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 100 );
		// register settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/* include files */
	public function includes() {

		// functions
		OpalHotel::instance()->_include( 'admin/opalhotel-admin-functions.php' );
		OpalHotel::instance()->_include( 'admin/opalhotel-admin-hooks.php' );
		// menu
		OpalHotel::instance()->_include( 'admin/class-opalhotel-admin-menu.php' );
		// setting page
		OpalHotel::instance()->_include( 'admin/class-opalhotel-admin-setting-page.php' );
		// settings class
		OpalHotel::instance()->_include( 'admin/class-opalhotel-admin-settings.php' );
		// metaboxes
		OpalHotel::instance()->_include( 'admin/class-opalhotel-admin-metaboxes.php' );
		OpalHotel::instance()->_include( 'admin/class-opalhotel-admin-elements.php' );

	}

	/* enqueue assets */
	public function enqueue_scripts( $hook ) {

		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-datepicker' );
		$schema = is_ssl() ? 'https://' : 'http://';
		wp_enqueue_style( 'jquery-ui-datepicker', $schema . 'code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css' );

		/* register */
		wp_register_script( 'opal-hotel-room-booking', OPALHOTEL_URI . 'assets/admin/js/opalhotel.js', array( 'opalhotel-global' ), OPALHOTEL_VERSION, true );
		wp_localize_script( 'opal-hotel-room-booking', 'OpalHotel', opalhotel_i18n() );
		// wp_register_script( 'tiptip', OPALHOTEL_URI . 'assets/libraries/tiptip/jquery.tipTip.minified.js', array( 'jquery' ), OPALHOTEL_VERSION, true );
		wp_register_script( 'tiptip', OPALHOTEL_URI . 'assets/libraries/tiptip/jquery.tipTip.min.js', array( 'jquery' ), OPALHOTEL_VERSION, true );
		wp_register_style( 'opal-hotel-room-booking', OPALHOTEL_URI . 'assets/admin/css/opalhotel.css', array(), OPALHOTEL_VERSION );

		/* select2 */
		if ( in_array( $hook, array( 'edit.php', 'post.php', 'post-new.php' ) ) ) {
			// remove woocommercer scripts, styles
			$current_screen = get_current_screen();
			global $post;
			if ( ( $post && in_array( $post->post_type, array( OPALHOTEL_CPT_ROOM, OPALHOTEL_CPT_BOOKING ) ) ) || ( isset( $current_screen->post_type ) && in_array( $current_screen->post_type, array( OPALHOTEL_CPT_ROOM, OPALHOTEL_CPT_BOOKING ) ) ) ) {
				wp_dequeue_style( 'select2' );
		        wp_deregister_style( 'select2' );
		        wp_dequeue_script( 'select2');
		        wp_deregister_script('select2');

			}
			if ( $post && $post->post_type === OPALHOTEL_CPT_ROOM ) {
		        wp_dequeue_style( 'woocommerce_admin_styles' );
		        wp_deregister_style( 'woocommerce_admin_styles' );
			}

			if ( $post && $post->post_type === OPALHOTEL_CPT_ANT ) {
		        wp_enqueue_style( 'custom-fields', OPALHOTEL_URI . 'assets/admin/css/custom-fields.css' );
				wp_enqueue_script( 'custom-fields',  OPALHOTEL_URI . 'assets/admin/js/custom-fields.js' );
			}
		}
		wp_register_script( 'opalhotel-select2', OPALHOTEL_URI . 'assets/libraries/select2/js/select2.full.min.js', array( 'jquery' ), OPALHOTEL_VERSION, true );
		wp_register_style( 'opalhotel-select2', OPALHOTEL_URI . 'assets/libraries/select2/css/select2.min.css', array(), OPALHOTEL_VERSION );

		wp_register_script( 'opalhotel-momentjs', OPALHOTEL_URI . 'assets/libraries/full-calendar/moment.min.js', array( 'jquery' ), OPALHOTEL_VERSION, true );
		wp_register_script( 'opalhotel-full-calendar', OPALHOTEL_URI . 'assets/libraries/full-calendar/fullcalendar.min.js', array( 'jquery' ), OPALHOTEL_VERSION, true );
		wp_register_script( 'opalhotel-full-calendar-lang-all', OPALHOTEL_URI . 'assets/libraries/full-calendar/lang-all.js', array( 'jquery' ), OPALHOTEL_VERSION, true );
		wp_register_style( 'opalhotel-full-calendar', OPALHOTEL_URI . 'assets/libraries/full-calendar/fullcalendar.min.css', array(), OPALHOTEL_VERSION );
		/* enqueue */
		wp_enqueue_script( 'tiptip' );
		wp_enqueue_script( 'opal-hotel-room-booking' );
		wp_enqueue_style( 'opal-hotel-room-booking' );
		/* select 2*/
		wp_enqueue_script( 'opalhotel-select2' );
		wp_enqueue_style( 'opalhotel-select2' );
		/* fullcalendar */
		wp_enqueue_script( 'opalhotel-momentjs' );
		wp_enqueue_script( 'opalhotel-full-calendar' );
		wp_enqueue_script( 'opalhotel-full-calendar-lang-all' );
		wp_enqueue_style( 'opalhotel-full-calendar' );
	}

	public function register_settings() {
		// setting group
		register_setting( OPALHOTEL_SETTING_GROUP_NAME, OPALHOTEL_SETTING_GROUP_NAME );
	}

}

new OpalHotel_Admin();
