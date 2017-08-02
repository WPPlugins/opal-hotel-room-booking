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

if ( ! class_exists( 'OpalHotel_Admin_Setting_Email' ) ) {

	class OpalHotel_Admin_Setting_Email extends OpalHotel_Admin_Setting_Page {

		public $id = 'email';

		public $title = null;

		function __construct() {

			$this->title = __( 'Email', 'opal-hotel-room-booking' );

			parent::__construct();
		}

		public function get_settings() {
			return apply_filters( 'opalhotel_admin_setting_fields_' . $this->id, array(

					array(
							'type'		=> 'section_start',
							'id'		=> 'email_general_setting',
							'title'		=> __( 'Sender Options', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'email',
							'id'		=> 'opalhotel_email_from_name',
							'title'		=> __( 'From Name', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Sender\'name', 'opal-hotel-room-booking' ),
							'default'	=> get_option( 'blogname' ),
							'placeholder'	=> get_option( 'blogname' ),
						),

					array(
							'type'		=> 'email',
							'id'		=> 'opalhotel_email_from_address',
							'title'		=> __( 'From Address', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Sender\'address', 'opal-hotel-room-booking' ),
							'default'	=> get_option( 'admin_email' ),
							'placeholder'	=> get_option( 'admin_email' ),
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'email_general_setting'
						)

				) );
		}

		public function output() {
			$current_section = null;

			if ( isset( $_REQUEST['section'] ) ) {
				$current_section = sanitize_text_field( $_REQUEST['section'] );
			}

			$emails = OpalHotel()->mailer->get_mails();
			if ( $current_section && $current_section !== 'general' ) {
				foreach ( $emails as $email ) {
					if ( $email->id === $current_section ) {
						$settings = $email->admin_settings();
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

			$mails = OpalHotel()->mailer->get_mails();
			foreach( $mails as $mail ) {
				$sections[$mail->id] = $mail->title;
			}
			return apply_filters( 'opalhotel_admin_setting_sections_' . $this->id, $sections );
		}

	}

}

return new OpalHotel_Admin_Setting_Email();