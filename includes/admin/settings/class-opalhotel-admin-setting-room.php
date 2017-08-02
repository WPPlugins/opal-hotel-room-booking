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


if ( ! class_exists( 'OpalHotel_Admin_Setting_Room' ) ) {

	class OpalHotel_Admin_Setting_Room extends OpalHotel_Admin_Setting_Page {

		public $id = 'room';

		public $title = null;

		function __construct() {

			$this->title = __( 'Rooms', 'opal-hotel-room-booking' );

			parent::__construct();
		}

		public function get_settings() {
			return apply_filters( 'opalhotel_admin_setting_fields_' . $this->id, array(

					array(
							'type'		=> 'section_start',
							'id'		=> 'room_setting',
							'title'		=> __( 'Room Images', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Appear image size on the frontend.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'image_size',
							'id'		=> 'opalhotel_room_catalog_image_size',
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
							'id'		=> 'opalhotel_room_thumbnail',
							'title'		=> __( 'Room Thumbnail Gallery Image', 'opal-hotel-room-booking' ),
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
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_room_lightbox_enable',
							'title'		=> __( 'Enable Lightbox Search Results', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Allows customers view gallery of room.', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'room_setting'
						)

				) );
		}

	}

}

return new OpalHotel_Admin_Setting_Room();