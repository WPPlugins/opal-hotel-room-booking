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

if ( ! class_exists( 'OpalHotel_Admin_Setting_General' ) ) {

	class OpalHotel_Admin_Setting_General extends OpalHotel_Admin_Setting_Page {

		public $id = 'general';

		public $title = null;

		function __construct() {

			$this->title = __( 'General', 'opal-hotel-room-booking' );

			parent::__construct();
		}

		/* setting fields */
		public function get_settings() {
			return apply_filters( 'opalhotel_admin_setting_fields_' . $this->id, array(

					array(
							'type'		=> 'section_start',
							'id'		=> 'general_settings',
							'title'		=> __( 'Page Settings', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Pages default of system.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'select_page',
							'id'		=> 'opalhotel_hotel_available_page_id',
							'title'		=> __( 'Hotel Search Available', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'select_page',
							'id'		=> 'opalhotel_reservation_page_id',
							'title'		=> __( 'Reservation Page', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'select_page',
							'id'		=> 'opalhotel_favorited_page_id',
							'title'		=> __( 'Favorite Page', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Show up hotels favorited list', 'opal-hotel-room-booking' )
						),

					// array(
					// 		'type'		=> 'select_page',
					// 		'id'		=> 'opalhotel_available_page_id',
					// 		'title'		=> __( 'Check Available Page', 'opal-hotel-room-booking' ),
					// 		'desc'		=> __( 'Available results', 'opal-hotel-room-booking' )
					// 	),

					// array(
					// 		'type'		=> 'select_page',
					// 		'id'		=> 'opalhotel_cart_page_id',
					// 		'title'		=> __( 'Cart Review', 'opal-hotel-room-booking' ),
					// 		'desc'		=> __( 'Review room selected', 'opal-hotel-room-booking' )
					// 	),

					// array(
					// 		'type'		=> 'select_page',
					// 		'id'		=> 'opalhotel_account_page_id',
					// 		'title'		=> __( 'Account Page', 'opal-hotel-room-booking' )
					// 	),

					array(
							'type'		=> 'select_page',
							'id'		=> 'opalhotel_terms_page_id',
							'title'		=> __( 'Terms And Conditions Page', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_terms_require',
							'title'		=> __( 'Term require', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'general_settings'
						),

					array(
							'type'		=> 'section_start',
							'id'		=> 'reservation_settings',
							'title'		=> __( 'Resevation Settings', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Resevation default of system.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'number',
							'id'		=> 'opalhotel_max_rooms_number',
							'title'		=> __( 'Max Room Number', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Optional for Hotel Search Form', 'opal-hotel-room-booking' ),
							'default'	=> 5,
							'step'		=> 1,
							'min'		=> 0
						),

					array(
							'type'		=> 'number',
							'id'		=> 'opalhotel_search_min_price',
							'title'		=> __( 'Search Min Price', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Optional for Hotel Search Form', 'opal-hotel-room-booking' ),
							'default'	=> 0,
							'step'		=> 'any',
							'min'		=> 0
						),

					array(
							'type'		=> 'number',
							'id'		=> 'opalhotel_search_max_price',
							'title'		=> __( 'Search Max Price', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Optional for Hotel Search Form', 'opal-hotel-room-booking' ),
							'default'	=> 2500,
							'step'		=> 'any',
							'min'		=> 0
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_search_enable_max_child',
							'title'		=> __( 'Search Enable Children', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Optional for Rooms Search Form', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_search_enable_room_type',
							'title'		=> __( 'Search Enable Room Type', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Optional for Rooms Search Form', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'number',
							'id'		=> 'opalhotel_search_available_max_adult',
							'title'		=> __( 'Max Adult Available Search', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Optional for Rooms & Hotels Search Form', 'opal-hotel-room-booking' ),
							'default'	=> '',
							'min'		=> 0,
							'step'		=> 1
						),

					array(
							'type'		=> 'number',
							'id'		=> 'opalhotel_search_available_max_child',
							'title'		=> __( 'Max Children Available Search', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Optional for Rooms Search Form', 'opal-hotel-room-booking' ),
							'default'	=> '',
							'min'		=> 0,
							'step'		=> 1
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'reservation_settings'
						),

					array(
							'type'		=> 'section_start',
							'id'		=> 'google_settings',
							'title'		=> __( 'Google Settings', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_google_map_api_key',
							'title'		=> __( 'Google Map API', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'This sets the google map api key.', 'opal-hotel-room-booking' ),
							'default'	=> 'AIzaSyDRVUZdOrZ1HuJFaFkDtmby0E93eJLykIk'
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'google_settings'
						),
					array(
							'type'		=> 'section_start',
							'id'		=> 'currency_settings',
							'title'		=> __( 'Currency Settings', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'The following options affect how prices are displayed on the frontend.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'select',
							'id'		=> 'opalhotel_currency',
							'desc'		=> __( 'This controls what currency prices are listed at in the catalog and which currency gateways will take payments in.', 'opal-hotel-room-booking' ),
							'title'		=> __( 'Currency', 'opal-hotel-room-booking' ),
							'options'	=> opalhotel_currencies(),
							'default'	=> 'USD'
						),

					array(
							'type'		=> 'select',
							'id'		=> 'opalhotel_price_currency_position',
							'desc'		=> __( 'The following options affect how prices are displayed on the frontend.', 'opal-hotel-room-booking' ),
							'title'		=> __( 'Currency Position', 'opal-hotel-room-booking' ),
							'options'	=> array(
									'left'		=> __('Left ( $69.99 )', 'opal-hotel-room-booking'),
									'right'		=> __('Right ( 69.99$ )', 'opal-hotel-room-booking'),
									'left_with_space'	=> __('Left with space ( $ 69.99 )', 'opal-hotel-room-booking'),
									'right_with_space'	=> __('Right with space ( 69.99 $ )', 'opal-hotel-room-booking')
								),
							'default'	=> 'left'
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_price_thousands_separator',
							'title'		=> __( 'Thousands Separator', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'This sets the thousand separator of displayed prices.', 'opal-hotel-room-booking' ),
							'default'	=> ','
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_price_decimals_separator',
							'title'		=> __( 'Decimals Separator', 'opal-hotel-room-booking' ),
							'desc'     => __( 'This sets the decimal separator of displayed prices.', 'opal-hotel-room-booking' ),
							'default'	=> '.'
						),

					array(
							'type'		=> 'number',
							'id'		=> 'opalhotel_price_number_of_decimal',
							'title'		=> __( 'Number of decimal', 'opal-hotel-room-booking' ),
							'desc'     => __( 'This sets the number of decimal points shown in displayed prices.', 'opal-hotel-room-booking' ),
							'default'	=> 1,
							'min'		=> 0,
							'max'		=> 3,
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'currency_settings'
						),
				) );
		}

	}

}

return new OpalHotel_Admin_Setting_General();