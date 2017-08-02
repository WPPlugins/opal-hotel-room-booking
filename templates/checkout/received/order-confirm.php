<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/checkout/received/order-confirm.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<?php do_action( 'opalhotel_before_order_confirm', $order ); ?>

<div class="opalhotel_order_confirm_order">

	<?php _e( '<h3>Thank you. Your reservation has been received.</h3>', 'opal-hotel-room-booking' ) ?>

	<div class="order_number column">
		<label><?php esc_html_e( 'Reservation Number', 'opal-hotel-room-booking' ); ?></label>
		<?php printf( '%s', $order->get_order_number() ) ?>
	</div>

	<div class="payment_method column">
		<label><?php esc_html_e( 'Payment Method', 'opal-hotel-room-booking' ); ?></label>
		<?php printf( '%s', $order->payment_method_title ) ?>
	</div>

	<div class="date column">
		<label><?php esc_html_e( 'Arrival', 'opal-hotel-room-booking' ); ?></label>
		<?php printf( '%s', opalhotel_format_date( $order->get_arrival_date() ) ) ?>
	</div>

	<div class="date column">
		<label><?php esc_html_e( 'Departure', 'opal-hotel-room-booking' ); ?></label>
		<?php printf( '%s', opalhotel_format_date( $order->get_departure_date() ) ) ?>
	</div>

	<div class="order_number column">
		<label><?php esc_html_e( 'Total', 'opal-hotel-room-booking' ); ?></label>
		<?php printf( '%s', opalhotel_format_price( $order->total, $order->payment_currency ) ) ?>
	</div>

</div>

<?php do_action( 'opalhotel_after_order_confirm', $order ); ?>