<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/loop-hotel/descriptions.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$hotel = opalhotel_get_hotel( get_the_ID() );

$arrival_datetime = ! empty( $_REQUEST['arrival_datetime'] ) ? $_REQUEST['arrival_datetime'] : date( 'Y-m-d' );
$departure_datetime = ! empty( $_REQUEST['departure_datetime'] ) ? $_REQUEST['departure_datetime'] : date( 'Y-m-d', time() + DAY_IN_SECONDS );
$adult = isset( $_REQUEST['adult'] ) ? absint( $_REQUEST['adult'] ) : 1;
$child = isset( $_REQUEST['child'] ) ? absint( $_REQUEST['child'] ) : 0;
$number_of_rooms = isset( $_REQUEST['number_of_rooms'] ) ? absint( $_REQUEST['number_of_rooms'] ) : 1;

$book_url = add_query_arg( array(
		'arrival_datetime'	=> $arrival_datetime,
		'departure_datetime'=> $departure_datetime,
		'adult'				=> $adult,
		'child'				=> $child,
		'number_of_rooms'	=> $number_of_rooms
	), get_the_permalink() );

?>

<a href="<?php echo esc_url( $book_url ) ?>" data-id="<?php echo esc_attr( get_the_ID() ) ?>" class="opalhotel-book-now opalhotel-book-now-hotel opalhotel-submit btn button btn-primary">
	<?php esc_html_e( 'Book Now', 'opal-hotel-room-booking' ); ?>
</a>