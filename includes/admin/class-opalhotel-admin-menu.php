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

class OpalHotel_Admin_Menu {

	public function __construct() {

		// add admin menu item
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/* admin menu */
	public function admin_menu() {

		// add menu page
		add_menu_page(
			__( 'OpalHotel', 'opal-hotel-room-booking' ),
			__( 'OpalHotel', 'opal-hotel-room-booking' ),
			'manage_options',
			'opal-hotel-room-booking',
			'',
			'dashicons-list-view',
			10
		);

		do_action( 'opalhotel_before_add_admin_menu_page' );
		// setting submenu
		add_submenu_page( 'opal-hotel-room-booking', __( 'OpalHotel Settings', 'opal-hotel-room-booking' ), __( 'Settings', 'opal-hotel-room-booking' ), 'manage_options', 'opalhotel-settings', array( 'OpalHotel_Admin_Settings', 'output' ) );

	}

}

new OpalHotel_Admin_Menu();