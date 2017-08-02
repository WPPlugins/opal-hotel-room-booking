<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/checkout/order-recived.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<div class="opalhotel_order_recived">

	<div class="row">
		<div class="col-md-8 col-xs-12">

			<!-- received/order-confirm.php -->
			<?php do_action( 'opalhotel_reservation_order_confirm', $order ); ?>

			<!-- received/order-customer-details.php -->
			<?php do_action( 'opalhotel_reservation_customer_details', $order ); ?>
		</div>
		<div class="col-md-4 col-xs-12">
			<!-- received/order-details.php -->
			<?php do_action( 'opalhotel_reservation_order_details', $order ); ?>
		</div>
	</div>

</div>
