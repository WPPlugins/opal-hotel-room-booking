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

/* print notices when apply coupon */
opalhotel_print_notices();

?>

<div class="opalhotel_checkout_booking_detail">

	<h3 class="opalhotel-section-title"><?php esc_html_e( 'Your Reservation', 'opal-hotel-room-booking' ); ?></h3>

	<?php opalhotel_get_template( 'search/review.php' ); ?>

</div>
