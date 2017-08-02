<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/loop-hotel/discount.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$arrival = ! empty( $_REQUEST['arrival_datetime'] ) ? sanitize_text_field( $_REQUEST['arrival_datetime'] ) : date( 'Y-m-d' );
$departure = ! empty( $_REQUEST['departure_datetime'] ) ? sanitize_text_field( $_REQUEST['departure_datetime'] ) : date( 'Y-m-d', current_time( 'timestamp' ) + DAY_IN_SECONDS );
$qty = ! empty( $_REQUEST['number_of_rooms'] ) ? absint( $_REQUEST['number_of_rooms'] ) : 1 ;

$adult = isset( $_REQUEST['adult'] ) ? absint( $_REQUEST['adult'] ) : 1;
$child = isset( $_REQUEST['child'] ) ? absint( $_REQUEST['child'] ) : 0;
$night = opalhotel_count_nights( strtotime( $arrival ), strtotime( $departure ) );

$hotel = opalhotel_get_hotel( get_the_ID() );
$room_id = $hotel->get_the_most_cheap_room();

$room = opalhotel_get_room( $room_id );
$base_price = $room->base_price() * $night * $qty;
$price = $room->get_price( array( 'arrival' => $arrival, 'departure' => $departure, 'adult' => $adult, 'child' => $child, 'qty' => $qty ) );

if ( $price >= $base_price ) return;

$discount = number_format( ( $base_price - $price ) * 100 / $base_price, 2 );

?>

<label class="discount">
	<?php printf( '%s %s', __( 'Save', 'opal-hotel-room-booking' ), $discount ); ?>%
</label>
