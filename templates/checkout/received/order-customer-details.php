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

<?php do_action( 'opalhotel_before_order_customer_details', $order ); ?>

<div class="opalhotel_order_customer_details">
	
	<h3><?php esc_html_e( 'Customer Details', 'opal-hotel-room-booking' ); ?></h3>

	
	<div class="row">
		<div class="customer col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<label><?php esc_html_e( 'Name', 'opal-hotel-room-booking' ); ?></label>
			<span class="customer_name"><?php printf( '%s', $order->get_customer_name() ) ?></span>
		</div>
		<div class="customer col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<label><?php esc_html_e( 'Email', 'opal-hotel-room-booking' ); ?></label>
			<span class="customer_email"><?php printf( '%s', $order->customer_email ) ?></span>
		</div>
		<div class="customer col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<label><?php esc_html_e( 'Phone', 'opal-hotel-room-booking' ); ?></label>
			<span class="customer_phone"><?php printf( '%s', $order->customer_phone ) ?></span>
		</div>
		<div class="customer col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<label><?php esc_html_e( 'Address', 'opal-hotel-room-booking' ); ?></label>
			<span class="customer_address"><?php printf( '%s', $order->customer_address ) ?></span>
		</div>
	</div>
	
	<div class="row">
		<div class="customer col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<label><?php esc_html_e( 'State', 'opal-hotel-room-booking' ); ?></label>
			<span class="customer_state"><?php printf( '%s', $order->customer_state ) ?></span>
		</div>
		<div class="customer col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<label><?php esc_html_e( 'City', 'opal-hotel-room-booking' ); ?></label>
			<span class="customer_city"><?php printf( '%s', $order->customer_city ) ?></span>
		</div>
		<div class="customer col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<label><?php esc_html_e( 'Country', 'opal-hotel-room-booking' ); ?></label>
			<span class="customer_country"><?php printf( '%s', $order->customer_country ) ?></span>
		</div>
	</div>

	<p><?php printf( '%s', $order->get_customer_notes() ) ?></p>
</div>

<?php do_action( 'opalhotel_after_order_customer_details', $order ); ?>