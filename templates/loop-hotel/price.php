<?php

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

?>

<div class="opalhotel-price">
	<!-- has sale -->
	<?php if ( $base_price > $price ) : ?>
		<del>
			<span class="price-value base-price">
			<?php printf( __( '%s', 'opal-hotel-room-booking' ), opalhotel_format_price( $base_price ) ) ?>
			</span>
		</del>
	<?php endif; ?>

	<ins>
		<span class="price-value">
			<?php printf( __( '%s', 'opal-hotel-room-booking' ), opalhotel_format_price( $room->get_price_display( $price ) ) ) ?>
		</span>
	</ins>
	<span class="price-title"><?php esc_html_e( 'From', 'opal-hotel-room-booking' ); ?></span> / <span class="price-unit"><?php echo ( $night == 1 ) ? esc_html( ' per night', 'opal-hotel-room-booking' ) : sprintf( '%d %s', $night, esc_html( 'nights', 'opal-hotel-room-booking' ) ) ?></span>
</div>