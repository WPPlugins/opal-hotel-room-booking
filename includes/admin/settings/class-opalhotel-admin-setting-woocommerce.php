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

if ( ! class_exists( 'OpalHotel_Admin_Setting_WooCommercer' ) ) {

	class OpalHotel_Admin_Setting_WooCommercer extends OpalHotel_Admin_Setting_Page {

		public $id = 'woocommerce';

		public $title = null;

		function __construct() {

			$this->title = __( 'WooCommerce', 'opal-hotel-room-booking' );

			parent::__construct();
		}

		public function get_settings() {
			return apply_filters( 'opalhotel_admin_setting_fields_' . $this->id, array(

					array(
							'type'		=> 'section_start',
							'id'		=> 'woocommercer_general_setting',
							'title'			=> __( 'WooCommerce payment', 'opal-hotel-room-booking' ),
							'desc'			=> __( 'WooCommerce payment gateways.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_enable_woo_payment',
							'title'		=> __( 'Enable', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Enable woocommerce payment', 'opal-hotel-room-booking' ),
							'default'	=> 12,
							'min'		=> 1,
						),
					array(
							'type'		=> 'section_end',
							'id'		=> 'woocommercer_general_setting'
						)

				) );
		}

	}

}

return new OpalHotel_Admin_Setting_WooCommercer();