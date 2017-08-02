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

if ( ! class_exists( 'OpalHotel_Admin_Setting_Tax' ) ) {

	class OpalHotel_Admin_Setting_Tax extends OpalHotel_Admin_Setting_Page {

		public $id = 'tax';

		public $title = null;

		function __construct() {

			$this->title = __( 'Tax', 'opal-hotel-room-booking' );

			parent::__construct();
		}

		/* setting fields */
		public function get_settings() {
			return apply_filters( 'opalhotel_admin_setting_fields_' . $this->id, array(

					array(
							'type'		=> 'section_start',
							'id'		=> 'tax_settings',
							'title'		=> __( 'Tax Settings', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Tax setting will appear on frontend.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_tax_enable',
							'title'		=> __( 'Enable Tax', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Enable tax will appear on frontend.', 'opal-hotel-room-booking' ),
							'sub_desc'	=> __( 'Enable taxes and tax calculations', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'number',
							'id'		=> 'opalhotel_tax',
							'title'		=> __( 'Tax', 'opal-hotel-room-booking' ),
							'desc'     	=> __( 'This set percent tax.', 'opal-hotel-room-booking' ),
							'default'	=> 10,
							'min'		=> 0,
							'step'		=> 'any'
						),

					array(
							'type'		=> 'select',
							'id'		=> 'opalhotel_tax_incl_room',
							'title'		=> __( 'Include Tax In Room Price', 'opal-hotel-room-booking' ),
							'options'	=> array(
									0	=> __( 'Exclude tax', 'opal-hotel-room-booking' ),
									1	=> __( 'Include tax', 'opal-hotel-room-booking' )
								)
						),

					array(
							'type'		=> 'select',
							'id'		=> 'opalhotel_tax_incl_cart',
							'title'		=> __( 'Include Tax In Cart', 'opal-hotel-room-booking' ),
							'options'	=> array(
									0	=> __( 'Exclude tax', 'opal-hotel-room-booking' ),
									1	=> __( 'Include tax', 'opal-hotel-room-booking' )
								)
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'tax_settings'
						)
				) );
		}

	}

}

return new OpalHotel_Admin_Setting_Tax();