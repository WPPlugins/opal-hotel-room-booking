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

if ( ! class_exists( 'OpalHotel_Admin_Setting_Checkout' ) ) {

	class OpalHotel_Admin_Setting_Checkout extends OpalHotel_Admin_Setting_Page {

		public $id = 'checkout';

		public $title = null;

		function __construct() {

			$this->title = __( 'Checkout', 'opal-hotel-room-booking' );

			parent::__construct();
		}

		public function get_settings() {
			return apply_filters( 'opalhotel_admin_setting_fields_' . $this->id, array(

					array(
							'type'		=> 'section_start',
							'id'		=> 'payment_general_setting'
						),

					array(
							'type'		=> 'number',
							'id'		=> 'opalhotel_cancel_payment',
							'title'		=> __( 'Cancel Payment', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Cancel Payment after hour(s)', 'opal-hotel-room-booking' ),
							'default'	=> 12,
							'min'		=> 1,
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_guest_checkout',
							'title'		=> __( 'Process', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Allows customers to checkout without creating an account.', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'payment_general_setting'
						),

					array(
							'type'		=> 'section_start',
							'title'		=> __( 'Checkout Enpoint', 'opal-hotel-room-booking' ),
							'id'		=> 'payment_reservation'
						),

					array(
							'type'		=> 'select_page',
							'id'		=> 'opalhotel_checkout_page_id',
							'title'		=> __( 'Checkout Page', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'You should be place shortcode <code>[opalhotel_checkout]</code> inside page content', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_reservation-received_enpoint',
							'title'		=> __( 'Reservation received', 'opal-hotel-room-booking' ),
							'default'	=> 'reservation-received',
							'placeholder'	=>  'reservation-received'
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_reservation-cancelled_enpoint',
							'title'		=> __( 'Reservation cancelled', 'opal-hotel-room-booking' ),
							'default'	=> 'reservation-cancelled',
							'placeholder'	=>  'reservation-cancelled'
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_reservation-notify_enpoint',
							'title'		=> __( 'Reservation notify', 'opal-hotel-room-booking' ),
							'default'	=> 'reservation-notify',
							'placeholder'	=>  'reservation-notify'
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'payment_reservation'
						)

				) );
		}

		public function output() {
			$current_section = null;

			if ( isset( $_REQUEST['section'] ) ) {
				$current_section = sanitize_text_field( $_REQUEST['section'] );
			}

			$payments = OpalHotel()->payment_gateways->get_payments();
			if ( $current_section && $current_section !== 'general' ) {
				foreach ( $payments as $payment ) {
					if ( $payment->id === $current_section ) {
						$settings = $payment->admin_settings();
						OpalHotel_Admin_Settings::render_fields( $settings );
						break;
					}
				}
			} else {
				parent::output();
			}
		}

		public function get_sections() {
			$sections = array();
			$sections['general'] = __( 'General', 'opal-hotel-room-booking' );

			$payments = OpalHotel()->payment_gateways->get_payments();
			foreach( $payments as $payment ) {
				$sections[$payment->id] = $payment->title;
			}
			return apply_filters( 'opalhotel_admin_setting_sections_' . $this->id, $sections );
		}

	}

}

return new OpalHotel_Admin_Setting_Checkout();