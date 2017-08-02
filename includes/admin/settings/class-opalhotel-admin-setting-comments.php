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

if ( ! class_exists( 'OpalHotel_Admin_Setting_Comments' ) ) {

	class OpalHotel_Admin_Setting_Comments extends OpalHotel_Admin_Setting_Page {

		public $id = 'comments';

		public $title = null;

		function __construct() {

			$this->title = __( 'Comments', 'opal-hotel-room-booking' );

			parent::__construct();
		}

		/* setting fields */
		public function get_settings() {
			return apply_filters( 'opalhotel_admin_setting_fields_' . $this->id, array(

					array(
							'type'		=> 'section_start',
							'id'		=> 'comment_rating_settings',
							'title'		=> __( 'Reviews Settings', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Reviews and rating for hotel options', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_enabled_rating',
							'title'		=> __( 'Enable Rating Hotel', 'opal-hotel-room-booking' ),
							'default'	=> '1'
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_enabled_require_rating',
							'title'		=> __( 'Require rating to post review.', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'select',
							'id'		=> 'opalhotel_comment_layout',
							'title'		=> __( 'Comment Layout', 'opal-hotel-room-booking' ),
							'default'	=> '',
							'options'	=> array(
									''			=> __( 'Default', 'opal-hotel-room-booking' ),
									'advance'	=> __( 'Advance', 'opal-hotel-room-booking' )
								)
						),

					array(
							'type'		=> 'select',
							'id'		=> 'opalhotel_comment_rating',
							'title'		=> __( 'Rating', 'opal-hotel-room-booking' ),
							'default'	=> '',
							'options'	=> function_exists( 'opalhotel_get_ratings_admin_options' ) ? opalhotel_get_ratings_admin_options() : array(),
							'trclass'	=> array(
									opalhotel_get_option( 'comment_layout' ) !== 'advance' ? 'hide-if-js' : ''
								)
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'comment_rating_settings'
						),
				) );
		}

	}

}

return new OpalHotel_Admin_Setting_Comments();