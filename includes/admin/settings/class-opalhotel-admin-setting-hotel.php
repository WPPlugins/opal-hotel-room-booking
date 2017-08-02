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

if ( ! class_exists( 'OpalHotel_Admin_Setting_Hotel' ) ) {

	class OpalHotel_Admin_Setting_Hotel extends OpalHotel_Admin_Setting_Page {

		public $id = 'hotel';

		public $title = null;

		function __construct() {

			$this->title = __( 'Hotel', 'opal-hotel-room-booking' );

			parent::__construct();
		}

		/* setting fields */
		public function get_settings() {
			return apply_filters( 'opalhotel_admin_setting_fields_' . $this->id, array(

					array(
							'type'		=> 'section_start',
							'id'		=> 'hotel_single_layout',
							'title'		=> __( 'Hotel Layout', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Appear Hotel Layout.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'select',
							'id'		=> 'opalhotel_single_hotel_layout',
							'title'		=> __( 'Single Layout', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Set global Single Layout.', 'opal-hotel-room-booking' ),
							'options'	=> opalhotel_single_hotel_layouts(),
							'default'	=> ''
						),

					array(
							'type'		=> 'number',
							'id'		=> 'opalhotel_loop_columns',
							'title'		=> __( 'Hotels & Rooms Columns', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Set how many columns in loop query.', 'opal-hotel-room-booking' ),
							'default'	=> 3,
							'min'		=> 1,
							'max'		=> 12
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'hotel_single_layout'
						),

					array(
							'type'		=> 'section_start',
							'id'		=> 'hotel_setting',
							'title'		=> __( 'Hotel Images', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Appear image size on the frontend.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'image_size',
							'id'		=> 'opalhotel_hotel_catalog_image_size',
							'title'		=> __( 'Catalog Image', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Set thumbnail image size on search results, archive, taxonomy page.', 'opal-hotel-room-booking' ),
							'options'	=> array(
									'width'		=> 360,
									'height'	=> 360
								),
							'default'	=> array(
									'width'		=> 360,
									'height'	=> 360
								)
						),

					array(
							'type'		=> 'image_size',
							'id'		=> 'opalhotel_hotel_thumbnail',
							'title'		=> __( 'Hotel Thumbnail Gallery Image', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Set thumbnail image size on single room page.', 'opal-hotel-room-booking' ),
							'options'	=> array(
									'width'		=> 84,
									'height'	=> 84
								),
							'default'	=> array(
									'width'		=> 84,
									'height'	=> 84
								)
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'hotel_setting'
						)

				) );
		}

		/**
		 * Information settings
		 *
		 * @since 1.1.7
		 */
		public function information_settings() {
			return apply_filters( 'opalhotel_admin_setting_information_fields_' . $this->id, array(

					array(
							'type'			=> 'section_start',
							'id'			=> 'opalhotel_infomation',
							'title'			=> __( 'Your hotel information','opal-hotel-room-booking' ),
							'desc'			=> __( 'Setup your hotel imformation, maybe attach when customer receive email.' )
						),

					array(
							'type'			=> 'text',
							'id'			=> 'opalhotel_hotel_name',
							'title'			=> __( 'Hotel Name', 'opal-hotel-room-booking' ),
							'default'		=> 'OpalHotel',
							'desc'     		=> __( 'This sets hotel name email infomation.', 'opal-hotel-room-booking' )
						),

					array(
							'type'			=> 'text',
							'id'			=> 'opalhotel_hotel_address',
							'title'			=> __( 'Hotel Address', 'opal-hotel-room-booking' ),
							'default'		=> 'Ha Noi',
							'desc'     		=> __( 'This sets hotel address.', 'opal-hotel-room-booking' )
						),

					array(
							'type'			=> 'text',
							'id'			=> 'opalhotel_hotel_city',
							'title'		=> __( 'City', 'opal-hotel-room-booking' ),
							'default'		=> 'Ha Noi',
							'desc'     		=> __( 'This sets hotel city.', 'opal-hotel-room-booking' )
						),

					array(
							'type'			=> 'text',
							'id'			=> 'opalhotel_hotel_state',
							'title'		=> __( 'State', 'opal-hotel-room-booking' ),
							'default'		=> 'Hanoi Daewoo Hotel',
							'desc'     		=> __( 'This sets hotel state.', 'opal-hotel-room-booking' )
						),

					array(
							'type'			=> 'text',
							'id'			=> 'opalhotel_hotel_country',
							'title'			=> __( 'Country', 'opal-hotel-room-booking' ),
							'default'		=> 'Vietnam',
							'desc'     		=> __( 'This sets hotel country.', 'opal-hotel-room-booking' )
						),

					array(
							'type'			=> 'text',
							'id'			=> 'opalhotel_hotel_zip_code',
							'title'			=> __( 'Zip / Postal Code', 'opal-hotel-room-booking' ),
							'default'		=> 10000,
							'desc'     		=> __( 'This sets hotel Zip / Postal Code.', 'opal-hotel-room-booking' )
						),

					array(
							'type'			=> 'text',
							'id'			=> 'opalhotel_hotel_phone_number',
							'title'			=> __( 'Phone Number', 'opal-hotel-room-booking' ),
							'default'		=> '0123456789',
							'desc'     		=> __( 'This sets hotel\'s phone number.', 'opal-hotel-room-booking' )
						),

					array(
							'type'			=> 'text',
							'id'			=> 'opalhotel_hotel_fax_number',
							'title'			=> __( 'Fax', 'opal-hotel-room-booking' ),
							'default'		=> '9876543210',
							'desc'     		=> __( 'This sets hotel\'s fax number.', 'opal-hotel-room-booking' )
						),

					array(
							'type'			=> 'text',
							'id'			=> 'opalhotel_hotel_email_address',
							'title'			=> __( 'Email', 'opal-hotel-room-booking' ),
							'default'		=> get_option( 'admin_email' ),
							'desc'     		=> __( 'This sets hotel\'s email address public to your customer.', 'opal-hotel-room-booking' )
						),

					array(
							'type'			=> 'section_end',
							'id'			=> 'opalhotel_infomation'
						)

				) );
		}

		public function get_sections() {
			$sections = array();
			$sections['general'] 		= __( 'General', 'opal-hotel-room-booking' );
			$sections['information']	= __( 'Information','opal-hotel-room-booking' );
			return apply_filters( 'opalhotel_admin_setting_sections_' . $this->id, $sections );
		}

		public function output() {
			$current_section = 'general';

			if ( isset( $_REQUEST['section'] ) ) {
				$current_section = sanitize_text_field( $_REQUEST['section'] );
			}

			if ( $current_section !== 'general' ) {
				OpalHotel_Admin_Settings::render_fields( $this->information_settings() );
			} else {
				parent::output();
			}
		}

	}

}

return new OpalHotel_Admin_Setting_Hotel();