<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/loop-hotel/count-room-available.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $post;

if ( ! isset( $post->available ) ) {
	return;
}

?>

<span class="count-rooms-available"><i class="fa fa-bed" aria-hidden="true"></i><?php printf( translate_nooped_plural( _n_noop( '%s room left', '%s rooms left' ), $post->available, 'opal-hotel-room-booking' ), $post->available ) ?></span>