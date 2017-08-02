<?php
/**
 * The template for displaying hotel content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-hotel/title.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<div class="hotel-box">
	<h3 class="title"><?php esc_html_e( 'Description', 'opal-hotel-room-booking' ); ?></h3>
	<div class="content">
		<?php the_content(); ?>
	</div>
</div>
