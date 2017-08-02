<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$hotel = opalhotel_get_hotel( get_the_ID() );
$room_id = $hotel->get_the_most_cheap_room();

$room = opalhotel_get_room( $room_id );