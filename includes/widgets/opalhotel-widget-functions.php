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

add_action( 'widgets_init', 'opalhotel_widgets_init' );
if ( ! function_exists( 'opalhotel_widgets_init' ) ) {
	/* widget init */
	function opalhotel_widgets_init() {

		$widgets = apply_filters( 'opalhotel_widgets_init', array(
				'OpalHotel_Widget_Check_Available',
				'OpalHotel_Widget_Mini_Cart',
				'OpalHotel_Widget_Hotel_Information',
				'OpalHotel_Widget_Hotels_Grid',
				'OpalHotel_Widget_Hotels',
				'OpalHotel_Widget_Single_Book_Room',
				'OpalHotel_Widget_Rooms',
				'OpalHotel_Widget_Rooms_Lastest_Deals',
				'OpalHotel_Widget_Hotels_Lastest_Deals',
				'OpalHotel_Widget_Hotel_Destination',
				'OpalHotel_Widget_Single_Hotel_Destination',
				'OpalHotel_Widget_Form_Hotel_Available',
				'OpalHotel_Widget_Hotel_Available_Filter'
			) );

		foreach ( $widgets as $widget ) {
			register_widget( $widget );
		}
	}
}