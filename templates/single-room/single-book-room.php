<?php
/**
 * The template for displaying room content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-room/single-book-room.php.
 *
 */

defined( 'ABSPATH' ) || exit();

global $post;

if ( ! is_singular( 'opalhotel_room' ) || ! $post || $post->post_type !== 'opalhotel_room' ) {
	return;
}

$room = opalhotel_get_room( $post->ID );
?>

<a href="#" class="opalhotel-single-book-room button button-primary" data-id="<?php echo esc_attr( $post->ID ) ?>"><?php esc_html_e( 'Book Now', 'opal-hotel-room-booking' ); ?></a>