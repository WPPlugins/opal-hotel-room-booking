<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/checkout/received/order-customer-details.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<?php do_action( 'opalhotel_before_email_customer_details', $order ); ?>

<h3><?php esc_html_e( 'Customer Details', 'opal-hotel-room-booking' ); ?></h3>

<div class="opalhotel_order_customer_details">

	<strong class="customer customer_name"><?php printf( '%s', $order->get_customer_name() ) ?></strong>
	<strong class="customer customer_phone"><?php printf( '%s', $order->customer_email ) ?></strong>
	<strong class="customer customer_email"><?php printf( '%s', $order->customer_phone ) ?></strong>
	<address>
		<small class="customer customer_address"><?php printf( '%s', $order->customer_address ) ?></small>
		<small class="customer customer_state"><?php printf( '%s', $order->customer_state ) ?></small>
		<small class="customer customer_city"><?php printf( '%s', $order->customer_city ) ?></small>
		<small class="customer customer_country"><?php printf( '%s', $order->customer_country ) ?></small>
	</address>

	<p><?php printf( '%s', $order->get_customer_notes() ) ?></p>
</div>

<?php do_action( 'opalhotel_after_email_customer_details', $order ); ?>