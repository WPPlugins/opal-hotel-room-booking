<?php
/**
 * The template for displaying room content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-room/room-details/descriptions.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>

<div class="room-description">
	<h4><?php esc_html_e( 'Description', 'opal-hotel-room-booking' ); ?></h4>
	<div class="content">
		<?php the_content() ?>
	</div>
</div>