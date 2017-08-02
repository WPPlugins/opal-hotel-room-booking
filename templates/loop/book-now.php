<?php
/**
 * The template for displaying hotel content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-hotel/rooms.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$hotel = opalhotel_get_hotel( get_the_ID() );

$arrival = ! empty( $_REQUEST['arrival_datetime'] ) ? sanitize_text_field( $_REQUEST['arrival_datetime'] ) : date( 'Y-m-d' );
$departure = ! empty( $_REQUEST['departure_datetime'] ) ? sanitize_text_field( $_REQUEST['departure_datetime'] ) : date( 'Y-m-d', current_time( 'timestamp' ) + DAY_IN_SECONDS );
global $post;

?>

<form action="" method="POST" class="opalhotel-add-to-cart-form">
	<input type="hidden" name="arrival" value="<?php echo esc_attr( $arrival ) ?>" />
	<input type="hidden" name="departure" value="<?php echo esc_attr( $departure ) ?>" />
	<button class="button btn opalhotel-view-details"><?php esc_html_e( 'Book Now', 'opal-hotel-room-booking' ); ?></button>
</form>
