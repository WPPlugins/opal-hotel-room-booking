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

<?php esc_html_e( 'Customer Details', 'opal-hotel-room-booking' ); ?>

<?php printf( '%s', $order->get_customer_name() ) ?>
<?php printf( '%s', $order->customer_phone ) ?>
<?php printf( '%s', $order->customer_address ) ?>
<?php printf( '%s', $order->customer_state ) ?>
<?php printf( '%s', $order->customer_state ) ?>
<?php printf( '%s', $order->customer_country ) ?>
<?php printf( '%s', $order->get_customer_notes() ) ?>

<?php do_action( 'opalhotel_after_email_customer_details', $order ); ?>