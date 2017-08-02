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

$sidebars = apply_filters( 'opalhotel_sidebar_arguments', array(
		array(
			'name'          => __( 'OpalHotel Sidebar', 'opal-hotel-room-booking' ),
			'id'            => 'opalhotel-sidebar',
			'description'   => __( 'Appears at the left of the content.', 'opal-hotel-room-booking' ),
	        'class'         => '',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widgettitle">',
			'after_title'   => '</h2>'
		)
) );

if ( ! empty( $sidebars ) ) {
	foreach ( $sidebars as $sidebar ) {
	   /**
		* Creates a sidebar
		* @param string|array  Builds Sidebar based off of 'name' and 'id' values.
		*/
		register_sidebar( $sidebar );
	}
}