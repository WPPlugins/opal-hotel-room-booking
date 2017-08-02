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

/* set global */
global $opalhotel_shortcodes;

$opalhotel_shortcodes = array();
$opalhotel_shortcodes['rooms'] 				= new OpalHotel_Shortcode_Rooms();
$opalhotel_shortcodes['checkout'] 			= new OpalHotel_Shortcode_Checkout();
$opalhotel_shortcodes['availalbe'] 			= new OpalHotel_Shortcode_Available();
$opalhotel_shortcodes['reservation'] 		= new OpalHotel_Shortcode_Reservation();
$opalhotel_shortcodes['mini_cart'] 			= new OpalHotel_Shortcode_Mini_Cart();
$opalhotel_shortcodes['hotel_info'] 		= new OpalHotel_Shortcode_Hotel_Info();
$opalhotel_shortcodes['hotels'] 			= new OpalHotel_Shortcode_Hotels();
$opalhotel_shortcodes['hotels_grid'] 		= new OpalHotel_Shortcode_Hotels_Grid();
$opalhotel_shortcodes['single-book-room'] 	= new OpalHotel_Shortcode_Single_Book_Room();

return $opalhotel_shortcodes;