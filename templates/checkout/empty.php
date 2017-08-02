<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/checkout/empty.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/* print notices */
opalhotel_print_notices();
?>

<div class="opalhotel-no-room-selected">
	<?php esc_html_e( 'Your cart is currently empty.', 'opal-hotel-room-booking' ); ?>
</div>